<?php

namespace SW_WAPF_PRO\Includes\Models {

    use SW_WAPF_PRO\Includes\Classes\Config;
    use SW_WAPF_PRO\Includes\Classes\Enumerable;

    if (!defined('ABSPATH')) {
        die;
    }

    class Field
    {

        public $id;

        public $label;

        public $description;

        public $type;

        public $required;

        public $options;

        public $conditionals;

        public $class;

        public $width;

        public $pricing;

        public $qty_based; 
        public $clone_txt; 
	    public $parent_qty_based; 

	    public $parent_clone;

        public $products;

	    public $clone;

	    private $choices_have_pricing;

        public $meta = [];

        public function __construct()
        {
            $this->label = '';
            $this->required = false;
            $this->options = [];
            $this->conditionals = [];
            $this->parent_clone = [];
            $this->products = [];
            $this->pricing = new FieldPricing();
            $this->parent_qty_based = false;
            $this->clone = [ 'enabled' => false ];

            $this->choices_have_pricing = null;

        }

        public function from_array($a) {

        	$this->id = $a['id'];
        	$this->label = $a['label'];
        	$this->description = $a['description'];
        	$this->type = $a['type'];
        	$this->required = $a['required'];
        	$this->class = $a['class'];
        	$this->width = $a['width'];
        	$this->qty_based = isset($a['qty_based']) ? $a['qty_based'] : false; 
        	$this->parent_qty_based = isset($a['parent_qty_based']) ? $a['parent_qty_based'] : false; 
	        if( !empty($a['clone_txt'])) $this->clone_txt = $a['clone_txt']; 

        	if( isset( $a['clone'] ) )
        		$this->clone = $a['clone'];

	        if( ! empty( $a['parent_clone'] ) )
		        $this->parent_clone = $a['parent_clone'];

            if( ! empty( $a['products'] ) )
                $this->products = $a['products'];

        	$this->options = $a['options'];
        	$p = new FieldPricing();
        	$p->type = $a['pricing']['type'];
        	$p->enabled = $a['pricing']['enabled'];
        	$p->amount = $a['pricing']['amount'];
        	$this->pricing = $p;

        	foreach($a['conditionals'] as $c) {
        		$cond = new Conditional();
        		foreach($c['rules'] as $r) {
        			$rule = new ConditionalRule();
        			$rule->condition = $r['condition'];
        			$rule->value = $r['value'];
        			if(isset($r['generated']))
        			    $rule->generated = $r['generated'];
        			$rule->field = $r['field'];
        			$cond->rules[] = $rule;
		        }
        		$this->conditionals[] = $cond;
	        }

            $this->meta = Config::get_field_definition_for( $this->type );

        	return $this;

        }

        public function to_array() {

        	$a = [
        		'id'                => $this->id,
        		'label'             => $this->label,
		        'description'       => $this->description,
		        'type'              => $this->type,
		        'required'          => $this->required,
		        'class'             => $this->class,
		        'width'             => $this->width,
		        'parent_clone'      => $this->parent_clone,
		        'options'           => $this->options,
		        'conditionals'      => [],
		        'clone'             => $this->clone,
		        'pricing'           => [
		        	'type'          => $this->pricing->type,
			        'amount'        => $this->pricing->amount,
			        'enabled'       => $this->pricing->enabled
		        ]
	        ];

            if( ! empty( $this->products ) )
                $a['products'] = $this->products;

        	foreach ($this->conditionals as $conditional) {
        		$c = ['rules' => [] ];

        		foreach ($conditional->rules as $rule) {
        			$r = [
        				'condition' => $rule->condition,
				        'value'     => $rule->value,
				        'field'     => $rule->field,
				        'generated' => $rule->generated
			        ];
        			$c['rules'][] = $r;
		        }

        		$a['conditionals'][] = $c;

	        }

        	return $a;

        }

        public function get_label() {

        	if(!empty($this->label))
        		return $this->label;

        	if($this->type === 'true-false' && !empty($this->options['message']))
        		return $this->options['message'];

        	return __('N/a','sw-wapf');

        }

        public function get_option($key, $default = null) {
        	if( isset( $this->options[ $key ] ) )
        		return $this->options[$key];
        	return $default;
        }

        public function is_quantities_field() {
            return $this->type === 'image-swatch-qty';
        }

        public function is_choice_field() {
            return ! empty( $this->meta['multi_choice'] );
        }

        public function is_multichoice_field() {
            return ! empty( $this->meta['multi_select'] );
        }

        public function is_normal_field() {
        	return !$this->is_content_field() && !$this->is_layout_field();
        }

        public function is_content_field() {
        	return in_array($this->type, ['p','img'] );
        }

        public function has_products() {

            return !empty( $this->meta['has_products'] );

        }

        public function is_layout_field(){
	        return in_array($this->type, ['section','sectionend'] );
        }

        public function has_conditionals() {
        	return count($this->conditionals) > 0;
        }

        public function get_clone_label() {

        	if( ! empty( $this->clone_txt) )
        		return $this->clone_txt;

	        return !$this->clone['enabled'] || empty( $this->clone['label']) ? '' : $this->clone['label'];

        }

        public function get_clone_type( $include_parent = false ) {

        	if( ! $this->clone['enabled'] ) {
        		if( $this->qty_based ) { 
        			return 'qty';
		        }
        		return $include_parent ? self::get_parent_clone_type() : '';
	        }

        	return $this->clone['type'];
        }

	    public function get_parent_clone_type() {

        	if( empty( $this->parent_clone ) && $this->parent_qty_based ) { 
        		return 'qty';
	        }

        	return empty( $this->parent_clone ) ? '' : $this->parent_clone['type'];
	    }

        public function pricing_enabled() {

	        if($this->is_choice_field() && !empty($this->options['choices'])) {

		        if($this->choices_have_pricing != null) return $this->choices_have_pricing;

		        $this->choices_have_pricing = Enumerable::from($this->options['choices'])->any(function($choice) {
			        return isset($choice['pricing_type']) && $choice['pricing_type'] !== 'none';
		        });

		        return $this->choices_have_pricing;
	        }

	        return $this->pricing->enabled;

        }

    }
}
<?php

namespace SW_WAPF_PRO\Includes\Classes {


    use SW_WAPF_PRO\Includes\Models\Field;
	use SW_WAPF_PRO\Includes\Models\FieldGroup;

	class Fields
    {

        public static function sanitize_value(Field $field,$value) {
            switch($field->type) {
                case 'text'                 :
                case 'url'                  : return sanitize_text_field(trim($value));
                case 'number'               : return filter_var(Helper::normalize_string_decimal($value), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                case 'email'                : return sanitize_email(trim($value));
                case 'textarea'             : return sanitize_textarea_field(trim($value));
                case 'checkboxes'           :
                case 'radio'                :
                case 'select'               :
                case 'multi-image-swatch'   :
                case 'image-swatch'         :
                case 'multi-color-swatch'   :
                case 'color-swatch'         :
	            case 'text-swatch'          :
	            case 'multi-text-swatch'    :
                    return join(', ', Enumerable::from((array) $value)->select(function($v) use ($field) {

                        $choice = Enumerable::from($field->options['choices'])->firstOrDefault(function($choice) use($v) {
                            return $choice['slug'] === $v;
                        });

                        return $choice ? esc_html($choice['label']) : '';

                    })->toArray());

                case 'true-false'           :
                	if($value == '1' || $value === 'true') 
                		return isset($field->options['label_true']) ? sanitize_text_field($field->options['label_true']) : 'true';
	                return isset($field->options['label_false']) ? sanitize_text_field($field->options['label_false']) : 'false';

	            case 'image-swatch-qty'     : return array_map( 'intval', trim( $value ) );

            }

            return apply_filters('wapf/sanitize_value', $value, $field);

        }

        public static function get_raw_field_value_from_request(Field $for_field, $clone_index = 0, $return_null = false) {

        	$field_name = 'field_' . $for_field->id . ($clone_index > 0 ? ('_clone_'.$clone_index):'');

            if($for_field->type === 'file' && !File_Upload::is_ajax_upload()) { 
            	$files = Cache::get_files();
            	if(empty($files))
            		return $return_null ? null : '';

            	if(!isset($files[$field_name]))
            		return $return_null ? null : '';

            	return Enumerable::from($files[$field_name])
                     ->where(function($x){return $x['name'] !== '';})
                     ->join(function($x){return $x['name'];},', ');

            }

            if( ! isset( $_REQUEST['wapf'] ) || ! isset( $_REQUEST['wapf'][$field_name] ) ) {
                return $return_null ? null : '';
            }

            if( $for_field->is_quantities_field() ) {

                if( empty( $for_field->options['choices'] ) )
                    return $return_null ? null : '';

                $values = [];

                $all_empty = true;

                foreach ( $for_field->options['choices'] as $choice ) {
                    $field_name = 'field_' . $for_field->id . ( $clone_index > 0 ? ( '_clone_' . $clone_index ) : '' ) . '_' . $choice['slug'];

                    if( isset( $_REQUEST['wapf'][$field_name] ) && $_REQUEST['wapf'][$field_name] != ''  && $_REQUEST['wapf'][$field_name] != '0' ) $all_empty = false;

                    $values[] = isset( $_REQUEST['wapf'][$field_name] ) ? floatval( $_REQUEST['wapf'][$field_name] ) : 0;
                }

                if( $all_empty || empty( $values ) )
                    return $return_null ? null : '';

                return $values;

            }

	        $value = $_REQUEST['wapf'][$field_name];

            if($for_field->is_choice_field()) {
            	$value = Enumerable::from((array) $value)->where(function($x){return $x !== "0" && $x !== '';})->toArray();

            	if(empty($value))
		            return $return_null ? null : '';
            	return $value;
            }

            if($for_field->type === 'true-false' && $value === '0')
                return $return_null ? null : '';

	        return is_string($value) ? stripslashes($value) : $value;
        }

        public static function raw_to_cartfield_values(Field $field, $raw_value,$clone_idx = 0) {

            $build_choice_value = function(Field $field, $choice, $raw_value = null) {

                $label = sanitize_text_field($choice['label']);

                $value = [
                    'label' => $raw_value !== null ? ('' . $raw_value ) : $label,
                    'price' => $choice['pricing_type'] === 'none' ? 0 : $choice['pricing_amount'],
                    'price_type' => $choice['pricing_type'],
                    'slug' => $choice['slug']
                ];

                if( $field->is_quantities_field() ) {
                    $value['use_label'] = true; 
                    $value['formatted_label'] = $label . ': ' . $raw_value;
                }

                return $value;
            };

        	$values = [];

            if( $field->is_quantities_field() ) {

                $raw_value = (array) $raw_value;

                for( $i = 0; $i < count( $raw_value ); $i++ ) {

                    if( !empty( $raw_value[$i] ) && isset( $field->options['choices'][$i] ) ) {

                        $choice = $field->options['choices'][$i];
                        $values[] = $build_choice_value($field, $choice, $raw_value[$i] );

                    }

                }

            }

        	else if($field->is_choice_field()) {

		        foreach ((array) $raw_value as $rv) {

		        	if(empty($rv))
		        		continue;

			        $choice = Enumerable::from($field->options['choices'])->firstOrDefault(function($choice) use($rv) {
				        return $choice['slug'] === $rv;
			        });

			        if(!$choice)
				        continue;

			        $values[] = $build_choice_value( $field, $choice );

		        }
	        }

        	else {

        		$price = $field->pricing_enabled() ? $field->pricing->amount : 0;

		        if(!isset($raw_value) || (is_string($raw_value) && strlen($raw_value) === 0) || ($field->type === 'true-false' && $raw_value == '0'))
        			$price = 0;

		        $label = self::get_value_label($field,$raw_value,$clone_idx);
		        $formatted_label = self::format_value_label($field, $label);

		        $value = [
			        'label' => $label,
			        'price' => $price,
			        'price_type' => $field->pricing_enabled() ? $field->pricing->type : 'none'
		        ];

		        if($formatted_label !== $label) {
			        $value['formatted_label'] = $formatted_label;
		        }

        		$values[] = $value;
	        }

        	return $values;

        }

        private static function get_value_label($field,$raw_value, $clone_idx = 0) {

	        if( $field->type === 'file' ) {

		        $files = Cache::get_files();

		        if(!empty($files)) {

			        $key = 'field_' . $field->id;
			        if($clone_idx > 0)
				        $key .= '_clone_'.$clone_idx;

			        if( isset($files[$key]) ) {
				        return Enumerable::from( $files[ $key ] )->join( function ( $x ) {
					        return $x['uploaded_file'];
				        }, ', ' );
			        }
		        }

		        if(empty($raw_value)) return '';

		        $base_url =  trailingslashit( wp_upload_dir()['baseurl'] ) . trailingslashit( File_Upload::$upload_parent_dir);
		        $files = explode( ',', $raw_value );

		        return Enumerable::from( $files )->join( function ( $x ) use ( $base_url ) {
			        return strpos($x, $base_url) !== false ? sanitize_text_field($x) : ( $base_url . sanitize_text_field( $x ) );
		        }, ', ' );

	        }

        	return self::sanitize_value($field,$raw_value);
        }

        public static function format_value_label(Field $field, $label) {

        	if($field->type === 'file') {

        		$display_label = [];

		        $file_urls = explode(', ', $label);

                foreach ($file_urls as $url) {
			        $split = explode( '/', $url );
			        $display_label[] = '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $split[ count( $split ) - 1 ] ) . '</a>';
		        }
		        return join(', ', $display_label);
	        }

        	return $label;

        }

        public static function do_pricing($is_qty_based_field, $pricing_type, $amount, $base_price, $qty, $val, $product_id, $cart_item_fields, $field_group_ids, $clone_idx = 0, $options_total = 0 ) {

            switch($pricing_type) {
                case 'percent':
	                $percent = $base_price * ($amount / 100);
                	return (float) $is_qty_based_field ? ($percent*$qty) : $percent;
	            case 'p':
	            	$percent = $base_price * ($amount / 100);
		            return (float) $is_qty_based_field ? $percent : $percent/$qty;
                case 'qt': return (float) ($is_qty_based_field ? ($amount*$qty) : $amount);
	            case 'nr':
	            	$v = floatval($val) * $amount;
	            	return $is_qty_based_field ? (float) $v : (float) $v/$qty;
	            case 'nrq': return (floatval($val) * $amount); 
	            case 'char':
	            	$v = mb_strlen($val) * $amount;
	            	return $is_qty_based_field ? (float)$v : (float) $v/$qty;
	            case 'charq': return mb_strlen($val) * $amount; 
	            case 'fx':
		            $field_groups = Field_Groups::get_by_ids($field_group_ids);
		            $variables = Enumerable::from($field_groups)->merge(function($x){return $x->variables;})->toArray();

		            		            $math = Helper::replace_in_formula( $amount, $qty, $base_price, $val, 0, $cart_item_fields, $product_id, $clone_idx );

		            		            if( ! empty( $variables ) ) {
			            $fields = Enumerable::from($field_groups)->merge( function( $x ) { return $x->fields; } )->toArray();
			            $math = Helper::evaluate_variables( $math, $fields, $variables, $product_id, $clone_idx, $base_price, $val, $qty, $options_total, $cart_item_fields );
		            }

	            	$x = Helper::parse_math_string( $math, $cart_item_fields, true, [ 'product_id' => $product_id ] );

		            return (float) ( $is_qty_based_field ? $x : ($x/$qty) );
                default: 
                    return $is_qty_based_field ? (float) $amount : (float) $amount/$qty;
            }
        }

        public static function is_field_value_valid(Field $field, $value = null) {

	        if($field->required) {

        		if($value === null)
        			return true;

		        if(empty($value))
		        	return false;

			}

			return true;
        }

        public static function get_field_state(FieldGroup $group, Field $field, $product_id, $clone_index = 0) {

	        if(!$field->has_conditionals())
		        return 'visible';

	        foreach ($field->conditionals as $conditional) {
		        if(self::validate_rules($group,$conditional->rules, $product_id, $clone_index)) 
			        return 'visible';
	        }

	        return 'invisible';
        }

        public static function should_field_be_filled_out(FieldGroup $group, Field $field, $product_id, $clone_index = 0) {

        	if(!$field->has_conditionals())
        		return true;

			foreach ($field->conditionals as $conditional) {
				if(self::validate_rules($group, $conditional->rules, $product_id, $clone_index)) 
					return true;
			}

			return false;

        }

        public static function validate_rules(FieldGroup $group, $rules, $product_id, $clone_index = 0) {

	       foreach ($rules as $rule) {

	       	    if(!self::is_valid_rule($group->fields,$rule->field,$rule->condition,$rule->value,$product_id,null,$clone_index))
			       return false;

	       }

	       return true;
        }

        public static function is_valid_rule($fields,$subject, $condition, $rule_value,$product_id,$cart_fields = null,$clone_index = 0, $qty = 1){

	        if($subject === 'qty')
		        $value = $qty;
	        else {
		        $field = Enumerable::from( $fields )->firstOrDefault( function ( $x ) use ( $subject ) {
			        return $x->id === $subject;
		        } );

		        if ( ! $field ) {
			        return false;
		        }
		        if ( strpos( $condition, 'product_var' ) !== false ) {
			        if ( $condition === 'product_var' )
				        return in_array( $product_id, explode( ',', $rule_value ) );
			        else
				        return ! in_array( $product_id, explode( ',', $rule_value ) );
		        }
		        if(strpos($condition,'patts') !== false) {
		        	$product = wc_get_product($product_id);

		        	if($condition === 'patts')
						return Conditions::product_has_attribute_values($product,explode(',',$rule_value),true);
		        	else return !Conditions::product_has_attribute_values($product,explode(',',$rule_value),true);
		        }

		        if(!empty($cart_fields)) {
			        $value = Enumerable::from( $cart_fields )->firstOrDefault( function ( $x ) use ( $subject ) {
				        return $x['id'] === $subject;
			        } );
			        if($value != null)
			        	$value = $value['raw'];
		        }
		        else
		        	$value = Fields::get_raw_field_value_from_request( $field, $clone_index, true );

                if ($value === null ) {
			        return false;
		        }

				if($field->type === 'date' && $rule_value) {
					if($value) {
						$date_format = get_option( 'wapf_date_format', 'mm-dd-yyyy' );
						$value       = \DateTime::createFromFormat( Helper::date_format_to_php_format( $date_format ), $value )->setTime( 0, 0 );
					}
					$rule_value = \DateTime::createFromFormat('m-d-Y',$rule_value)->setTime(0,0);
				}

	        }

	        switch($condition) {
		        case "check"        : return $value === '1';
		        case "!check"       : return $value === '0';
		        case '=='           : return $rule_value instanceof \DateTime ? $value && $value == $rule_value : in_array($rule_value, (array) $value);
		        case '!='           : return $rule_value instanceof \DateTime ? $value && $value != $rule_value : !in_array($rule_value, (array) $value);
		        case 'empty'        : return empty($value);
		        case '!empty'       : return !empty($value);
		        case '==contains'   : return is_array($value) ? in_array($rule_value, $value) : strpos($value,$rule_value) !== false;
		        case '!=contains'   : return is_array($value) ? !in_array($rule_value, $value) : strpos($value,$rule_value) === false;
		        case 'lt'           : return floatval($value) < floatval($rule_value);
		        case 'gt'           : return floatval($value) > floatval($rule_value);
		        case 'gtd'          : return $value && $value > $rule_value;
		        case 'ltd'          : return $value && $value < $rule_value;
	        }

	        return false;

        }

    }
}
<?php

namespace SW_WAPF_PRO\Includes\Classes {

    class Config
    {

        public static function get_field_visibility_conditions() {

            $options = [
                [
                    'type'          => 'text',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '==contains', 'label' => __('Value contains','sw-wapf'), 'type' => 'text'],
                    ]
                ],
                [
                    'type'          => 'email',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '==contains', 'label' => __('Value contains','sw-wapf'), 'type' => 'text'],
                    ]
                ],
                [
                    'type'          => 'url',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '==contains', 'label' => __('Value contains','sw-wapf'), 'type' => 'text'],
                    ]
                ],
                [
                    'type'          => 'number',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => 'gt', 'label' => __('Value is greater than','sw-wapf'), 'type' => 'number'],
                        ['value' => 'lt', 'label' => __('Value is lesser than','sw-wapf'), 'type' => 'number'],
                        ['value' => '==contains', 'label' => __('Value contains','sw-wapf'), 'type' => 'text'],
                    ]
                ],
                [
                    'type'          => 'textarea',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'text'],
                        ['value' => '==contains', 'label' => __('Value contains','sw-wapf'), 'type' => 'text'],
                    ]
                ], [
                    'type'          => 'true-false',
                    'conditions'    => [
                        ['value' => 'check', 'label' => __('Is checked', 'sw-wapf'), 'type' => false],
                        ['value' => '!check', 'label' => __('Is not checked', 'sw-wapf'), 'type' => false]
                    ]
                ], [
                    'type'          => 'select',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'options'],
                    ]
                ], [
                    'type'          => 'checkboxes',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Nothing selected','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Anything selected','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Selection contains','sw-wapf'), 'type' => 'options'], 
                        ['value' => '!=', 'label' => __('Selection does not contain','sw-wapf'), 'type' => 'options'],
                    ]
                ], [
                    'type'          => 'radio',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'date',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Is any date','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Is equal to','sw-wapf'), 'type' => 'text', 'desc' => __('Use this date format: mm-dd-yyyy','sw-wapf')],
                        ['value' => '!=', 'label' => __('Is not equal to','sw-wapf'), 'type' => 'text', 'desc' => __('Use this date format: mm-dd-yyyy','sw-wapf')],
                        ['value' => 'gtd', 'label' => __('Is later than','sw-wapf'), 'type' => 'text', 'desc' => __('Use this date format: mm-dd-yyyy','sw-wapf')],
                        ['value' => 'ltd', 'label' => __('Is older than','sw-wapf'), 'type' => 'text', 'desc' => __('Use this date format: mm-dd-yyyy','sw-wapf')],
                    ]
                ],
                [
                    'type'          => 'image-swatch',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'multi-image-swatch',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Selection contains','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Selection does not contain','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'color-swatch',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'color-swatches',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'multi-color-swatch',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Selection contains','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Selection does not contain','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'text-swatch',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Value is equal to','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Value is not equal to','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'multi-text-swatch',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==', 'label' => __('Selection contains','sw-wapf'), 'type' => 'options'],
                        ['value' => '!=', 'label' => __('Selection does not contain','sw-wapf'), 'type' => 'options'],
                    ]
                ],
                [
                    'type'          => 'file',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Has no files selected','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Has file(s) selected','sw-wapf'), 'type' => false],
                    ]
                ],
                [
                    'type'          => 'image-swatch-qty',
                    'conditions'    => [
                        ['value' => 'empty', 'label' => __('Value is empty','sw-wapf'), 'type' => false],
                        ['value' => '!empty', 'label' => __('Value is any value','sw-wapf'), 'type' => false],
                        ['value' => '==contains', 'label' => __('Selection contains','sw-wapf'), 'type' => 'number'],
                        ['value' => '!=contains', 'label' => __('Selection does not contain','sw-wapf'), 'type' => 'number'],
                    ]
                ],
            ];

            return $options;

        }

        public static function get_fieldgroup_visibility_conditions() {

            $product_options = [

                [
                    'group'                 => __('User','sw-wapf'),
                    'children'              => [
                        [
                            'id'            => 'auth',
                            'label'         => __('Authentication','sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'auth',
                                    'label' => __('Logged in', 'sw-wapf'),
                                    'value' => []
                                ], [
                                    'id'    => '!auth',
                                    'label' => __('Not logged in', 'sw-wapf'),
                                    'value' => []
                                ]
                            ]
                        ],[
                            'id'            => 'role',
                            'label'         => __('User role','sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'role',
                                    'label' => __('Is equal to', 'sw-wapf'),
                                    'value' => [
                                        'type'          => 'select',
                                        'data'          => Helper::get_all_roles()
                                    ]
                                ], [
                                    'id'    => '!role',
                                    'label' => __('Is not equal to', 'sw-wapf'),
                                    'value' => [
                                        'type'          => 'select',
                                        'data'          => Helper::get_all_roles()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'group'                 => __('Product', 'sw-wapf'),
                    'children'              => [
                        [
                            'id'            => 'product',
                            'label'         => __('Product', 'sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'product',
                                    'label' => __('Is equal to','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a product...",'sw-wapf'),
                                        'action'        => 'wapf_search_products',
                                        'single'        => true
                                    ]
                                ],
                                [
                                    'id'    => '!product',
                                    'label' => __('Is not equal to','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a product...",'sw-wapf'),
                                        'action'        => 'wapf_search_products',
                                        'single'        => true
                                    ]
                                ],
                                [
                                    'id'    => 'products',
                                    'label' => __('Any in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a product...",'sw-wapf'),
                                        'action'        => 'wapf_search_products',
                                    ]
                                ],
                                [
                                    'id'    => '!products',
                                    'label' => __('Not in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a product...",'sw-wapf'),
                                        'action'        => 'wapf_search_products',
                                    ]
                                ],

                            ]
                        ],
                        [
                            'id'            => 'product_variation',
                            'label'         => __('Product variation', 'sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'product_var',
                                    'label' => __('Any in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a product variation...",'sw-wapf'),
                                        'action'        => 'wapf_search_variations'
                                    ]
                                ],
                                [
                                    'id'    => '!product_var',
                                    'label' => __('Not in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a product variation...",'sw-wapf'),
                                        'action'        => 'wapf_search_variations'
                                    ]
                                ],
                            ]
                        ], [
                            'id'            => 'product_cat',
                            'label'         => __('Product category', 'sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'product_cat',
                                    'label' => __('Is equal to','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a category...",'sw-wapf'),
                                        'action'        => 'wapf_search_cat',
                                        'single'        => true
                                    ]
                                ],
                                [
                                    'id'    => '!product_cat',
                                    'label' => __('Is not equal to','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a category...",'sw-wapf'),
                                        'action'        => 'wapf_search_cat',
                                        'single'        => true
                                    ]
                                ],
                                [
                                    'id'    => 'product_cats',
                                    'label' => __('Any in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a category...",'sw-wapf'),
                                        'action'        => 'wapf_search_cat',
                                        'single'        => false
                                    ]
                                ],
                                [
                                    'id'    => '!product_cats',
                                    'label' => __('Not in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a category...",'sw-wapf'),
                                        'action'        => 'wapf_search_cat',
                                        'single'        => false
                                    ]
                                ],
                            ]
                        ],
                        [
                            'id'            => 'var_att',
                            'label'         => __('Product attribute', 'sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'patts',
                                    'label' => __('Any in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __('Search a product attribute...','sw-wapf'),
                                        'action'        => 'wapf_search_attributes'
                                    ]
                                ],
                                [
                                    'id'    => '!patts',
                                    'label' => __('Not in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __('Search a product attribute...','sw-wapf'),
                                        'action'        => 'wapf_search_attributes'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'id'            => 'product_tag',
                            'label'         => __('Product tag', 'sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'p_tags',
                                    'label' => __('Any in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a tag...",'sw-wapf'),
                                        'action'        => 'wapf_search_tag',
                                        'single'        => false
                                    ]
                                ],
                                [
                                    'id'    => '!p_tags',
                                    'label' => __('Not in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Search a tag...",'sw-wapf'),
                                        'action'        => 'wapf_search_tag',
                                        'single'        => false
                                    ]
                                ],
                            ]
                        ],
                        [
                            'id'            => 'product_type',
                            'label'         => __('Product type', 'sw-wapf'),
                            'conditions'    => [
                                [
                                    'id'    => 'product_type',
                                    'label' => __('Any in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Select a type",'sw-wapf'),
                                        'data'          => [
                                            ['id' => 'simple', 'text' => 'Simple product'],
                                            ['id' => 'variable', 'text' => 'Variable product'],
                                            ['id' => 'grouped', 'text' => 'Grouped product'],
                                        ]
                                    ]
                                ],
                                [
                                    'id'    => '!product_type',
                                    'label' => __('Not in list','sw-wapf'),
                                    'value' => [
                                        'type'          => 'select2',
                                        'placeholder'   => __("Select a type",'sw-wapf'),
                                        'data'          => [
                                            ['id' => 'simple', 'text' => 'Simple product'],
                                            ['id' => 'variable', 'text' => 'Variable product'],
                                            ['id' => 'grouped', 'text' => 'Grouped product'],
                                        ]
                                    ]
                                ],
                            ]
                        ]

                    ]
                ]
            ];

            $system = ['group' => __('System','sw-wapf'), 'children' => [] ];

            $languages = Helper::get_available_languages();

            if ( !empty($languages)) {
                $system['children'][] = [
                    'id'            => 'lang',
                    'label'         => __('Language','sw-wapf'),
                    'conditions'    => [
                        [
                            'id'    => 'lang',
                            'label' => __('Is equal to', 'sw-wapf'),
                            'value' => [
                                'type'          => 'select',
                                'data'          => $languages
                            ]
                        ]
                    ]
                ];

                $product_options[] = $system;
            }

            $product_options = apply_filters('wapf/field_group/condition_options', $product_options);

            return [ 'wapf_product' => $product_options ];
        }

        public static function get_pricing_options($field_type = '') {

            $options = [
                'fixed'     => __('Flat fee', 'sw-wapf'),
                'qt'        => __('Quantity based flat fee', 'sw-wapf'),
                'p'         => __('Percentage based fee','sw-wapf'),
                'percent'   => __('Quantity based percentage fee', 'sw-wapf'),
                'fx'        => __('Formula based pricing', 'sw-wapf')
            ];

            $allowed = [ 'text','textarea','email','url' ];
            if( in_array( $field_type, $allowed ) ) {
                $options['char'] =  __('Amount &times; character count','sw-wapf');
                $options['charq'] =  __('Amount &times; character count &times; qty','sw-wapf');
            }

            if( $field_type === 'number' || $field_type === 'image-swatch-qty' ) {
                $options['nr'] = __('Amount &times; field value','sw-wapf');
                $options['nrq'] = __('Amount &times; field value &times; qty','sw-wapf');
            }

            return apply_filters('wapf/admin/pricing_options',$options, $field_type);

        }

        public static function get_field_options() {

            $all_file_type = File_Upload::get_all_allowed_filetypes();

            $allowed_file_types = [];
            foreach ($all_file_type as $k => $v) {
                $allowed_file_types[$k] = $k;
            }
            ksort($allowed_file_types);

            $options =  [
                'true-false' => [
                    [
                        'type'          => 'text',
                        'id'            => 'message',
                        'label'         => __('Message','sw-wapf'),
                        'description'   => __('Displays text alongside the checkbox.','sw-wapf'),
                    ],
                    [
                        'type'          => 'select',
                        'options'       => [
                            'checked'   => __('Checked','sw-wapf'),
                            'unchecked' => __('Unchecked', 'sw-wapf')
                        ],
                        'default'       => 'unchecked',
                        'id'            => 'default',
                        'label'         => __('Default value','sw-wapf'),
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'label_true',
                        'label'         => __("Label for 'true'", 'sw-wapf'),
                        'description'   => __('Alternative text for "true" on cart, checkout, and order.'),
                        'default'       => __('true', 'sw-wapf')
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'label_false',
                        'label'         => __("Label for 'false'", 'sw-wapf'),
                        'description'   => __('Alternative text for "false" on cart, checkout, and order.'),
                        'default'       => __('false','sw-wapf')
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => "pricing",
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Does this field affect product price?','sw-wapf'),
                    ]
                ],

                'text'      => [
                    [
                        'type'          => 'text',
                        'id'            => 'default',
                        'label'         => __('Default value','sw-wapf'),
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'placeholder',
                        'label'         => __('Placeholder text','sw-wapf'),
                        'description'   => __('Appears within the input field as a placholder.','sw-wapf')
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'minlength',
                        'min'           => 1,
                        'label'         => __('Minimum length','sw-wapf'),
                        'note'          => __('Leave blank if there is no restriction.', 'sw-wapf'),
                        'description'   => __('The minimum text length required.','sw-wapf'),
                        'postfix'       => __('characters','sw-wapf'),
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'maxlength',
                        'min'           => 1,
                        'label'         => __('Maximum length','sw-wapf'),
                        'note'          => __('Leave blank if there is no restriction.', 'sw-wapf'),
                        'description'   => __('The maximum text length allowed.','sw-wapf'),
                        'postfix'       => __('characters','sw-wapf'),
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'pattern',
                        'label'         => __('HTML5 validation regex','sw-wapf'),
                        'description'   => __('Restrict the input by adding a <a href="http://html5pattern.com/" target="_blank">HTML5 regex</a> here.','sw-wapf')
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => "pricing",
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Does this field affect product price?','sw-wapf'),
                    ],
                ],

                'textarea'      => [
                    [
                        'type'          => 'textarea',
                        'id'            => 'default',
                        'label'         => __('Default value','sw-wapf'),
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'placeholder',
                        'label'         => __('Placeholder text','sw-wapf'),
                        'description'   => __('Displays as placeholder in the field.','sw-wapf')
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'minlength',
                        'min'           => 1,
                        'label'         => __('Minimum length','sw-wapf'),
                        'note'          => __('Leave blank if there is no restriction.', 'sw-wapf'),
                        'description'   => __('The minimum text length required.','sw-wapf'),
                        'postfix'       => __('characters','sw-wapf'),
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'maxlength',
                        'min'           => 1,
                        'label'         => __('Maximum length','sw-wapf'),
                        'note'          => __('Leave blank if there is no restriction.', 'sw-wapf'),
                        'description'   => __('The maximum text length allowed.','sw-wapf'),
                        'postfix'       => __('characters','sw-wapf'),
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => "pricing",
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Does this field affect product price?','sw-wapf'),
                    ],
                ],

                'number'      => [
                    [
                        'type'          => 'select',
                        'id'            => 'number_type',
                        'label'         => __('Number type','sw-wapf'),
                        'description'   => __('Allow integers (whole numbers) or decimals.','sw-wapf'),
                        'options'       => [
                            'int'       => __('Integer','sw-wapf'),
                            'any'       => __('Integer & decimals','sw-wapf')
                        ],
                        'default'       => 'int'
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'default',
                        'label'         => __('Default value','sw-wapf'),
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'placeholder',
                        'label'         => __('Placeholder text','sw-wapf'),
                        'description'   => __('Displays as placeholder in the field.','sw-wapf')
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'minimum',
                        'label'         => __('Minimum value','sw-wapf'),
                        'placeholder'   => __('No minimum','sw-wapf')
                    ],
                    [
                        'type'          => 'number',
                        'id'            => 'maximum',
                        'label'         => __('Maximum value','sw-wapf'),
                        'placeholder'   => __('No maximum','sw-wapf')
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => "pricing",
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Does this field affect product price?','sw-wapf'),
                    ],
                    [
                        'type'          => 'true-false',
                        'id'            => 'hide_zero',
                        'label'         => __('Hide zero from cart and checkout', 'sw-wapf'),
                        'description'   => __("When the field value is zero, don't show it in the cart, checkout and order screens.", 'sw-wapf')
                    ]
                ],

                'email'     => [
                    [
                        'type'          => 'email',
                        'id'            => 'default',
                        'label'         => __('Default value','sw-wapf'),
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'placeholder',
                        'label'         => __('Placeholder text','sw-wapf'),
                        'description'   => __('Displays as placeholder in the field.','sw-wapf')
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => "pricing",
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Does this field affect product price?','sw-wapf'),
                    ],
                ],

                'url'       => [
                    [
                        'type'          => 'url',
                        'id'            => 'default',
                        'label'         => __('Default value','sw-wapf'),
                    ],
                    [
                        'type'          => 'text',
                        'id'            => 'placeholder',
                        'label'         => __('Placeholder text','sw-wapf'),
                        'description'   => __('Displays as placeholder in the field.','sw-wapf')
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => 'pricing',
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Does this field affect product price?','sw-wapf'),
                    ],
                ],

                'select'    => [
                    [
                        'type'                  => 'options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Add the options for this select list.','sw-wapf'),
                        'multi_option'          => false,
                        'show_pricing_options'  => true
                    ],
                ],

                'date'      => [
                    [
                        'type'          => 'text',
                        'id'            => 'default',
                        'label'         => __('Default value','sw-wapf'),
                        'description'   => __('Preset date on page load','sw-wapf'),

                                                'note'          => __('Use format mm-dd-yyyy or mm-dd for yearly recurring dates.','sw-wapf'),

                                                ],
                    [
                        'type'          => 'text',
                        'id'            => 'placeholder',
                        'label'         => __('Placeholder text','sw-wapf'),
                        'description'   => __('Displays as placeholder in the field.','sw-wapf')
                    ],
                    [
                        'type'                  => 'true-falses',
                        'true_label'            => __('On','sw-wapf'),
                        'false_label'           => __('Off','sw-wapf'),
                        'options'               => [
                            'disable_past'      => __("Dates in the past can't be selected",'sw-wapf'),
                            'disable_future'    => __("Dates in the future can't be selected",'sw-wapf'),
                            'disable_today'     => __("Today's date can't be selected",'sw-wapf'),
                        ],
                        'label'                 => __('Selection options','sw-wapf'),
                        'description'           => __("Define which dates can or can't be selected.",'sw-wapf'),
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => "pricing",
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Should the product\'s final price change?','sw-wapf'),
                    ],
                ],

                'checkboxes'  => [
                    [
                        'type'                  => 'options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Each option is a checkbox.','sw-wapf'),
                        'multi_option'          => true,
                        'show_pricing_options'  => true
                    ],
                    [
                        'type'                  => 'number',
                        'id'                    => 'min_choices',
                        'min'                   => '1',
                        'label'                 => __('Minimum choices needed','sw-wapf'),
                        'description'           => __('Set the minimum number of choices needed.','sw-wapf'),
                    ],
                    [
                        'type'                  => 'number',
                        'id'                    => 'max_choices',
                        'min'                   => '1',
                        'label'                 => __('Maximum choices allowed','sw-wapf'),
                        'description'           => __('Set the maximum number of choices allowed.','sw-wapf'),
                    ],
                ],

                'radio'  => [
                    [
                        'type'                  => 'options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Each option is a radio button.','sw-wapf'),
                        'multi_option'          => false,
                        'show_pricing_options'  => true
                    ],
                ],

                'image-swatch'   => [
                    [
                        'type'                  => 'imageswatch-options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Define images here. Ensure they all have same dimensions for optimal layout.','sw-wapf'),
                        'show_pricing_options'  => true
                    ],
                    [
                        'type'                  => 'select',
                        'id'                    => 'label_pos',
                        'label'                 => __('Label position','sw-wapf'),
                        'description'           => __('How to display the swatch label?'),
                        'options'               => [
                            'default'           => __('Below image','sw-wapf'),
                            'hide'              => __('Hide the label','sw-wapf'),
                            'tooltip'           => __('As tooltip','sw-wapf'),
                        ],
                        'default'               => 'default'
                    ],
                    [
                        'type'                  => 'selects',
                        'label'                 => __('Items per row','sw-wapf'),
                        'description'           => __('Max. swatches per row.','sw-wapf'),
                        'lists'                 => [
                            [
                                'id'            => 'items_per_row',
                                'title'         => __('On desktop','sw-wapf'),
                                'options'       => [
                                    1           => '1',
                                    2           => '2',
                                    3           => '3',
                                    4           => '4',
                                    5           => '5',
                                    6           => '6',
                                    7           => '7',
                                    8           => '8',
                                    9           => '9',
                                    10          => '10',
                                    11          => '11',
                                    12          => '12',
                                    13          => '13',
                                    14          => '14',
                                    15          => '15'
                                ],
                                'default'       => 3
                            ], [
                                'id'            => 'items_per_row_tablet',
                                'title'         => __('On tablet','sw-wapf'),
                                'options'       => [
                                    1           => '1',
                                    2           => '2',
                                    3           => '3',
                                    4           => '4',
                                    5           => '5',
                                    6           => '6',
                                    7           => '7',
                                    8           => '8',
                                    9           => '9',
                                    10          => '10',
                                ],
                                'default'       => 3
                            ], [
                                'id'            => 'items_per_row_mobile',
                                'title'         => __('On mobile','sw-wapf'),
                                'options'       => [
                                    1           => '1',
                                    2           => '2',
                                    3           => '3',
                                    4           => '4',
                                    5           => '5',
                                    6           => '6',
                                    7           => '7',
                                    8           => '8',
                                    9           => '9',
                                    10          => '10',
                                ],
                                'default'       => 3
                            ]
                        ]
                    ],
                ],

                'multi-image-swatch'   => [
                    [
                        'type'                  => 'imageswatch-options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Define images here. Ensure they all have same dimensions for optimal layout.','sw-wapf'),
                        'show_pricing_options'  => true,
                        'multi_option'          => true,
                    ],
                    [
                        'type'                  => 'select',
                        'id'                    => 'label_pos',
                        'label'                 => __('Label position','sw-wapf'),
                        'description'           => __('How to display the swatch label?'),
                        'options'               => [
                            'default'           => __('Below image','sw-wapf'),
                            'hide'              => __('Hide the label','sw-wapf'),
                            'tooltip'           => __('As tooltip','sw-wapf'),
                        ],
                        'default'               => 'default'
                    ],
                    [
                        'type'                  => 'selects',
                        'label'                 => __('Items per row','sw-wapf'),
                        'description'           => __('Max. swatches per row.','sw-wapf'),
                        'lists'                 => [
                            [
                                'id'            => 'items_per_row',
                                'title'         => __('On desktop','sw-wapf'),
                                'options'       => [
                                    1           => '1',
                                    2           => '2',
                                    3           => '3',
                                    4           => '4',
                                    5           => '5',
                                    6           => '6',
                                    7           => '7',
                                    8           => '8',
                                    9           => '9',
                                    10          => '10',
                                    11          => '11',
                                    12          => '12',
                                    13          => '13',
                                    14          => '14',
                                    15          => '15'
                                ],
                                'default'       => 3
                            ], [
                                'id'            => 'items_per_row_tablet',
                                'title'         => __('On tablet','sw-wapf'),
                                'options'       => [
                                    1           => '1',
                                    2           => '2',
                                    3           => '3',
                                    4           => '4',
                                    5           => '5',
                                    6           => '6',
                                    7           => '7',
                                    8           => '8',
                                    9           => '9',
                                    10          => '10',
                                ],
                                'default'       => 3
                            ], [
                                'id'            => 'items_per_row_mobile',
                                'title'         => __('On mobile','sw-wapf'),
                                'options'       => [
                                    1           => '1',
                                    2           => '2',
                                    3           => '3',
                                    4           => '4',
                                    5           => '5',
                                    6           => '6',
                                    7           => '7',
                                    8           => '8',
                                    9           => '9',
                                    10          => '10',
                                ],
                                'default'       => 3
                            ]
                        ]
                    ],
                    [
                        'type'                  => 'numbers',
                        'label'                 => __('Minimum and maximum choices','sw-wapf'),
                        'description'           => __('Set the min and max choices needed (over all choices).','sw-wapf'),
                        'note'                  => __("Leave blank if you don't require a min or max.",'sw-wapf'),
                        'empty'                 => 1,
                        'numbers'               => [
                            [
                                'title'         => __('Minimum needed','sw-wapf'),
                                'id'            => 'min_choices',
                                'min'           => '1',
                            ],
                            [
                                'title'         => __('Maximum allowed','sw-wapf'),
                                'id'            => 'max_choices',
                                'min'           => '1',
                            ]
                        ]
                    ]
                ],
                'color-swatch'  => [
                    [
                        'type'                  => 'colorswatch-options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Define your color swatches here.','sw-wapf'),
                        'show_pricing_options'  => true,
                        'multi_option'          => false,
                    ],
                    [
                        'type'                  => 'select',
                        'id'                    => 'layout',
                        'label'                 => __('Swatch layout','sw-wapf'),
                        'default'               => 'circle',
                        'options'               => [
                            'square'            => __('Square','sw-wapf'),
                            'rounded'           => __('Rounded corners','sw-wapf'),
                            'circle'            => __('Circle', 'sw-wapf')
                        ],
                        'description'           => __('Set the swatch appearance.','sw-wapf'),
                    ],
                    [
                        'type'                  => 'number',
                        'id'                    => 'size',
                        'min'                   => '5',
                        'max'                   => "500",
                        'postfix'               => 'px',
                        'default'               => 30,
                        'label'                 => __('Size (in pixels)','sw-wapf'),
                        'description'           => __('Size of the color swatch.','sw-wapf'),
                    ],
                    [
                        'type'                  => 'select',
                        'id'                    => 'label_pos',
                        'label'                 => __('Label position','sw-wapf'),
                        'description'           => __('How to display the swatch label?'),
                        'options'               => [
                            'default'           => __('Below image','sw-wapf'),
                            'hide'              => __('Hide the label','sw-wapf'),
                            'tooltip'           => __('As tooltip','sw-wapf'),
                        ],
                        'default'               => 'tooltip'
                    ],
                ],

                'multi-color-swatch'  => [
                    [
                        'type'                  => 'numbers',
                        'label'                 => __('Selection limits','sw-wapf'),
                        'description'           => __('The min and max choices required.','sw-wapf'),
                        'note'                  => __("Set both to '1' if you only allow a single choice. Leave blank if you don't require a min or max",'sw-wapf'),
                        'numbers'               => [
                            [
                                'title'         => __('Minimum needed','sw-wapf'),
                                'id'            => 'min_choices',
                                'min'           => '1',
                            ],
                            [
                                'title'         => __('Maximum allowed','sw-wapf'),
                                'id'            => 'max_choices',
                                'min'           => '1',
                            ]
                        ]
                    ],
                    [
                        'type'                  => 'colorswatch-options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Define your color swatches here.','sw-wapf'),
                        'show_pricing_options'  => true,
                        'multi_option'          => true,
                    ],
                    [
                        'type'                  => 'select',
                        'id'                    => 'layout',
                        'label'                 => __('Swatch layout','sw-wapf'),
                        'default'               => 'circle',
                        'options'               => [
                            'square'            => __('Square','sw-wapf'),
                            'rounded'           => __('Rounded corners','sw-wapf'),
                            'circle'            => __('Circle', 'sw-wapf')
                        ],
                        'description'           => __('Set the swatch appearance.','sw-wapf'),
                    ],
                    [
                        'type'                  => 'number',
                        'id'                    => 'size',
                        'min'                   => '5',
                        'max'                   => "500",
                        'postfix'               => 'px',
                        'default'               => 30,
                        'label'                 => __('Size (in pixels)','sw-wapf'),
                        'description'           => __('Size of the color swatch.','sw-wapf'),
                    ],
                    [
                        'type'                  => 'select',
                        'id'                    => 'label_pos',
                        'label'                 => __('Label position','sw-wapf'),
                        'description'           => __('How to display the swatch label?'),
                        'options'               => [
                            'default'           => __('Below image','sw-wapf'),
                            'hide'              => __('Hide the label','sw-wapf'),
                            'tooltip'           => __('As tooltip','sw-wapf'),
                        ],
                        'default'               => 'tooltip'
                    ],
                ],

                'text-swatch'  => [
                    [
                        'type'                  => 'textswatch-options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Define your swatches here.','sw-wapf'),
                        'show_pricing_options'  => true,
                        'multi_option'          => false,
                    ]
                ],

                'multi-text-swatch'  => [
                    [
                        'type'                  => 'textswatch-options',
                        'id'                    => 'options',
                        'label'                 => __('Options','sw-wapf'),
                        'description'           => __('Define your swatches here.','sw-wapf'),
                        'show_pricing_options'  => true,
                        'multi_option'          => true,
                    ],
                    [
                        'type'                  => 'numbers',
                        'label'                 => __('Minimum and maximum choices','sw-wapf'),
                        'description'           => __('Set the min and max choices needed (over all choices).','sw-wapf'),
                        'note'                  => __("Leave blank if you don't require a min or max.",'sw-wapf'),
                        'numbers'               => [
                            [
                                'title'         => __('Minimum needed','sw-wapf'),
                                'id'            => 'min_choices',
                                'min'           => '1',
                            ],
                            [
                                'title'         => __('Maximum allowed','sw-wapf'),
                                'id'            => 'max_choices',
                                'min'           => '1',
                            ]
                        ]
                    ]
                ],

                'file' => [
                    [
                        'type'                  => 'true-false',
                        'id'                    => 'multiple',
                        'label'                 => __('Allow multiple','sw-wapf'),
                        'description'           => __('Allow multiple files to be uploaded', 'sw-wapf')
                    ],
                    [
                        'type'                  => 'select',
                        'multiple'              => true,
                        'id'                    => 'accept',
                        'label'                 => __('Accepted file types','sw-wapf'),
                        'description'           => __('What file types can be uploaded?','sw-wapf'),
                        'note'                  => __('For security reasons, you should limit the allowed file types here.'),
                        'options'               => $allowed_file_types,
                        'select2'               => true,
                    ],
                    [
                        'type'                  => 'number',
                        'id'                    => 'maxsize',
                        'default'               => 1,
                        'label'                 => __('Maximum file size (MB)','sw-wapf'),
                        'description'           => __('Maximum allowed size for 1 file.', 'sw-wapf'),
                        'postfix'               => __('MB','sw-wapf')
                    ],
                    [
                        'type'          => 'pricing',
                        'id'            => "pricing",
                        'label'         => __('Adjust pricing','sw-wapf'),
                        'description'   => __('Should the product\'s final price change?','sw-wapf'),
                    ],
                ],

                'p' => [
                    [
                        'type'                  => 'textarea',
                        'id'                    => 'p_content',
                        'label'                 => __("Content",'sw-wapf'),
                        'description'           => __('Can contain some basic HTML.', 'sw-wapf')
                    ]
                ],

                'img' => [
                    [
                        'type'                  => 'image',
                        'id'                    => 'image',
                        'label'                 => __("Image",'sw-wapf'),
                    ]
                ],

                ];

            $options = apply_filters('wapf/field_options', $options);

            foreach($options as &$group) {
                foreach($group as &$option) {
                    $option['is_field_setting'] = true;
                }
            }

            return $options;

        }

        public static function get_function_definitions() {

            $functions = [

                [
                    'name'                  => 'min',
                    'category'              => __('Number operations', 'sw-wapf'),
                    'description'           => __( 'Find the lowest value among the provided numbers.', 'sw-wapf' ),
                    'examples'              => [
                        [
                            'example'           => 'min(5; 1; 3)',
                            'solution'          => '1',
                        ]
                    ],
                    'parameters'            => [
                        [
                            'name'          => 'number_1',
                            'description'   => __( 'The first number to compare.', 'sw-wapf' ),
                            'required'      => true,
                        ],  [
                            'name'          => 'number_2',
                            'description'   => __( 'The 2nd number to compare.', 'sw-wapf' ),
                            'required'      => true,
                        ], [
                            'name'          => 'number_n, ...',
                            'description'   => __( 'More numbers to compare.', 'sw-wapf' ),
                            'required'      => false,
                        ]
                    ]
                ],
                [
                    'name'                  => 'max',
                    'category'              => __('Number operations', 'sw-wapf'),
                    'description'           => __( 'Find the highest value among the provided numbers.', 'sw-wapf' ),
                    'examples'              => [
                        [
                            'example'           => 'max(5; 1; 3)',
                            'solution'          => '5',
                        ]
                    ],
                    'parameters'            => [
                        [
                            'name'          => 'number_1',
                            'description'   => __( 'The first number to compare.', 'sw-wapf' ),
                            'required'      => true,
                        ],  [
                            'name'          => 'number_2',
                            'description'   => __( 'The 2nd number to compare.', 'sw-wapf' ),
                            'required'      => true,
                        ], [
                            'name'          => 'number_n, ...',
                            'description'   => __( 'More numbers to compare.', 'sw-wapf' ),
                            'required'      => false,
                        ]
                    ]

                ],
                [
                    'name'                  => 'len',
                    'category'              => __('Text operations', 'sw-wapf'),
                    'description'           => __( 'Returns the number of characters in a text.', 'sw-wapf' ),
                    'examples'              => [
                        [
                            'example'           => 'len(a quick brown fox; false)',
                            'solution'          => '17',
                            'description'   => __( 'spaces count as a character.', 'sw-wapf' ),
                        ], [
                            'example'           => 'len(a quick brown fox; true)',
                            'solution'          => '14',
                            'description'   => __( ' spaces do not count as a character.', 'sw-wapf' ),
                        ]
                    ],
                    'parameters'            => [
                        [
                            'name'          => 'text',
                            'description'   => __( 'The text for which to find the length. By default, spaces count as characters.', 'sw-wapf' ),
                            'required'      => true,
                        ],  [
                            'name'          => 'exclude_spaces',
                            'description'   => __( 'True or false. When set to true, spaces won\'t count as characters.', 'sw-wapf' ),
                            'required'      => false,
                        ]

                    ]
                ], [
                    'name'                  => 'lookuptable',
                    'category'              => __('Other', 'sw-wapf'),
                    'description'           => __( 'Returns a price from a price matrix (lookup table). <a href="https://www.studiowombat.com/knowledge-base/what-is-lookup-table-pricing-and-how-does-it-work/" target="_blank">Read detailed steps</a> on how to use this.', 'sw-wapf' ),
                    'examples'              => [
                        [
                            'example'           => 'lookuptable(yourtablename; 5e8ecdf46ea7d; 1d3exl146en4w)',
                            'solution'          => '150',
                            'description'   => __( 'it was defined in a matrix table.', 'sw-wapf' ),
                        ]
                    ],
                    'parameters'            => [
                        [
                            'name'          => 'table_name',
                            'description'   => __( 'The name of your lookup table.', 'sw-wapf' ),
                            'required'      => true,
                        ],  [
                            'name'          => 'field_id_1',
                            'description'   => __( 'The ID of the input field holding the 1st number for the number pair to look up.', 'sw-wapf' ),
                            'required'      => true,
                        ], [
                            'name'          => 'field_id_2',
                            'description'   => __( 'The ID of the input field holding the 2nd number for the number pair to look up.', 'sw-wapf' ),
                            'required'      => true,
                        ]

                    ]
                ]

            ];

            return apply_filters('wapf/function_definitions', $functions);

        }

                public static function get_field_definitions( $mode = 'frontend' ) {

            $fields = Cache::get('fields_' . $mode );

            if( $fields ) return $fields;

            $fields = [
                'text' => [
                    'id'                => 'text',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'textarea' => [
                    'id'                => 'textarea',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'number' => [
                    'id'                => 'number',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products' => false,
                ],
                'email' => [
                    'id'                => 'email',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'url' => [
                    'id'                => 'url',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'select' => [
                    'id'                => 'select',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'checkboxes' => [
                    'id'                => 'checkboxes',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => true,
                    'has_products'      => false,
                ],
                'radio' => [
                    'id'                => 'radio',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'true-false' => [
                    'id'                => 'true-false',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'image-swatch' => [
                    'id'                => 'image-swatch',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'multi-image-swatch' => [
                    'id'                => 'multi-image-swatch',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => true,
                    'has_products'      => false,
                ],
                'color-swatch' => [
                    'id'                => 'color-swatch',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'multi-color-swatch' => [
                    'id'                => 'multi-color-swatch',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => true,
                    'has_products'      => false,
                ],
                'text-swatch' => [
                    'id'                => 'text-swatch',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'multi-text-swatch' => [
                    'id'                => 'multi-text-swatch',
                    'type'              => 'field',
                    'multi_choice'      => true,
                    'multi_select'      => true,
                    'has_products'      => false,
                ],
                'file' => [
                    'id'                => 'file',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products'      => false,
                ],
                'p' => [
                    'id'                => 'p',
                    'type'              => 'content',
                    'has_products'      => false,
                ],
                'img' => [
                    'id'            => 'img',
                    'type'          => 'content',
                    'has_products'  => false,
                ],
                'section' => [
                    'id'            => 'section',
                    'type'          => 'layout',
                    'has_products'  => false,
                ],
                'sectionend' => [
                    'id'            => 'sectionend',
                    'type'          => 'layout',
                    'has_products'  => false,
                ],
            ];

            if(get_option('wapf_datepicker','no') === 'yes') {
                $fields['date'] =  [
                    'id'                => 'date',
                    'type'              => 'field',
                    'multi_choice'      => false,
                    'multi_select'      => false,
                    'has_products'      => false,
                ];
            }

            $fields = apply_filters('wapf/field_types', $fields);

            if( $mode === 'frontend' ) {
                Cache::set( 'fields_' . $mode, $fields );
                return $fields;
            }

            $merge = [
                'text' => [
                    'title'         => __('Text','sw-wapf'),
                    'description'   => __('A single-line input field.', 'sw-wapf'),
                    'group_label'       => __('Basic', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M2 5V3a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V4h-7v16h3a1 1 0 0 1 0 2H8a1 1 0 0 1 0-2h3V4H4v1a1 1 0 0 1-2 0Z"/></svg>'
                ],
                'textarea' => [
                    'title'         => __('Text Area','sw-wapf'),
                    'description'   => __('A multi-line text field.', 'sw-wapf'),
                    'group_label'       => __('Basic', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M2 5V3a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V4h-7v16h3a1 1 0 0 1 0 2H8a1 1 0 0 1 0-2h3V4H4v1a1 1 0 0 1-2 0Z"/></svg><svg xmlns="http://www.w3.org/2000/svg" style="margin-left: -5px" width="12" height="12" viewBox="0 0 24 24"><path d="M2 5V3a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V4h-7v16h3a1 1 0 0 1 0 2H8a1 1 0 0 1 0-2h3V4H4v1a1 1 0 0 1-2 0Z"/></svg>'
                ],
                'number' => [
                    'title'         => __('Number','sw-wapf'),
                    'description'   => __('A number input field', 'sw-wapf'),
                    'group_label'       => __('Basic', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M20 14h-4.3l.73-4H20a1 1 0 0 0 0-2h-3.21l.69-3.81A1 1 0 0 0 16.64 3a1 1 0 0 0-1.22.82L14.67 8h-3.88l.69-3.81A1 1 0 0 0 10.64 3a1 1 0 0 0-1.22.82L8.67 8H4a1 1 0 0 0 0 2h4.3l-.73 4H4a1 1 0 0 0 0 2h3.21l-.69 3.81A1 1 0 0 0 7.36 21a1 1 0 0 0 1.22-.82L9.33 16h3.88l-.69 3.81a1 1 0 0 0 .84 1.19 1 1 0 0 0 1.22-.82l.75-4.18H20a1 1 0 0 0 0-2zM9.7 14l.73-4h3.87l-.73 4z"/></svg>'
                ],
                'email' => [
                    'title'         => __('E-mail','sw-wapf'),
                    'description'   => __('An email input field.', 'sw-wapf'),
                    'group_label'       => __('Basic', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="#828282" stroke-width="2" d="M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm0 0v1.5a2.5 2.5 0 0 0 2.5 2.5v0a2.5 2.5 0 0 0 2.5-2.5V12a9 9 0 1 0-9 9h4"/></svg>'
                ],
                'url' => [
                    'title'         => __('URL','sw-wapf'),
                    'description'   => __('An input field only accepting URLs.', 'sw-wapf'),
                    'group_label'       => __('Basic', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="#828282" stroke-width="2" d="M9 17H7A5 5 0 0 1 7 7h2m6 10h2a5 5 0 0 0 0-10h-2m-8 5h10"/></svg>'
                ],
                'select' => [
                    'title'         => __('Select list','sw-wapf'),
                    'description'   => __('A dropdown list where the user selects one option.', 'sw-wapf'),
                    'group_label'       => __('Choice', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="24" height="24"><path fill="#828282" d="m44.48 36.82 5.2-6.15H39.2l5.27 6.15Z"/><path fill="none" stroke="#828282" stroke-width="4" d="M54.5 41.56c0 1.89-1.74 3.42-3.88 3.42H12.94c-2.15 0-3.88-1.53-3.88-3.42V26.22c0-1.88 1.73-3.41 3.88-3.41h37.68c2.14 0 3.88 1.53 3.88 3.41v15.34Z"/></svg>'
                ],
                'checkboxes' => [
                    'title'         => __('Checkboxes','sw-wapf'),
                    'description'   => __('A series of checkboxes. The user can select multiple.', 'sw-wapf'),
                    'group_label'       => __('Choice', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path d="M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm1 2v14h14V5H5zm6.003 11L6.76 11.757l1.414-1.414 2.829 2.829 5.656-5.657 1.415 1.414L11.003 16z"/></svg>'
                ],
                'radio' => [
                    'title'         => __('Radio buttons','sw-wapf'),
                    'description'   => __('A series of radio buttons. The user can select one.', 'sw-wapf'),
                    'group_label'       => __('Choice', 'sw-wapf'),
                    'icon'          => '<svg height="15" width="15" viewBox="0 0 16 16"><path d="M8 4c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"></path><path d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"></path></svg>',
                ],
                'true-false' => [
                    'title'         => __('True/False','sw-wapf'),
                    'description'   => __('One checkbox indicating true ("yes") or false ("no").', 'sw-wapf'),
                    'group_label'       => __('Choice', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path d="M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm1 2v14h14V5H5zm6.003 11L6.76 11.757l1.414-1.414 2.829 2.829 5.656-5.657 1.415 1.414L11.003 16z"/></svg>'
                ],
                'image-swatch' => [
                    'title'         => __('Image swatches','sw-wapf'),
                    'description'   => __('A series of image options. The user can select only one.', 'sw-wapf'),
                    'group_label'       => __('Swatch', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"><path stroke="#828282" stroke-width="2" d="m4 17 3.59-3.23a2 2 0 0 1 2.75.07L11.5 15l3.59-3.59a2 2 0 0 1 2.82 0L20 13.5M11 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2Z"/></svg>'
                ],
                'multi-image-swatch' => [
                    'title'         => __('Multi-select image swatches','sw-wapf'),
                    'description'   => __('A series of image options. The user can select multiple.', 'sw-wapf'),
                    'group_label'       => __('Swatch', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"><path stroke="#828282" stroke-width="2" d="m4 17 3.59-3.23a2 2 0 0 1 2.75.07L11.5 15l3.59-3.59a2 2 0 0 1 2.82 0L20 13.5M11 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2Z"/></svg>'
                ],
                'color-swatch' => [
                    'title'         => __('Color swatches','sw-wapf'),
                    'description'   => __('A series of color options. The user can select only one.', 'sw-wapf'),
                    'group_label'       => __('Swatch', 'sw-wapf'),
                    'icon'          => '<svg height="14" width="14" viewBox="0 0 14 14"><path d="M8 4c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"></path></svg><svg height="14" width="14" viewBox="0 0 14 14" style="margin-left: -4px;"><path d="M8 4c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"></path></svg><svg height="14" width="14" viewBox="0 0 14 14" style="display: block;margin-left: 6px; margin-top: -8px;"><path d="M8 4c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"></path></svg>',
                ],
                'multi-color-swatch' => [
                    'title'         => __('Multi-select color swatches','sw-wapf'),
                    'description'   => __('A series of color options. The user can select multiple.', 'sw-wapf'),
                    'group_label'       => __('Swatch', 'sw-wapf'),
                    'icon'          => '<svg height="14" width="14" viewBox="0 0 14 14"><path d="M8 4c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"></path></svg><svg height="14" width="14" viewBox="0 0 14 14" style="margin-left: -4px;"><path d="M8 4c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"></path></svg><svg height="14" width="14" viewBox="0 0 14 14" style="display: block;margin-left: 6px; margin-top: -8px;"><path d="M8 4c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"></path></svg>',
                ],
                'text-swatch' => [
                    'title'         => __('Text swatches','sw-wapf'),
                    'description'   => __('A series of text badges. The user can select only one.', 'sw-wapf'),
                    'group_label'       => __('Swatch', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48"><path fill="none" d="M0 0h48v48H0z"></path><path d="M43 3H5a2 2 0 0 0-2 2v38a2 2 0 0 0 2 2h38a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm-2 38H7V7h34Z"></path><path d="M17.2 31h13.6l2.6 6h4.4L26.5 11h-5L10.2 37h4.4ZM24 14.9 29.1 27H18.9Z"/></path></svg>'
                ],
                'multi-text-swatch' => [
                    'title'         => __('Multi-select text swatches','sw-wapf'),
                    'description'   => __('A series of text badges. The user can select multiple.', 'sw-wapf'),
                    'group_label'       => __('Swatch', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48"><path fill="none" d="M0 0h48v48H0z"></path><path d="M43 3H5a2 2 0 0 0-2 2v38a2 2 0 0 0 2 2h38a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm-2 38H7V7h34Z"></path><path d="M17.2 31h13.6l2.6 6h4.4L26.5 11h-5L10.2 37h4.4ZM24 14.9 29.1 27H18.9Z"/></path></svg>'
                ],
                'file' => [
                    'title'         => __('File upload','sw-wapf'),
                    'description'   => __('Allows users to upload one or more files.', 'sw-wapf'),
                    'group_label'       => __('Advanced', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"><path stroke="#828282" stroke-width="2" d="M13 3H7a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h10a2 2 0 0 0 2-2V9m-6-6 6 6m-6-6v5a1 1 0 0 0 1 1h5"/></svg>'
                ],
                'p' => [
                    'title'         => __('Text & HTML','sw-wapf'),
                    'description'   => __('A text paragraph. Some HTML and shortcodes are allowed.', 'sw-wapf'),
                    'group_label'       => __('Content', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"><path stroke="#828282" stroke-width="2" d="M12 18 8 5H7L3 18m1.23-4h6.54m3.73-4C16 9 20 8 20 11.5V18m0-5.5c-1.5.5-6 .5-6 3.5s4.5 2 6-.5"/></svg>'
                ],
                'img' => [
                    'title'         => __('Image','sw-wapf'),
                    'description'   => __('An image.', 'sw-wapf'),
                    'group_label'       => __('Content', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"><path stroke="#828282" stroke-width="2" d="m4 17 3.59-3.23a2 2 0 0 1 2.75.07L11.5 15l3.59-3.59a2 2 0 0 1 2.82 0L20 13.5M11 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2Z"/></svg>'
                ],

                'section' => [
                    'title'         => __('Section','sw-wapf'),
                    'description'   => __('A wrapper for fields, handy for styling or to enable a repeater for multiple fields.','sw-wapf'),
                    'group_label'       => __('Layout', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 15 15"><path fill="#828282" fill-rule="evenodd" d="M2 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM2 5v5h11V5H2Zm0-1a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H2Zm-.5 10a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1ZM4 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM3.5 14a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1ZM6 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM5.5 14a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1ZM8 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM7.5 14a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1ZM10 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM9.5 14a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1ZM12 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM11.5 14a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1ZM14 1.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM13.5 14a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1Z" clip-rule="evenodd"/></svg>'
                ],
                'sectionend' => [
                    'title'         => __('Section end','sw-wapf'),
                    'description'   => __('Ends a previously started section.','sw-wapf'),
                    'group_label'       => __('Layout', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"><path fill="#828282" d="M2.5 12c0-.41.34-.75.75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 2.5 12ZM6.5 12c0-.41.34-.75.75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 6.5 12ZM10.5 12c0-.41.34-.75.75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM14.5 12c0-.41.34-.75.75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM18.5 12c0-.41.34-.75.75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75ZM4.75 2a.75.75 0 0 0-.75.75V7c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V2.75a.75.75 0 0 0-1.5 0V7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5V2.75A.75.75 0 0 0 4.75 2ZM19.25 22c.41 0 .75-.34.75-.75V17a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v4.25a.75.75 0 0 0 1.5 0V17c0-.27.22-.5.5-.5h12c.28 0 .5.23.5.5v4.25c0 .41.34.75.75.75Z"/></svg>'
                ],
            ];

            if(get_option('wapf_datepicker','no') === 'yes') {
                $merge['date'] =  [
                    'title'         => __('Date','sw-wapf'),
                    'description'   => __('Allows users to select a date from a calendar.', 'sw-wapf'),
                    'group_label'       => __('Advanced', 'sw-wapf'),
                    'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="M6 22h12a3 3 0 0 0 3-3V7a2 2 0 0 0-2-2h-2V3a1 1 0 0 0-2 0v2H9V3a1 1 0 0 0-2 0v2H5a2 2 0 0 0-2 2v12a3 3 0 0 0 3 3Zm-1-9.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5V19a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1Z"/></svg>'
                ];
            }

                        $all = array_merge_recursive( $fields, $merge);
            Cache::set(  'fields_' . $mode, $all );

            return $all;

        }

        public static function get_field_definition_for( $type ) {

            $all = self::get_field_definitions( 'frontend' );

            return $all[ $type ];

        }

    }
}

<?php


printf('<strong> %s</strong>: %s',__('Min estimate date','pi-edd'), $min_estimate_date_display);
woocommerce_form_field( 'pi_overall_estimate_min_date', array(
    'type'            => 'text',
    'class'           => array('custom-question-field', 'form-row-wide'),
    'input_class'     => array('pisol-order-date-picker'),
    'custom_attributes' => array('readonly' => 'readonly')
), $min_estimate_date );

printf('<strong> %s</strong>: %s',__('Max estimate date','pi-edd'), $max_estimate_date_display);
woocommerce_form_field( 'pi_overall_estimate_max_date', array(
    'type'            => 'text',
    'class'           => array('custom-question-field', 'form-row-wide'),
    'input_class'     => array('pisol-order-date-picker'),
    'custom_attributes' => array('readonly' => 'readonly')
), $max_estimate_date );



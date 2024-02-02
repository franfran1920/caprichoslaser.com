<?php
/**
 * Plugin Update Checker Library 5.3
 * http://w-shadow.com/
 *
 * Copyright 2022 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */

require dirname(__FILE__) . '/load-v5p3.php';

if ( ! class_exists( 'Puc_v4_Factory' ) ) :
    class Puc_v4_Factory extends \YahnisElsts\PluginUpdateChecker\v5p3\PucFactory {

    }
endif;

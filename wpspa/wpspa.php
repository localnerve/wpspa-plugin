<?php

/*
 * Plugin Name: WPSPA by LocalNerve.com
 * Plugin URI: http://wordpress.org/extend/plugins/wpspa/
 * Description: WPSPA helps you use your self-hosted Wordpress site as the back-end for a dynamic, data-driven, single page application.
 * Author: LocalNerve
 * Version: 0.0.1
 * Author URI: http://localnerve.com
 * License: GPL2+
 */

define( 'WPSPA__MINIMUM_WP_VERSION', '3.5' );
define( 'WPSPA__VERSION', '0.0.1' );
define( 'WPSPA__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( WPSPA__PLUGIN_DIR . 'class.wpspa.php'       );
require_once( WPSPA__PLUGIN_DIR . 'wpspa-admin.php'       );
require_once( WPSPA__PLUGIN_DIR . 'wpspa-actions.php'     );
require_once( WPSPA__PLUGIN_DIR . 'wpspa-controllers.php' );

register_activation_hook( __FILE__, array( 'WPSPA', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'WPSPA', 'plugin_deactivation' ) );
add_action( 'init', array( 'WPSPA', 'init' ) );

?>

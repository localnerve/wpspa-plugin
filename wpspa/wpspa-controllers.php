<?php
define( 'WPSPA__CONTROLLER_DIR', plugin_dir_path( __FILE__ ) . 'controllers/' );
define( 'WPSPA__MODEL_DIR', plugin_dir_path( __FILE__ ) . 'models/' );
define( 'WPSPA__MODULE_DIR', plugin_dir_path( __FILE__ ) . 'modules/' );

require_once( WPSPA__MODULE_DIR . 'class.links.php' );

$wpspa_controller_path = WPSPA__CONTROLLER_DIR . 'class.wpspa-jsonapi-controller.php';

require_once( $wpspa_controller_path );

// Extend json-api controller array
function wpspa_controllers($controllers) {
  array_push($controllers, 'wpspa');
  return $controllers;
}

// Reveal the wpspa controller path to json-api
function wpspa_path($path) {
  global $wpspa_controller_path;
  return $wpspa_controller_path;
}

// Get the calls from json-api
add_filter( 'json_api_controllers', 'wpspa_controllers' );
add_filter( 'json_api_wpspa_controller_path', 'wpspa_path' );

?>

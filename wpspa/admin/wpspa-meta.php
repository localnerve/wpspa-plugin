<?php

define( 'WPSPA__META_DIR', plugin_dir_path( __FILE__ ) . 'meta/' );

require_once( WPSPA__META_DIR . 'class.meta.php' );

add_action( 'add_meta_boxes', array( 'WPSPA_Meta', 'add_custom_box' ) );
add_action( 'save_post', array( 'WPSPA_Meta', '_save_post' ) );

?>

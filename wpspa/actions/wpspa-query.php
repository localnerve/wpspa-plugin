<?php

define( 'WPSPA__QUERY_DIR', plugin_dir_path( __FILE__ ) . 'query/' );

require_once( WPSPA__QUERY_DIR . 'class.wpspa-query.php' );

add_action( 'json_api_query', array( 'WPSPA_Query', 'query' ), 1, 1 );
add_action( 'json_api_import_wp_post', array( 'WPSPA_Query', 'import_wp_post' ), 1, 2 );

?>
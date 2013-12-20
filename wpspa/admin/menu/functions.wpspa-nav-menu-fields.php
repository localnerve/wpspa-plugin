<?php

add_filter( 'WPSPA_nav_menu_item_additional_fields', 'WPSPA_menu_item_additional_fields' );
function WPSPA_menu_item_additional_fields( $fields ) {
	$fields['route'] = array(
		'name' => 'route',
		'label' => __('WPSPA Route', 'WPSPA'),
		'input_type' => 'text'
	);
	$fields['menu_text'] = array(
		'name' => 'menu_text',
		'label' => __('WPSPA Menu Text', 'WPSPA'),
		'input_type' => 'text'
	);
    $fields['post_type'] = array(
      'name' => 'post_type',
      'label' => __('WPSPA Post Type', 'WPSPA'),
      'input_type' => 'text'
    );
    $fields['object_id'] = array(
      'name' => 'object_id',
      'label' => __('WPSPA Object ID', 'WPSPA'),
      'input_type' => 'text'
    );
	return $fields;
}

?>

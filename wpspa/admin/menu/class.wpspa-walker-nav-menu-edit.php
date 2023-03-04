<?php

require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

class WPSPA_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item_output = '';
		parent::start_el($item_output, $data_object, $depth, $args);
		// Inject $new_fields before: <div class="menu-item-actions description-wide submitbox">
		if ( $new_fields = WPSPA_Nav_Menu_Item_Custom_Fields::get_field( $data_object, $depth, $args ) ) {
			$item_output = preg_replace('/(?=<div[^>]+class="[^"]*submitbox)/', $new_fields, $item_output);
		}
		$output .= $item_output;
	}
}

?>
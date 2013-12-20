<?php

require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

class WPSPA_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {
	function start_el(&$output, $item, $depth, $args) {
		$item_output = '';
		parent::start_el($item_output, $item, $depth, $args);
		// Inject $new_fields before: <div class="menu-item-actions description-wide submitbox">
		if ( $new_fields = WPSPA_Nav_Menu_Item_Custom_Fields::get_field( $item, $depth, $args ) ) {
			$item_output = preg_replace('/(?=<div[^>]+class="[^"]*submitbox)/', $new_fields, $item_output);
		}
		$output .= $item_output;
	}
}

?>
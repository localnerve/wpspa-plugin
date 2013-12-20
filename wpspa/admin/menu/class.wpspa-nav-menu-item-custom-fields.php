<?php

class WPSPA_Nav_Menu_Item_Custom_Fields {
	private static $options = array(
		'item_tpl' => '
			<p class="additional-menu-field-{name} description description-thin">
				<label for="edit-menu-item-{name}-{id}">
					{label}<br>
					<input
						type="{input_type}"
						id="edit-menu-item-{name}-{id}"
						class="widefat code edit-menu-item-{name}"
						name="menu-item-{name}[{id}]"
						value="{value}">
				</label>
			</p>
		',
	);
 
	private static function get_fields_schema( $new_fields ) {
		$schema = array();
		foreach( $new_fields as $name => $field) {
			if (empty($field['name'])) {
				$field['name'] = $name;
			}
			$schema[] = $field;
		}
		return $schema;
	}
 
	private static function get_menu_item_postmeta_key($name) {
		return '_menu_item_wpspa_' . $name;
	}
 
	/**
   * Retrieve a field, called from WPSPA_Walker_Nav_Menu_Edit
	 */
	public static function get_field( $item, $depth, $args ) {
		$new_fields = '';
		foreach( self::$options['fields'] as $field ) {
			$field['value'] = get_post_meta($item->ID, self::get_menu_item_postmeta_key($field['name']), true);
            $field['id'] = $item->ID;
            
            if ( $item->object == 'page' && empty($field['value']))
            {
                if ($field['name'] == 'post_type')
                    $field['value'] = 'page';
                else if ($field['name'] == 'object_id')
                    $field['value'] = get_post_meta($item->ID, '_menu_item_object_id', true);
            }
            
            $new_fields .= str_replace(
                array_map(function($key){ return '{' . $key . '}'; }, array_keys($field)),
                array_values(array_map('esc_attr', $field)),
                self::$options['item_tpl']
            );
		}
		return $new_fields;
	}
 
    /**
     * Setup the functionality that pertains to this WP nav menu items extension.
     */
    public static function setup() {
		if ( !is_admin() )
			return;
 
		$new_fields = apply_filters( 'WPSPA_nav_menu_item_additional_fields', array() );
		if ( empty($new_fields) )
			return;
		self::$options['fields'] = self::get_fields_schema( $new_fields );
 
		add_filter( 'wp_edit_nav_menu_walker', function () {
			return 'WPSPA_Walker_Nav_Menu_Edit';
		});

		add_action( 'save_post', array( __CLASS__, '_save_post' ), 10, 2 );
	}

	/**
	 * Save the newly submitted fields
	 * @hook {action} save_post
	 */
	public static function _save_post($post_id, $post) {
		if ( $post->post_type !== 'nav_menu_item' ) {
			return $post_id; // prevent weird things from happening
		}
 
		foreach( self::$options['fields'] as $field_schema ) {
			$form_field_name = 'menu-item-' . $field_schema['name'];
			if (isset($_POST[$form_field_name][$post_id])) {
				$key = self::get_menu_item_postmeta_key($field_schema['name']);
				$value = stripslashes($_POST[$form_field_name][$post_id]);
				update_post_meta($post_id, $key, $value);
			}
		}
	}
 
}

?>
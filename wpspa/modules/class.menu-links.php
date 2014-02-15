<?php

require_once( WPSPA__MODULE_DIR . 'class.links.php' );

class WPSPA_Menu_Links {
    
  // Make sense out of the _wpspa_prefetch custom field.
  // If it exists and set to any non-falsy string at all, it is true.
  // All other cases, it is false.
  static public function get_prefetch_preference($custom) {
    $result = false;

    if ($custom['_wpspa_prefetch'] && count($custom['_wpspa_prefetch']) > 0) {
      $result = (bool)$custom['_wpspa_prefetch'][0];
    }

    return $result;
  }

  // Receives a post from the post_type=nav_menu_item query
  // Updates the post's custom_fields with a _menu_item_wpspa_object_links array
  // that contains objects:
  //   { id, type, href, has_comments, can_comment, prefetch }
  static protected function deep_links_from_menu_post($menuItem) {
    $custom_fields = $menuItem->custom_fields;
    $target_id = $custom_fields->_menu_item_wpspa_object_id[0];
    $post = get_post($target_id);
    $custom_fields->_menu_item_wpspa_object_links = array();

    $children = WPSPA_Links::get_link_children($post);

    $single_post_types = array( 'post', 'page' );
    $single_post_types = apply_filters('WPSPA_single_post_types', $single_post_types);

    foreach ($children as $id => $child) {
      $custom = get_post_custom((int)$id);
      $type = get_post_type((int)$id);
      array_push($custom_fields->_menu_item_wpspa_object_links,
        (object)array(
          'id' => (int)$id,
          'type' => $type,
          'name' => $child->post->post_name,
          'href' => $child->href,
          'is_single' => in_array($type, $single_post_types),
          'prefetch' => WPSPA_Menu_Links::get_prefetch_preference($custom)
        )
      );
    }
  }
  
  // The collection of custom_fields this class contributes
  static public function get_custom_fields() {
    return array('_menu_item_wpspa_object_links');
  }
  
  //
  // Get the deep links for the menu item's target posts.
  // Receives an array of nav_item_menu posts.
  static public function object_links($posts) {
    foreach ($posts as $post) {
      WPSPA_Menu_Links::deep_links_from_menu_post($post);
    }
  }
}

?>

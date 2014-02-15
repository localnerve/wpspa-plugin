<?php

/*
 * Add target post object properties to the custom fields of menu items
 */
class WPSPA_Menu_Props {
  
  // Custom check to see if a post is single. For now, this means
  // !is_archive
  static protected function is_single($target_query) {
    return !$target_query->is_archive;
  }

  // Receives a menu item from the post_type=nav_menu_item query,
  //  and the inner query of menu targets from custom field _menu_item_wpspa_object_id.
  // Updates the menu_item's custom_fields with a _menu_item_wpspa_object_props object
  //  that contains properties about the target post specified in 
  // _menu_item_wpspa_object_id:
  //   { is_single }
  static protected function props_from_menu_post($menu_item, $target_query) {
    
    // _menu_item_wpspa_object_props defaults
    $props = array(
      'is_single' => false
    );

    if ($target_query)
      $props['is_single'] = WPSPA_Menu_props::is_single($target_query);

    $custom_fields = $menu_item->custom_fields;
    $custom_fields->_menu_item_wpspa_object_props = (object)$props;
  }

  // Return the target object id for the menuItem
  static protected function get_target_ids($menuItem) {
    $custom_fields = $menuItem->custom_fields;
    return $custom_fields->_menu_item_wpspa_object_id[0];
  }

  // Return the post IDs
  static protected function get_found_ids($post) {
    return $post->ID;
  }

  // Return the target object id for the menuItem
  static protected function get_menu_target_map($menuItem) {
    $custom_fields = $menuItem->custom_fields;
    return array(
      $custom_fields->_menu_item_wpspa_object_id[0] => $menuItem
    );
  }

  // Reduce the menu target map to a single association where target_id is the key .
  // target_id => menu_item
  static protected function reduce_menu_target_map($acc, $item) {
    $key = array_shift(array_keys($item));
    $acc[(string)$key] = $item[$key];
    return $acc;
  }

  // The collection of custom_fields this class contributes
  static public function get_custom_fields() {
    return array('_menu_item_wpspa_object_props');
  }
  
  //
  // Get the relevant contributing properties for the menu item's target posts.
  // Receives an array of nav_item_menu posts.
  static public function object_props($posts) {
    // get an array of target object_ids for each menu item
    $target_ids = array_map(array(__CLASS__, "get_target_ids"), $posts);
    // get an array of target_id to menu_item mappings [target_id] => menu_item
    $menu_item_target_map = array_reduce(
      array_map(array(__CLASS__, "get_menu_target_map"), $posts),
      array(__CLASS__, "reduce_menu_target_map"),
      array()
    );

    // run the query for the target object_ids
    $target_query = new WP_Query(array('post_type' => 'any', 'post__in' => $target_ids));

    // give any target_ids not found the defaults
    $not_founds = array_diff(
      $target_ids, array_map(array(__CLASS__, "get_found_ids"), $target_query->posts)
    );
    foreach ($not_founds as $not_found) {
      WPSPA_Menu_Props::props_from_menu_post(
        $menu_item_target_map[$not_found],
        null        
      );
    }

    // loop through the targets and update the menu_item accordingly
    while($target_query->have_posts()) {
      $target_query->next_post();
      WPSPA_Menu_props::props_from_menu_post(
        $menu_item_target_map[$target_query->post->ID], 
        $target_query        
      );
    }

    //wp_reset_query();
    wp_reset_postdata();
  }  
}

?>
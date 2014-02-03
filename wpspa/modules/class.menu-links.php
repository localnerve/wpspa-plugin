<?php

class WPSPA_Menu_Links {
    
  // Make sense out of the _wpspa_prefetch custom field.
  // If it exists and set to any non-falsy string at all, it is true.
  // All other cases, it is false.
  static protected function get_prefetch_preference($custom) {
    $result = false;

    if ($custom['_wpspa_prefetch'] && count($custom['_wpspa_prefetch']) > 0) {
      $result = (bool)$custom['_wpspa_prefetch'][0];
    }

    return $result;
  }
    
  // Pull all hrefs in anchors from the content.
  // Returns an array of hrefs.
  static protected function parse_anchor_hrefs_from_html($content) {
    $links = array();

    if (preg_match_all( '#(?:<a[^>]+?href=["|\'](?P<link_url>[^\s]+?)["|\'][^>]*?>\s*){1}(?:[^<]+</a>)?#is', $content, $links )) {
      foreach ( $links as $key => $unused ) {
        // Simplify the output as much as possible, mostly for confirming test results.
        if ( is_numeric( $key ) && $key > 0 )
          unset( $links[$key] );
      }
    }
        
    return $links['link_url'];
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
    
    if ($post) {      
      $hrefs = 
          WPSPA_Menu_Links::parse_anchor_hrefs_from_html($post->post_content);
      foreach ($hrefs as $href) {
        $id = url_to_postid($href);
        if ($id != 0) {
          $deep_post = get_post($id);
          $custom = get_post_custom($id);
          array_push($custom_fields->_menu_item_wpspa_object_links,
            (object)array(
              'id' => $id,
              'type' => get_post_type($id),
              'href' => $href,
              'has_comments' => $deep_post->comment_count > 0,
              'can_comment' => $deep_post->comment_status == "open",
              'prefetch' => WPSPA_Menu_Links::get_prefetch_preference($custom)
          ));
        }
      }
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

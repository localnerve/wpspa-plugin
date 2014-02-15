<?php

class WPSPA_Links {
    
  // Pull all hrefs in anchors from the content.
  // Returns an array of hrefs.
  static public function parse_anchor_hrefs_from_html($content) {
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

  // Get child posts of the given parent by scanning the parents content for anchor hrefs
  static public function get_link_children($parent) {
    $children = array();
    $sub_children = array();

    $hrefs = WPSPA_Links::parse_anchor_hrefs_from_html($parent->post_content);

    foreach ($hrefs as $href) {
      $id = url_to_postid($href);
      if ($id != 0) {
        $child = get_post($id);
        $child->child = true;
        $children[(string)$id] = (object)array('href' => $href, 'post' => $child);
        $sub_children = WPSPA_Links::get_link_children($child);
        foreach ($sub_children as $sub_id => $sub_child) {
          $children[(string)$sub_id] = $sub_child->post;
        }
      }
    }

    return $children;
  }

  // Call get_link_children on an array of wp_posts and add to it
  static public function add_link_children() {
    global $wp_query;

    $keyed_children = array();
    $keyed_posts = array();
    $children = array();

    foreach ($wp_query->posts as $post) {      
      $keyed_posts[(string)$post->ID] = $post;
      $children = WPSPA_Links::get_link_children($post);
      foreach ($children as $id => $child) {
        $keyed_children[(string)$id] = $child->post;
      }
    }

    $keyed_result = array_merge($keyed_posts, $keyed_children);
    $wp_query->post_count = count($keyed_result);
    $wp_query->posts = $keyed_result;
  }
}

?>
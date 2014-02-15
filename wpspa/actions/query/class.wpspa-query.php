<?php

class WPSPA_Query {

  // looks this might get covered in WP 3.9
  // http://wpseek.com/is_nav_menu_item/
  protected static function is_nav_menu_item($wp_post) {    
    return ( ! is_wp_error( $wp_post->ID ) && ( 'nav_menu_item' == $wp_post->post_type ) );
  }

  // The handler for the json_api_import_wp_post action
  public static function import_wp_post($json_api_post, $wp_post) {
    $custom_fields = $json_api_post->custom_fields;
    if ( isset($wp_post->child) ) {
      $custom_fields->_wpspa_child_link = $wp_post->child;
    } else {
      if ( !WPSPA_Query::is_nav_menu_item($wp_post) )
        $custom_fields->_wpspa_child_link = false;
    }
  }

  // The handler for the json_api_query action
  public static function query($wp_query) {
    WPSPA_Links::add_link_children();
  }
}

?>
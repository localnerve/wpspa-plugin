<?php

class WPSPA_Query {

  // The handler for the json_api_import_wp_post action
  public static function import_wp_post($json_api_post, $wp_post) {
    $custom_fields = $json_api_post->custom_fields;
    if (isset($wp_post->child)) {      
      $custom_fields->_wpspa_child_link = $wp_post->child;
    } else {
      $custom_fields->_wpspa_child_link = false;
    }
  }

  // The handler for the json_api_query action
  public static function query($wp_query) {
    WPSPA_Links::add_link_children();
  }
}

?>
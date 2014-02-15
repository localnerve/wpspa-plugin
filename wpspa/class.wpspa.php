<?php

class WPSPA {
    
  // This stops a bug in Wordpress:canonical.php misinterpreting host:port in the HTTP 1.1 header "host"
  // This is crucial to allowing WP to be used as a back-end by a reverse-proxy on the front-end server.
  public static function _stop_redirect_canonical() {
    return false;
  }

  public static function _json_api_dependency_error() {
    echo "<div id=\"json-api-error\" class=\"error\"><p>JSON API plugin not found or active. This plugin depends on the JSON API plugin to function.</p></div>";
  }
    
  public static function init() {
    add_filter('redirect_canonical', array(__CLASS__, '_stop_redirect_canonical'));
    if (!class_exists('JSON_API')) {
      add_action('admin_notices', array(__CLASS__, '_json_api_dependency_error'));
      return;
    }
  }
        
  public static function plugin_activation() {
    add_filter('redirect_canonical', array(__CLASS__, '_stop_redirect_canonical'));
  }
    
  public static function plugin_deactivation() {
    remove_filter('redirect_canonical', array(__CLASS__, '_stop_redirect_canonical'));
  }

}

?>

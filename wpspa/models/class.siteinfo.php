<?php

class WPSPA_SiteInfo {
  var $name;            // String
  var $description;     // String
  
  function __construct() {
    $this->name = get_bloginfo('name');
    $this->description = get_bloginfo('description');
  }
}
?>

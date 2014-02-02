<?php

class WPSPA_SiteInfo {
  var $name;            // String
  var $description;     // String
  
  function WPSPA_SiteInfo() {
    $this->name = get_bloginfo('name');
    $this->description = get_bloginfo('description');
  }
}
?>

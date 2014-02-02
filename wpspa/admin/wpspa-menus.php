<?php

define( 'WPSPA__MENU_DIR', plugin_dir_path( __FILE__ ) . 'menu/' );

require_once( WPSPA__MENU_DIR . 'class.wpspa-nav-menu-item-custom-fields.php' );
require_once( WPSPA__MENU_DIR . 'functions.wpspa-nav-menu-fields.php'  );

if (is_admin()) {
  require_once( WPSPA__MENU_DIR . 'class.wpspa-walker-nav-menu-edit.php' );
}

add_action( 'init', array( 'WPSPA_Nav_Menu_Item_Custom_Fields', 'setup' ) );

?>
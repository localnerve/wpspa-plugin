<?php
/*
Controller name: WPSPA
Controller description: JSON API Extension for WPSPA Client
*/

require_once( WPSPA__MODEL_DIR . 'class.siteinfo.php' );
require_once( WPSPA__MODULE_DIR . 'class.menu-links.php' );
        
class json_api_wpspa_controller {

    protected function posts_result($posts) {
        global $wp_query;
        return array(
          'count' => count($posts),
          'count_total' => (int) $wp_query->found_posts,
          'pages' => $wp_query->max_num_pages,
          'posts' => $posts
        );
    }
        
    // ------------------------------------------------------------------------
    // Public Controller Methods
    // ------------------------------------------------------------------------
    
    //
    // Get basic site info
    //
    public function site_info() {
        return new WPSPA_SiteInfo();
    }
    
    //
    // Get wpspa extended menu, including some dynamic custom_fields provided
    // by introspection of target posts
    //
    public function menu() {
        global $json_api;
        $url = parse_url($_SERVER['REQUEST_URI']);
        $defaults = array(
            'post_type' => 'nav_menu_item',
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'custom_fields' => '_menu_item_menu_item_parent,_menu_item_wpspa_route,_menu_item_wpspa_menu_text,_menu_item_wpspa_post_type,_menu_item_wpspa_object_id',
            'ignore_sticky_posts' => true
        );
        $query = wp_parse_args($url['query']);
        unset($query['json']);
        unset($query['post_status']);
        $query = array_merge($defaults, $query);
        $_REQUEST["custom_fields"] = $query['custom_fields'];
        
        $posts = $json_api->introspector->get_posts($query);
        
        WPSPA_Menu_Links::object_links($posts);

        $query['custom_fields'] .= "," .
            implode(",", WPSPA_Menu_Links::get_custom_fields());
        
        $result = $this->posts_result($posts);
        $result['query'] = $query;
        return $result;
    }
}

?>

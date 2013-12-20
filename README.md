# WPSPA-Plugin
> A Wordpress plugin that helps turn your site into a back-end for an SPA

## Description
WPSPA is a WordPress plugin that works with the [json-api](http://wordpress.org/extend/plugins/json-api/) plugin and SPA client code like the [WPSPA](http://github.com/localnerve/wpspa) app to enable a high-performance, data-driven single page application architecture.
If you are not planning on using an SPA to represent your Wordpress site, this plugin is probably not for you.

You can use this plugin together with an SPA app like the [WPSPA](http://github.com/localnerve/wpspa) app to ease the development of your Wordpress driven SPA.
For a full front-end sample implementation, more documentation, and performance metrics, check out the [WPSPA](http://github.com/localnerve/wpspa) application.

## Prerequisites
Currently, this plugin only extends the wonderful [json-api](http://wordpress.org/plugins/json-api/) plugin. The json-api plugin is required to be installed and activated when this plugin is activated. 
> Future work may include the Wordpress public API, usable on custom installations with the Jetpack plugin.

## Features
+ Simplified menu api over json-api alone.
+ Extends standard Wordpress menu to allow you to specify metadata for menu items.
+ Extends pages and posts with custom fields to assist an SPA.
+ Automatically crawls the content of your posts and pages for local site links and sends those down as deep routes to the SPA.
+ Deep routes can be classified as prefetch or on-demand.

## Extensions
The WPSPA Plugin extends Wordpress Admin functions to supply additional meta data to post types. It also extends the json-api plugin to supply a new controller to simplify the retrieval of some site data.

### Menu Admin Extension
The WPSPA Plugin extends Wordpress menu items by adding fields to the Wordpress admin menu screen. These extra fields are stored in the postmeta table.

+ `WPSPA Route`
  + wp_postmeta key: `_menu_item_wpspa_route`
  + This represents the in-app route for this menu item. This is intended to be a url path without protocol or hostname.
  + Examples: `/`, `/sample-page`
+ `WPSPA Menu Text`
  + wp_postmeta key: `_menu_item_wpspa_menu_text`
  + This represents the text that should be presented with this item in the application.
  + Examples: `Home`, `Sample Page`
+ `WPSPA Post Type`
  + wp_postmeta key: `_menu_item_wpspa_post_type`
  + This is auto filled as a `page`, but can really be any string at all. This is intended to be a Wordpress standard or custom post type.
  + Examples: `page`, `post`, `my-custom-type`
+ `WPSPA Object ID`
  + wp_postmeta key: `_menu_item_wpspa_object_id`
  + This represents the underlying Wordpress post id. This is intended to be the actual Wordpress post id of the item represented by this menu entry.

### Meta Admin Extension
The WPSPA Plugin extends page, post, or custom types with a few extra admin meta fields that can help your SPA.
If you have a custom post type and would like to add these fields in the Wordpress admin interface, hook the filter `WPSPA_meta_box_post_types`, add your custom post type(s) to the post type array, and return the result.
In order to receive these custom fields for a page or post in your SPA, add the appropriate custom_fields to your query string on your json-api call. You can receive some or all of these fields. 
Example: `custom_fields=_wpspa_meta_description,_wpspa_page_title`

+ `Meta Description`
  + wp_postmeta key: `_wpspa_meta_description`
  + This represents the page meta description for SEO purposes. Any text you place here is intended to go to the description meta tag when the SPA navigates to the corresponding in-app route.
  + This can be any text that would normally appear in a meta description tag in the head of an html document. Limited to 165 characters.
+ `Page Title`
  + wp_postmeta key: `_wpspa_page_title`
  + This represents the page title for SEO purposes. Any text you place here is intended to go to the title tag when the SPA navigates to the corresponding in-app route.
  + This can be any text that would normally appear in a title tag in the head of an html document. Limited to 65 characters.
+ `Prefetch`
  + wp_postmeta key: `_wpspa_prefetch`
  + This represents a boolean that instructs an SPA to prefetch content. This is only useful for content that has not yet been fetched by the SPA. An example of this situation is when you have a page in navigation that contains links to other pages or posts that are not yet fetched. In this case, this flag can be used to direct the SPA to prefetch the deeper content, so when the user clicks the deeper links the content will already be ready to present.

### WPSPA Controller
The WPSPA Controller extends json-api by adding the `wpspa` controller space. This controller is accessible just like the other json-api controllers.
Example: `/api/wpspa/site_info`

#### `site_info`
  Gets basic site blog info.

  + `GET` method only  
  + No arguments
  + Request example: `curl 'http://mysite/api/wpspa/site_info'`
  + Response example:
  ``` javascript
    {
      "status": "ok",
      "name": "Mysite Name",
      "description": "Just another WordPress site"
    }
  ```

#### `menu`
  This method is an alias for the json-api core method `get_posts` but internally hardcodes the following arguments:
  ``` javascript
  /api/get_posts/?post_type=nav_menu_item&order=ASC&orderby=menu_order&custom_fields=_menu_item_menu_item_parent,_menu_item_wpspa_route,_menu_item_wpspa_menu_text,_menu_item_wpspa_post_type,_menu_item_wpspa_object_id`
  ```
  In addition to this, it also finds any anchor links inside the content of each target menu item. These deep links are placed in the reponse in a dynamic custom field called `_menu_item_wpspa_object_links`
  
  + `GET` method only
  + All arguments are optional and overridable
  + Retrieves navigation menu and all related WPSPA meta data
  + Request example: `curl 'http://mysite/api/wpspa/menu'`
  + Response example:
  ``` javascript
  {
    "status": "ok",
    "count": 4,
    "count_total": 4,
    "pages": 1,
    "posts": [
      {
        "id": 59,
        "type": "nav_menu_item",
        "slug": "home",
        "url": "http:\/\/jsonapi.local\/home\/",
        "status": "publish",
        "title": "Home",
        "title_plain": "Home",
        "content": "",
        "excerpt": "",
        "date": "2013-08-15 21:49:29",
        "modified": "2013-10-01 17:49:39",
        "categories": [
          
        ],
        "tags": [
          
        ],
        "author": {
          "id": 1,
          "slug": "jsonuser",
          "name": "jsonuser",
          "first_name": "",
          "last_name": "",
          "nickname": "jsonuser",
          "url": "",
          "description": ""
        },
        "comments": [
          
        ],
        "attachments": [
          
        ],
        "comment_count": 0,
        "comment_status": "open",
        "custom_fields": {
          "_menu_item_menu_item_parent": [
            "0"
          ],
          "_menu_item_wpspa_route": [
            "\/"
          ],
          "_menu_item_wpspa_menu_text": [
            "Home"
          ],
          "_menu_item_wpspa_post_type": [
            "recent"
          ],
          "_menu_item_wpspa_object_id": [
            "1"
          ],
          "_menu_item_wpspa_object_links": [
            
          ]
        }
      },
      {
        "id": 60,
        "type": "nav_menu_item",
        "slug": "60",
        "url": "http:\/\/jsonapi.local\/60\/",
        "status": "publish",
        "title": "",
        "title_plain": "",
        "content": "",
        "excerpt": "",
        "date": "2013-08-15 21:49:29",
        "modified": "2013-10-01 17:49:39",
        "categories": [
          
        ],
        "tags": [
          
        ],
        "author": {
          "id": 1,
          "slug": "jsonuser",
          "name": "jsonuser",
          "first_name": "",
          "last_name": "",
          "nickname": "jsonuser",
          "url": "",
          "description": ""
        },
        "comments": [
          
        ],
        "attachments": [
          
        ],
        "comment_count": 0,
        "comment_status": "open",
        "custom_fields": {
          "_menu_item_menu_item_parent": [
            "0"
          ],
          "_menu_item_wpspa_route": [
            "\/sample-page"
          ],
          "_menu_item_wpspa_menu_text": [
            "Sample Page"
          ],
          "_menu_item_wpspa_post_type": [
            "page"
          ],
          "_menu_item_wpspa_object_id": [
            "2"
          ],
          "_menu_item_wpspa_object_links": [
            
          ]
        }
      },
      {
        "id": 61,
        "type": "nav_menu_item",
        "slug": "61",
        "url": "http:\/\/jsonapi.local\/61\/",
        "status": "publish",
        "title": "",
        "title_plain": "",
        "content": "",
        "excerpt": "",
        "date": "2013-08-15 21:49:30",
        "modified": "2013-10-01 17:49:39",
        "categories": [
          
        ],
        "tags": [
          
        ],
        "author": {
          "id": 1,
          "slug": "jsonuser",
          "name": "jsonuser",
          "first_name": "",
          "last_name": "",
          "nickname": "jsonuser",
          "url": "",
          "description": ""
        },
        "comments": [
          
        ],
        "attachments": [
          
        ],
        "comment_count": 0,
        "comment_status": "open",
        "custom_fields": {
          "_menu_item_menu_item_parent": [
            "0"
          ],
          "_menu_item_wpspa_route": [
            "\/page-two"
          ],
          "_menu_item_wpspa_menu_text": [
            "Page Two"
          ],
          "_menu_item_wpspa_post_type": [
            "page"
          ],
          "_menu_item_wpspa_object_id": [
            "11"
          ],
          "_menu_item_wpspa_object_links": [
            {
              "id": 76,
              "type": "page",
              "href": "http:\/\/jsonapi.local\/an-internal-page\/",
              "has_comments": false,
              "can_comment": true,
              "prefetch": false
            }
          ]
        }
      },
      {
        "id": 68,
        "type": "nav_menu_item",
        "slug": "68",
        "url": "http:\/\/jsonapi.local\/68\/",
        "status": "publish",
        "title": "",
        "title_plain": "",
        "content": "",
        "excerpt": "",
        "date": "2013-10-01 17:47:03",
        "modified": "2013-10-01 17:49:39",
        "categories": [
          
        ],
        "tags": [
          
        ],
        "author": {
          "id": 1,
          "slug": "jsonuser",
          "name": "jsonuser",
          "first_name": "",
          "last_name": "",
          "nickname": "jsonuser",
          "url": "",
          "description": ""
        },
        "comments": [
          
        ],
        "attachments": [
          
        ],
        "comment_count": 0,
        "comment_status": "open",
        "custom_fields": {
          "_menu_item_menu_item_parent": [
            "61"
          ],
          "_menu_item_wpspa_route": [
            "\/page-two\/page-two-subpage"
          ],
          "_menu_item_wpspa_menu_text": [
            "Page Two Subpage"
          ],
          "_menu_item_wpspa_post_type": [
            "page"
          ],
          "_menu_item_wpspa_object_id": [
            "66"
          ],
          "_menu_item_wpspa_object_links": [
            
          ]
        }
      }
    ],
    "query": {
      "post_type": "nav_menu_item",
      "order": "ASC",
      "orderby": "menu_order",
      "custom_fields": "_menu_item_menu_item_parent,_menu_item_wpspa_route,_menu_item_wpspa_menu_text,_menu_item_wpspa_post_type,_menu_item_wpspa_object_id,_menu_item_wpspa_object_links",
      "ignore_sticky_posts": true
    }
  }
  ```
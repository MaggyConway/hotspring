<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_mc_collection_type{

  public function __construct(){
    add_action( 'init', [ $this, 'new_type' ], 0);
    //init fields
    $this->init_fields();
  }


  // Register new Custom Post Type
  public function new_type() {
    $labels = [
      "name" => __( "Collections", "textdomain" ),
      "singular_name" => __( "Collection", "textdomain" ),
      "menu_name" => __( "Collections", "textdomain" ),
    ];

    $args = [
      "label" => __( "Collections", "textdomain" ),
      "labels" => $labels,
      "description" => "",
      "public" => true,
      "publicly_queryable" => true,
      "show_ui" => true,
      "show_in_rest" => false,
      "rest_base" => "",
      "rest_controller_class" => "WP_REST_Posts_Controller",
      "has_archive" => false,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "delete_with_user" => false,
      "exclude_from_search" => false,
      "capability_type" => "post",
      "map_meta_cap" => true,
      "hierarchical" => false,
      "rewrite" => [ "slug" => "shop-hot-tub-models", "with_front" => true ],
      "query_var" => true,
      "menu_position" => 99,
      "menu_icon" => "dashicons-admin-multisite",
      "supports" => [ "title", "editor", "thumbnail" ],
      "show_in_graphql" => false,
    ];

    register_post_type( "collections", $args );
  }

  // init ACF Fields
  function init_fields() {
  }
}

new class_mc_collection_type();

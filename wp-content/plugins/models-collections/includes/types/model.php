<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_mc_model_type{

  public function __construct(){
    add_action( 'init', array( $this, 'new_type'), 0);
    //init fields
    $this->init_fields();
    add_filter( 'post_type_link', array( $this, 'model_permalinks' ), 10, 2 );
    add_action( 'generate_rewrite_rules', array( $this, 'add_rewrite_rules' ) );
  }


  // Register new Custom Post Type
  public function new_type() {
    $labels = [
      "name" => __( "Models", "textdomain" ),
      "singular_name" => __( "Model", "textdomain" ),
      "menu_name" => __( "Models", "textdomain" ),
    ];

    $args = [
      "label" => __( "Models", "textdomain" ),
      "labels" => $labels,
      "description" => "",
      "public" => true,
      "publicly_queryable" => true,
      "show_ui" => true,
      "show_in_rest" => false,
      "rest_base" => "model",
      "rest_controller_class" => "WP_REST_Posts_Controller",
      "has_archive" => false,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "delete_with_user" => false,
      "exclude_from_search" => false,
      "capability_type" => "post",
      "map_meta_cap" => true,
      "hierarchical" => false,
      "rewrite" => [ "slug" => "shop-hot-tub-models/%collection%", "with_front" => false ],
      "query_var" => true,
      "menu_position" => 99,
      "menu_icon" => "dashicons-products",
      "supports" => [ "title", "editor", "thumbnail" ],
      "show_in_graphql" => false,
    ];

    register_post_type( "model", $args );
  }

  // init ACF Fields
  function init_fields(){
    if (function_exists('acf_add_local_field_group')) {
      acf_add_local_field_group(array(
        'key' => 'group_model_collection',
        'title' => 'Model: Collection',
        'fields' => array(
          array(
            'key' => 'field_model_collection',
            'label' => 'Select collection',
            'name' => 'model_collection',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'post_type' => array(
              0 => 'collections',
            ),
            'taxonomy' => array(
            ),
            'allow_null' => 1,
            'multiple' => 0,
            'return_format' => 'object',
            'ui' => 1,
          ),
        ),
        'location' => array(
          array(
            array(
              'param' => 'post_type',
              'operator' => '==',
              'value' => 'model',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
      ));
    }
  }

  // replace category in permalink
  public function model_permalinks( $permalink, $post ) {
    if ( $post->post_type !== 'model' ) {
        return $permalink;
    }

    $collection = get_field( 'model_collection', $post->ID );
    if ( !$collection ) {
        return str_replace( '%collection%', 'collection', $permalink );
    }

    return str_replace( '%collection%', $collection->post_name, $permalink );
  }

  // add rewrite rules (shop-hot-tub-models/collection/name)
  public function add_rewrite_rules( $wp_rewrite ) {
    $new_rules = array(
        // shop-hot-tub-models/nxt/grandee-nxt/
        'shop-hot-tub-models/(.+?)/(.+?)/?$' => 'index.php?post_type=model&name=' . $wp_rewrite->preg_index( 2 ),
    );
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
  }
}

new class_mc_model_type();
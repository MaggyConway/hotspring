<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_dc_coupon_type{

  public function __construct(){
    add_action( 'init', array( $this, 'new_type'), 0);
    //@TODO check how it worcking on import process
    add_filter('acf/update_value', array( $this, 'field_update_value'), 10, 3);
    //init fields
    $this->init_fields();

    add_filter('acf/prepare_field/key=dcc_default_headline', array( $this, 'coupon_fields'));
    add_filter('acf/prepare_field/key=dc_dealer', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dc_campaign', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_active', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_weight', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_style_css', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_coupon_print_image', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_coupon_preview_image', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_show_info', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_show_info', array( $this, 'coupon_hide_fields'));
    add_filter('acf/prepare_field/key=dcc_website', array( $this, 'coupon_admin_update_field'));
    add_filter('acf/prepare_field/key=dcc_legal_text', array( $this, 'coupon_admin_update_field'));

    //remove yoast(wp_seo) from edls form if user not admin
    add_action('add_meta_boxes', array( $this, 'coupon_yoast_remove'), 999);

  }

  /**
   * remove yoast panels
   */
  function coupon_yoast_remove(){
    if(!in_array('administrator',  wp_get_current_user()->roles)){
      remove_meta_box('wpseo_meta', 'dc_coupon', 'normal', 10000);
    }
  }

  /**
   * Remove required
   */
  function coupon_fields( $field  ) {
    $screen = get_current_screen();
    if($screen->post_type == 'dc_coupon' && $field['key'] == 'dcc_default_headline'){
      $field['required']=0;
    }
    return $field;
  }

  /**
   * hide fields
   */
  function coupon_hide_fields( $field  ) {
    $screen = get_current_screen();
    if( isset($screen->post_type) && trim($screen->post_type) == 'dc_coupon'
    && !in_array('administrator',  wp_get_current_user()->roles)
    ){
      return FALSE;
    }
    return $field;
  }

  /**
   * remove field for non admin roles
   */
  function coupon_admin_update_field( $field  ) {
    if(!in_array('administrator',  wp_get_current_user()->roles)){
      return FALSE;
    }
    return $field;
  }

  function field_update_value( $value, $post_id, $field  ) {
    // only do it to certain custom fields
    if( $field['name'] == 'dc_campaign' ) {
      // get the old (saved) value
      $old_value = get_field($field['name'], $post_id);

      // get the new (posted) value
      $new_value = $_POST['acf'][$field['name']['key']];
        wp_set_post_terms( $post_id, array( (int) $value ), 'dc_campaign', false );
    }
    return $value;
  }

  // Register new Custom Post Type
  public function new_type() {

    $labels = array(
      'name'                  => _x( 'Coupons', 'Post Type General Name', 'dc' ),
      'singular_name'         => _x( 'Coupon', 'Post Type Singular Name', 'dc' ),
      'menu_name'             => __( 'Coupons', 'dc' ),
      'name_admin_bar'        => __( 'Coupons', 'dc' ),
      'archives'              => __( 'Coupon Archives', 'dc' ),
      'attributes'            => __( 'Coupon Attributes', 'dc' ),
      'parent_item_colon'     => __( 'Parent Coupon:', 'dc' ),
      'all_items'             => __( 'All Coupons', 'dc' ),
      'add_new_item'          => __( 'Add New Coupon', 'dc' ),
      'add_new'               => __( 'Add New', 'dc' ),
      'new_item'              => __( 'New Coupon', 'dc' ),
      'edit_item'             => __( 'Edit Coupon', 'dc' ),
      'update_item'           => __( 'Update Coupon', 'dc' ),
      'view_item'             => __( 'View Coupon', 'dc' ),
      'view_items'            => __( 'View Coupons', 'dc' ),
      'search_items'          => __( 'Search coupon', 'dc' ),
      'not_found'             => __( 'Not found', 'dc' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'dc' ),
      'featured_image'        => __( 'Featured Image', 'dc' ),
      'set_featured_image'    => __( 'Set featured image', 'dc' ),
      'remove_featured_image' => __( 'Remove featured image', 'dc' ),
      'use_featured_image'    => __( 'Use as featured image', 'dc' ),
      'insert_into_item'      => __( 'Insert into Coupon', 'dc' ),
      'uploaded_to_this_item' => __( 'Uploaded to this item', 'dc' ),
      'items_list'            => __( 'Coupon list', 'dc' ),
      'items_list_navigation' => __( 'Coupon list navigation', 'dc' ),
      'filter_items_list'     => __( 'Filter Coupon list', 'dc' ),
    );
    $capabilities = array(
      'edit_post'             => 'edit_coupon',
      'read_post'             => 'read_coupon',
      'delete_post'           => 'delete_coupon',
      'edit_posts'            => 'edit_coupons',
      'edit_others_posts'     => 'edit_others_coupons',
      'publish_posts'         => 'publish_coupons',
      'read_private_posts'    => 'read_private_coupons',
      'create_posts'          => 'create_coupons',
    );
    $args = array(
      'label'               => __( 'Coupons', '' ),
      'labels'              => $labels,
      'description'         => 'Coupons',
      'public'              => true,
      'publicly_queryable'  => true,
      'show_ui'             => true,
      'show_in_rest'        => false,
      'rest_base'           => '',
      //'has_archive'         => false,
      'show_in_menu'        => true,
      'exclude_from_search' => false,
      'hierarchical'        => false,
      'rewrite'             => array( 'slug' => 'coupon',),
      'capability_type'     => ['coupon','coupons'],
      'query_var'           => true,
      'supports'            => array( 'title', ),
      'capabilities'        => $capabilities,
      'menu_icon'           => 'dashicons-tag',
    );
    register_post_type( 'dc_coupon', $args );
  }

  // init ACF Fields
  function init_fields(){
    if( function_exists('acf_add_local_field_group') ):

      acf_add_local_field_group(array (
        'key' => 'group_dc_coupon',
        'title' => 'Coupon',
        'fields' =>
        array (
          0 =>
          array (
            'key' => 'dc_dealer',
            'label' => 'Dealer',
            'name' => 'dc_dealer',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' =>
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'post_type' =>
            array (
              0 => 'edls',
            ),
            'taxonomy' =>
            array (
            ),
            'allow_null' => 0,
            'multiple' => 0,
            'return_format' => 'id',
            'ui' => 1,
          ),
          1 =>
          array (
            'key' => 'dc_campaign',
            'label' => 'Сampaign',
            'name' => 'dc_campaign',
            'type' => 'taxonomy',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' =>
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'taxonomy' => 'dc_campaign',
            'field_type' => 'select',
            'allow_null' => 0,
            'add_term' => 1,
            'save_terms' => 0,
            'load_terms' => 0,
            'return_format' => 'id',
            'multiple' => 0,
          ),
        ),
        'location' =>
        array (
          0 =>
          array (
            0 =>
            array (
              'param' => 'post_type',
              'operator' => '==',
              'value' => 'dc_coupon',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
      ));

    endif;
  }
}

new class_dc_coupon_type();

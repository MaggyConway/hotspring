<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_dc_campaign_type{

  public function __construct(){
    add_action( 'init', array( $this, 'new_type' ));
    //init fields
    $this->init_fields();
    add_filter( 'bulk_actions-edit-dc_campaign', array( $this, 'register_bulk_actions' ));
    add_filter( 'handle_bulk_actions-edit-dc_campaign', array( $this, 'bulk_action'), 10, 3 );
    add_action( 'admin_notices', array( $this, 'bulk_action_notice') );
  }

  /**
   * Hook info for bulk actions
   */
  function register_bulk_actions($bulk_actions) {
    $bulk_actions['dc_add_coupons'] = 'Add Coupons to all Dealers';
    $bulk_actions['dc_delete_all'] = 'Delete all Coupons';
    return $bulk_actions;
  }

  /**
   * get all cupons include cupons in trash
   */
  function getAllCuponsByTermID($term_id){
    $args = array(
  	'post_type' => 'dc_coupon',
    'posts_per_page' => '-1',
  	'meta_query' => array(
      array(
        'key' => 'dc_campaign',
        'value' => $term_id,
      )),
    );
    $query = new WP_Query($args);
    return $query->posts;
  }

  /**
   * create compagin cupons for all dealers
   * @param  [type] $term_id [description]
   * @return [type]          [description]
   */
  function createAllCuponsByTermID($term_id){
    $term_meta = get_term_meta($term_id);
    $term = get_term($term_id);
    //get all dealers
    $dealers = _get_dealers_list();
    foreach ($dealers as $dealer) {
      if(_check_dealer_coupons($dealer->ID, $term->term_id)){
        continue;
      }
      $coupon_post = array(
         'ID' => 0,
         'post_title'    => wp_strip_all_tags($term->name . ' for ' . $dealer->post_title),
         'post_content'  => '',
         'post_status'   => 'publish',
         'post_category' => array($term->term_id),
         //'post_author'  => $user_id,
         'post_type' => 'dc_coupon',
         'meta_input'   => array(
           'dc_campaign' => $term->term_id,
           'dc_dealer' => $dealer->ID,
           'dcc_default_headline' => $term_meta['dcc_default_headline'][0],
           'dcc_coupon_text' => $term_meta['dcc_coupon_text'][0],
           'dcc_legal_text' => $term_meta['dcc_legal_text'][0],
           'dcc_website' => $term_meta['dcc_website'][0],
           'dcc_coupon_preview_image' => $term_meta['dcc_coupon_preview_image'][0],
           'dcc_coupon_print_image' => $term_meta['dcc_coupon_print_image'][0],
           'dcc_active' => $term_meta['dcc_active'][0],
           'dcc_weight' => $term_meta['dcc_weight'][0],
         ),
       );
       // Insert the post into the database
       $post_id = wp_insert_post($coupon_post);
       if(!empty($post_id)&&!is_object($post_id)){
         //add compain as term to coupons
         wp_set_post_terms( $post_id, array($term->term_id), 'dc_campaign', false );
       }
   }
   return count($dealers);
  }

  /**
   * bulk callback
   * @param  [type]  $sendback [description]
   * @param  boolean $action   [description]
   * @param  boolean $items    [description]
   * @param  boolean $site_id  [description]
   * @return [type]            [description]
   */
  function bulk_action( $sendback, $action = false, $items = false, $site_id = false ) {

    if ( $action == 'dc_delete_all' ) {
      foreach ($items as $item) {
        $posts = $this->getAllCuponsByTermID($item);
        foreach ($posts as $key => $post) {
          wp_delete_post($post->ID);
        }
      }
      $count = count($items)*count($posts);
      $sendback = add_query_arg( 'dc_delete_all', $count , $sendback );

    }

    if ( $action === 'dc_add_coupons' ) {
      $count = 0;
      foreach ($items as $key => $item) {
         $count = $count + $this->createAllCuponsByTermID($item);
      }
      $sendback = add_query_arg( 'dc_add_coupons', $count , $sendback );
    }
    return $sendback;
  }

  function bulk_action_notice() {
    $current_screen = get_current_screen();
    if ( isset( $_REQUEST['dc_add_coupons'] ) ) {
      printf( '<div id="message" class="updated fade"><p>Added %s cupons</p></div>', $_REQUEST['dc_add_coupons']);
    }
    if ( isset( $_REQUEST['dc_delete_all'] ) ) {
      printf( '<div id="message" class="updated fade"><p>Delete %s cupons</p></div>', $_REQUEST['dc_delete_all']);
    }
  }

  // Register Custom Post Type
  public function new_type() {
    // Add a taxonomy like tags
    $labels = array(
      'name'                       => _x( 'Campaigns', 'Taxonomy General Name', 'dc' ),
      'singular_name'              => _x( 'Campaign', 'Taxonomy Singular Name', 'dc' ),
      'menu_name'                  => __( 'Campaign', 'dc' ),
      'all_items'                  => __( 'All Campaigns', 'dc' ),
      'parent_item'                => __( 'Parent Campaign', 'dc' ),
      'parent_item_colon'          => __( 'Parent Campaign:', 'dc' ),
      'new_item_name'              => __( 'New Campaign Name', 'dc' ),
      'add_new_item'               => __( 'Add New Campaign', 'dc' ),
      'edit_item'                  => __( 'Edit Campaign', 'dc' ),
      'update_item'                => __( 'Update Campaign', 'dc' ),
      'view_item'                  => __( 'View Campaign', 'dc' ),
      'separate_items_with_commas' => __( 'Separate campaigns with commas', 'dc' ),
      'add_or_remove_items'        => __( 'Add or remove campaigns', 'dc' ),
      'choose_from_most_used'      => __( 'Choose from the most used', 'dc' ),
      'popular_items'              => __( 'Popular campaigns', 'dc' ),
      'search_items'               => __( 'Search campaigns', 'dc' ),
      'not_found'                  => __( 'Not Found', 'dc' ),
      'no_terms'                   => __( 'No campaigns', 'dc' ),
      'items_list'                 => __( 'Campaigns list', 'dc' ),
      'items_list_navigation'      => __( 'Campaigns list navigation', 'dc' ),
    );
    $rewrite = array(
      'slug'                       => 'campaign',
      // 'with_front'                 => true,
      // 'hierarchical'               => false,
    );
    $capabilities = array(
      'manage_terms'               => 'manage_campaigns',
      'edit_terms'                 => 'manage_campaigns',
      'delete_terms'               => 'manage_campaigns',
      'assign_terms'               => 'edit_coupon',
    );
    $args = array(
      'labels'                     => $labels,
      'hierarchical'               => false,
      'public'                     => false,
      'show_ui'                    => true,
      'show_admin_column'          => false,
      'show_in_nav_menus'          => false,
      'show_tagcloud'              => false,
      //'query_var'                  => 'campaign',
      'rewrite'                    => $rewrite,
      'capabilities'               => $capabilities,
      'meta_box_cb'                => false,
      // 'rewrite' => array('slug' => 'dealers'),
      // 'capability_type'     => ['dealer','dealers'],
      // 'map_meta_cap'        => true,
    );
    register_taxonomy( 'dc_campaign', array( 'dc_coupon' ), $args );
  }

  // init ACF Fields
  function init_fields(){
    if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array (
      'key' => 'dc_campaign',
      'title' => 'Campaign',
      'fields' =>
      array (
        0 =>
        array (
          'key' => 'dcc_default_headline',
          'label' => 'Default Headline',
          'name' => 'dcc_default_headline',
          'type' => 'text',
          'instructions' => 'Please limit your coupon Headline text to 20 characters so it will fit on the coupon',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' =>
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'placeholder' => 'Headline',
          'prepend' => '',
          'append' => '',
          'maxlength' => '',
        ),
        1 =>
        array (
          'key' => 'dcc_coupon_text',
          'label' => 'Coupon Text',
          'name' => 'dcc_coupon_text',
          'type' => 'textarea',
          'instructions' => 'Please limit your coupon text to 250 characters so it will fit on the coupon',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' =>
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'placeholder' => '',
          'maxlength' => '',
          'rows' => '',
          'new_lines' => '',
        ),
        2 =>
        array (
          'key' => 'dcc_legal_text',
          'label' => 'Legal Text',
          'name' => 'dcc_legal_text',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' =>
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'placeholder' => '',
          'maxlength' => '',
          'rows' => '',
          'new_lines' => '',
        ),
        // 3 =>
        // array (
        //   'key' => 'dcc_website',
        //   'label' => 'Redirect coupon preview to this URL',
        //   'name' => 'dcc_website',
        //   'type' => 'url',
        //   'instructions' => '',
        //   'required' => 0,
        //   'conditional_logic' => 0,
        //   'wrapper' =>
        //   array (
        //     'width' => '',
        //     'class' => '',
        //     'id' => '',
        //   ),
        //   'default_value' => '',
        //   'placeholder' => '',
        // ),
        4 =>
        array (
          'key' => 'dcc_coupon_preview_image',
          'label' => 'Coupon Preview Image',
          'name' => 'dcc_coupon_preview_image',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' =>
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'array',
          'preview_size' => 'thumbnail',
          'library' => 'all',
          'min_width' => '',
          'min_height' => '',
          'min_size' => '',
          'max_width' => '',
          'max_height' => '',
          'max_size' => '',
          'mime_types' => '',
        ),
        5 =>
        array (
          'key' => 'dcc_coupon_print_image',
          'label' => 'Coupon Print Image',
          'name' => 'dcc_coupon_print_image',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' =>
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'array',
          'preview_size' => 'thumbnail',
          'library' => 'all',
          'min_width' => '',
          'min_height' => '',
          'min_size' => '',
          'max_width' => '',
          'max_height' => '',
          'max_size' => '',
          'mime_types' => '',
        ),
        6 =>
        array (
    			'key' => 'dcc_active',
    			'label' => 'Active',
    			'name' => 'dcc_active',
    			'type' => 'true_false',
    			'instructions' => '',
    			'required' => 0,
    			'conditional_logic' => 0,
    			'wrapper' => array (
    				'width' => '',
    				'class' => '',
    				'id' => '',
    			),
    			'message' => '',
    			'default_value' => 0,
    			'ui' => 0,
    			'ui_on_text' => '',
    			'ui_off_text' => '',
    		),
        7 => array (
    			'key' => 'dcc_weight',
    			'label' => 'Weight',
    			'name' => 'dcc_weight',
    			'type' => 'number',
    			'instructions' => '',
    			'required' => 0,
    			'conditional_logic' => 0,
    			'wrapper' => array (
    				'width' => '',
    				'class' => '',
    				'id' => '',
    			),
    			'default_value' => '10',
    			'placeholder' => '',
    			'prepend' => '',
    			'append' => '',
    			'min' => -100,
    			'max' => 100,
    			'step' => '',
    		),
        8 =>
        array (
          'key' => 'dcc_dont_show_info',
          'label' => 'Don\'t show the text of coupon',
          'name' => 'dcc_dont_show_info',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 0,
          'ui' => 0,
          'ui_on_text' => '',
          'ui_off_text' => '',
        ),
        9 =>
        array (
    			'key' => 'dcc_style_css',
    			'label' => 'Style CSS',
    			'name' => 'dcc_style_css',
    			'type' => 'textarea',
    			'instructions' => '',
    			'required' => 0,
    			'conditional_logic' => 0,
    			'wrapper' => array (
    				'width' => '',
    				'class' => '',
    				'id' => '',
    			),
    			'default_value' => '',
    			'placeholder' => '',
    			'maxlength' => '',
    			'rows' => '',
    			'new_lines' => '',
    		),
      ),
      'location' =>
      array (
        0 =>
        array (
          0 =>
          array (
            'param' => 'taxonomy',
            'operator' => '==',
            'value' => 'dc_campaign',
          ),
        ),
        1 =>
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
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => 1,
      'description' => '',
      //'modified' => 1508924377,
    ));

    endif;
  }

}

new class_dc_campaign_type();

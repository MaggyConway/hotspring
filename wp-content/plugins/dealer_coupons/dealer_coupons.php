<?php
/*
Plugin Name: Dealers Coupons
Description: Coupons and Campaign
*/

if ( ! defined('ABSPATH')) exit;  // if direct access


class DealersCoupons{

	public function __construct(){
    $this->define_constants();

    add_action('acf/include_field_types', 	array($this, 'include_field_types')); // v5
    add_action('acf/register_fields', 		array($this, 'include_field_types')); // v4
    add_action( 'init', array( $this, 'init' ), 1 );
		add_filter( 'template_include', array( $this, 'include_template_function' ), 1 );

		//
    add_action( 'wp_insert_post', array( $this, 'edl_post_insert' ), 10, 3 );
    add_action( 'before_delete_post',  array( $this, 'edl_post_delete' ) );


    $this->loading_functions();

		$this->declare_classes();
		$this->declare_types();
		$this->declare_actions();


		register_activation_hook( __FILE__, array( $this, 'install' ) );
    //add_filter( 'post_type_link', array( $this, 'post_type_link' ), 10, 3 );
    //add_filter( 'manage_edit-dc_coupon', array( $this, 'manage_edit_post_columns' ) );
    //
    add_filter( 'map_meta_cap', array( $this, 'map_meta_cap'), 10, 4  );
    add_filter( 'parse_query', array( $this, 'coupons_dealer_filter') );


		add_filter('manage_edit-dc_campaign_columns' , array( $this, 'taxonomy_columns') );
    add_filter( 'manage_dc_campaign_custom_column', array( $this, 'taxonomy_columns_content' ), 10, 3 );
	}

  /**
   * Implementation hook post update
   *
   * @param [type] $post_id
   * @param [type] $post
   * @param [type] $update
   * @return void
   */
  function edl_post_insert( $post_id, $post, $update ) {
    // check post type
    if($post->post_type != 'edls'){
      return;
    }
    $default_campaign = _dc_get_default_campaign();
    $dealer_id = get_post_meta( $post->ID , 'dealership_id' , TRUE);
    $campaign_id = $default_campaign->term_id;
    // check the campaign of dealers
    if ( !_check_dealer_coupons($post->ID, $campaign_id) ) {
      $term_meta = get_term_meta($campaign_id);
      $term = get_term($campaign_id);
      dc_create_coupon_dealer($term, $term_meta, $dealer_id, NULL, NULL, FALSE);
    }
  }

  /**
   * Implementation hook post delete
   *
   * @param [type] $post_id
   * @return void
   */
  function edl_post_delete( $post_id ) {
    // get the dealer object
    $post = wp_get_single_post($post_id);
    // check post type
    if($post->post_type != 'edls'){
      return;
    }
    $coupons = _get_all_dealer_coupons($post_id);
    foreach ($coupons as $key => $coupon) {
      wp_delete_post( $coupon->ID, TRUE );
    }
  }


  /**
   * Add custom columns
   */
  function taxonomy_columns_content( $content, $column_name, $term_id ){
    // if ( 'dcc_active' == $column_name ) {
    //     $active = get_term_meta( $term_id, 'dcc_active', TRUE );
    //     $content = ($active)? 'Active':'Passive' ;
    // }
    if ( 'dcc_weight' == $column_name ) {
        $weight = get_term_meta( $term_id, 'dcc_weight', TRUE );
        $content = $weight ;
    }
    return $content;
  }

	/**
	 * Add custom columns head
	 */
	function taxonomy_columns( $columns ){
		// $columns['dcc_active'] = __('Active');
    $columns['dcc_weight'] = __('Weight');
		return $columns;
	}
  /**
   * get dealer post by dealership_id field value
   * @param  string $DealershipId
   * @return [type]               [description]
   */
  function _get_dealers_by_dealership_id($DealershipIDs){
    $args = array(
  	'post_type' => 'edls',
  	'meta_query' => array(
      'relation' => 'OR',
      array(
        'key' => 'dealership_id',
        'value' => $DealershipIDs,
        'compare' => 'IN',
      ),
      array(
        'key'     => 'dealership_billto',
        'value'   => $DealershipIDs,
        'compare' => 'IN',
      ),
    ),

    );
    $query = new WP_Query($args);
    return $query->posts;
  }

  // filter for dealer role
  function coupons_dealer_filter( $query ){
    global $pagenow;

		$type = isset($query->query['post_type']) ? $query->query['post_type'] : 'post';
    $user = wp_get_current_user();

    if ( in_array( 'dealer', (array) $user->roles )
    && !in_array( 'admin', (array) $user->roles )
    && $type == 'dc_coupon'
    && $pagenow == 'edit.php' ) {
      $user_id = $user->ID;
      $data = get_user_meta( $user_id, 'dealer_reference', true );
      $data = explode (',' , $data);
      $data = array_map('trim', $data);
      $dealers = $this->_get_dealers_by_dealership_id($data);
      $dealers_id = array();
      foreach ($dealers as $key => $dealer) {
        $dealers_id[] = $dealer->ID;
      }
      $query->set( 'meta_query', array(
        array(
          'key' => 'dc_dealer',
          'value' => $dealers_id,
          'compare' => 'IN',
        )));
      }
  }

  /**
   * Helper function
   */
  function dealer_user_can_edit( $user_id, $page_id ) {

    $coupon = get_post( $page_id );
    if($coupon->post_type == 'dc_coupon'){
      $user = get_user_by( 'ID', $user_id );
      $dealer_id = get_post_meta( $coupon->ID, 'dc_dealer', $single = false );

      if(!empty($dealer_id[0])){
        $dealership_id = get_post_meta( $dealer_id[0], 'dealership_id', true );
        $dealership_billto = get_post_meta( $dealer_id[0], 'dealership_billto', true );

        return _dealer_check_dealer_reference_at_user($user_id, $dealership_id, $dealership_billto);
      }
    }
    return FALSE;
  }

  function map_meta_cap( $caps, $cap, $user_id, $args ){
    // return [ 'exist' ];
    $to_filter = [
        'edit_post', //I can't changed this, see "post.php" file for more detales
        'edit_coupon',
        'edit_others_coupons',
        'edit_published_coupons',
      ];
    // If the capability being filtered isn't of our interest, just return current value
    if ( !in_array( $cap, $to_filter, true ) ) {
      return $caps;
    }

    if(!empty($args[0])
     && $this->dealer_user_can_edit( $user_id, $args[0])
     ){
      return [ 'exist' ];
    }

    // redirect after submit, see "post.php" file for more detales
    if(isset($_POST['action']) && $_POST['action'] == 'editpost'
    && isset($_POST['user_ID'])
    && isset($_POST['post_ID'])
    && $this->dealer_user_can_edit( $_POST['user_ID'], $_POST['post_ID'])){
      return [ 'exist' ];
    }

    // Use deafult access
    // User is not allowed, let's tell that to WP
    // return [ 'do_not_allow' ];
    return $caps;
  }
	function include_template_function( $template_path ) {

    if ( get_post_type() == 'dc_coupon' ) {
      if ( is_single() ) {
        $template_path = plugin_dir_path( __FILE__ ) . '/single-coupon.php';
        return $template_path;
      }


      // if ( is_single() ) {
      //     // checks if the file exists in the theme first,
      //     // otherwise serve the file from the plugin
      //     if ( $theme_file = locate_template( array ( 'single-coupon.php' ) ) ) {
      //         $template_path = $theme_file;
      //     } else {
      //         $template_path = plugin_dir_path( __FILE__ ) . '/single-coupon.php';
      //     }
      // }
    }
    return $template_path;
	}

  function manage_edit_post_columns( $columns ) {
    // print($columns);
    // exit();
  }
  function post_type_link( $post_link, $post, $leavename ) {
    // // if ( 'race' != $post->post_type || 'publish' != $post->post_status ) {
    // //     return $post_link;
    // // }
    // // $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
    // // return $post_link;
    // if($post->post_type == 'dc_coupon'){
    //   print $post_link;
    //   exit();
    // }
    // if($post->post_type == 'dc_coupon'){
    //   print $post_link;
    //   exit();
    // }
  }
  public function init() {
    // include field


    // add_filter('query_vars', array($this,'query_vars'));
    // add_rewrite_rule('find-dealer/([^/]+)/([^/]+)/([^/]+)/?','index.php?pagename=find-dealer&country=$matches[1]&state=$matches[2]&city=$matches[3]','top' );
    // add_rewrite_rule('find-dealer/([^/]+)/([^/]+)/?','index.php?pagename=find-dealer&country=$matches[1]&state=$matches[2]','top' );
    // add_rewrite_rule('find-dealer/([^/]+)/?','index.php?pagename=find-dealer&country=$matches[1]','top' );

		// Add automatic image sizes
		// if ( function_exists( 'add_image_size' ) ) {
		// 	add_image_size( 'gallery-thumb', 9999, 200, false );
		// 	add_image_size( 'gallery-bigimg', 800, 9999, false );
		// }

	}
  function include_field_types( $version = false ) {
    // support empty $version
		if( !$version ) $version = 4;
		// include
		require_once( DC_PLUGIN_DIR . 'includes/classes/class-acf-field.php');
	}


	public function install() {
    // for admin role
    $admins = get_role( 'administrator' );

    // coupon
    $admins->add_cap( 'edit_coupon' );
    $admins->add_cap( 'read_coupon' );
    $admins->add_cap( 'delete_coupon' );
    $admins->add_cap( 'edit_coupons' );
    $admins->add_cap( 'edit_others_coupons' );
    $admins->add_cap( 'publish_coupons' );
    $admins->add_cap( 'read_private_coupons' );
    $admins->add_cap( 'create_coupons' );
    // campaign
    $admins->add_cap( 'manage_campaigns' );

	}

  public function declare_types() {
  	require_once( DC_PLUGIN_DIR . 'includes/types/campaign.php');
    require_once( DC_PLUGIN_DIR . 'includes/types/coupon.php');
  }

	public function user_profile_loading_widgets() {
	}

	public function widget_register() {
	}

	public function loading_functions() {
		require_once( DC_PLUGIN_DIR . 'includes/functions.php');
	}

	public function loading_plugin() {

	}

	public function loading_script() {
	}

	public function declare_actions() {
	}

	public function declare_classes() {
		//require_once( DC_PLUGIN_DIR . 'includes/classes/class-bulk.php');
    require_once( DC_PLUGIN_DIR . 'includes/classes/class-coupons-import.php');
	}

	public function define_constants() {
		$this->define('DC_PLUGIN_URL', plugins_url('/', __FILE__)  );
		$this->define('DC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		$this->define('DC_TD', 'dc' );
	}

	private function define( $name, $value ) {
		if( $name && $value )
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
} $GLOBALS['wpdc'] = new DealersCoupons();

<?php
/*
Plugin Name: Dealers
Description: Dealers profile.
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;  // if direct access
}

class Dealers {

  public function __construct() {
    add_action( 'init', array( $this, 'init' ), 1 );
    $this->define_constants();
    $this->declare_classes();
    $this->declare_shortcodes();
    $this->declare_actions();
    $this->loading_functions();
    register_activation_hook( __FILE__, array( $this, 'install' ) );
    register_activation_hook( __FILE__, array( $this, 'add_cron' ) );
    add_action( 'daily_dealers_import', array( $this, 'daily_import_process' ) );
    add_filter( 'template_include', array( $this, 'include_template' ), 1 );
    add_filter( 'acf/fields/google_map/api', array( $this, 'my_acf_google_map_api' ) );
    add_action( 'admin_print_styles', array( $this, 'custom_admin_print_styles' ) );
    add_action( 'default_hidden_meta_boxes', array( $this, 'acme_remove_meta_boxes' ), 10, 2 );

    //add_action( 'admin_action_dt_dpp_post_as_draft', array($this,'dt_dpp_post_as_draft') );
    add_filter( 'post_row_actions', array($this,'dt_dpp_post_link'), 100, 2);
    add_filter( 'page_row_actions', array($this,'dt_dpp_post_link'), 100, 2);
  }

  /*Add link to action*/
  function dt_dpp_post_link( $actions, $post ) {
    if( in_array( 'rsm', $GLOBALS['userdata']->roles ) || in_array( 'dealer', $GLOBALS['userdata']->roles ) ){
      unset($actions['dpp']);
    }
    return $actions;
  }

  /**
   * Removes the category, author, post excerpt, and slug meta boxes.
   *
   * @since    1.0.0
   *
   * @param    array  $hidden    The array of meta boxes that should be hidden for Acme Post Types
   * @param    object $screen    The current screen object that's being displayed on the screen
   * @return   array    $hidden    The updated array that removes other meta boxes
   */
  function acme_remove_meta_boxes( $hidden, $screen ) {
    $user   = wp_get_current_user();
    if ( ( $screen->post_type == 'edls' || $screen->post_type == 'dc_coupon' ) && ( in_array( 'dealer', $user->roles ) || in_array( 'RSM', $user->roles ) ) ) {
      $hidden[] = 'sep_metabox_id';
    }
    return $hidden;
  }

  // hide elements on the dealer edit page
  function custom_admin_print_styles() {
    $screen = get_current_screen();
    $user   = wp_get_current_user();
    if ( ( $screen->post_type == 'edls' || $screen->post_type == 'dc_coupon' ) && ( in_array( 'dealer', $user->roles ) || in_array( 'RSM', $user->roles ) ) ) {
      echo '<style>
      #posts-filter .search-box,
      #posts-filter .tablenav,
      .subsubsub{ display: none; }
      .misc-pub-section.misc-pub-post-status,
      .misc-pub-section.misc-pub-visibility,
      #misc-publishing-actions #major-publishing-actions,
      .misc-publishing-actions, #edit-slug-box{ display: none; }
      </style>';
    }
  }

  public function my_acf_google_map_api( $api ) {
    $api['key'] = get_option( 'dealer_gma_key_web' );
    return $api;
  }

  public function add_cron() {
    if ( ! wp_next_scheduled( 'daily_dealers_import' ) ) {
      wp_schedule_event( time(), 'daily', 'daily_dealers_import' );
    }
  }

  function daily_import_process() {
    $importer = new DealerImporterSoap( 'Hotspring' );
    $importer->getAccounts();
    $importer->getDealerships();
    $importer->process();
  }

  function include_template( $template_path ) {
    if ( get_post_type() == 'edls' ) {
      if ( is_single() ) {
        if ( $theme_file = locate_template( array( 'single-edls.php' ) ) ) {
          $template_path = $theme_file;
        } else {
          $template_path = plugin_dir_path( __FILE__ ) . '/templates/single-edls.php';
        }
      }
    }
    return $template_path;
  }

  public function init() {
    $this->init_rsm_role();
  }

  function remove_quick_edit( $actions ) {
    unset( $actions['inline hide-if-no-js'] );
    return $actions;
  }

  public function init_rsm_role() {
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
    add_action( 'admin_bar_menu', array( $this, 'dealer_remove_toolbar_node' ), 999 );
    add_action( 'admin_menu', array( $this, 'dealer_remove_admin_menu_items' ), 999 );
    add_filter( 'map_meta_cap', array( $this, 'dealer_map_meta_cap' ), 10, 4 );
    if ( ! current_user_can( 'manage_options' ) ) {
      add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 1 );
    }
  }

  function dealer_remove_toolbar_node( $wp_admin_bar ) {
    if ( in_array( 'rsm', $GLOBALS['userdata']->roles ) || in_array( 'dealer', $GLOBALS['userdata']->roles ) ) {
      $wp_admin_bar->remove_node( 'comments' );
      $wp_admin_bar->remove_node( 'new-content' );
      $wp_admin_bar->remove_node( 'wpseo-menu' );
      $wp_admin_bar->remove_node( 'archive' );
      $wp_admin_bar->remove_node( 'updates' );
      $wp_admin_bar->remove_node( 'content' );

    }
  }
  function dealer_remove_admin_menu_items() {
    // $remove_menu_items = array('post-new.php','edit.php?post_type=page','edit.php','edit-comments.php','index.php','edit.php?post_type=herosections');
    $remove_other_items = array(
      'edit.php?post_type=edls',
      'profile.php',
      'edit.php?post_type=dc_coupon',
      'admin.php?action=dt_dpp_post_as_draft',
    // , 'update-core.php'
    );
    $current_user = wp_get_current_user();
    $current_user->roles;

    if ( in_array( 'rsm', $current_user->roles ) || in_array( 'dealer', $current_user->roles ) ) {
      global $menu;
      foreach ( $menu as $key => $value ) {
        if ( ! in_array( $value[2], $remove_other_items ) ) {
          unset( $menu[ $key ] );
        }
      }
    }
  }
  function dealer_user_can_edit( $user_id, $page_id ) {
    $page = get_post( $page_id );
    $user = get_user_by( 'ID', $user_id );
    if ( $page->post_type == 'edls' && in_array( 'dealer', $user->roles ) ) {
      $dealership_id     = get_post_meta( $page_id, 'dealership_id', true );
      $dealership_billto = get_post_meta( $page_id, 'dealership_billto', true );

      return _dealer_check_dealer_reference_at_user( $user_id, $dealership_id, $dealership_billto );
    }
    if ( $page->post_type == 'edls' && in_array( 'rsm', $user->roles ) ) {
      $rsm_id = get_post_meta( $page_id, 'dealership_rsm_id', true );

      return _dealer_check_rsm_reference_at_user( $user_id, $rsm_id );
    }
    return false;
  }
  function dealer_map_meta_cap( $caps, $cap, $user_id, $args ) {
    $pathComponents = explode( '?', trim( $_SERVER['REQUEST_URI'], '/' ) );
    // parse_str($pathComponents[1], $query);
    $pathComponents = explode( '/', trim( $pathComponents[0], '/' ) );

    $default = [ 'do_not_allow' ];
    if (
    (
    $pathComponents[0] == 'wp-admin'
    && (!empty($pathComponents[1]) && ( $pathComponents[1] == 'edit.php' || $pathComponents[1] == 'post-new.php' || $pathComponents[1] == 'post.php' ))
    && ( $query['post_type'] == 'edls' )
    )
    ) {
    }
    if (
    ( $_SERVER['REQUEST_URI'] == '/wp-admin/post-new.php'
      || $_SERVER['REQUEST_URI'] == '/wp-admin/post-new.php?post_type=herosections'
      || $_SERVER['REQUEST_URI'] == '/wp-admin/post-new.php?post_type=hottubs101'
      || $_SERVER['REQUEST_URI'] == '/wp-admin/post-new.php?post_type=wellness'
      || $_SERVER['REQUEST_URI'] == '/wp-admin/post-new.php?post_type=faq'
      || $_SERVER['REQUEST_URI'] == '/wp-admin/post-new.php?post_type=video'
      || $_SERVER['REQUEST_URI'] == '/wp-admin/post-new.php?post_type=ae_global_templates'
      || $_SERVER['REQUEST_URI'] == '/wp-admin/edit-comments.php'
    )
     && ( in_array( 'rsm', $GLOBALS['userdata']->roles ) || in_array( 'dealer', $GLOBALS['userdata']->roles ) )
    ) {
      return [ 'do_not_allow' ];
    }

    $to_filter = [
      'edit_post', // I can't changed this, see "post.php" file for more detales
      'edit_dealer',
      'edit_others_dealers',
      'edit_published_dealers',
    ];

    // If the capability being filtered isn't of our interest, just return current value
    if ( ! in_array( $cap, $to_filter, true ) ) {
      return $caps;
    }

    if ( ! empty( $args[0] ) && $this->dealer_user_can_edit( $user_id, $args[0] ) ) {
      return [ 'exist' ];
    }
    if ( $cap == 'edit_others_dealers' ) {
      return [ 'edit_posts' ];
    }

    // redirect after submit, see "post.php" file for more detales
    if ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost'
    && isset( $_POST['user_ID'] )
    && isset( $_POST['post_ID'] )
    && $this->dealer_user_can_edit( $_POST['user_ID'], $_POST['post_ID'] )
    ) {
      return [ 'exist' ];
    }
    // return [ 'exist' ];
    // Use deafult access
    // User is not allowed, let's tell that to WP
    // return [ 'do_not_allow' ];
    return $caps;
  }

  public function install() {
    add_role(
      'dealer', 'Dealer', array(
        'read'          => true,
        'edit_posts'    => true,
        'delete_posts'  => false,
        'publish_posts' => false,
        'upload_files'  => false,
        'edit_dealers'  => true,
        'read_dealer'   => true,
      )
    );
    add_role(
      'internal', 'Internal', array(
        'read'          => true,
        'edit_posts'    => false,
        'delete_posts'  => false,
        'publish_posts' => false,
        'upload_files'  => false,
      )
    );
    add_role(
      'rsm', 'RSM', array(
        'read'          => true,
        'edit_posts'    => true,
        'delete_posts'  => false,
        'publish_posts' => false,
        'upload_files'  => false,
        'edit_dealers'  => true,
        'read_dealer'   => true,
      )
    );

    // @TODO Add defalt cap. for other roles
    // for admin role
    $admins = get_role( 'administrator' );
    $admins->add_cap( 'import_dealers' );
    $admins->add_cap( 'edit_dealers' );
    $admins->add_cap( 'edit_others_dealers' );
    $admins->add_cap( 'publish_dealers' );
    $admins->add_cap( 'read_private_dealers' );
    $admins->add_cap( 'read_dealer' );
    $admins->add_cap( 'delete_dealers' );
    $admins->add_cap( 'delete_private_dealers' );
    $admins->add_cap( 'delete_published_dealers' );
    $admins->add_cap( 'edit_private_dealers' );
    $admins->add_cap( 'edit_published_dealers' );
    $admins->add_cap( 'create_dealers' );
    $admins->add_cap( 'delete_others_dealers' );

    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $table_name = $wpdb->prefix . 'WPD_sessions';
    $collate    = '';
    if ( $wpdb->has_cap( 'collation' ) ) {
      $collate = $wpdb->get_charset_collate();
    }

    $tables = "CREATE TABLE {$table_name} (
      session_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      session_key char(32) NOT NULL,
      session_value longtext NOT NULL,
      session_expiry BIGINT UNSIGNED NOT NULL,
      PRIMARY KEY  (session_key),
      UNIQUE KEY session_id (session_id)
      ) $collate;";

    dbDelta( $tables );

  }

  public function declare_shortcodes() {
    require_once WPD_PLUGIN_DIR . 'includes/shortcodes/class-shortcode.php';
  }

  public function user_profile_loading_widgets() {
  }

  public function widget_register() {
  }

  public function loading_functions() {
    require_once WPD_PLUGIN_DIR . 'includes/functions.php';
    // $this->get = new class_dp_functions();
  }

  public function loading_plugin() {
  }

  public function loading_script() {
  }

  public function declare_actions() {
    require_once WPD_PLUGIN_DIR . 'includes/classes/class-actions.php';
  }

  public function declare_classes() {
    if ( class_exists( 'SoapClient' ) ) {
      require_once WPD_PLUGIN_DIR . 'includes/assets/WSSoapClient.class.php';
    }

    require_once WPD_PLUGIN_DIR . 'includes/assets/wp-async-request.php';
    require_once WPD_PLUGIN_DIR . 'includes/assets/wp-background-process.php';
    require_once WPD_PLUGIN_DIR . 'includes/classes/class-dealer-import.php';
    require_once WPD_PLUGIN_DIR . 'includes/classes/class-functions.php';
    require_once WPD_PLUGIN_DIR . 'includes/classes/class-import.php';
    require_once WPD_PLUGIN_DIR . 'includes/classes/class-exclude-from-search.php';
    // require_once( WPD_PLUGIN_DIR . 'includes/classes/class-settings.php' );
    require_once WPD_PLUGIN_DIR . 'includes/menus/settings.php';
    // require_once( WPD_PLUGIN_DIR . 'includes/menus/import.php' );
    // require_once( WPD_PLUGIN_DIR . 'includes/menus/import-csv.php' );
    require_once WPD_PLUGIN_DIR . 'includes/types/dealer.php';

  }

  public function define_constants() {
    $this->define( 'WPD_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
    $this->define( 'WPD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    $this->define( 'WPD_TEXTDOMAIN', 'dealers' );

    $this->define( 'DEALE_RDIS_NAME', 'rdi.services@reddoor.biz' );
    $this->define( 'DEALE_RDIS_PASS', 'R3dd00r1ntEr@ct1v3' );
    $this->define( 'DEALE_RDIS_WSDL', 'https://rdiservices.watkinsmfg.com/RDIService.svc?wsdl' );
    $this->define( 'DEALE_RDIS_LOCATION', 'https://rdiservices.watkinsmfg.com/RDIService.svc' );
    $this->define( 'DEALE_RDIS_PASS', 'AIzaSyBG13OEK8JGojY11IT7EumkNIJIzw-P4-Y' );
  }

  private function define( $name, $value ) {
    if ( $name && $value && ! defined( $name ) ) {
      define( $name, $value );
    }
  }

}
$GLOBALS['wpd'] = new Dealers();

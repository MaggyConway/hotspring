<?php
/*
Plugin Name: Gravity Forms GT Add-On
Plugin URI: http://www.gravityforms.com
Description: Simple addon to add GT Event fields.
Version: 0.1
*/

if ( ! class_exists( 'WPGT' ) ) {
  class WPGT {
    /**
     * Class constructor
     */
    function __construct() {
      add_action( 'init', [ $this, 'init' ] );
      add_action( 'wp_footer', [ $this, 'print_inline_script' ], 100 );
      add_action( 'pre_get_posts', [ $this, 'search_filter' ] );
    }
    function init() {
      $GLOBALS['masterCustomerKey'] = $this->getMasterCustomerKey();
    }

    // Customer Intelligence Key creation (mid CI solution)
    function getMasterCustomerKey() {
      $two_years = time() + 60 * 60 * 24 * 30 * 12 * 2;
      if ( ! isset( $_COOKIE['masterCustomerKey'] ) ) {  // if the masterCustomerKey is not yet set
        $masterCustomerKey = uniqid( '', true );      // creates a unique key of 23 characters (it comes with a period in it)
        $masterCustomerKey = trim( str_replace( '.', '', $masterCustomerKey ) );    // strip out the period
        setcookie( 'masterCustomerKey', $masterCustomerKey, strtotime( '+730 days' ), COOKIEPATH, COOKIE_DOMAIN );
        $_COOKIE['masterCustomerKey'] = $masterCustomerKey;
        return htmlspecialchars( $masterCustomerKey );
      }
      return htmlspecialchars( $_COOKIE['masterCustomerKey'] );
    }

    function print_inline_script() {
      global $dataLayer;
      $dealer_name = 'not found';
      if ( isset( $_COOKIE['dealer_did'] ) && ! empty( $_COOKIE['dealer_did'] ) ) {
        $dealer      = array_pop( _dealer_get_posts_by_dealership_id( htmlspecialchars( $_COOKIE['dealer_did'] ) ) );
        $dealer_name = $dealer->post_title;
      }

      $params[]['Dealer_AutoLocation'] = $dealer_name;
      $params[]['hotspring_id'] = $this->getMasterCustomerKey();
      if ( ! empty( $dataLayer ) ) {
        //$params[] = $dataLayer;
        $params = array_merge( $params, $dataLayer );
      }
      if( empty($params) ){
        return;
      }
      print '
      <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];';
      foreach ($params as $key => $param) {
        if(empty($param)){
          continue;
        }
        print 'window.dataLayer.push(' . json_encode( $param ) . ');';
      }
      print '</script>';
    }

    /**
     * alter the search code request.
     *
     * @since 1.0.0
     * @return void
     */
    public function search_filter( WP_Query $query ) {
      if ( ! is_admin() && $query->is_main_query() ) {
        if ( $query->is_search ) {
          if ( isset( $query->query_vars['s'] ) ) {
            $query->query_vars['s'] = trim( $query->query_vars['s'] );
            global $dataLayer;
            $current_page = max( 1, get_query_var( 'paged' ) );
            // @TODO Security check
            $allsearch     = new WP_Query( 's=' . urlencode( htmlspecialchars( $query->query_vars['s'] ) ) . '&showposts=-1' );
            $total_results = $allsearch->found_posts;
            $dataLayer[] = [
              'Search_Term' => $query->query_vars['s'],
              'Search_Results' => $total_results,
              'Search_Page' => $current_page
            ];
          }
        }
      }
    }
  }
  new WPGT();
}

add_action( 'gform_loaded', array( 'GF_GT_Bootstrap', 'load' ), 5 );
class GF_GT_Bootstrap {
  public static function load() {
    if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
      return;
    }
    require_once 'class-gf-gt.php';
    GFAddOn::register( 'GFGT' );
  }
}

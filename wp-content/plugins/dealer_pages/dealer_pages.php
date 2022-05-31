<?php
/*
Plugin Name: Dealers Pages
Description: Socialize your user profile.
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

class DealersPages{

	public function __construct(){
    add_action( 'init', array( $this, 'init' ), 1 );
		$this->define_constants();
		$this->declare_classes();
		$this->declare_shortcodes();
		$this->declare_actions();
		$this->loading_functions();

		register_activation_hook( __FILE__, array( $this, 'install' ) );
	}

  public function init() {
    add_filter('query_vars', array($this,'query_vars'));
    add_rewrite_rule('hot-tub-dealers/(USA|Canada)/([^/]+)/([^/]+)/?','index.php?pagename=hot-tub-dealers&country=$matches[1]&state=$matches[2]&city=$matches[3]','top' );
    add_rewrite_rule('hot-tub-dealers/(USA|Canada)/([^/]+)/?','index.php?pagename=hot-tub-dealers&country=$matches[1]&state=$matches[2]','top' );
		add_rewrite_rule('hot-tub-dealers/(USA|Canada)/?','index.php?pagename=hot-tub-dealers&country=$matches[1]','top' );
		//add_rewrite_rule('hot-tub-dealers/([^/]+)/?','index.php?pagename=hot-tub-dealers&state=$matches[1]','top' );
  }

	public function query_vars($public_query_vars) {
		$public_query_vars[] = "country";
    $public_query_vars[] = "state";
		$public_query_vars[] = "city";
    return $public_query_vars;
	}
	public function install() {
	}
  public function declare_shortcodes() {
  	require_once( DP_PLUGIN_DIR . 'includes/shortcodes/class-shortcode.php');
  }

	public function user_profile_loading_widgets() {

	}

	public function widget_register() {

	}

	public function loading_functions() {
		require_once( DP_PLUGIN_DIR . 'includes/functions.php');
    $this->get = new class_dp_functions();
	}

	public function loading_plugin() {

	}

	public function loading_script() {

	}

	public function declare_actions() {
    require_once( DP_PLUGIN_DIR . 'includes/classes/class-actions.php');
	}

	public function declare_classes() {
		require_once( DP_PLUGIN_DIR . 'includes/classes/class-functions.php');
		require_once( DP_PLUGIN_DIR . 'includes/classes/class-settings.php');
	}

	public function define_constants() {

		$this->define('DP_PLUGIN_URL', plugins_url('/', __FILE__)  );
		$this->define('DP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		$this->define('DP_TEXTDOMAIN', 'dealer-pages' );
	}

	private function define( $name, $value ) {
		if( $name && $value )
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

} $GLOBALS['wpdp'] = new DealersPages();

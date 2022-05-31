<?php
if(!defined('ABSPATH'))
  exit; // Exit if accessed directly

/*
Plugin Name: My Devel
Description: this is helper functions for developers
*/

if(!class_exists('MDCommon')) {

  class MDCommon {
    /**
     * Class constructor
     */
    function __construct() {

      $this->define_constants();
      require( WPMD_PLUGIN_DIR . '/vendor/autoload.php' );
      Kint::$aliases[] = 'dpm';
      $this->includes();
      krumo::$skin = 'blue';

      MDAddOn::init_addons();

      // add_action( 'admin_notices', array( $this, 'update_nag' ), 1 );
      //add_action( 'admin_init', array( $this, 'admin_init' ), 1 );
    }


    public function admin_init() {
  		// add_action( 'admin_notices', array( $this, 'remove_nag' ), 1 );
  		//add_action( 'admin_notices', array( $this, 'print_messages' ), 1 );
	  }

    //add message to queue
    public static function addMessage($arg) {
  		if ( ! session_id() ) @ session_start();
      //if ( ! isset($_SESSION['devel']) $_SESSION['devel'] = array();
      $_SESSION['devel']=array();
  		$_SESSION['devel'][] = $arg;
    }

    //get array of messages
    public function getMessages() {
      if ( ! session_id() ) @ session_start();
  		// add_action( 'admin_notices', array( $this, 'remove_nag' ), 1 );
  		add_action( 'admin_notices', array( $this, 'print_messages' ), 1 );
	  }

    //get array of messages
    public static function removeMessage($key) {
  		if ( ! session_id() ) @ session_start();
      unset($_SESSION['devel'][$key]);
	  }

    public static function remove_nag() {
		  remove_action( 'admin_notices', 'update_nag', 3);
    }

    public function print_message($key, $arg) {
      echo "<div class='devel-message'>";
      // krumo($this);
      // Kint::dump($this);
      print $arg;
      echo "</div>";
    }
    public function print_messages() {
      if ( ! session_id() ) @ session_start();
      //d($_SESSION['devel']);
      if(isset($_SESSION['devel'])){
        foreach ($_SESSION['devel'] as $key => $value) {
          $this->print_message($key, $value);
          $this->removeMessage($key);
        }
      }
    }


    /**
     * Setup plugin constants.
     *
     * @since 1.0.0
     * @return void
     */
    public function define_constants() {
      define('WPMD_PLUGIN_DIR', plugin_dir_path(__FILE__));
      define('WPMD_PLUGIN_URL', plugins_url('/', __FILE__)  );
    }

    /**
     * Include the required files.
     *
     * @since 1.0.0
     * @return void
     */
    public function includes() {
      require_once( WPMD_PLUGIN_DIR . 'inc/functions.php' );
      require_once( WPMD_PLUGIN_DIR . 'inc/menus/settings.php' );
      require_once( WPMD_PLUGIN_DIR . 'inc/addons.php' );
      require_once( WPMD_PLUGIN_DIR . 'inc/menus/addons.php' );
      require_once( WPMD_PLUGIN_DIR . 'inc/menus/php.php' );
      $this->include_addons();
      $this->register_addons();

    }
    /**
     * Include the required Addons.
     *
     * @since 1.0.0
     * @return void
     */
    public function include_addons() {
      require_once( WPMD_PLUGIN_DIR . 'addons/php/php.php' );
      require_once( WPMD_PLUGIN_DIR . 'addons/watchdog/watchdog.php' );
    }

    /**
     * Regester the required Addons.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_addons() {
      MDAddOn::register( 'MDPhpAddon' );
      MDAddOn::register( 'MDWatchdogAddon' );
    }
  }
  new MDCommon();
}
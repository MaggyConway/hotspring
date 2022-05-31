<?php

/**
 * Admin class
 */
if(!defined('ABSPATH')) {
  exit;
}

if(!class_exists('WPDZ_Admin')) {

  /**
   * Handle the backend of the store locator
   *
   * @since 1.0.0
   */
  class WPDZ_Admin {

    /**
     * @since 2.0.0
     * @var WPDZ_Settings
     */
    public $settings_page;

    /**
     * Class constructor
     */
    function __construct() {
      $this->includes();
      add_action('init', array($this, 'init'));
    }

    /**
     * Include the required files.
     *
     * @since 2.0.0
     * @return void
     */
    public function includes() {
      require_once( dirname(__FILE__) . '/class-settings.php' );
    }

    /**
     * Init the classes.
     *
     */
    public function init() {
      $this->settings_page = new WPDZ_Settings();
    }

  }

  $GLOBALS['wpdz_admin'] = new WPDZ_Admin();
}

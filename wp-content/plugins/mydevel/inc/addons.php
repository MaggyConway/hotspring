<?php
abstract class MDAddOn {

  private static $_registered_addons = array( 'active' => array(), 'inactive' => array() );

  /**
   * Class constructor which hooks the instance into the WordPress init action
   */
  function __construct() {
    add_action( 'init', array( $this, 'init' ) );
    $this->pre_init();

  }

  /**
   * Registers an addon so that it gets initialized appropriately
   *
   * @param string $class - The class name
   * @param string $overrides - Specify the class to replace/override
   */
  public static function register( $class, $overrides = null ) {
    //Ignore classes that have been marked as inactive
    if ( in_array( $class, self::$_registered_addons['inactive'] ) ) {
      return;
    }

    //Mark classes as active. Override existing active classes if they are supposed to be overridden
    $index = array_search( $overrides, self::$_registered_addons['active'] );
    if ( $index !== false ) {
      self::$_registered_addons['active'][ $index ] = $class;
    } else {
      self::$_registered_addons['active'][] = $class;
    }

    //Mark overridden classes as inactive.
    if ( ! empty( $overrides ) ) {
      self::$_registered_addons['inactive'][] = $overrides;
    }

  }

  /**
   * Gets all active, registered Add-Ons.
   *
   * @since  Unknown
   * @access public
   *
   * @uses MDAddOn::$_registered_addons
   *
   * @return array Active, registered Add-Ons.
   */
  public static function get_registered_addons() {
    return self::$_registered_addons['active'];
  }

  /**
   * Initializes all addons.
   */
  public static function init_addons() {

    //Removing duplicate add-ons
    $active_addons = array_unique( self::$_registered_addons['active'] );

    foreach ( $active_addons as $addon ) {
      call_user_func( array( $addon, 'get_instance' ) );
    }

  }

  /**
   * Gets executed before all init functions. Override this function to perform
   * initialization tasks that must be done prior to init
   */
  public function pre_init() {

  }

  /**
   * Plugin starting point. Handles hooks and loading of language files.
   */
  public function init() {

  }
}

<?php
/**
 * Handle data for the current user session
 *
 * @class       WPD_Session
 * @version     1.0.0
 * @category    Abstract Class
 */
abstract class WPD_Session {

    /** @var int $_customer_id */
    protected $_id;

    /** @var array $_data  */
    protected $_data = array();

    /** @var bool $_dirty When something changes */
    protected $_dirty = false;

    /**
     * __get function.
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key ) {
        return $this->get( $key );
    }

    /**
     * __set function.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set( $key, $value ) {
        $this->set( $key, $value );
    }

     /**
      * __isset function.
      *
      * @param mixed $key
      * @return bool
      */
    public function __isset( $key ) {
        return isset( $this->_data[ sanitize_title( $key ) ] );
    }

    /**
     * __unset function.
     *
     * @param mixed $key
     */
    public function __unset( $key ) {
        if ( isset( $this->_data[ $key ] ) ) {
            unset( $this->_data[ $key ] );
            $this->_dirty = true;
        }
    }

    /**
     * Get a session variable.
     *
     * @param string $key
     * @param  mixed $default used if the session variable isn't set
     * @return array|string value of session variable
     */
    public function get( $key, $default = null ) {
        $key = sanitize_key( $key );
        return isset( $this->_data[ $key ] ) ? maybe_unserialize( $this->_data[ $key ] ) : $default;
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set( $key, $value ) {
        if ( $value !== $this->get( $key ) ) {
            $this->_data[ sanitize_key( $key ) ] = maybe_serialize( $value );
            $this->_dirty = true;
        }
    }

    /**
     * get_customer_id function.
     *
     * @access public
     * @return int
     */
    public function get_id() {
        return $this->_id;
    }
}


class WPD_Session_Handler extends WPD_Session {
  protected $_cash_group = 'dcs';
  protected $_table = '';
  /**
   * Constructor for the session class.
   */
  public function __construct() {
    global $wpdb;
    $this->_table =  $wpdb->prefix . 'WPD_sessions';
    $this->_session_expiration = time() + intval( 60 * 60 * 1 ); // 1 Hour
  }

  /**
   * Returns the session.
   *
   * @param string $key
   * @param mixed $default
   * @return string|array
   */
  public function get_data( $key, $default = false ) {
    global $wpdb;
    $value = wp_cache_get( $key, $this->_cash_group, false);

    if ( false === $value ) {
      $value = $wpdb->get_var( $wpdb->prepare( "SELECT session_value FROM $this->_table WHERE session_key = %s", $key ) );

      if ( is_null( $value ) ) {
          $value = $default;
      }

      wp_cache_add( $key, $value, $this->_cash_group, $this->_session_expiration - time() );
    }

    return maybe_unserialize( $value );
  }

  /**
   * add the session.
   *
   * @param string $user_id
   * @return string|array
   */
  public function set_data( $key, $data ) {
    global $wpdb;
    $wpdb->replace(
        $this->_table,
        array(
            'session_key' => $key,
            'session_value' => maybe_serialize( $data ),
            'session_expiry' => $this->_session_expiration,
        ),
        array(
            '%s',
            '%s',
            '%d',
        )
    );

    // Set cache
    wp_cache_set( $key, $data, $this->_cash_group, $this->_session_expiration - time() );
  }

  /**
   * delete the session.
   *
   * @param string $user_id
   */
  public function delete_data( $key ) {
    global $wpdb;
    $wpdb->query( $wpdb->prepare( "DELETE FROM $this->_table WHERE session_key = %s", $key) );
    wp_cache_delete( $key, $this->_cash_group );
  }
}

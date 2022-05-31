<?php
/**
 * Plugin Name: Components Kit
 * Description: Components Kit Plugin.
 * Version:     0.0.1
 * Author:      Bob Newman
 * Author URI:  https://www.bigtunainteractive.com
 * Text Domain: hotspring-lang
 * Elementor tested up to: 3.5.0
 * Elementor Pro tested up to: 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Components Kit Elementor Class
 *
 * @since 0.0.1
 */
class ComponentsKit {

	/**
	 * Plugin Version
	 *
	 * @since 0.0.1
	 * @var string The plugin version.
	 */
	const VERSION = '0.0.1';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 0.0.1
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 0.0.1
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();

		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function define_constants() {
		if ( ! defined( 'CK_PLUGIN_DIR' ) ) {
			define( 'CK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
	}

	/**
	 * Includes
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function includes() {
		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'elementor.php' );
		require_once( 'function.php' );
	}

	/**
	 * Initialize the plugin
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function init() {


	}

}

// Instantiate ComponentsKitElementor.
new ComponentsKit();

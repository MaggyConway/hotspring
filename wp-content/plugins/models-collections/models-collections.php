<?php
/**
 * Plugin Name:  Models and Collections
 * Description:  Post type for models and collections
 * Version:      0.0.1
 * Author:       Bob Newman
 * Author URI:   https://www.bigtunainteractive.com/
 * License:      GPLv2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  models-collections
 *
 * @package models-collections
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'modelsCollections' ) ) {


    /**
     * modelsCollections Class
     */
    class modelsCollections {

        function __construct() {
            $this->defineConstants();
            $this->includes();
            $this->defineShortcodes();
            $this->defineTypes();
            add_action( 'init', array( $this, 'init' ), 1 );
        }

        /**
         * Setup plugin constants.
         *
         * @since 0.0.1
         * @return void
         */
        public function defineConstants() {
            if ( !defined('MC_PLUGIN_DIR') ) {
                define( 'MC_PLUGIN_URL', plugins_url('/', __FILE__) );
                define( 'MC_PLUGIN_DIR', plugin_dir_path(__FILE__) );
            }
        }

        /**
         * Base plugin includes.
         *
         * @since 0.0.1
         * @return void
         */
        function includes(){
            require_once MC_PLUGIN_DIR . 'includes/functions.php';
        }

        /**
         * Base plugin init.
         *
         * @since 0.0.1
         * @return void
         */
        public function init() {

        }

        function defineShortcodes(){
            //add_shortcode( 'main-menu', [$this,'mainMenuShortcode'] );
        }
        function defineTypes(){
            require_once MC_PLUGIN_DIR . 'includes/types/collection.php';
            require_once MC_PLUGIN_DIR . 'includes/types/model.php';
        }

    }
    new modelsCollections();
}

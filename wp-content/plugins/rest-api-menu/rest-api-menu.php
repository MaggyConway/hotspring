<?php
/**
 * Plugin Name:  rest-api-menu
 * Description:  Rest API Main Menu data source
 * Version:      0.0.1
 * Author:       Bob Newman
 * Author URI:   https://www.bigtunainteractive.com/
 * License:      GPLv2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  rest-api-menu
 *
 * @package rest-api-menu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'restApiMenu' ) ) {


    /**
     * restApiMenu Class
     */
    class restApiMenu {
        var $itemBD = [];
        var $itemSrc = [];
        var $mainMenu = [];

        /**
         * Construct
         *
         * @since 0.0.1
         * @return void
         */
        function __construct() {
            $this->defineConstants();
            $this->includes();
            $this->defineShortcodes();

            add_action( 'init', [$this, 'init'], 1 );
        }

        /**
         * Define shortcodes
         *
         * @since 0.0.1
         * @return void
         */
        function defineShortcodes(){
            add_shortcode( 'main-menu', [$this,'mainMenuShortcode'] );
            add_shortcode( 'additional-menu', [$this,'additionalMenuShortcode'] );

        }

        /**
         * Main menu shortcode
         *
         * @since 0.0.1
         * @return void
         */
        function mainMenuShortcode() {
            $this->initJson();
            $result = '<ul class="main-menu">';
            foreach ( $this->mainMenu['mainMenuItems'] as $item_id => $item ) {
                $result .= wpram_get_template( 'item.php', $item );
            }
            $result .= '</ul>';
            return $result;
        }

        /**
         * Additional menu shortcode
         *
         * @since 0.0.1
         * @return void
         */
        function additionalMenuShortcode() {
            $this->initJson();
            $lang = '';
            $result = '<ul class="additional-menu">';
            foreach ( $this->mainMenu['aditionalItems'] as $item_id => $item ) {
                if (is_array($item)){
                    $result .= wpram_get_template( 'item.php', $item );
                } else {
                    $result .='<li class="menu__item"><a href="'.$item.'">'.$item_id.'</a></li>';
                }
            }

            $result .= '</ul>';
            // $result .= '<ul class="additional-menu">';
            // // foreach ( $this->mainMenu['aditionalItems'] as $item_id => $item ) {
            // //     $result .= wpram_get_template( 'item.php', $item );
            // // }
            // foreach ($this->mainMenu['languageItems'] as $key => $link) {
            //     $lang .= '<li><a href="'.$link.'">'.$key.'</a></li>';
            // }
            // $result .= '
            // <li>Language
            // <ul>'.$lang.'</ul>
            // </li>
            // ';
            // $result .= '</ul>';
            return $result;
        }

        /**
         * Includes other files
         *
         * @since 0.0.1
         * @return void
         */
        function includes(){
            require_once WPRAM_PLUGIN_DIR . 'includes/functions.php';
        }

        /**
         * Setup plugin constants.
         *
         * @since 0.0.1
         * @return void
         */
        public function defineConstants() {
            if ( !defined('WPRAM_PLUGIN_DIR') ) {
                define( 'WPRAM_PLUGIN_URL', plugins_url('/', __FILE__) );
                define( 'WPRAM_PLUGIN_DIR', plugin_dir_path(__FILE__) );
            }
        }

        /**
         * Plugin init.
         *
         * @since 0.0.1
         * @return void
         */
        public function init() {
            add_action( 'rest_api_init', [$this, 'restApiInit'] );
            // add_action( 'rest_api_init', function () {
            //   register_rest_route( 'fetch/v1', '/mainmenu', array(
            //     'methods' => 'GET',
            //     'callback' => [$this, 'rest_get_main_email'],
            //         ));
            // });
        }

        /**
         * Rest API init.
         *
         * @since 0.0.1
         * @return void
         */
        public function restApiInit() {
            register_rest_route( 'fetch/v1', '/mainmenu', [
                'methods' => 'GET',
                'callback' => [$this, 'getMainMenuCallback'],
            ]);
            register_rest_route( 'fetch/v1', '/mainmenu/(?P<slug>[\w-]+)', [
                'methods' => 'GET',
                'callback' => [$this, 'getSinglItemMainMenuCallback'],
            ]);
            //this if you want to load paraments like /mainmenu-slug/menu1/menu1-childe1/menu1-childe1/menu1-childe1-childe2
            // register_rest_route( 'fetch/v1', '/mainmenu-slug/(?P<param_name>.+)', [
            //     'methods' => 'GET',
            //     'callback' => [$this, 'getMainMenuCallbackTest'],
            // ]);
        }

        public function getRenderItem( $item_key, $item, $level ) {
            if (!is_array($item)){
                return $item;
            }
            $item['level'] = $level;
            // render other links
            if ( !empty( $item['links'] ) ) {
                foreach ($item['links'] as $key => $value) {
                    $item['links'][$key] = $this->getRenderItem( $key, $value, $level+1 );
                }
            }
            // render a product
            if ( !empty( $item['product_id'] ) ) {
                // @todo fill data from real product data
                $item['model_data'] = getMenuModelDataById( $item['product_id'] );
                if ( empty($item['link']) ) {
                    $item['link'] = $item['model_data']['link'];
                }
                $item['content'] = wpram_get_template( 'product.php', $item );
            }
            // render a collection
            if ( !empty( $item['collection_id'] ) ) {

                // @todo fill data from real product data
                $item['collection_data'] = getMenuCollectionDataById( $item['collection_id'] );
                if ( empty($item['link']) ) {
                    $item['link'] = $item['collection_data']['link'];
                }
                $item['content'] = wpram_get_template( 'collection.php', $item );
            }
            if ( !empty( $item['title'] ) ) {
                $this->itemBD[$item_key] = $item;
            }

            return $item;
        }

        /**
         * Load json data.
         *
         * @since 0.0.1
         * @api v1
         * @return void
         */
        public function initJson() {
            if ( !empty($this->mainMenu) ) {
                return $this->mainMenu;
            }
            $mainMenuJson = file_get_contents(WPRAM_PLUGIN_DIR . '/data/mainMenu.json', true);
            $mainMenu = json_decode($mainMenuJson, true);
            $this->itemSrc = $mainMenu;
            foreach ($mainMenu['mainMenuItems'] as $key => $value) {
                $mainMenu['mainMenuItems'][$key] = $this->getRenderItem( $key, $value, 1 );
            }
            $this->mainMenu = $mainMenu;
        }

        /**
         * Gat the data of the main menu .
         *
         * @since 0.0.1
         * @api v1
         * @return void
         */
        public function getMainMenuData() {
            if ( empty($this->mainMenu) ) {
                $this->initJson();
                return ( !empty($this->mainMenu) ) ? $this->mainMenu : FALSE;
            }
            return $this->mainMenu;
        }

        /**
         * Rest API - mainmenu callback.
         *
         * @since 0.0.1
         * @api v1
         * @return void
         */
        public function getMainMenuCallback() {
            $this->initJson();
            $menuData = $this->getMainMenuData();
            if ( empty($menuData) ) {
                wp_send_json_error();
            } else {
                wp_send_json_success($menuData);
            }
        }

        public function getSinglItemMainMenuCallback(WP_REST_Request $request) {
            $this->initJson();
            $params = $request->get_params();
            if ( empty( $this->itemBD[$params['slug']] ) ) {
                wp_send_json_error();
            } else {
                wp_send_json_success( $this->itemBD[$params['slug']] );
            }
        }

        public function getMainMenuCallbackTest(WP_REST_Request $request) {
            print_r($request);
            // $this->itemBD
            exit();
            // $mainMenuJson = file_get_contents(WPRAM_PLUGIN_DIR . '/data/mainMenu.json', true);
            // $mainMenu = json_decode($mainMenuJson, true);
            // print_r($mainMenu);
            // foreach ($mainMenu['mainMenuItems'] as $key => $value) {
            //     $this->getRenderItem( $key, $value );
            // }
            // exit();
            // if ( !empty($main_email) ) {
            //     wp_send_json_error();
            // } else {
            //     wp_send_json_success(json_decode($mainMenuJson));
            // }
        }

    }
    new restApiMenu();
}

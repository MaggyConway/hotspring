<?php
/*
Plugin Name: Gravity Forms Google Address Add-On
Plugin URI: http://www.gravityforms.com
Description: Simple addon to hide the address fields and display field of address in one line.
Version: 0.1

*/

define( 'GF_GOOGLE_ADDESS_VERSION', '0.1' );

add_action( 'gform_loaded', array( 'GF_Google_Adress_Bootstrap', 'load' ), 5 );

class GF_Google_Adress_Bootstrap {

    public static function load() {

        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-gf-gaddress.php' );

        GFAddOn::register( 'GFGAddress' );
    }

}

function gf_google_address_addon() {
    return GFSimpleAddOn::get_instance();
}

// REMOVE EMOJI ICONS (emoji slows down the performance of site)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

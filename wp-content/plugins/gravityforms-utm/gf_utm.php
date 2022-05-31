<?php
/*
Plugin Name: Gravity Forms UTM Add-On
Plugin URI: http://www.gravityforms.com
Description: Simple addon to add a hidden fields.
Version: 0.1
*/

add_action( 'gform_loaded', array( 'GF_UTM_Bootstrap', 'load' ), 5 );
class GF_UTM_Bootstrap {
  public static function load() {
    if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
      return;
    }
    require_once( 'class-gf-utm.php' );
    GFAddOn::register( 'GFUTM' );
  }
}
<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_dp_shortcode{

  public function __construct(){
		add_shortcode( 'list-of-states-and-city', array( $this, 'display' ) );
  }

  public function display($atts, $content = null ) {
		$atts = shortcode_atts( array(), $atts);
		ob_start();
		include( DP_PLUGIN_DIR . 'templates/dealers-pages/list-of-states-and-city.php');
		return ob_get_clean();
  }

}

new class_dp_shortcode();

<?php
/*
Plugin Name: Gravity Forms Mini Salesforce Integrates
Description: Integrates Gravity Forms with Salesforce
Version: 0.0.1
Text Domain: gfMiniSalesforce
*/

define( 'GF_MINI_SALESFORCE_VERSION', '0.0.0' );

add_action( 'gform_loaded', array( 'GF_Mini_Salesforce_Bootstrap', 'load' ), 5 );

/**
 * Tells GravityForms to load up the Add-On
 */
class GF_Mini_Salesforce_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		if (!class_exists('MiniSalesforceAPI')) {
			require_once( 'MiniSalesforceAPI.php' );
		}

		require_once( 'class-gf-mini-salesforce.php' );

		GFAddOn::register( 'GFMiniSalesforce' );
	}
}

function gf_mini_salesforce() {
	return GFMiniSalesforce::get_instance();
}

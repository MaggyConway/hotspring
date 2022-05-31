<?php

/**
 * Handle the plugin settings.
 *
 */
if(!defined('ABSPATH')) {
  exit;
}

if(!class_exists('WPDZ_Settings')) {

  class WPDZ_Settings {

    public function __construct() {
      // add_action('admin_init', array( $this, 'register_settings' ));
      // add_action('admin_menu', array( $this, 'menu_items' ));
    }

//    public function menu_items() {
//
//      // add_options_page('SalesForce Options', 'SalesForce Options', 'manage_options', 'salesforce-options', array( $this, 'salesforce_options_page' ));
//
//
//      add_options_page(
//          'Salesforce Options', 'Salesforce Options', 'manage_options', 'salesforce-options', array($this, 'salesforce_options_page')
//      );
//    }
//
//    public function salesforce_options_page() {
//      settings_fields('header_section');
//      settings_fields('salesforce_section');
//      do_settings_sections('salesforce-options');
//
//      submit_button();
//    }
//
//    /**
//     * Register the settings.
//     *
//     * @since 2.0.0
//     * @return void
//     */
//    public function register_settings() {
//
//      add_settings_section('header_section', 'Header Options', array($this, 'section_form_elements'), 'salesforce-options');
//      add_settings_section('salesforce_section', 'Salesforce Options', array($this, 'section_form_elements'), 'salesforce-options');
//
//      add_settings_field('wpdz_settings', 'Salesforce', array($this, 'form_elements'), 'salesforce-options', 'header_section');
//      register_setting('header_section', 'wpdz_settings', array($this, 'sanitize_settings'));
//
//
//      add_settings_field('salesforce_brand', 'Salesforce brand', array($this, 'field_salesforce_brand'), 'salesforce-options', 'salesforce_section');
//      register_setting('salesforce_section', 'salesforce_brand');
//
//      add_settings_field('salesforce_country', 'Salesforce country', array($this, 'field_salesforce_country'), 'salesforce-options', 'salesforce_section');
//      register_setting('salesforce_section', 'salesforce_country');
//
//      add_settings_field('salesforce_iss', 'Salesforce iss', array($this, 'field_salesforce_iss'), 'salesforce-options', 'salesforce_section');
//      register_setting('salesforce_section', 'salesforce_iss');
//
//      add_settings_field('salesforce_aud', 'Salesforce aud', array($this, 'field_salesforce_aud'), 'salesforce-options', 'salesforce_section');
//      register_setting('salesforce_section', 'salesforce_aud');
//
//      add_settings_field('salesforce_prn', 'Salesforce prn', array($this, 'field_salesforce_prn'), 'salesforce-options', 'salesforce_section');
//      register_setting('salesforce_section', 'salesforce_prn');
//
//      add_settings_field('salesforce_endpoint', 'Salesforce endpoint', array($this, 'field_salesforce_endpoint'), 'salesforce-options', 'salesforce_section');
//      register_setting('salesforce_section', 'salesforce_endpoint');
//
//      add_settings_field('salesforce_dealer', 'Salesforce dealer', array($this, 'field_salesforce_dealer'), 'salesforce-options', 'salesforce_section');
//      register_setting('salesforce_section', 'salesforce_dealer');
//    }
//
//    public function field_salesforce_brand() {
//      print '<input type="text" name="salesforce_brand" id="salesforce_brand" value="' . get_option('salesforce_brand') . '" />';
//    }
//
//    public function field_salesforce_country() {
//      print '<input type="text" name="salesforce_country" id="salesforce_country" value="' . get_option('salesforce_country') . '" />';
//    }
//
//    public function field_salesforce_iss() {
//      print '<input type="text" name="salesforce_iss" id="salesforce_iss" value="' . get_option('salesforce_country') . '" />';
//    }
//
//    public function field_salesforce_aud() {
//      print '<input type="text" name="salesforce_aud" id="salesforce_aud" value="' . get_option('salesforce_aud') . '" />';
//    }
//
//    public function field_salesforce_prn() {
//      print '<input type="text" name="salesforce_prn" id="salesforce_prn" value="' . get_option('salesforce_prn') . '" />';
//    }
//
//    public function field_salesforce_endpoint() {
//      print '<input type="text" name="salesforce_endpoint" id="salesforce_endpoint" value="' . get_option('salesforce_endpoint') . '" />';
//    }
//
//    public function field_salesforce_dealer() {
//      print '<input type="text" name="salesforce_dealer" id="salesforce_dealer" value="' . get_option('salesforce_dealer') . '" />';
//    }
//
//    public function section_form_elements() {
//      echo 'The header of the theme';
//    }
//
//    public function form_elements() {
//      print_r(get_option('wpdz_settings'));
//    }
//
//    /**
//     * Sanitize the submitted plugin settings.
//     *
//     * @since 1.0.0
//     * @return array $output The setting values
//     */
//    public function sanitize_settings() {
//      //
//      global $wpdz_settings, $wpdz_admin;
//
//      $output = array();
//      return $output;
//    }

  }

}

// func

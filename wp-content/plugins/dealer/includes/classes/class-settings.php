<?php

if ( ! defined('ABSPATH')) exit;  // if direct access

// 
// class class_wpd_settings  {
//
// 	public function __construct(){
// 		add_action('admin_init', array( $this, 'register_settings' ));
// 		add_action('admin_menu', array( $this, 'admin_menu' ));
// 	}
//
// 	public function admin_menu() {
//     add_options_page(
//         'Dealers', 'Dealers', 'manage_options', 'dealer-settings', array($this,'settings_page')
//     );
// 	}
//
// 	public function settings_page(){
// 		include( WPD_PLUGIN_DIR. 'includes/menus/settings.php' );
// 	}
//
//   public function register_settings() {
//     add_settings_section(
//     'dealer_settings_section',
//     'Dealers Page Settings',
//     array($this, 'section_elements'),
//     'dealer-settings');
//
//     add_settings_section('dealer_settings_section', 'Dealers Settings',array($this,'dealer_settings_section_form_elements1'), 'dealer-settings');
//
//     add_settings_field('dealer_gma_key_ip', 'Server IP Key', array( $this, 'field_gma_key_ip' ), 'dealer-settings', 'dealer_settings_section');
//     register_setting('dealer_settings_section', 'dealer_gma_key_ip');
//
//     add_settings_field('dealer_gma_key_web', 'Web Key', array( $this, 'field_gma_key_web' ), 'dealer-settings', 'dealer_settings_section');
//     register_setting('dealer_settings_section', 'dealer_gma_key_web');
//
//     // add_settings_field('dealer_page_header', 'Header', array($this,'field_header'), 'dealer-settings', 'dealer_settings_section');
//     // register_setting('dealer_settings_section', 'dealer_page_header');
//     //
//     // add_settings_field('dealer_page_footer', 'Footer', array($this,'field_footer'), 'dealer-settings', 'dealer_settings_section');
//     // register_setting('dealer_settings_section', 'dealer_page_footer');
//
//     add_settings_section('dealer_settings_section_gma_key_web', 'Google Address Check', array($this,'settings_section_gma_key_web'), 'dealer-settings');
//
//
//     //$this->register_settings_main_metatag();
//     // $this->register_settings_state_metatag();
//     // $this->register_settings_city_metatag();
//   }
//
//   // public function register_settings_main_metatag() {
//   //   add_settings_section(
//   //   'dealer_settings_section_main_metatag',
//   //   'Dealers Page Settings main metatag',
//   //   array($this, 'section_elements_main_meta'),
//   //   'dealer-settings-main');
//   //
//   //   add_settings_field('dealer_page_main_metatag_title', 'Title', array($this,'field_main_metatag_title'), 'dealer-settings-main', 'dealer_settings_section_main_metatag');
//   //   register_setting('dealer_settings_section_main_metatag', 'dealer_page_main_metatag_title');
//   //
// 	// 	// add_settings_field('dealer_page_main_metatag_description', 'Description', array($this,'field_main_metatag_description'), 'dealer-settings-main', 'dealer_settings_section_main_metatag');
//   //   // register_setting('dealer_settings_section_main_metatag', 'dealer_page_main_metatag_description');
//   //   //
//   //   // add_settings_field('dealer_page_main_metatags', 'Meta-tags', array($this,'field_main_metatags'), 'dealer-settings-main', 'dealer_settings_section_main_metatag');
//   //   // register_setting('dealer_settings_section_main_metatag', 'dealer_page_main_metatags');
//   // }
//   //
//   // public function register_settings_state_metatag() {
//   //   add_settings_section(
//   //   'dealer_settings_section_state_metatag',
//   //   'Dealers Page Settings state metatag',
//   //   array($this, 'section_elements_state_metatag'),
//   //   'dealer-settings-state');
//   //
//   //   add_settings_field('dealer_page_state_metatag_title', 'Title', array($this,'field_state_metatag_title'), 'dealer-settings-state', 'dealer_settings_section_state_metatag');
//   //   register_setting('dealer_settings_section_state_metatag', 'dealer_page_state_metatag_title');
//   //
// 	// 	add_settings_field('dealer_page_state_metatag_description', 'Description', array($this,'field_state_metatag_description'), 'dealer-settings-state', 'dealer_settings_section_state_metatag');
//   //   register_setting('dealer_settings_section_state_metatag', 'dealer_page_state_metatag_description');
//   //
//   //   add_settings_field('dealer_page_state_metatags', 'Meta-tags', array($this,'field_state_metatags'), 'dealer-settings-state', 'dealer_settings_section_state_metatag');
//   //   register_setting('dealer_settings_section_state_metatag', 'dealer_page_state_metatags');
//   // }
//   //
//   // public function register_settings_city_metatag() {
//   //   add_settings_section(
//   //   'dealer_settings_section_city_metatag',
//   //   'Dealers Page Settings city metatag',
//   //   array($this, 'section_elements_city_metatag'),
//   //   'dealer-settings-city');
//   //
//   //   add_settings_field('dealer_page_city_metatag_title', 'Title', array($this,'field_city_metatag_title'), 'dealer-settings-city', 'dealer_settings_section_city_metatag');
//   //   register_setting('dealer_settings_section_city_metatag', 'dealer_page_city_metatag_title');
//   //
// 	// 	add_settings_field('dealer_page_city_metatag_description', 'Description', array($this,'field_city_metatag_description'), 'dealer-settings-city', 'dealer_settings_section_city_metatag');
//   //   register_setting('dealer_settings_section_city_metatag', 'dealer_page_city_metatag_description');
//   //
//   //   add_settings_field('dealer_page_city_metatags', 'Meta-tags', array($this,'field_city_metatags'), 'dealer-settings-city', 'dealer_settings_section_city_metatag');
//   //   register_setting('dealer_settings_section_city_metatag', 'dealer_page_city_metatags');
//   // }
//
//   public function dealer_settings_section_form_elements1() {
//     echo 'You can managed yours keys on the <a href="https://console.developers.google.com">Google console</a> ';
//   }
//
//   public function settings_section_gma_key_web(){
//     print '<pre>'. print_r( dealer_lookup_address( '9650 Old Redwood Highway,95492' ), true ) .'</pre>';
//   }
//
//   public function field_gma_key_ip() {
//     print '<input name="dealer_gma_key_ip" id="dealer_gma_key_ip" style="width: 100%;" value="' . get_option('dealer_gma_key_ip') . '" />';
//   }
//   public function field_gma_key_web() {
//     print '<input name="dealer_gma_key_web" id="dealer_gma_key_web" style="width: 100%;" value="' . get_option('dealer_gma_key_web') . '" />';
//   }
//
//   // public function field_footer() {
//   //   print '<textarea name="dealer_page_footer" id="dealer_page_footer" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_footer')
//   //       . '</textarea>';
//   // }
//   //
//   // public function field_header() {
//   //   print '<textarea name="dealer_page_header" id="dealer_page_header" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_header')
//   //       . '</textarea>';
//   // }
//   //
//   // public function field_main_metatag_title() {
//   //   print '<textarea name="dealer_page_main_metatag_title" id="dealer_page_main_metatag_title" rows="2" style="width: 100%;">'
//   //       . get_option('dealer_page_main_metatag_title')
//   //       . '</textarea>';
//   // }
//   //
// 	// public function field_main_metatag_description() {
//   //   print '<textarea name="dealer_page_main_metatag_description" id="dealer_page_main_metatag_description" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_main_metatag_description')
//   //       . '</textarea>';
//   // }
//   //
//   // public function field_main_metatags() {
//   //   print '<textarea name="dealer_page_main_metatags" id="dealer_page_main_metatags" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_main_metatags')
//   //       . '</textarea>';
//   // }
//   //
//   // public function field_state_metatag_title() {
//   //   print '<input name="dealer_page_state_metatag_title" id="dealer_page_state_metatag_title" style="width: 100%;" value="' . get_option('dealer_page_state_metatag_title') . '" />';
//   // }
//   //
// 	// public function field_state_metatag_description() {
//   //   print '<textarea name="dealer_page_state_metatag_description" id="dealer_page_state_metatag_description" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_state_metatag_description')
//   //       . '</textarea>';
//   // }
//   //
//   // public function field_state_metatags() {
//   //   print '<textarea name="dealer_page_state_metatags" id="dealer_page_state_metatags" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_state_metatags')
//   //       . '</textarea>';
//   // }
//   //
//   // public function field_city_metatag_title() {
//   //   print '<textarea name="dealer_page_city_metatag_title" id="dealer_page_city_metatag_title" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_city_metatag_title')
//   //       . '</textarea>';
//   // }
//   //
// 	// public function field_city_metatag_description() {
//   //   print '<textarea name="dealer_page_city_metatag_description" id="dealer_page_city_metatag_description" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_city_metatag_description')
//   //       . '</textarea>';
//   // }
//   //
//   // public function field_city_metatags() {
//   //   print '<textarea name="dealer_page_city_metatags" id="dealer_page_city_metatags" rows="7" style="width: 100%;">'
//   //       . get_option('dealer_page_city_metatags')
//   //       . '</textarea>';
//   // }
//
// 	public function help(){
// 		include( WPD_PLUGIN_DIR. 'includes/menus/help.php' );
// 	}
//
// } new class_wpd_settings();

<?php

if ( ! defined('ABSPATH')) exit;  // if direct access


class class_dp_settings  {

	public function __construct(){
		add_action('admin_init', array( $this, 'register_settings' ));
		add_action('admin_menu', array( $this, 'admin_menu' ));
	}

	public function admin_menu() {
    add_options_page(
        'Dealer pages', 'Dealer pages', 'manage_options', 'dealer-page-settings', array($this,'settings_page')
    );
	}

	public function settings_page(){
		include( DP_PLUGIN_DIR. 'includes/menus/settings.php' );
	}

  public function register_settings() {
    add_settings_section(
    'dealer_page_settings_section',
    'Dealers Page Settings',
    array($this, 'section_elements'),
    'dealer-page-settings');

    add_settings_field('dealer_page_header', 'Header', array($this,'field_header'), 'dealer-page-settings', 'dealer_page_settings_section');
    register_setting('dealer_page_settings_section', 'dealer_page_header');

    add_settings_field('dealer_page_footer', 'Footer', array($this,'field_footer'), 'dealer-page-settings', 'dealer_page_settings_section');
    register_setting('dealer_page_settings_section', 'dealer_page_footer');

    $this->register_settings_main_metatag();
    $this->register_settings_state_metatag();
    $this->register_settings_city_metatag();
  }

  public function register_settings_main_metatag() {
    add_settings_section(
    'dealer_page_settings_section_main_metatag',
    'Dealers Page Settings main metatag',
    array($this, 'section_elements_main_meta'),
    'dealer-page-settings-main');

    add_settings_field('dealer_page_main_metatag_title', 'Title', array($this,'field_main_metatag_title'), 'dealer-page-settings-main', 'dealer_page_settings_section_main_metatag');
    register_setting('dealer_page_settings_section_main_metatag', 'dealer_page_main_metatag_title');

		add_settings_field('dealer_page_main_metatag_description', 'Description', array($this,'field_main_metatag_description'), 'dealer-page-settings-main', 'dealer_page_settings_section_main_metatag');
    register_setting('dealer_page_settings_section_main_metatag', 'dealer_page_main_metatag_description');

    add_settings_field('dealer_page_main_metatags', 'Meta-tags', array($this,'field_main_metatags'), 'dealer-page-settings-main', 'dealer_page_settings_section_main_metatag');
    register_setting('dealer_page_settings_section_main_metatag', 'dealer_page_main_metatags');
  }

  public function register_settings_state_metatag() {
    add_settings_section(
    'dealer_page_settings_section_state_metatag',
    'Dealers Page Settings state metatag',
    array($this, 'section_elements_state_metatag'),
    'dealer-page-settings-state');

    add_settings_field('dealer_page_state_metatag_title', 'Title', array($this,'field_state_metatag_title'), 'dealer-page-settings-state', 'dealer_page_settings_section_state_metatag');
    register_setting('dealer_page_settings_section_state_metatag', 'dealer_page_state_metatag_title');

		add_settings_field('dealer_page_state_metatag_description', 'Description', array($this,'field_state_metatag_description'), 'dealer-page-settings-state', 'dealer_page_settings_section_state_metatag');
    register_setting('dealer_page_settings_section_state_metatag', 'dealer_page_state_metatag_description');

    add_settings_field('dealer_page_state_metatags', 'Meta-tags', array($this,'field_state_metatags'), 'dealer-page-settings-state', 'dealer_page_settings_section_state_metatag');
    register_setting('dealer_page_settings_section_state_metatag', 'dealer_page_state_metatags');
  }

  public function register_settings_city_metatag() {
    add_settings_section(
    'dealer_page_settings_section_city_metatag',
    'Dealers Page Settings city metatag',
    array($this, 'section_elements_city_metatag'),
    'dealer-page-settings-city');

    add_settings_field('dealer_page_city_metatag_title', 'Title', array($this,'field_city_metatag_title'), 'dealer-page-settings-city', 'dealer_page_settings_section_city_metatag');
    register_setting('dealer_page_settings_section_city_metatag', 'dealer_page_city_metatag_title');

		add_settings_field('dealer_page_city_metatag_description', 'Description', array($this,'field_city_metatag_description'), 'dealer-page-settings-city', 'dealer_page_settings_section_city_metatag');
    register_setting('dealer_page_settings_section_city_metatag', 'dealer_page_city_metatag_description');

    add_settings_field('dealer_page_city_metatags', 'Meta-tags', array($this,'field_city_metatags'), 'dealer-page-settings-city', 'dealer_page_settings_section_city_metatag');
    register_setting('dealer_page_settings_section_city_metatag', 'dealer_page_city_metatags');
  }

  public function section_elements() {
    echo 'Dealers Page Settings';
  }

  public function section_elements_main_meta() {
    echo 'Dealers Page Settings';
  }

  public function section_elements_state_metatag() {
    echo 'Dealers Page Settings State metatag';
  }

	public function section_elements_city_metatag() {
    echo 'Dealers Page Settings City metatag';
  }

  public function field_footer() {
    print '<textarea name="dealer_page_footer" id="dealer_page_footer" rows="7" style="width: 100%;">'
        . get_option('dealer_page_footer')
        . '</textarea>';
  }

  public function field_header() {
    print '<textarea name="dealer_page_header" id="dealer_page_header" rows="7" style="width: 100%;">'
        . get_option('dealer_page_header')
        . '</textarea>';
  }

  public function field_main_metatag_title() {
    print '<textarea name="dealer_page_main_metatag_title" id="dealer_page_main_metatag_title" rows="2" style="width: 100%;">'
        . get_option('dealer_page_main_metatag_title')
        . '</textarea>';
  }

	public function field_main_metatag_description() {
    print '<textarea name="dealer_page_main_metatag_description" id="dealer_page_main_metatag_description" rows="7" style="width: 100%;">'
        . get_option('dealer_page_main_metatag_description')
        . '</textarea>';
  }

  public function field_main_metatags() {
    print '<textarea name="dealer_page_main_metatags" id="dealer_page_main_metatags" rows="7" style="width: 100%;">'
        . get_option('dealer_page_main_metatags')
        . '</textarea>';
  }

  public function field_state_metatag_title() {
    print '<textarea name="dealer_page_state_metatag_title" id="dealer_page_state_metatag_title" rows="7" style="width: 100%;">'
        . get_option('dealer_page_state_metatag_title')
        . '</textarea>';
  }

	public function field_state_metatag_description() {
    print '<textarea name="dealer_page_state_metatag_description" id="dealer_page_state_metatag_description" rows="7" style="width: 100%;">'
        . get_option('dealer_page_state_metatag_description')
        . '</textarea>';
  }

  public function field_state_metatags() {
    print '<textarea name="dealer_page_state_metatags" id="dealer_page_state_metatags" rows="7" style="width: 100%;">'
        . get_option('dealer_page_state_metatags')
        . '</textarea>';
  }

  public function field_city_metatag_title() {
    print '<textarea name="dealer_page_city_metatag_title" id="dealer_page_city_metatag_title" rows="7" style="width: 100%;">'
        . get_option('dealer_page_city_metatag_title')
        . '</textarea>';
  }

	public function field_city_metatag_description() {
    print '<textarea name="dealer_page_city_metatag_description" id="dealer_page_city_metatag_description" rows="7" style="width: 100%;">'
        . get_option('dealer_page_city_metatag_description')
        . '</textarea>';
  }

  public function field_city_metatags() {
    print '<textarea name="dealer_page_city_metatags" id="dealer_page_city_metatags" rows="7" style="width: 100%;">'
        . get_option('dealer_page_city_metatags')
        . '</textarea>';
  }

	public function help(){
		include( DP_PLUGIN_DIR. 'includes/menus/help.php' );
	}

} new class_dp_settings();

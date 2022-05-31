<?php

/**
 * Handle the plugin settings.
 *
 */
if(!defined('ABSPATH')) {
  exit;
}

if(!class_exists('WPBV_Settings')) {

  class WPBV_Settings {

    public function __construct() {
      add_action('admin_init', array( $this, 'register_settings' ));
      add_action('admin_menu', array( $this, 'menu_items' ));
    }

   public function menu_items() {
     add_options_page(
         'Bazaarvoice Options', 'Bazaarvoice Options', 'manage_options', 'bazaarvoic-options', array($this, 'bazaarvoic_options_page')
     );
   }

   public function bazaarvoic_options_page() {
		 ?>
    <div class='wrap'>
        <div id='icon-options-general' class='icon32'></div>
        <form method='post' action='options.php'>
        <?php
        settings_fields('bazaarvoic_section');
        do_settings_sections('bazaarvoic-options');
        submit_button();

        if( isset( $_GET['import']) && $_GET['import'] == 'now' ){
          // $GLOBALS['wpbv']->bv_importer->processSEO('bv_hotspring_smartseo.zip');
        }
        // print_r(wp_upload_dir());

        ?>
        </form>
        <a href="?page=bazaarvoic-options&import=now">Import Now</a>
    </div>
    <?php
   }

   /**
    * Register the settings.
    *
    * @since 2.0.0
    * @return void
    */
   public function register_settings() {
     add_settings_section('bazaarvoic_section', 'bazaarvoic Options', array($this, 'section_form_elements'), 'bazaarvoic-options');

     add_settings_field('bazaarvoic_api_key', 'Bazaarvoic API Key', array($this, 'field_bazaarvoic_api_key'), 'bazaarvoic-options', 'bazaarvoic_section');
     register_setting('bazaarvoic_section', 'bazaarvoic_api_key');

     add_settings_field('bazaarvoic_host', 'Bazaarvoic Host', array($this, 'field_bazaarvoic_host'), 'bazaarvoic-options', 'bazaarvoic_section');
     register_setting('bazaarvoic_section', 'bazaarvoic_host');

     add_settings_field('bazaarvoic_user', 'Bazaarvoic User', array($this, 'field_bazaarvoic_user'), 'bazaarvoic-options', 'bazaarvoic_section');
     register_setting('bazaarvoic_section', 'bazaarvoic_user');

     add_settings_field('bazaarvoic_pass', 'Bazaarvoic Pass', array($this, 'field_bazaarvoic_pass'), 'bazaarvoic-options', 'bazaarvoic_section');
     register_setting('bazaarvoic_section', 'bazaarvoic_pass');

   }

   public function field_bazaarvoic_api_key() {
     print '<input type="text" name="bazaarvoic_api_key" id="bazaarvoic_api_key" value="' . get_option('bazaarvoic_api_key') . '" />';
   }
   public function field_bazaarvoic_host() {
     print '<input type="text" name="bazaarvoic_host" id="bazaarvoic_host" value="' . get_option('bazaarvoic_host') . '" />';
   }
   public function field_bazaarvoic_user() {
     print '<input type="text" name="bazaarvoic_user" id="bazaarvoic_user" value="' . get_option('bazaarvoic_user') . '" />';
   }
   public function field_bazaarvoic_pass() {
     print '<input type="text" name="bazaarvoic_pass" id="bazaarvoic_pass" value="' . get_option('bazaarvoic_pass') . '" />';
   }

   public function section_form_elements() {
     echo 'The options of bazaarvoic';
   }

   public function form_elements() {
     print_r(get_option('wpdz_settings'));
   }

   /**
    * Sanitize the submitted plugin settings.
    *
    * @since 1.0.0
    * @return array $output The setting values
    */
   public function sanitize_settings() {
     //
     global $wpdz_settings, $wpdz_admin;

     $output = array();
     return $output;
   }

  }

}

// func

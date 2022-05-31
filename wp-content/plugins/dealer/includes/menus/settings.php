<?php

if(!defined('ABSPATH')) {
  exit;
}

class class_wpd_settings  {

	public function __construct(){
		add_action('admin_init', array( $this, 'register_settings' ));
		add_action('admin_menu', array( $this, 'admin_menu' ));
	}

	public function admin_menu() {
    add_options_page(
        'Dealers', 'Dealers', 'manage_options', 'dealer-settings', array($this,'settings_page')
    );
	}

  function settings_page() { ?>
  <div class='wrap'>
    <div id='icon-options-general' class='icon32'></div>
    <form method='post' action='options.php'>
    <?php
    settings_fields('dealer_settings_section');
    do_settings_sections('dealer-settings');
    submit_button();
    ?>
    </form>
    <?php print '<pre>'. print_r(dealer_lookup_address('9650 Old Redwood Highway,95492'),true) .'</pre>'; ?>
  </div>
  <?php
  }

  public function register_settings() {
    add_settings_section('dealer_settings_section', 'Dealers Settings', array( $this, 'dealer_settings_section_form_elements' ), 'dealer-settings');

    add_settings_field('dealer_gma_key_ip', 'Server IP Key', array( $this, 'dealer_settings_field_gma_key_ip' ), 'dealer-settings', 'dealer_settings_section');
    register_setting('dealer_settings_section', 'dealer_gma_key_ip');

    add_settings_field('dealer_gma_key_web', 'Web Key', array( $this, 'dealer_settings_field_gma_key_web' ), 'dealer-settings', 'dealer_settings_section');
    register_setting('dealer_settings_section', 'dealer_gma_key_web');

    add_settings_field('dealer_roles', 'Roles excluded from daily EDL update', array( $this, 'dealer_settings_field_roles' ), 'dealer-settings', 'dealer_settings_section');
    register_setting('dealer_settings_section', 'dealer_roles');

  }

  public function dealer_settings_field_gma_key_ip() {
    print '<input type="text" name="dealer_gma_key_ip" id="dealer_gma_key_ip" value="' . get_option('dealer_gma_key_ip') . '" />';
  }

  public function dealer_settings_field_gma_key_web() {
    print '<input type="text" name="dealer_gma_key_web" id="dealer_gma_key_web" value="' . get_option('dealer_gma_key_web') . '" />';
  }

  public function dealer_settings_section_form_elements() {
    echo 'You can managed yours keys on the <a href="https://console.developers.google.com">Google console</a> ';
  }

  public function dealer_settings_field_roles() {
    $roles = get_editable_roles();
    $old = get_option('dealer_roles');
    if(empty($old)){
      $old = array();
    }
    foreach ($roles as $key => $value) {
      if( in_array( $key, $old ) ){
        print '<input type="checkbox" name="dealer_roles[]" id="dealer_roles_checkbox" value="'.$key.'" checked/>'.$value['name'].'<br />';
      }else{
        print '<input type="checkbox" name="dealer_roles[]" id="dealer_roles_checkbox" value="'.$key.'" />'.$value['name'].'<br />';
      }
    }
  }

} new class_wpd_settings();

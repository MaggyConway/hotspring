<?php

if(!defined('ABSPATH')) {
  exit;
}

class class_wpd_import  {

	public function __construct(){
		add_action('admin_init', array( $this, 'register_settings' ));
		add_action('admin_menu', array( $this, 'admin_menu' ));
	}

	public function admin_menu() {
    add_submenu_page( 'edit.php?post_type=edls', 'Import Dealer (SOAP)', 'Import Dealer', 'import_dealers', 'dealer-import', array( $this, 'dealer_import_page' ) );

    // add_options_page(
    //     'Dealers', 'Dealers', 'manage_options', 'dealer-settings', array($this,'settings_page')
    // );
	}

  function dealer_import_page() { ?>
  <div class='wrap'>
    <div id='icon-options-general' class='icon32'></div>
    <form action="edit.php?post_type=edls&page=dealer-import" method="post" enctype="multipart/form-data">
    <?php
      //settings_fields('dealer_import_section');
      do_settings_sections( 'dealer-import' );
      wp_nonce_field( 'dealer-import' );
      submit_button( 'Import', 'primary', 'submit-form', false );
    ?>
    </form>
  </div>
  <?php
  }

  public function register_settings() {
    add_settings_section('dealer_import_section', 'Dealers Import', array( $this, 'dealer_settings_section_form_elements' ), 'dealer-import');

    // add_settings_field('dealer_gma_key_ip', 'Server IP Key', array( $this, 'dealer_settings_field_gma_key_ip' ), 'dealer-import', 'dealer_import_section');
    // register_setting('dealer_import_section', 'dealer_gma_key_ip');
    //
    // add_settings_field('dealer_gma_key_web', 'Web Key', array( $this, 'dealer_settings_field_gma_key_web' ), 'dealer-import', 'dealer_import_section');
    // register_setting('dealer_import_section', 'dealer_gma_key_web');
  }

  public function dealer_settings_field_gma_key_ip() {
    print '<input type="text" name="dealer_gma_key_ip" id="dealer_gma_key_ip" value="' . get_option('dealer_gma_key_ip') . '" />';
  }

  public function dealer_settings_field_gma_key_web() {
    print '<input type="text" name="dealer_gma_key_web" id="dealer_gma_key_web" value="' . get_option('dealer_gma_key_web') . '" />';
  }

  public function dealer_settings_section_form_elements() {
    print "<p>Please click on the import of button if you want to start import</p>";
    $nonce = $_REQUEST['_wpnonce'];
    if ( wp_verify_nonce( $nonce, 'dealer-import' ) && current_user_can( 'import_dealers' ) ) {
        // print_r($_POST);
        $importer = new DealerImporterSoap( 'Hotspring' );
        $importer->getAccounts();
        $importer->getDealerships();
        $importer->process();
    }

    $count_queue = $GLOBALS['wpd-background-process']->count_queue();
    if($count_queue){
      print '<p>Count of the queues is ' . $count_queue . '</p>';
    }
  }

} new class_wpd_import();

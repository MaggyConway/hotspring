<?php

if ( ! defined('ABSPATH')) exit;  // if direct access

class class_dc_bulk{
  var $nonce;
  public function __construct(){
    //$nonce = wp_create_nonce(basename(__FILE__));

    add_action('admin_menu', array( $this, 'page_menu') );
    //add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

    // //notice how we registering our handler
    // //wp_ajax_[bulk_process] - where [bulk_process] will be action name in javascript
    // add_action('wp_ajax_bulk_process', array( $this, 'bulk_process' ));
  }
  public function enqueue_scripts(){

  }
  public function page_menu(){
    $page = add_menu_page('Bulk Page Title', 'Bulk Menu Title','manage_options',
    'bulk_page_slug', array( $this, 'page') );
    add_action('admin_print_scripts-' . $page, array( $this, 'print_scripts' ) );
    add_action( 'admin_print_styles-' . $page, array( $this, 'print_styles' ));
  }

  public function print_styles(){
    wp_enqueue_style( 'dcbulk', plugins_url( 'css/bulk.css', DC_PLUGIN_DIR . 'ddd/' ) );
  }
  //register and enqueue needed scripts and styles here
  public function print_scripts(){

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'dcbulk', plugins_url( 'js/bulk.js', DC_PLUGIN_DIR . 'ddd/' ));

    $this->nonce = wp_create_nonce(basename(__FILE__));
    wp_localize_script( 'dcbulk', 'dc_nonce', $this->nonce);

  }

  //page handler is simple function that renders page
  public function page(){
?>

<div class="wrap">
    <!--
        Here is our simple form, it just contains one input to configure number of actions
        but form may be as big as u want
    -->
    <div class="container-progress-bar">
      <div class="bar">
        <span class="bar-fill"></span>
      </div>
    </div>

    <div id="bulk_form">
        <input type="number" name="bulk_count" id="bulk_count" placeholder="number of iterations..." required>
        <input id="bulk_submit" type="submit" value="Submit">
    </div>
    <!--
        #bulk_process will be used to show process metrics, just for example
    -->
    <div id="bulk_process" style="display:none">
        <table cellspacing="0" cellpadding="5" border="1">
            <tr>
                <th>success</th>
                <th>total</th>
                <th>success</th>
                <th>fail</th>
                <th>current</th>
                <th>remainded</th>
                <th>percentage</th>
                <th>start</th>
                <th>elapsed</th>
                <th>remainded</th>
                <th>per item</th>
                <th>message</th>
            </tr>
        </table>
    </div>
</div>
<?php
  }

  // our ajax handler, all data accessible via $_POST
  public function bulk_process(){
      //check correct nonce (setted in bulk_page_handler function)
      check_ajax_referer(basename(__FILE__));
      //try to turn off error reporting
      @error_reporting(0);
      //we are going to retrieve json response
      header('Content-type: application/json');

      try {

          //TODO: do something heavy weight here
          //throw exceptions on error
          if ($_POST['index'] == 2) {
              throw new Exception('Wrong id');
          }
          //fill $response variable at the end
          sleep(rand(1, 3));

          $response = array(
              'success' => true,
              'message' => $_POST['index'] . ' - done'
          );

      } catch (Exception $ex) {
          $response = array(
              'success' => false,
              'message' => $ex->getMessage()
          );
      }

      //echoing response
      echo json_encode($response);
      //do not forget about this die()
      die();
  }
}

new class_dc_bulk();

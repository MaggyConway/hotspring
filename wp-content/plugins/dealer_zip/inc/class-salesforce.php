<?php
/**
 * Admin class
 */
if(!defined('ABSPATH')) {
  exit;
}

if(!class_exists('WPDZ_Salesforce')) {

  /**
   * Handle the backend of the store locator
   *
   * @since 1.0.0
   */
  class WPDZ_Salesforce {

    /**
     * @since 2.0.0
     * @var WPDZ_Settings
     */
    public $settings_page;

    /**
     * Class constructor
     */
    function __construct() {
      $this->init();
      $this->includes();
    }

    /**
     * Include the required files.
     *
     * @since 2.0.0
     * @return void
     */
    public function includes() {
      require_once( WPDZ_PLUGIN_DIR . 'inc/jwt.php' );
    }

    /**
     * Init the classes.
     *
     */
    public function init() {
      add_action('admin_init', array($this, 'register_settings'));
      add_action('admin_menu', array($this, 'menu_items'));
    }

    public function menu_items() {

      // add_options_page('SalesForce Options', 'SalesForce Options', 'manage_options', 'salesforce-options', array( $this, 'salesforce_options_page' ));


      add_options_page(
          'Salesforce Options', 'Salesforce Options', 'manage_options', 'salesforce-options', array($this, 'salesforce_options_page')
      );
      // add_options_page(
      //     'test', 'Test', 'manage_options', 'test-options', array($this, 'salesforce_test_page')
      // );
    }

    // public function salesforce_test_page() {    //
    //   $did = $this->get_dealer_info('12345');
    //   $dealer = array_pop(_dealer_get_posts_by_dealership_id($did));
    //   wp_redirect($dealer->guid, $status = 302);
    //   //redirect_post($dealer->ID);
    //   //var_dump($dealer);
    //   print_r($dealer);
    //   //exit();
    //
    //     }

        public function salesforce_options_page() {
          ?>
      <div class='wrap'>
          <div id='icon-options-general' class='icon32'></div>
          <form method='post' action='options.php'>
          <?php
          settings_fields('salesforce_section');
          do_settings_sections('salesforce-options');
          submit_button();
          ?>
          </form>
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



      add_settings_section('salesforce_section', 'Salesforce Options', array($this, 'section_form_elements'), 'salesforce-options');

      add_settings_field('salesforce_brand', 'Salesforce brand', array($this, 'field_salesforce_brand'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_brand');

      add_settings_field('salesforce_country', 'Salesforce country', array($this, 'field_salesforce_country'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_country');

      add_settings_field('salesforce_iss', 'Salesforce iss', array($this, 'field_salesforce_iss'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_iss');

      add_settings_field('salesforce_aud', 'Salesforce aud', array($this, 'field_salesforce_aud'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_aud');

      add_settings_field('salesforce_prn', 'Salesforce prn', array($this, 'field_salesforce_prn'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_prn');

      add_settings_field('salesforce_endpoint', 'Salesforce endpoint', array($this, 'field_salesforce_endpoint'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_endpoint');

      add_settings_field('salesforce_dealer', 'Salesforce dealer', array($this, 'field_salesforce_dealer'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_dealer');

      add_settings_field('salesforce_key', 'Salesforce key', array($this, 'field_salesforce_key'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_key');

      add_settings_field('salesforce_error', 'Salesforce last error', array($this, 'field_salesforce_error'), 'salesforce-options', 'salesforce_section');
      register_setting('salesforce_section', 'salesforce_error');
    }

    public function field_salesforce_brand() {
      print '<input type="text" name="salesforce_brand" id="salesforce_brand" value="' . get_option('salesforce_brand') . '" />';
    }

    public function field_salesforce_country() {
      print '<input type="text" name="salesforce_country" id="salesforce_country" value="' . get_option('salesforce_country') . '" />';
    }

    public function field_salesforce_iss() {
      print '<input type="text" name="salesforce_iss" id="salesforce_iss" value="' . get_option('salesforce_iss') . '" />';
    }

    public function field_salesforce_aud() {
      print '<input type="text" name="salesforce_aud" id="salesforce_aud" value="' . get_option('salesforce_aud') . '" />';
    }

    public function field_salesforce_prn() {
      print '<input type="text" name="salesforce_prn" id="salesforce_prn" value="' . get_option('salesforce_prn') . '" />';
    }

    public function field_salesforce_endpoint() {
      print '<input type="text" name="salesforce_endpoint" id="salesforce_endpoint" value="' . get_option('salesforce_endpoint') . '" />';
    }

    public function field_salesforce_dealer() {
      print '<input type="text" name="salesforce_dealer" id="salesforce_dealer" value="' . get_option('salesforce_dealer') . '" />';
    }

    public function field_salesforce_key() {
      print '<textarea name="salesforce_key" id="salesforce_key" rows="28" cols="65">'
          . get_option('salesforce_key')
          . '</textarea>';
    }
    public function field_salesforce_error() {
      print '<pre>' . print_r( get_option('salesforce_error'), true ) . '</pre>';

      // print '<textarea name="salesforce_key" id="salesforce_key" rows="28" cols="65">'
      //     . get_option('salesforce_key')
      //     . '</textarea>';
    }

    public function section_form_elements() {
      echo 'The header of the theme';
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
      // //  wpdz_set_default_settings();
      // //  exit();
      //   print_r(ddd);
      // //  print_r($wpdz_settings);
      // //   //      print_r( $_POST['header_logo']);
      // //   //      print_r( $_POST['header_logo']['bb']);
      // //   //      foreach ($_POST['header_logo'] as $key => $value) {
      // //   //        print $key." => " . $value ."\n";
      // //   //      }
      // //   //   // dd($_POST);
      // //    exit;
      //     $output = $wpdz_settings;
      //     // $output['wpdz_settings'] = $wpdz_settings;
      //
        //     $output['salesforce_brand'] = $_POST['wpdz_settings']['salesforce_brand'];
      //

            return $output;
    }

    public function get_key() {
      $temp = get_option('salesforce_key');
      $temp = str_replace("-----BEGIN PRIVATE KEY-----", "", $temp);
      $temp = str_replace("-----END PRIVATE KEY-----", "", $temp);
      return $temp;
    }

    public function request_token() {
      $key = $this->get_key();

      if(!$key) {
        return;
      }

      $token = array(
        "iss" => get_option('salesforce_iss'),
        "aud" => get_option('salesforce_aud'),
        "prn" => get_option('salesforce_prn'),
        "exp" => strtotime("+5 minutes"),
      );

      $jwt = JWT::encode($token, $key, 'RS256');
      $data = array(
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
      );

      $data = http_build_query($data);
      $url = get_option('salesforce_endpoint');

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded", "Content-length: " . strlen($data)));

      // Turning on some debug info
      curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

      $result = curl_exec($ch);
      curl_close($ch);
      $token = json_decode($result);

      return $token;
    }

    public function get_dealer_info($zip, $country = 'US') {
      $zip = trim($zip);
      $brand = get_option('salesforce_brand');

      if(empty($brand)) {
        // drupal_set_message('Please Set Brand in the SalesForce API settings');
        return FALSE;
      }
      $result = $this->request_token();
      $token = $result->access_token;
      $url = get_option('salesforce_dealer');
      $data = array(
        "Brand" => $brand,
        "ZipCode" => $zip,
        "Country" => $country,
      );

      $data = json_encode($data);

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json;charset=UTF-8", "Authorization: Bearer " . $token, "Content-length: " . strlen($data)));
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      // Turning on some debug info
      curl_setopt($curl, CURLINFO_HEADER_OUT, 1);

      $response = curl_exec($curl);

      if(empty($response)) {
        // some kind of an error happened
        die(curl_error($curl));
        curl_close($curl); // close cURL handler
      } else {
        $info = curl_getinfo($curl);

        curl_close($curl); // close cURL handler
        if($info['http_code'] != 200 && $info['http_code'] != 201) {
          $jsonResponse = array();
          $jsonResponse = json_decode($response, TRUE);
          $jsonResponse['error_data'] = date(DATE_RFC822);
          $option = get_option( 'salesforce_error' );
          if(empty($option )){
            add_option( 'salesforce_error', $jsonResponse );
          }else{
            update_option( 'salesforce_error', $jsonResponse );
          }

          //echo "Received error: " . $info['http_code']. "\n";
          //echo "Raw response:".$response."\n";
        }else{

        }
      }

      // Convert the result from JSON format to a PHP array
      $jsonResponse = json_decode($response, TRUE);
      $dealer = array_pop($jsonResponse);

      $dealerID = $dealer['DealerId'];
      return $dealerID;
    }

    /**
     * Add the admin menu pages.
     *
     * @since 1.0.0
     * @return void
     */
    public function create_admin_menu() {

    }

    /**
     * Load the correct page template.
     *
     */
    public function load_template() {

    }

  }

  // $GLOBALS['wpdz_salesforce'] = new WPDZ_Salesforce();
}

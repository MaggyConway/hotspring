<?php

GFForms::include_feed_addon_framework();

class GFMiniSalesforce extends GFFeedAddOn {

  protected $_version                  = GF_MINI_SALESFORCE_VERSION;
  protected $_min_gravityforms_version = '1.9.16';
  protected $_slug                     = 'minisalesforce';
  protected $_path                     = 'minisalesforce/minisalesforce.php';
  protected $_full_path                = __FILE__;
  protected $_title                    = 'Gravity Forms Mini Salesforce Feed Add-On';
  protected $_short_title              = 'Mini Salesforce API';
  private static $_instance            = null;
  /**
   * Get an instance of this class.
   *
   * @return GFSimpleFeedAddOn
   */
  public static function get_instance() {
    if ( self::$_instance == null ) {
      self::$_instance = new GFMiniSalesforce();
    }
    return self::$_instance;
  }
  /**
   * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
   */
  public function init() {
    parent::init();
    $this->add_delayed_payment_support(
      array(
        'option_label' => esc_html__( 'Subscribe contact to service x only when payment is received.', 'simplefeedaddon' ),
      )
    );
  }
  // convert state to sate code
  public function stateName2isoFormat( $state ) {
    $states    = array(
      // US
      'AL' => 'Alabama',
      'AK' => 'Alaska',
      'AS' => 'American Samoa',
      'AZ' => 'Arizona',
      'AR' => 'Arkansas',
      'CA' => 'California',
      'CO' => 'Colorado',
      'CT' => 'Connecticut',
      'DE' => 'Delaware',
      'DC' => 'District Of Columbia',
      'FM' => 'Federated States Of Micronesia',
      'FL' => 'Florida',
      'GA' => 'Georgia',
      'GU' => 'Guam',
      'HI' => 'Hawaii',
      'ID' => 'Idaho',
      'IL' => 'Illinois',
      'IN' => 'Indiana',
      'IA' => 'Iowa',
      'KS' => 'Kansas',
      'KY' => 'Kentucky',
      'LA' => 'Louisiana',
      'ME' => 'Maine',
      'MH' => 'Marshall Islands',
      'MD' => 'Maryland',
      'MA' => 'Massachusetts',
      'MI' => 'Michigan',
      'MN' => 'Minnesota',
      'MS' => 'Mississippi',
      'MO' => 'Missouri',
      'MT' => 'Montana',
      'NE' => 'Nebraska',
      'NV' => 'Nevada',
      'NH' => 'New Hampshire',
      'NJ' => 'New Jersey',
      'NM' => 'New Mexico',
      'NY' => 'New York',
      'NC' => 'North Carolina',
      'ND' => 'North Dakota',
      'MP' => 'Northern Mariana Islands',
      'OH' => 'Ohio',
      'OK' => 'Oklahoma',
      'OR' => 'Oregon',
      'PW' => 'Palau',
      'PA' => 'Pennsylvania',
      'PR' => 'Puerto Rico',
      'RI' => 'Rhode Island',
      'SC' => 'South Carolina',
      'SD' => 'South Dakota',
      'TN' => 'Tennessee',
      'TX' => 'Texas',
      'UT' => 'Utah',
      'VT' => 'Vermont',
      'VI' => 'Virgin Islands',
      'VA' => 'Virginia',
      'WA' => 'Washington',
      'WV' => 'West Virginia',
      'WI' => 'Wisconsin',
      'WY' => 'Wyoming',
      // Canada
      'AB' => 'Alberta',
      'BC' => 'British Columbia',
      'LB' => 'Labrador',
      'MB' => 'Manitoba',
      'NB' => 'New Brunswick',
      'NF' => 'Newfoundland',
      'NS' => 'Nova Scotia',
      'NU' => 'Nunavut',
      'NW' => 'North West Terr.',
      'ON' => 'Ontario',
      'PE' => 'Prince Edward Is.',
      'QC' => 'Quebec',
      'SK' => 'Saskatchewen',
      'YU' => 'Yukon',
    );
    $state_iso = array_search( $state, $states );
    return ! empty( $state_iso ) ? $state_iso : $state;
  }

  public function get_field_map() {
    // @TODO Add types
    $constituent_fields = array(
      // 'orgid' => '00D40000000N01h',
      // 'retURL' => 'http://calderaspas.com/shopping-tools/customer-service/success',
      // 'debug' => '1',
      // 'debugEmail' => 'admin8@eightcloud.com',
      // '00N33000002wr3Q' => 'Caldera',
      '00N33000002wn1B' => isset( $form->first_name ) ? $form->first_name : '',
      '00N33000002wn1D' => isset( $form->last_name ) ? $form->last_name : '',
      '00N33000002wn1G' => isset( $form->google_street_address ) ? $form->google_street_address : '',
      '00N33000002wn13' => isset( $form->country ) ? $form->country : '',
      '00N33000002wn1F' => isset( $form->state ) ? $form->state : '',
      '00N33000002wn12' => isset( $form->city ) ? $form->city : '',
      '00N33000002wn1H' => isset( $form->zip_code ) ? $form->zip_code : '',
      '00N33000002wn11' => isset( $form->what_caldera_model_do_you_own ) ? str_replace( chr( 174 ), '', $form->what_caldera_model_do_you_own ) : '',
      '00N33000002wn14' => isset( $form->purchase_date ) ? $form->purchase_date : '',
      '00N33000002wn1E' => isset( $form->serial_number ) ? $form->serial_number : '',
      '00N33000002wn19' => isset( $form->dealer_name ) ? $form->dealer_name : '',
      '00N33000002wn15' => isset( $form->google_street_dealer_address ) ? $form->google_street_dealer_address : '',
      '00N33000002wn16' => isset( $form->dealer_city ) ? $form->dealer_city : '',
      '00N33000002wn18' => isset( $form->dealer_state ) ? $form->dealer_state : '',
      '00N33000002wn17' => isset( $form->dealer_country ) ? $form->dealer_country : '',
      '00N33000002wn1A' => isset( $form->describe_in_detail_your_service_question_issue_or_request ) ? $form->describe_in_detail_your_service_question_issue_or_request : '',
      'firstname' => isset( $form->first_name ) ? $form->first_name : '',
      'lastname' => isset( $form->last_name ) ? $form->last_name : '',
      'country' => isset( $form->country ) ? $form->country : '',
      'postalcode' => isset( $form->zip_code ) ? $form->zip_code : '',
      'city' => isset( $form->city ) ? $form->city : '',
      'address' => isset( $form->google_street_address ) ? $form->google_street_address : '',
      'stateprovince' => isset( $form->state ) ? $form->state : '',
      'emailaddress' => ( $form->email ) ? $form->email : '',
      'email'           => ( $form->email ) ? $form->email : '',
      'phone'           => isset( $form->phone ) ? preg_replace( '/[^0-9]/', '', $form->phone ) : '',
      'modelinterest' => '',
      'timeframetopurchase' => '',
      'utmcampaign' => '',
      'utmmedium' => '',
      'utmsource' => '',
      'utmterm' => '',
      'utmcontent' => '',
      'googleclientid' => '',
      'googletransactionid' => '',
      'company' => '',
      'campaignname' => '',
      'comments' => '',
      'companyname' => '',
      'businessphone' => '',
      'mobilephone' => '',
      'fax' => '',
      'howhearaboutus' => '',
      'lengthofbusiness' => '',
      'numberretaillocations' => '',
      'annualspasales' => '',
      'primaryspadisplaybrand' => '',
      'whyinterested' => '',
      'interests1' => '',
      'interests2' => '',
      'interests3' => '',
      'interests4' => '',
    );
    return $constituent_fields;
  }

  /**
   * Return an array of Eloqua supporter fields which can be mapped to the Form fields/entry meta.
   *
   * @return array
   */
  public function constituent_field_map() {

    $field_map = array();

    $constituent_fields = $this->get_field_map();

    foreach ( $constituent_fields as $field_label => $field_type ) {
      $field_map[] = array(
        'name'       => $field_label,
        'label'      => $field_label,
        'field_type' => $field_type,
      );
    }

    return $field_map;
  }
  /**
   * Returns the value of the selected field.
   *
   * @param array  $form    The form object currently being processed.
   * @param array  $entry  The entry object currently being processed.
   * @param string $field_id The ID of the field being processed.
   *
   * @return array
   */
  public function get_field_value( $form, $entry, $field_id ) {

    $field_value = '';

    switch ( strtolower( $field_id ) ) {

      case 'form_title':
        $field_value = rgar( $form, 'title' );
        break;

      case 'date_created':
        $date_created = rgar( $entry, strtolower( $field_id ) );
        if ( empty( $date_created ) ) {
          // the date created may not yet be populated if this function is called during the validation phase and the entry is not yet created
          $field_value = gmdate( 'Y-m-d H:i:s' );
        } else {
          $field_value = $date_created;
        }
        break;

      case 'ip':
      case 'source_url':
        $field_value = rgar( $entry, strtolower( $field_id ) );
        break;

      default:
        $field = GFFormsModel::get_field( $form, $field_id );

        if ( is_object( $field ) ) {

          $is_integer = $field_id === intval( $field_id );
          $input_type = RGFormsModel::get_input_type( $field );
          switch ( $input_type ) {
              // case 'address':
              // $field_value = $this->get_full_address( $entry, $field_id );
              // break;
            case 'name':
              $field_value = $this->get_full_name( $entry, $field_id );
              break;
            case 'checkbox':
              // @TODO Refact..
              $selected = array();
              foreach ( $field->inputs as $input ) {
                $index = (string) $input['id'];
                if ( ! rgempty( $index, $entry ) ) {
                  $selected[] = rgar( $entry, $index );
                }
              }
              $field_value = implode( '|', $selected );
              break;
            case 'phone':
              $field_value = rgar( $entry, $field_id );
              if ( 'standard' == $field->phoneFormat ) {
                // format: NPA-NXX-LINE (404-555-1212) when US/CAN
                // $field_value = rgar( $entry, $field_id );
                if ( ! empty( $field_value ) && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $field_value, $matches ) ) {
                  $field_value = sprintf( '%s-%s-%s', $matches[1], $matches[2], $matches[3] );
                }
              }
              break;
            default:
              if ( is_callable( array( 'GF_Field', 'get_value_export' ) ) ) {
                $field_value = $field->get_value_export( $entry, $field_id );
                // if( $field_value == 'United States' ){
                // @TODO To think, how better to change the value of country field
                // $field_value = 'US';
                // }
              } else {
                $field_value = rgar( $entry, $field_id );
              }
              break;
          }
        } else {

          $field_value = rgar( $entry, $field_id );

        }
    }

    // //do a Drupal like token replace
    // $field_value = $this->token_replace($field_value);
    return $field_value;
  }
  public function getAltMap( $feed, $entry, $form ) {
    $settings = $this->get_plugin_settings();
    // $field_map = $this->get_dynamic_field_map_fields( $feed, 'mappedFields' );
    $field_map = $feed['meta']['mappedFields'];
    $params    = array();
    // Loop through the fields, populating $post_vars as necessary
    foreach ( $field_map as $field_id ) {
      // $field_value = $this->get_field_value( $form, $entry, $field_id );
      $field_value = rgar( $entry, $field_id['value'] );
      if ( ! empty( $field_value ) ) {
        if ( isset( $field_id['custom_key'] ) && ! empty( $field_id['custom_key'] ) ) {
          $params[ $field_id['custom_key'] ] = $field_value;
        } else {
          $params[ $field_id['key'] ] = $field_value;
        }
      }
    }
    return $params;
  }

  // # FEED PROCESSING -----------------------------------------------------------------------------------------------
  /**
   * Process the feed e.g. subscribe the user to a list.
   *
   * @param array $feed The feed object to be processed.
   * @param array $entry The entry object currently being processed.
   * @param array $form The form object currently being processed.
   *
   * @return bool|void
   */
  public function process_feed( $feed, $entry, $form ) {
    $settings  = $this->get_plugin_settings();
    $field_map = $this->get_dynamic_field_map_fields( $feed, 'mappedFields' );
    $params    = [];
    $custom_params = [];
    $custom_key_value = $feed['meta']['custom_key_value'];
    $custom_params = [];
    foreach ( explode( ',', $custom_key_value ) as $kv ) {
      $value = explode( ':', $kv );
      $custom_params[$value[0]] = $value[1];
        $search = [
        "{time}",
      ];
      $replace = [
        // https://en.wikipedia.org/wiki/ISO_8601 .
        str_replace('+00:00', '.000Z', date('c')),
      ];
      $custom_params[$value[0]] = str_replace( $search, $replace, $custom_params[$value[0]] );
    }
    if($body_params['overwrite_interest'] != $output){
      $body_params['modelinterest'] = $output;
    }

    // Loop through the fields, populating $post_vars as necessary
    foreach ( $field_map as $name => $field_id ) {
      $field_value = $this->get_field_value( $form, $entry, $field_id );
      if ( ! empty( $field_value ) ) {
        $params[ $name ] = $field_value;
      }
    }
    $altMap = $this->getAltMap($feed, $entry, $form);
    foreach ($altMap as $name => $field_value) {
      $params[ $name ] = $field_value;
    }
    $feed_settings = $feed['meta'];

    // unset system fields
    unset( $feed_settings['feedName'] );
    unset( $feed_settings['mappedFields'] );
    unset( $feed_settings['custom_key_value'] );
    unset( $feed_settings['feed_condition_conditional_logic'] );
    unset( $feed_settings['feed_condition_conditional_logic_object'] );

    if (isset($params['country'])) {
      switch($params['country']){
        case 'United States':
        case 'us':
        case 'US':
          $params['country'] = 'US';
          break;
        case 'Canada':
        case 'ca':
        case 'CA':
          $params['country'] = 'CA';
          break;
        default:
          $params['country'] = 'other';
          break;
      }
    }

    if (empty($params['country']) && !empty($params['postalcode'])) {
      // get the country name form the postal code.
      if ( preg_match('/^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/i', $params['postalcode'] ) ) {
        $params['country'] = 'CA';
      }
      else {
        $params['country'] = 'US';
      }
    }

    // merge all params to one array
    $body_params = array_merge( $feed_settings, $custom_params, $params );
    $this->log_debug( __METHOD__ . '(): Calling - Parameters ' . print_r( $body_params, true ) );
    if ( ! empty( $body_params['00N33000002wn14'] ) ) {
      $body_params['00N33000002wn14'] = date( 'm/d/Y', strtotime( $body_params['00N33000002wn14'] ) );
    }
    $body_params['00N33000002wn1F'] = $this->stateName2isoFormat( $body_params['00N33000002wn1F'] );
    $body_params['00N33000002wn18'] = $this->stateName2isoFormat( $body_params['00N33000002wn18'] );

    //fix
    foreach ( ['firstname','lastname','emailaddress','postalcode'] as $value) {
      if($body_params[$value] == 'NoN' || $body_params[$value] == 'NoN@test.com'){
        $body_params[$value] = '';
      }
    }
    if(!empty($body_params['overwrite_interest'])){
      $output = $body_params['overwrite_interest'];
      foreach ($body_params as $key => $value) {
        $tagToReplace = '{'.$key.'}';
        $output = str_replace($tagToReplace, $value, $output);
      }
      if(!empty($body_params['interests1'])){
        switch ($body_params['interests1']) {
          case 'Highlife series':
            $output = empty($body_params['interests2']) ? $body_params['interests1'] : str_replace('{interests-model}', $body_params['interests2'], $output);
            break;
          case 'Limelight series':
            $output = empty($body_params['interests3']) ? $body_params['interests1'] : str_replace('{interests-model}', $body_params['interests3'], $output);
            break;
          case 'Hot Spot series':
            $output = empty($body_params['interests4']) ? $body_params['interests1'] : str_replace('{interests-model}', $body_params['interests4'], $output);
            break;
          default:
            $output = $body_params['interests1'];
            break;
        }
      }
      if($body_params['overwrite_interest'] != $output){
        $body_params['modelinterest'] = $output;
      }
      unset($body_params['interests1']);
      unset($body_params['interests2']);
      unset($body_params['interests3']);
      unset($body_params['interests4']);
    }
    unset($body_params['overwrite_interest']);

    // dont_send
    if ( isset( $feed_settings['dont_send'] ) && ! empty( $feed_settings['dont_send'] ) ) {
      print_r( $body_params );
      exit();
    }

    $api           = new MiniSalesforceAPI();
    $response      = $api->send( $body_params );
    $current_user  = wp_get_current_user();
    $response_info = $api->getResponseInfo();

    if ( $current_user->data->ID == 1 && $_GET['debug'] == 1 ) {
      print_r( $response );
      print_r( $response_info );
      exit();
    }
    $this->log_debug( __METHOD__ . '(): Calling - response ' . print_r( $response, true ) );
    $this->log_debug( __METHOD__ . '(): Calling - response info ' . print_r( $response_info, true ) );
    // $api->host           = $settings['luminate_servlet'];
    // $api->api_key        = $settings['luminate_api_key'];
    // $api->short_name     = $settings['luminate_organization'];
    // $api->login_name     = $settings['luminate_api_user'];
    // $api->login_password = $settings['luminate_api_pass'];
    // $this->api           = $api;
    // Send the values to the third-party service.
  }

  /**
   * Custom format the phone type field values before they are returned by $this->get_field_value().
   *
   * @param array          $entry The Entry currently being processed.
   * @param string         $field_id The ID of the Field currently being processed.
   * @param GF_Field_Phone $field The Field currently being processed.
   *
   * @return string
   */
  public function get_phone_field_value( $entry, $field_id, $field ) {
    // Get the field value from the Entry Object.
    $field_value = rgar( $entry, $field_id );
    // If there is a value and the field phoneFormat setting is set to standard reformat the value.
    if ( ! empty( $field_value ) && $field->phoneFormat == 'standard' && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $field_value, $matches ) ) {
      $field_value = sprintf( '%s-%s-%s', $matches[1], $matches[2], $matches[3] );
    }
    return $field_value;
  }

  // # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------
  /**
   * Return the scripts which should be enqueued.
   *
   * @return array
   */
  public function scripts() {
    $scripts = array(
      // array(
      // 'handle'  => 'my_script_js',
      // 'src'     => $this->get_base_url() . '/js/my_script.js',
      // 'version' => $this->_version,
      // 'deps'    => array( 'jquery' ),
      // 'strings' => array(
      // 'first'  => esc_html__( 'First Choice', 'simplefeedaddon' ),
      // 'second' => esc_html__( 'Second Choice', 'simplefeedaddon' ),
      // 'third'  => esc_html__( 'Third Choice', 'simplefeedaddon' ),
      // ),
      // 'enqueue' => array(
      // array(
      // 'admin_page' => array( 'form_settings' ),
      // 'tab'        => 'simplefeedaddon',
      // ),
      // ),
      // ),
    );
    return array_merge( parent::scripts(), $scripts );
  }

  /**
   * Return the stylesheets which should be enqueued.
   *
   * @return array
   */
  public function styles() {
    $styles = array(
      // array(
      // 'handle'  => 'my_styles_css',
      // 'src'     => $this->get_base_url() . '/css/my_styles.css',
      // 'version' => $this->_version,
      // 'enqueue' => array(
      // array( 'field_types' => array( 'poll' ) ),
      // ),
      // ),
    );
    return array_merge( parent::styles(), $styles );
  }

  // # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------
  /**
   * Creates a custom page for this add-on.
   */
  // public function plugin_page() {
  // echo 'This page appears in the Forms menu';
  // }
  /**
   * Configures the settings which should be rendered on the add-on settings tab.
   *
   * @return array
   */
  public function plugin_settings_fields() {
    return array(
      array(
        'title'  => esc_html__( 'Mini Sales Add-On Settings', 'simplefeedaddon' ),
        'fields' => array(
          array(
            'name'    => 'textbox',
            'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
            'label'   => esc_html__( 'This is the label', 'simplefeedaddon' ),
            'type'    => 'text',
            'class'   => 'small',
          ),
        ),
      ),
    );
  }

  /**
   * Configures the settings which should be rendered on the feed edit page in the Form Settings > Simple Feed Add-On area.
   *
   * @return array
   */
  public function feed_settings_fields() {
    return array(
      array(
        'title'  => esc_html__( 'Mini Sales Feed Settings', 'simplefeedaddon' ),
        'fields' => array(
          array(
            'label'   => esc_html__( 'Feed name', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'feedName',
            'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Organization ID', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'orgid',
            'tooltip' => esc_html__( 'Organization ID', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Reaturn URL', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'retURL',
            'tooltip' => esc_html__( 'Reaturn URL', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Don\'t send', 'simplefeedaddon' ),
            'type'    => 'checkbox',
            'name'    => 'dont_send',
            'tooltip' => esc_html__( 'Don\'t send to salesforce', 'simplefeedaddon' ),
            'choices' => array(
              array(
                'label' => esc_html__( 'Enabled', 'simplefeedaddon' ),
                'name'  => 'dont_send',
              ),
            ),
          ),
          array(
            'label'   => esc_html__( 'Debug mode', 'simplefeedaddon' ),
            'type'    => 'checkbox',
            'name'    => 'debug',
            'tooltip' => esc_html__( 'Debug mode', 'simplefeedaddon' ),
            'choices' => array(
              array(
                'label' => esc_html__( 'Enabled', 'simplefeedaddon' ),
                'name'  => 'debug',
              ),
            ),
          ),
          array(
            'label'   => esc_html__( 'Debug Email', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'debugEmail',
            'tooltip' => esc_html__( 'Debug Email', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( '00N33000002wr3Q', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => '00N33000002wr3Q',
            'tooltip' => esc_html__( 'Some identifier', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'leadtype', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'leadtype',
            'tooltip' => esc_html__( 'LeadType (Valid options are hot_spring, caldera, endless_pools, fantasy, freeflow)', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'leadsource', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'leadsource',
            'tooltip' => esc_html__( 'LeadSource (This will be used to distinguish between the forms)', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'formdescription', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'formdescription',
            'tooltip' => esc_html__( 'FormDescription (This will be used to distinguish between the forms)', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Custom endpoint', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'custom_endpoint',
            'tooltip' => esc_html__( 'Custom Endpoint URL. If empty, https://www.salesforce.com/servlet/servlet.WebToCase?encoding=UTF-8 will be used instead', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Initial Lead Source', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'LeadSourceOriginal',
            'tooltip' => esc_html__( 'Initial Lead Source value for campaign', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Request Type', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'RequestType',
            'tooltip' => esc_html__( 'Request Type value for campaign', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Campaign Name', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'CampaignName',
            'tooltip' => esc_html__( 'Campaign Name value for campaign', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Campaign Date', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'CampaignDate',
            'tooltip' => esc_html__( 'Campaign Date value for campaign', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Market Segment', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'MarketSegment',
            'tooltip' => esc_html__( 'Market Segment value for campaign', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'CustomerVertical', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'CustomerVertical',
            'tooltip' => esc_html__( 'Customer Vertical value for campaign', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'NationalCampaign', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'NationalCampaign',
            'tooltip' => esc_html__( 'Marketing campaign source', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Source (for Prospect Endpoint)', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'Source',
            'tooltip' => esc_html__( 'Source (for Prospect Endpoint)', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'PrimaryBrandInterest (for Prospect Endpoint)', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'PrimaryBrandInterest',
            'tooltip' => esc_html__( 'Source (for Prospect Endpoint). Valid options are hot_spring, caldera,
endless_pools, fantasy, freeflow or other', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Template user interest', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'overwrite_interest',
            'tooltip' => esc_html__( 'for ex. "{interests1} - {interests-model}". Remember if this field is not empty, the points interests1, interests2, interests3, interests4 will be deleted', 'simplefeedaddon' ),
            'class'   => 'small',
          ),
          array(
            'label'   => esc_html__( 'Custom Key Value', 'simplefeedaddon' ),
            'type'    => 'text',
            'name'    => 'custom_key_value',
            'tooltip' => esc_html__( 'Custom Key-Value for ex: test_key1:value1,test_key2:value2', 'simplefeedaddon' ),
            'class'   => 'small',
          ),


          // 'orgid' => '00D40000000N01h',
          // 'retURL' => 'http://calderaspas.com/shopping-tools/customer-service/success',
          // 'debug' => '1',
          // 'debugEmail' => 'admin8@eightcloud.com',
          // '00N33000002wr3Q' => 'Caldera',
          array(
            'name'      => 'mappedFields',
            'label'     => esc_html__( 'Constituent Map Fields', 'gfeloqua' ),
            'type'      => 'dynamic_field_map',
            'field_map' => $this->constituent_field_map(),
            'tooltip'   => '<h6>' . esc_html__( 'Constituent Map Fields', 'simplefeedaddon' ) . '</h6>' . esc_html__( 'Associate your Salesforce constituent fields with the appropriate Gravity Form fields.', 'simplefeedaddon' ),
          ),

          array(
            'name'           => 'condition',
            'label'          => esc_html__( 'Condition', 'simplefeedaddon' ),
            'type'           => 'feed_condition',
            'checkbox_label' => esc_html__( 'Enable Condition', 'simplefeedaddon' ),
            'instructions'   => esc_html__( 'Process this Salesforce feed if', 'simplefeedaddon' ),
          ),
        ),
      ),
    );
  }
  /**
   * Configures which columns should be displayed on the feed list page.
   *
   * @return array
   */
  public function feed_list_columns() {
    return array(
      'feedName' => esc_html__( 'Name', 'simplefeedaddon' ),
      'orgid'    => esc_html__( 'Organization ID', 'simplefeedaddon' ),
    );
  }
  /**
   * Format the value to be displayed in the mytextbox column.
   *
   * @param array $feed The feed being included in the feed list.
   *
   * @return string
   */
  public function get_column_value_orgid( $feed ) {
    return '<b>' . rgars( $feed, 'meta/orgid' ) . '</b>';
  }
  /**
   * Prevent feeds being listed or created if an api key isn't valid.
   *
   * @return bool
   */
  public function can_create_feed() {
    // Get the plugin settings.
    $settings = $this->get_plugin_settings();
    // Access a specific setting e.g. an api key
    // $key = rgar( $settings, 'apiKey' );
    return true;
  }

  /**
   * Returns the combined value of the specified Address field.
   * Street 2 and Country are the only inputs not required by MailChimp.
   * If other inputs are missing MailChimp will not store the field value, we will pass a hyphen when an input is empty.
   * MailChimp requires the inputs be delimited by 2 spaces.
   *
   * @param array  $entry The entry currently being processed.
   * @param string $field_id The ID of the field to retrieve the value for.
   *
   * @return string
   */
  public function get_full_address( $entry, $field_id ) {
    $street_value  = str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.1' ) ) );
    $street2_value = str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.2' ) ) );
    $city_value    = str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.3' ) ) );
    $state_value   = str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.4' ) ) );
    $zip_value     = trim( rgar( $entry, $field_id . '.5' ) );
    $country_value = trim( rgar( $entry, $field_id . '.6' ) );

    if ( ! empty( $country_value ) ) {
      $country_value = GF_Fields::get( 'address' )->get_country_code( $country_value );
    }

    $address = array(
      ! empty( $street_value ) ? $street_value : '-',
      $street2_value,
      ! empty( $city_value ) ? $city_value : '-',
      ! empty( $state_value ) ? $state_value : '-',
      ! empty( $zip_value ) ? $zip_value : '-',
      $country_value,
    );

    return implode( '  ', $address );
  }

  static function is_valid_email( $email ) {
    return (bool) filter_var( $email, FILTER_VALIDATE_EMAIL );
  }
}

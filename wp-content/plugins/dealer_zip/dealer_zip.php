<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
Plugin Name: Dealer ZIP
Description: Dealer ZIP search
*/
if ( ! class_exists( 'WP_Dealer_Zip' ) ) {

	class WP_Dealer_Zip {
		/**
		 * Class constructor
		 */
		function __construct() {
			add_action( 'init', array( $this, 'alter_request' ), 1 );
			add_action( 'rest_api_init', array( $this, 'conversion_monk_api_register_routes') );
			$this->define_constants();
			$this->includes();
			$this->plugin_settings();
			$this->plugin_shortcodes();

			add_action( 'pre_get_posts', array( $this, 'search_filter' ) );
			register_activation_hook( __FILE__, array( $this, 'install' ) );
			add_action( 'gform_after_submission', array( $this, 'gform_field_id_integration' ), 10, 2 );

			add_filter( 'query_vars', array( $this, 'parameter_queryvars' ), 10, 1 );
			add_filter( 'dz_info_block', array( $this, 'dz_info_block' ), 10, 1 );

			$this->fieldMap = array(
				// form key => MIS key
				'first_name'             => 'first_name',
				'last_name'              => 'last_name',
				'country'                => 'primary_address_country',
				'thoroughfare'           => 'primary_address_street',
				'locality'               => 'primary_address_city',
				'administrative_area'    => 'primary_address_state',
				'postal_code'            => 'primary_address_postalcode',
				'zipcode'                => 'primary_address_postalcode',
				'phone'                  => 'phone_home',
				'email'                  => 'email',
				'utopia'                 => 'modelinterest[]',
				'paradise'               => 'modelinterest[]',
				'vacanza'                => 'modelinterest[]',
				'spa_interest'           => 'modelinterest',
				'timeframe_for_purchase' => 'purchasehorizon',
				'yes_please_sign_me_up_for_exclusive_promotions_and_product_news' => 'enews',
				'yes_please_sign_me_up'  => 'enews',
				'cid'                    => 'trackingcode',
				'source'                 => 'source',
				'campaignid'             => 'campaignid',
				'hp_name'                => 'hp_name',
				'hp_address'             => 'hp_address',
				'googleclientid'         => 'googleclientid',
				'googletransactionid'    => 'googletransactionid',
				'marinid'                => 'marinid',
				'utm_campaign'           => 'utmcampaign',
				'utm_source'             => 'utmsource',
				'utm_medium'             => 'utmmedium',
				'company'                => 'company',
				'name'                   => 'description',
				'initial_leadsource'     => 'initialleadsource',
				'request_type'           => 'requesttype',
				'campaign_name'          => 'campaignname',
				'campaign_date'          => 'campaigndate',
				'nationalcampaign_name'  => 'nationalcampaignname',
				'brand_name'             => 'brandname',
				'market_segment'         => 'marketsegment',
				'customer_vertical'      => 'customervertical',
				'keywords'               => 'keywords',
				'mailed_infokit'         => 'mailed_infokit',
				'hotspringOptIn'         => 'enews',
			);

		}

		function dz_info_block( $zipPostal ) {

			if ( empty( $zipPostal ) ) {
				return '';
			}
			$country = 'US';
      if ($this->isPostalCode($zipPostal)){
        $country = 'CA';
      }

			// Get dealer info
			$dealer_param = array(
				'dealerID'          => '',
				'dealerName'        => '',
				'dealerAddress1'    => '',
				'dealerAddress2'    => '',
				'dealerCity'        => '',
				'dealerState'       => '',
				'dealerPostcode'    => '',
				'dealerCountry'     => '',
				'dealerPhoneNumber' => '',
				'mapURL'            => '',
				'dealerWebsite'     => '',
				'dealerEmail'       => '',
				'mondayHours'       => '',
				'tuesdayHours'      => '',
				'wednesdayHours'    => '',
				'thursdayHours'     => '',
				'fridayHours'       => '',
				'saturdayHours'     => '',
				'sundayHours'       => '',
			);

			// Get dealer info
			if ( ! empty( $zipPostal ) ) {
				  // Get dealer did from salesforce by zipcode
				  $did = $this->salesforce->get_dealer_info( $zipPostal, $country );
				  // $did = '03892';
				  // get WordPress objest of dealer
				  $dealer = array_pop( _dealer_get_posts_by_dealership_id( $did ) );

				if ( ! empty( $dealer ) ) {
					$coordinats   = get_post_meta( $dealer->ID, 'dealership_coordinates', true );
					$dealer_param = array(
						'dealerID'            => $dealer->dealership_id,
						'dealerName'          => get_post_meta( $dealer->ID, 'dealership_name', true ),
						'dealerAddress1'      => get_post_meta( $dealer->ID, 'dealership_address_1', true ),
						'dealerAddress2'      => get_post_meta( $dealer->ID, 'dealership_address_2', true ),
						'dealerCity'          => get_post_meta( $dealer->ID, 'dealership_city', true ),
						'dealerState'         => get_post_meta( $dealer->ID, 'dealership_state_code', true ),
						'dealerPostcode'      => get_post_meta( $dealer->ID, 'dealership_zip', true ),
						'dealerCountry'       => get_post_meta( $dealer->ID, 'dealership_country_code', true ),
						'dealerCountryCode'   => get_post_meta( $dealer->ID, 'dealership_country_code', true ),
						'dealerPhoneNumber'   => get_post_meta( $dealer->ID, 'dealer_phone', true ),
						'dealerEmail'         => get_post_meta( $dealer->ID, 'dealer_email', true ),
						'dealerWebsite'       => get_post_meta( $dealer->ID, 'dealer_website', true ),

						'dealerSocialMedia'   => get_post_meta( $dealer->ID, 'dealer_social_media', true ),
						'dealerFacebook'      => get_post_meta( $dealer->ID, 'dealer_facebook', true ),
						'dealerTwitter'       => get_post_meta( $dealer->ID, 'dealer_twitter', true ),
						'dealerLinked'        => get_post_meta( $dealer->ID, 'dealer_linked', true ),
						'dealerYoutube'       => get_post_meta( $dealer->ID, 'dealer_youtube', true ),
						'dealerFacebookLink'  => get_post_meta( $dealer->ID, 'dealer_fb_link', true ),
						'dealerTwitterLink'   => get_post_meta( $dealer->ID, 'dealer_twitter_link', true ),
						'dealerLinkedLink'    => get_post_meta( $dealer->ID, 'dealer_linked_link', true ),
						'dealerYoutubeLink'   => get_post_meta( $dealer->ID, 'dealer_youtube_link', true ),
						'dealerHours' 				=> get_post_meta( $dealer->ID , 'edl_dealer_hours', true),
						// 'dealerHoursMonFri'   => get_post_meta( $dealer->ID, 'mon_fri', true ),
						// 'dealerHoursSaturday' => get_post_meta( $dealer->ID, 'hours_saturday', true ),
						// 'dealerHoursSunday'   => get_post_meta( $dealer->ID, 'hours_sunday', true ),

						'mapURL'              => '(' . $coordinats['lat'] . ',' . $coordinats['lng'] . ')',
						'lat'                 => $coordinats['lat'],
						'lng'                 => $coordinats['lng'],
					// @TODO ADD this params of dealer after adding adTrack
					// 'dealerWebsite' => array(),
					// 'dealerEmail' => array('email','text'),
					// 'mondayHours' => array(),
					// 'tuesdayHours' => array(),
					// 'wednesdayHours' => array(),
					// 'thursdayHours' => array(),
					// 'fridayHours' => array(),
					// 'saturdayHours' => array(),
					// 'sundayHours' => array(),
					);
					$dealer_hours_formatted = '';
          if ($dealer_param['dealerHoursMonFri']) {
            $dealer_hours_formatted = 'M-F: ' . $dealer_param['dealerHoursMonFri'];
          }
          if ($dealer_param['dealerHoursSaturday'] && $dealer_param['dealerHoursMonFri']) {
            if ($dealer_param['dealerHoursSaturday'] == $dealer_param['dealerHoursMonFri']) {
              $dealer_hours_formatted = 'M-Sat: ' . $dealer_param['dealerHoursMonFri'];
            }
            else {
              $dealer_hours_formatted .= '; Sat: ' . $dealer_param['dealerHoursSaturday'];
            }
          }
          if ($dealer_param['dealerHoursSunday']) {
            $dealer_hours_formatted .= '; Sun: ' . $dealer_param['dealerHoursSunday'];
					}

					$dealer_param['dealer_hours_formatted'] = $dealer_hours_formatted;

				}
			}

			if ( empty( $dealer_param['lat'] ) ) {
				return '';
			}
			$location = array(
				'lat' => $dealer_param['lat'],
				'lng' => $dealer_param['lng'],
			);

			// include scripts for map
            wp_enqueue_script('main_map');
            wp_enqueue_script('map_init');

			ob_start();
			// add dealer info block
			require WPDZ_PLUGIN_DIR . 'inc/dz-info-block.php';
			$variable = ob_get_clean();
			return $variable;
		}

		/**
		 * Register the /wp-json/myplugin/v1/foo route
		 *
		 * curl POST -F zipPostal=92008 http://hswp.vm/wp-json/api/conversion-monk--dealer
		 */
		public function conversion_monk_api_register_routes() {
			register_rest_route('api', 'conversion-monk--dealer', array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array(  $this , 'conversion_monk_api' ),
			) );
		}

		/**
		 * register_rest_route callback
		 *
		 * @param $request
		 * @return void
		 */
		public function conversion_monk_api($request) {
			$params = $request->get_params();
			$did = $this->salesforce->get_dealer_info( $params['zipPostal'] );
			$dealer = array_pop( _dealer_get_posts_by_dealership_id( $did ) );
			if(!empty($dealer)){
				$coordinats = get_post_meta( $dealer->ID , 'dealership_coordinates', true );
				$dealer_param = array(
					'dealerID' => $dealer->dealership_id,
					'dealerName' => get_post_meta( $dealer->ID , 'dealership_name', true ),
					'dealerAddress1' => get_post_meta( $dealer->ID , 'dealership_address_1', true ),
					'dealerAddress2' => get_post_meta( $dealer->ID , 'dealership_address_2', true ),
					'dealerCity' => get_post_meta( $dealer->ID , 'dealership_city', true ),
					'dealerState' => get_post_meta( $dealer->ID , 'dealership_state_code', true ),
					'dealerPostcode' => get_post_meta( $dealer->ID , 'dealership_zip', true ),
					'dealerCountry' => get_post_meta( $dealer->ID , 'dealership_country_code', true ),
					'dealerPhoneNumber' => get_post_meta( $dealer->ID , 'dealer_phone', true ),
					'dealerWebsite' => get_post_meta( $dealer->ID , 'dealer_website', true ),
					'dealerEmail' => get_post_meta( $dealer->ID , 'dealer_email', true ),
					'mapURL' => $coordinats['lat'] . ',' . $coordinats['lng'],
					'dealerHours' => get_post_meta( $dealer->ID , 'edl_dealer_hours', true),
					// 'mondayHours' => get_post_meta( $dealer->ID , 'mon_fri', true ),
					// 'tuesdayHours' => get_post_meta( $dealer->ID , 'mon_fri', true ),
					// 'wednesdayHours' => get_post_meta( $dealer->ID , 'mon_fri', true ),
					// 'thursdayHours' => get_post_meta( $dealer->ID , 'mon_fri', true ),
					// 'fridayHours' => get_post_meta( $dealer->ID , 'mon_fri', true ),
					// 'saturdayHours' => get_post_meta( $dealer->ID , 'hours_saturday', true ),
					// 'sundayHours' => get_post_meta( $dealer->ID , 'hours_sunday', true ),
					'customerCountry' => get_post_meta( $dealer->ID , 'dealership_country_code', true ),
				);
				wp_send_json( [
					'status' => 1,
					'data' => $dealer_param,
				] );
			}
			else {
				wp_send_json( [
					'status' => 0,
				] );
			}
		}

		// alter $_GET['zip']
		public function alter_request() {
      		$zip_search = sanitize_text_field((!empty($_GET['zip-search']) ? $_GET['zip-search'] : '' ));
			if ( $this->isZipCode( $zip_search ) ) {
				$did = $this->salesforce->get_dealer_info( $zip_search, 'US' );
			}
			if ( $this->isPostalCode( $zip_search ) ) {
				$did = $this->salesforce->get_dealer_info( $zip_search, 'CA' );
      		}
      		if( !empty($did) ){
				setcookie( 'dealer_did', $did, strtotime( '+300 days' ), COOKIEPATH, COOKIE_DOMAIN );
				$dealer = array_pop( _dealer_get_posts_by_dealership_id( $did ) );
        		if ( isset( $dealer->guid ) ) {
						$redirect_url = get_permalink( $dealer->ID );
						wp_safe_redirect(  $redirect_url, 302 );
            		exit();
        		}
      		}
			$state_info = db_code_of_state($zip_search);
			if(!empty($state_info['state_code'])){
				wp_redirect( '/hot-tub-dealers/'.$state_info['country_name'].'/'.$state_info['state_code'].'/', $status = 302 );
				exit();
			}
		}
		// add new query vars
		public function parameter_queryvars( $qvars ) {
			$qvars[] = 'zipPostal';
			$qvars[] = 'zip-search';
			// check is this isn't front page
			// is_home() and is_front_page() not working
			$request = explode( '?', $_SERVER['REQUEST_URI'] );
			if ( $request[0] !== '/' ) {
				$qvars[] = 'utm_campaign';
				$qvars[] = 'utm_source';
				$qvars[] = 'utm_medium';
				$qvars[] = 'utm_term';
				$qvars[] = 'utm_content';
			}
			return $qvars;
		}

		/**
		 * Given a form entry, build an array of entry data keyed with its field IDs.
		 *
		 * The resulting array will include any value from $entry on a $form field with an assigned fieldID
		 * property. Complex fields are handled as long as the field IDs are passed as a comma-separated
		 * list _and_ we have enough IDs for each non-hidden input within a field.
		 *
		 * For example, if $form has a GF_Field_Name field containing a first and last name, but we only
		 * provide a single field ID (e.g. "name"), only the first name would be saved. Instead, we want to
		 * be sure we're using field IDs like "firstname, lastname" to ensure that all data gets mapped.
		 *
		 * @param array $entry The Gravity Forms entry object.
		 * @param array $form  The Gravity Forms form object.
		 * @return array An array of entry values from fields with IDs attached.
		 */
		public function get_mapped_fields( $entry, $form ) {
			$mapping = array();

			foreach ( $form['fields'] as $field ) {
				if ( ! isset( $field['fieldID'] ) || ! $field['fieldID'] ) {
					continue;
				}

				// Explode field IDs.
				$field_ids = array_map( 'trim', explode( ',', $field['fieldID'] ) );

				// We have a complex field, with multiple inputs.
				if ( ! empty( $field['inputs'] ) ) {
					foreach ( $field['inputs'] as $input ) {
						if ( isset( $input['isHidden'] ) && $input['isHidden'] ) {
							  continue;
						}

						$field_id = array_shift( $field_ids );

						// If $field_id is empty, don't map this input.
						if ( ! $field_id ) {
							continue;
						}

						// Finally, map this value based on the $field_id and $input['id'].
						$mapping[ $field_id ] = $entry[ $input['id'] ];
					}
				} else {
					$mapping[ $field_ids[0] ] = $entry[ $field['id'] ];
				}
			}

			return $mapping;
		}

		/**
		 * Integrate with some third-party service.
		 *
		 * @param array $entry The Gravity Forms entry.
		 * @param array $form  The Gravity Forms form.
		 */
		public function gform_field_id_integration( $entry, $form ) {
			$mapped_fields = $this->get_mapped_fields( $entry, $form );
			if ( ! empty( $mapped_fields ) ) {
			}
		}

    // USA zip code
    public function isZipCode($code){
      if((bool)preg_match('/^([0-9]{5})(-[0-9]{4})?$/i',$code)){
        return TRUE;
      }
      return FALSE;
    }

    // Canada postal code
    public function isPostalCode($code){
      $expression = '/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/';
      if((bool)preg_match($expression, $code)){
        return TRUE;
      }
      return FALSE;
    }

		// redirect to code
		public function redirectToStateCode( $code ) {
			$state_list = dp_list_of_state();
			$state_code = strtoupper($code);
			$state_name = $state_list['USA'][$state_code];
			if( !empty( $state_name ) ){
				$redirect_url = get_home_url( null,'/hot-tub-dealers/USA/' . $state_code );
				wp_redirect( $redirect_url, $status = 302 );
				exit();
			}
			$state_name = $state_list['Canada'][$state_code];
			if( !empty( $state_name ) ){
				$redirect_url = get_home_url( null,'/hot-tub-dealers/Canada/' . $state_code );
				wp_redirect( $redirect_url, $status = 302 );
				exit();
			}
    }

		/**
		 * alter the search code request.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function search_filter( $query ) {
			if ( ! is_admin() && $query->is_main_query() ) {
				if( isset( $query->query_vars['zip-search'] ) && !empty($query->query_vars['zip-search']) ){
					$query->query_vars['zip-search'] = trim( $query->query_vars['zip-search'] );
					//check and redirect if the input is state code
					$this->redirectToStateCode( $query->query_vars['zip-search'] );
				}
				if ( $query->is_search ) {
					if ( isset( $query->query_vars['s'] ) ) {
            $query->query_vars['s'] = trim( $query->query_vars['s'] );
						//check and redirect if the input is state code
						$this->redirectToStateCode( $query->query_vars['s'] );
						if ( $this->isZipCode( $query->query_vars['s'] ) ) {
							$did = $this->salesforce->get_dealer_info( $query->query_vars['s'], 'US' );
						}
						if ( $this->isPostalCode( $query->query_vars['s'] ) ) {
							$did = $this->salesforce->get_dealer_info( $query->query_vars['s'], 'CA' );
						}
						$dealer = array_pop( _dealer_get_posts_by_dealership_id( $did ) );
						if ( isset( $dealer->guid ) ) {
								$redirect_url = get_permalink( $dealer->ID );
								setcookie( 'dealer_did', $did, strtotime( '+300 days' ), COOKIEPATH, COOKIE_DOMAIN );
								wp_safe_redirect(  $redirect_url, 302 );
							  exit();
						}
					}
				}
			}
		}

		/**
		 * Shortcodes init hook.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function plugin_shortcodes() {
			add_shortcode( 'dealer-zip-form', array( $this, 'dealer_zip_form_shortcode' ) );
      add_shortcode( 'dz-info-block', array( $this, 'dealer_zip_info_block_shortcode' ) );
      add_shortcode( 'dealer-zip-form-new', array( $this, 'dealer_zip_form_new_shortcode' ) );
		}

		/**
		 * Setup plugin constants.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function define_constants() {
			if ( ! defined( 'WPDZ_PLUGIN_DIR' ) ) {
				define( 'WPDZ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}
		}

		/**
		 * Include the required files.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function includes() {
			require_once WPDZ_PLUGIN_DIR . 'inc/wpdz-functions.php';
			require_once WPDZ_PLUGIN_DIR . 'inc/class-salesforce.php';

			if ( is_admin() || defined( 'WP_CLI' ) && WP_CLI ) {
				require_once WPDZ_PLUGIN_DIR . 'admin/class-admin.php';
			}
		}

		/**
		 * Setup the plugin settings.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function plugin_settings() {
			$this->salesforce = new WPDZ_Salesforce();
		}

		/**
		 * Install the plugin data.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function install( $network_wide ) {
			require_once WPDZ_PLUGIN_DIR . 'inc/install.php';
			wpdz_install( $network_wide );
		}

		// Dealer info block short code [dz-info-block zipPostal=defult-zip-code]
		public function dealer_zip_info_block_shortcode( $attributes ) {
			$default_zip = isset($attributes['zipPostal']) ? $attributes['zipPostal'] : NULL;
			$zipPostal = get_query_var( 'zipPostal', $default_zip );
			return $this->dz_info_block( $zipPostal );
		}

		// [addtoany url="http://example.com/page.html" title="Some Example Page"]
		public function dealer_zip_form_shortcode( $attributes ) {
			extract(
			shortcode_atts( array(
			'placeholder'   => 'Zip Code / State',
			'button' => 'Find Dealer',
			), $attributes ) );
			

      return '
<form class="form-inline zip-serch" role="zip-search" method="get" action="/hot-tub-dealers/" >
		<div class="form-group col-lg-8 offset-lg-2 col-md-12">
      <div class="input-group col-lg-8 offset-lg-2 col-md-12">
        <input type="text" name="zip-search" value="'.sanitize_text_field($_GET['zip-search']).'" class="form-control form-control-sm" placeholder="Zip Code / State" aria-label="Zip Code" aria-describedby="basic-addon2">
        <div class="input-group-append">
        <button class="btn btn-primary" type="submit">Find Dealer</button>
        </div>
			</div>
		</div>
</form>';
		}
		
		public function dealer_zip_form_new_shortcode( $attributes ) {
      extract(
        shortcode_atts( array(
        'placeholder'   => 'Zip Code',
        'button' => 'Find My Dealer <i class="fa fa-play-circle" aria-hidden="true"></i>',
        ), $attributes ) 
      );			

      return '
<form class="form-inline zip-serch" role="zip-search" method="get" action="/hot-tub-dealers/" >
  <div class="form-group-row">
    <div class="col-input">
      <input type="text" name="zip-search" value="' . sanitize_text_field($_GET['zip-search']) . '" class="form-control" placeholder="'.$placeholder.'" aria-label="Zip Code" aria-describedby="basic-addon2">
    </div>
    <div class="col-btn">
      <button type="submit" class="btn btn-primary">' . $button . '</button>
    </div>
  </div>
</form>
';
		}

	}

	$GLOBALS['wpdz'] = new WP_Dealer_Zip();
}
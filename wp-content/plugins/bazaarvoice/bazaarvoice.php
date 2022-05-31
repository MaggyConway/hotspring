<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// function dd( $text ) {
//   if(is_array($text)){
//     $text = print_r($text,1);
//   }
//   $fp = fopen( plugin_dir_path( __FILE__ ) . 'log.txt', 'a');
//   fwrite($fp, $text . "\n");
//   fclose($fp);
// }

/*
Plugin Name: Bazaarvoice
Description: Bazaarvoice integration
*/
if ( ! class_exists( 'WP_Bazaarvoice' ) ) {

	class WP_Bazaarvoice {

		/**
		 * Class constructor
		 */
		function __construct() {

			$this->define_constants();
			$this->includes();

			$this->plugin_settings();
			$this->plugin_shortcodes();
			$this->fields();

			// register_activation_hook( __FILE__, array( $this, 'install' ) );
			// Register the script
			$script_url = '//hotspring.ugc.bazaarvoice.com/static/0526-en_us/bvapi.js';
			// if ($GLOBALS['_SERVER']['SERVER_NAME'] != 'www.hotspring.com') {
			// 	$script_url = '//hotspring.ugc.bazaarvoice.com/bvstaging/static/0526-en_us/bvapi.js';
			// }

			wp_register_script( 'bazaarvoice-js', $script_url, '', '', true );
			wp_register_script( 'bazaarvoice-js-custom', WPBV_PLUGIN_URL . '/assets/js/bazaarvoice-custom.js', array( 'bazaarvoice-js' ) );
			wp_register_script( 'bazaarvoice-js-collection', WPBV_PLUGIN_URL . '/assets/js/bazaarvoice-collection.js', array( 'bazaarvoice-js' ) );
			wp_register_script( 'bazaarvoice-js-submission', WPBV_PLUGIN_URL . '/assets/js/bazaarvoice-submission.js', array( 'bazaarvoice-js' ) );
			wp_register_script( 'bazaarvoice-js-product-reviews', WPBV_PLUGIN_URL . '/assets/js/bazaarvoice-product-reviews.js', array( 'bazaarvoice-js' ) );
			$this->restApi();
        }

        // restApi - setup rest router
		public function restApi() {
			add_action('rest_api_init', function () {
				register_rest_route( 'syndication/v1', 'bvid/(?P<bvid>.+)',array(
					'methods'  => 'GET',
					'callback' => [$this,'getBvId']
				  ));
			  });
    }

    // getBvId - rest api handler
		public function getBvId($request){
      //Remove all special characters
      $request['bvid'] = preg_replace('/[^A-Za-z0-9\-]/', '', $request['bvid']);
			$args = array(
				'post_type' => ['model','collections'],
				'meta_query' => array(
				array(
				'key' => 'bazaarvoice_id',
				'value' => $request['bvid'],
				)),
			);
			$query = new WP_Query($args);
			if ( empty( $query->posts[0] ) ) {
				return new WP_Error( 'bvid', 'Bazaarvoice ID', array( 'status' => 404 ) );
			}
			$post = $query->posts[0];
			$bazaarvoice_data  = get_field( 'bazaarvoice_data', $post->ID );
			$bazaarvoice_data  = json_decode( $bazaarvoice_data, true );
			$bazaarvoice_data['subjectID'] = $request['bvid'];
			$bazaarvoice_data['totalReviews'] = intval($bazaarvoice_data['totalReviews']);
			$bazaarvoice_data['averageRating'] = floatval($bazaarvoice_data['averageRating']);
			$bazaarvoice_data['reviewsUrl'] = 'https://hotspring.ugc.bazaarvoice.com/0526-en_us/'.$request['bvid'].'/reviews.htm?format=brandvoice';
			$bazaarvoice_data['averageRating'] = floatval($bazaarvoice_data['averageRating']);
			$bazaarvoice_data['totalReviews'] = intval($bazaarvoice_data['totalReviews']);
      // hack a json api for return javascript function
      header('Content-Type: application/javascript');
			print 'BVHandleSummaryResults('.stripcslashes(json_encode($bazaarvoice_data)).');';
			exit();
      return;
      // @todo think about how to pass javascript code correctly
			//return new WP_REST_Response($bazaarvoice_data, 200);
		}

		public function fields() {
			/**
			 * Add cusom fields
			 */
			if ( function_exists( 'register_field_group' ) ) {
				register_field_group(
					array(
						'id'         => 'bazaarvoice',
						'title'      => 'Bazaarvoice',
						'fields'     => array(
							array(
								'key'           => 'bazaarvoice_id',
								'label'         => 'Bazaarvoice ID',
								'name'          => 'bazaarvoice_id',
								'type'          => 'text',
								'instructions'  => 'This Bazaarvoice ID <a href="http://knowledge.bazaarvoice.com/wp-content/conversations/en_US/SEO/Configure_SEO.html">see</a> ',
								'default_value' => '',
								'placeholder'   => '',
								'prepend'       => '',
								'append'        => '',
								'formatting'    => 'none',
								'maxlength'     => '',
								'wrapper'       => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
							),
							// array(
							// 	'key'           => 'bazaarvoice_seo',
							// 	'label'         => 'Bazaarvoice SEO',
							// 	'name'          => 'bazaarvoice_seo',
							// 	'type'          => 'textarea',
							// 	'instructions'  => '',
							// 	'default_value' => '',
							// 	'placeholder'   => '',
							// 	'prepend'       => '',
							// 	'append'        => '',
							// 	'formatting'    => 'html',
							// 	'maxlength'     => '',
							// 	'wrapper'       => array(
							// 		'width' => '',
							// 		'class' => '',
							// 		'id'    => '',
							// 	),
							// ),
							array(
								'key'           => 'bazaarvoice_data',
								'label'         => 'Bazaarvoice Data',
								'name'          => 'bazaarvoice_data',
								'type'          => 'textarea',
								'instructions'  => 'Json format',
								'default_value' => '',
								'placeholder'   => '',
								'prepend'       => '',
								'append'        => '',
								'formatting'    => 'none',
								'maxlength'     => '',
								'wrapper'       => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
							),
						),
						'location'   => array(
							array(
								array(
									'param'    => 'post_type',
									'operator' => '==',
									'value'    => 'model',
									'order_no' => 0,
									'group_no' => 0,
								),
							),
							array(
								array(
									'param'    => 'post_type',
									'operator' => '==',
									'value'    => 'collections',
									'order_no' => 0,
									'group_no' => 0,
								),
							),
						),
						'options'    => array(
							'position'       => 'normal',
							'layout'         => 'default',
							'hide_on_screen' => array(),
						),
						'menu_order' => 0,
					)
				);
			}
		}

		public function plugin_shortcodes() {
			// add_shortcode( 'bazaarvoice-seo', array( $this, 'bazaarvoice_seo_shortcodes' ) );
			// add_shortcode( 'bazaarvoice-model', array( $this, 'bazaarvoice_model_shortcodes' ) );
			// add_shortcode( 'bazaarvoice-model-new', array( $this, 'bazaarvoice_model_shortcodes_new' ) );
			// add_shortcode( 'bazaarvoice-summary', array( $this, 'bazaarvoice_model_summary_shortcodes' ) );
			// add_shortcode( 'bazaarvoice-submission', array( $this, 'bazaarvoice_model_submission_shortcodes' ) );
			// add_shortcode( 'bazaarvoice-collection', array( $this, 'bazaarvoice_model_collection_shortcodes' ) );
			// add_shortcode( 'bazaarvoice-product-reviews', array( $this, 'bazaarvoice_product_reviews_shortcodes' ) );
		}

		// public function bazaarvoice_seo_shortcodes( $attributes ) {
		// 	return '';
		// }

		// public function getCollectionUrl( $name ) {
		// 	$review_url_string = '';
		// 	switch ( $name ) {
		// 		case 'highlife':
		// 			$review_url_string = get_site_url( null, 'why-hot-spring-hot-tubs/product-reviews/highlife' );
		// 			break;
		// 		case 'limelight':
		// 			$review_url_string = get_site_url( null, 'why-hot-spring-hot-tubs/product-reviews/limelight' );
		// 			break;
		// 		case 'hot-spot':
		// 			$review_url_string = get_site_url( null, 'why-hot-spring-hot-tubs/product-reviews/hotspot' );
		// 			break;
		// 		case 'nxt':
		// 			$review_url_string = get_site_url( null, 'why-hot-spring-hot-tubs/product-reviews/nxt' );
		// 			break;
		// 	}
		// 	return $review_url_string;
		// }

		// public function getCollectionBvId( $name ) {
		// 	$bvid = '';
		// 	// 'a2012', // Limelight
		// 	// 'a2013', // Highlife NXT
		// 	// 'a2011', // Hotspot
		// 	// 'a2010', // Highlife
		// 	switch ( $name ) {
		// 		case 'limelight':
		// 			$review_url_string = 'a2013';
		// 			break;
		// 		case 'highlife':
		// 			$review_url_string = 'a2012';
		// 			break;
		// 		case 'hot-spot':
		// 			$review_url_string = 'a2011';
		// 			break;
		// 		case 'nxt':
		// 			$review_url_string = 'a2010';
		// 			break;
		// 	}
		// 	return $bvid;
		// }
		
		// public function bazaarvoice_product_reviews_jsonld($post_id) {
		//   $post = get_post( $post_id );
		//   $bazaarvoice_data = get_field( 'bazaarvoice_data', $post->ID );
		//   $bazaarvoice_data = json_decode( $bazaarvoice_data, true );

    //   $product = array(
    //     '@context'        => 'http://schema.org',
    //     '@type'           => 'Product',
    //     'name'            => $post->post_title,
    //     'url'             => get_permalink( $post ),
    //   );

    //   if ($bazaarvoice_data['totalReviews'] != 0 ) {
    //     $AggregateRating = (object) array(
    //     '@type' => 'AggregateRating',
    //     '@id' => get_permalink( $post ) . '#ar',
    //     'ratingValue' => (string) round( $bazaarvoice_data['averageRating'], 1 ),
    //     'bestRating' => '5',
    //     'reviewCount' => (string) str_replace( ',', '', $bazaarvoice_data['totalReviews'] ),
    //     'itemReviewed' => [
    //       '@type' => 'Thing',
    //       'name' => $post->post_title,
    //     ],
    //     );
    //     $product['aggregateRating'] = $AggregateRating;
    //   }

    //   $product = (object)$product;
    //   return '<script type="application/ld+json">' . json_encode( $product ) . '</script>';
		// }

		// public function bazaarvoice_product_reviews_shortcodes( $attributes ) {
		// 	$id         = isset( $attributes['id'] ) ? $attributes['id'] : get_the_ID();
		// 	$subject_id = isset( $attributes['bazaarvoice'] ) ? $attributes['bazaarvoice'] : get_field( 'bazaarvoice_id', $id );
		// 	$disable_ldjson = isset( $attributes['disable_ldjson'] ) ? $attributes['disable_ldjson'] : false;
		// 	$url = get_site_url( null, '/submission-page' );

		// 	wp_localize_script(
		// 		'bazaarvoice-js-product-reviews', 'bazaarvoice', [
		// 			'url'        => $url,
		// 			'subject_id' => $subject_id,
		// 		]
		// 	);
		// 	wp_enqueue_script( 'bazaarvoice-js-product-reviews' );

		// 	$bv_container = '<div id="BVRRContainer"></div>';
		// 	if ($disable_ldjson === false) {
		// 		$bv_container .= $this->bazaarvoice_product_reviews_jsonld($id);
		// 	}
		// 	return $bv_container;
		// }

		// public function bazaarvoice_model_collection_shortcodes( $attributes ) {
		// 	$id                = isset( $attributes['id'] ) ? $attributes['id'] : get_the_ID();
		// 	$subject_id        = isset( $attributes['bazaarvoice'] ) ? $attributes['bazaarvoice'] : get_field( 'bazaarvoice_id', $id );
		// 	$post              = get_post( $id );
		// 	$model_collection  = get_field( 'model_collection', $post );
		// 	$parentProductName = $post->post_title;
		// 	$parentProductName = $model_collection->post_title;
		// 	$url               = get_site_url( null, '/submission-page' );

		// 	$review_url_string = $this->getCollectionUrl( $model_collection->post_name );

		// 	wp_localize_script(
		// 		'bazaarvoice-js-collection', 'bazaarvoice', [
		// 			'url'        => $url,
		// 			'subject_id' => $subject_id,
		// 		]
		// 	);
		// 	wp_enqueue_script( 'bazaarvoice-js-collection' );

		// 	$output = '
		// 	<div class="productReviews col-lg-10 offset-lg-1">
		// 		<div class="bv-block" id="bv-block-reviews">
		// 			<div class="reviews-container" id="BVRRContainer"></div>
		// 		</div>
		// 	</div>';
		// 	return $output;
		// }

		// public function bazaarvoice_model_shortcodes( $attributes ) {
		// 	$id                = isset( $attributes['id'] ) ? $attributes['id'] : get_the_ID();
		// 	$subject_id        = isset( $attributes['bazaarvoice'] ) ? $attributes['bazaarvoice'] : get_field( 'bazaarvoice_id', $id );
		// 	$post              = get_post( $id );
		// 	$model_collection  = get_field( 'model_collection', $post );
		// 	$parentProductName = $post->post_title;
		// 	$parentProductName = $model_collection->post_title;
		// 	$url               = get_site_url( null, '/submission-page' );

		// 	$review_url_string = $this->getCollectionUrl( $model_collection->post_name );

		// 	wp_localize_script(
		// 		'bazaarvoice-js-custom', 'bazaarvoice', [
		// 			'url'                    => $url,
		// 			'subject_id'             => $subject_id,
		// 			'parentProductName'      => $parentProductName,
		// 			'parentProductReviewUrl' => $review_url_string,
		// 		]
		// 	);
		// 	wp_enqueue_script( 'bazaarvoice-js-custom' );

		// 	$output = '
		// 		<div class="productReviews col-lg-10 offset-lg-1">
		// 		<div class="bv-block collapse" id="bv-block-reviews">
		// 			<div class="reviews-container" id="BVRRContainer"></div>
		// 		</div>
		// 		</div>
		// 		<div class="bv-button-wrap text-center">
		// 		<button class="btn btn-primary bv-button collapsed" type="button"
		// 			data-toggle="collapse" data-target="#bv-block-reviews" aria-expanded="false" aria-controls="bv-block-reviews">
		// 			View All Reviews
		// 		</button>
		// 		</div>';
		// 	return $output;
		// }

		// public function bazaarvoice_model_shortcodes_new( $attributes, $content = null) {
		// 	$id                = isset( $attributes['id'] ) ? $attributes['id'] : get_the_ID();
		// 	$post              = get_post( $id );
		// 	$bazaarvoice_id	   = get_field( 'bazaarvoice_id', $post->ID );
		// 	$bazaarvoice_data  = get_field( 'bazaarvoice_data', $post->ID );
		// 	$bazaarvoice_data  = json_decode( $bazaarvoice_data, true );

		// 	$subject_id        = isset( $attributes['bazaarvoice'] ) ? $attributes['bazaarvoice'] : $bazaarvoice_id;

		// 	$model_collection  = get_field( 'model_collection', $post );
		// 	$parentProductName = $post->post_title;
		// 	$parentProductName = $model_collection->post_title;
		// 	$url               = get_site_url( null, '/submission-page' );
		// 	$title = isset( $attributes['title'] ) ? $attributes['title'] : 'Model Reviews';
		// 	$review_url_string = $this->getCollectionUrl( $model_collection->post_name );

		// 	wp_localize_script(
		// 		'bazaarvoice-js-custom', 'bazaarvoice', [
		// 			'url'                    => $url,
		// 			'subject_id'             => $subject_id,
		// 			'parentProductName'      => $parentProductName,
		// 			'parentProductReviewUrl' => $review_url_string,
		// 		]
		// 	);
		// 	wp_enqueue_script( 'bazaarvoice-js-custom' );

		// 	$output = '
		// 	<div class="productReviewsHeader">
		// 		<h2 class="productReviewsHeader--title text-center title title--sub">' . $title . '</h2>
		// 		<div class="productReviewsHeader--description text-center pb-3">
		// 			<p class="body-text">' . $content . '</p>
		// 		</div>
		// 	</div>
		// 	<div class="bv-button-wrap text-center">
		// 		<button class="btn btn-primary bv-button collapsed" type="button"
		// 			data-toggle="collapse" data-target="#bv-block-reviews" aria-expanded="false" aria-controls="bv-block-reviews">
		// 			View All Reviews
		// 		</button>
		// 		<a class="btn btn-outline-primary bv-button collapsed button button--secondary" type="button" href="/hot-tub-owners/rate-your-spa">Write a Review</a>
		// 	</div>
		// 	<div class="productReviews col-lg-10 offset-lg-1">
		// 		<div class="bv-block collapse" id="bv-block-reviews">
		// 			<div class="reviews-container" id="BVRRContainer"></div>
		// 		</div>
		// 	</div>
		// 	';
		// 	return $output;
		// }

		// public function bazaarvoice_model_summary_shortcodes( $attributes ) {
		// 	$id         = isset( $attributes['id'] ) ? $attributes['id'] : get_the_ID();
		// 	$subject_id = isset( $attributes['bazaarvoice'] ) ? $attributes['bazaarvoice'] : get_field( 'bazaarvoice_id', $id );

		// 	$post              = get_post( $id );
		// 	$model_collection  = get_field( 'model_collection', $post );
		// 	$parentProductName = $post->post_title;
		// 	$parentProductName = $model_collection->post_title;
		// 	$url               = get_site_url( null, '/submission-page' );

		// 	$review_url_string = $this->getCollectionUrl( $model_collection->post_name );

		// 	wp_localize_script(
		// 		'bazaarvoice-js-custom', 'bazaarvoice', [
		// 			'url'                    => $url,
		// 			'subject_id'             => $subject_id,
		// 			'parentProductName'      => $parentProductName,
		// 			'parentProductReviewUrl' => $review_url_string,
		// 		]
		// 	);
		// 	wp_enqueue_script( 'bazaarvoice-js-custom' );

		// 	$output = '<div class="productSummaryContainer"><div id="BVRRSummaryContainer"></div></div>';
		// 	return $output;
		// }

		// public function bazaarvoice_model_submission_shortcodes( $attributes ) {
		// 	wp_enqueue_script( 'bazaarvoice-js-submission' );
		// 	$output = '<div class="productSubmissionContainer"><div id="BVSubmissionContainer"></div></div>';
		// 	return $output;
		// }


		/**
		 * Setup plugin constants.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function define_constants() {
			if ( ! defined( 'WPBV_PLUGIN_URL' ) ) {
				define( 'WPBV_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
			}
			if ( ! defined( 'WPBV_PLUGIN_DIR' ) ) {
				define( 'WPBV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}
		}

		/**
		 * Include the required files.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function includes() {
			set_include_path( WPBV_PLUGIN_DIR . 'inc/phpseclib/' );
			require_once 'Net/SSH2.php';
			require_once 'Net/SFTP.php';
			require_once WPBV_PLUGIN_DIR . 'inc/bvseosdk.php';
			require_once WPBV_PLUGIN_DIR . 'admin/class-settings.php';
			require_once WPBV_PLUGIN_DIR . 'cron.php';
		}

		/**
		 * Setup the plugin settings.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function plugin_settings() {
			$this->bv_settings = new WPBV_Settings();
			// @todo wrap to config form
			// $this->bv_importer = new bv_importer( get_option( 'bazaarvoic_host' ), get_option( 'bazaarvoic_user' ), get_option( 'bazaarvoic_pass' ) );
			$this->bv_api_key = get_option( 'bazaarvoic_api_key' );
		}

	}

	$GLOBALS['wpbv'] = new WP_Bazaarvoice();
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;  // if direct access
}

class DealersImport {

	var $campaign_list = array();
	var $campaigns     = array();
	var $campaign;
	var $campaigns_meta = array();
	var $log            = array();


	public function __construct() {
		// $this->define_constants();
		$this->loading_functions();
		$this->declare_classes();
		$this->declare_types();
		$this->declare_actions();

		$this->session = new WPD_Session_Handler();

		register_activation_hook( __FILE__, array( $this, 'install' ) );
		add_action( 'init', array( $this, 'init' ), 1 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_ajax_queue_process', array( $this, 'dc_queue_process' ) );
	}

	public function admin_menu() {
		$page = add_submenu_page( 'edit.php?post_type=edls', 'CSV Import', 'CSV Import', 'manage_options', 'smart-import', array( $this, 'form' ) );
		add_action( 'admin_print_scripts-' . $page, array( $this, 'print_scripts' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'print_styles' ) );
	}

	public function deleteOldDealers( $dealership_array ) {
		// $dealership_array = $this->session->get_data('wpd_import_qeue_dealership_array');
		$dealers = _get_dealers_array();
		$diff    = array_diff( $dealers, $dealership_array );
		foreach ( $diff as $dealership_id ) {
			$dealer = _dealer_get_posts_by_dealership_id( $dealership_id );
			$dealer = _dealer_check_posts_by_dealership_id( $dealer, $dealership_id );
			//wp_trash_post( $dealer[0]->ID );
		}
	}

	// our ajax handler, all data accessible via $_POST
	public function dc_queue_process() {
		@error_reporting( 0 );
		$this->init_data();
		// we are going to retrieve json response
		header( 'Content-type: application/json' );

		try {
			$count = count( $this->session->get_data( 'wpd_import_qeue' ) );
			if ( $count > 0 ) {
				$array = $this->session->get_data( 'wpd_import_qeue' );
				$data  = array_shift( $array );
				$this->session->set_data( 'wpd_import_qeue', $array );
				$this->process( $data );
			} else {
				$this->deleteOldDealers( $this->session->get_data( 'wpd_import_qeue_dealership_array' ) );
				$this->session->delete_data( 'wpd_import_qeue' );
				$this->session->delete_data( 'wpd_import_qeue_total' );
				$this->session->delete_data( 'wpd_import_qeue_dealership_array' );
				$this->session->delete_data( 'wpd_import_qeue_options' );
			}

			$response = array(
				'success' => true,
				'message' => 'Processing:<span></span> ' . $count . ' of ' . (int) $this->session->get_data( 'wpd_import_qeue_total' ),
			);

		} catch ( Exception $ex ) {
			$response = array(
				'success' => false,
				'message' => $ex->getMessage(),
			);
		}

		// echoing response
		echo json_encode( $response );
		// do not forget about this die()
		die();
	}
	private static function get_existing_attachment_id( $original_id, $post_id ) {
		global $wpdb;
		$sql     = 'SELECT * FROM ' . $wpdb->posts . " WHERE `post_type` = 'attachment'  AND `post_title` = '" . $original_id . "' LIMIT 1";
		$results = $wpdb->get_results( $sql );
		if ( isset( $results[0]->ID ) ) {
			return $results[0]->ID;
		} else {
			return false;
		}
	}

	protected function attachment2post( $remoteUrl, $post_id ) {

		$original_id = 'Dealer gallery ' . md5( $remoteUrl );

		if ( $attachment_id = $this->get_existing_attachment_id( $original_id, $post_id ) ) {
			return $attachment_id;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$file             = array();
		$path             = parse_url( $remoteUrl, PHP_URL_PATH );
		$name             = array_pop( explode( '/', $path ) );
		$file['name']     = $name;
		$file['tmp_name'] = download_url( trim( $remoteUrl ) );

		// do the validation and storage stuff
		$id        = media_handle_sideload( $file, $post_id, $original_id );
		$local_url = wp_get_attachment_url( $id );

		// If error storing permanently, unlink
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
		} else {
			// create the thumbnails
			$attach_data = wp_generate_attachment_metadata( $id, get_attached_file( $id ) );
			wp_update_attachment_metadata( $id, $attach_data );
		}
		return $id;
	}

	/**
	 * Process csv rows
	 *
	 * @param  array $params
	 * @return boolean
	 */
	public function process( $params ) {
		$this->init_data();
		$options = $this->session->get_data( 'wpd_import_qeue_options' );
		foreach ( $params as $param ) {
			if ( strpos( $param[1], ' (' ) !== false ) {
				$param[1] = substr( $param[1], 0, -8 );
			}

			$post_id               = 0;
			$dealership_id         = str_pad( $param['DealershipId'], 5, '0', STR_PAD_LEFT );
			$dealership_name       = $param['Title'];
			$dealer_path           = ltrim( $param['Path'], '/' );
			$dealer_about          = $param['About Dealer'];
			$dealer_hours          = ( $param['Dealer Hours'] == '0' ) ? '' : $param['Dealer Hours'];
			$we_also_offer         = $param['We Also Offer'];
			$areas_we_serve        = $param['Areas We Serve'];
			$membership_and_awards = $param['Membership and Awards'];
			$dealership_services   = $param['Dealership Available Services'];
			$lock_address          = ! empty( $param['Lock Address'] ) ? true : false;
			$lock_title            = ! empty( $param['Lock Title'] ) ? true : false;
			$dealership_address1   = $param['Address'];
			$dealership_city       = $param['City'];
			$dealership_state_code = $param['State Code'];
			$dealership_zip        = $param['Postal code'];
			$dealership_phone      = $param['Phone'];
			$dealership_email      = $param['Dealer Email'];
			$dealership_website    = $param['Website'];
			$dealership_mg_tmp     = explode( '|', $param['Media Gallery'] );
			$dealership_mg         = [];
			$dealership_mgv        = [];

			foreach ( $dealership_mg_tmp as $value ) {
				// https://www.hotspring.com/sites/default/files/adtrack_gallery_5405.jpg?itok=oguyUcel
				// public://adtrack_gallery_5422.jpg,
				// youtube://v/yiMo7PYuz6Y,
				$string = trim( $value );
				if ( ! empty( $string ) ) {
					if ( strpos( $string, 'youtube://v/' ) !== false ) {
						$dealership_mgv[] = str_replace( 'youtube://v/', 'https://www.youtube.com/watch?v=', $string );
					}else{
						$dealership_mg[] = str_replace( 'public://', 'https://www.hotspring.com/sites/default/files/', $string );
					}
				}
			}

			// get the country name form the postal code
			if ( preg_match( '/^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/i', $dealership_zip ) ) {
				$dealership_country = 'Canada';
			} else {
				$dealership_country = 'United States';
			}

			$posts = _dealer_get_posts_by_dealership_id( $dealership_id );
			$posts = _dealer_check_posts_by_dealership_id( $posts, $dealership_id );

			if ( isset( $posts[0]->ID ) ) {
				$post_id = $posts[0]->ID;
			}

			$exclude     = false;
			$coordinates = null;

			$old_lock_title   = get_post_meta( $post_id, 'locking_edls_title', true );
			$old_lock_address = get_post_meta( $post_id, 'locking_edls_address', true );
			$old_coordinates  = get_post_meta( $post_id, 'dealership_coordinates', true );
			$old_address      = get_post_meta( $post_id, 'dealership_address_1', true );

			$edl_services = [];
			// Backyard Consultation, Delivery Available, Service Department, Test Soak, Watercare Analysis
			$dedault_services = array_flip(
				[
					'backyard-consultation' => 'Backyard Consultation',
					'delivery-available'    => 'Delivery Available',
					'service-department'    => 'Service Department',
					'test-soak'             => 'Test Soak',
					'watercare-analysis'    => 'Watercare Analysis',
				]
			);

			foreach ( explode( ',', $dealership_services ) as $key => $value ) {
				  $edl_services[] = $dedault_services[ trim( $value ) ];
			}

			// to check if no old value of old_coordinates. For best performance.
			if ( empty( $old_coordinates ) || $dealership_address1 !== $old_address ) {
				$zip         = empty( $dealership_zip ) ? '' : ', ' . $dealership_zip;
				$coordinates = dealer_lookup_address( $dealership_address1 . $zip );
			} else {
				$coordinates = $old_coordinates;
			}

			$meta_input = array(
				'dealership_id'          => $dealership_id,
				// 'dealership_name'        => $dealership_name,
				'dealer_phone'           => $dealership_phone,
				'dealer_email'           => $dealership_email,
				'dealer_website'         => $dealership_website,
				'about_description'      => $dealer_about,
				'dealership_coordinates' => $coordinates,
				'dealership_exclude'     => $exclude,
				'locking_edls_title'     => $lock_title,
				'locking_edls_address'   => $lock_address,
				'edl_dealer_hours'       => $dealer_hours,
				'areas_we_serve'         => $areas_we_serve,
				'we_also_offer'          => $we_also_offer,
				'membership_and_awards'  => $membership_and_awards,
			// 'edl_services'           => $dealership_services,
			);

			if ( ! empty( $edl_services ) ) {
				$meta_input['edl_services'] = $edl_services;
			}
			if ( ! empty( $dealer_path ) ) {
				$meta_input['custom_permalink'] = $dealer_path;
			}

			$title = esc_html( $posts[0]->post_title );
			// check if title not locked
			if ( ! $old_lock_title ) {
				$title                         = $dealership_name;
				$meta_input['dealership_name'] = $dealership_name;
			}

			// check if address not locked
			if ( ! $old_lock_address ) {
				$meta_input = $meta_input + array(
					'dealership_address_1'  => $dealership_address1,
					// 'dealership_address_2' => $dealership->Address2,
					'dealership_city'       => $dealership_city,
					// 'dealership_state' => $dealership->State,
					'dealership_state_code' => $dealership_state_code,
					'dealership_country'    => $dealership_country,
					// 'dealership_country_code' => $dealership->CountryCode,
					'dealership_zip'        => $dealership_zip,
				// 'dealership_territory_code' => $dealership->TerritoryCode,
				);
			}

			$dealership_post = array(
				'ID'            => $post_id,
				'post_title'    => wp_strip_all_tags( $title ),
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_category' => array(),
				'post_author'   => $user_id,
				'post_type'     => 'edls',
				'meta_input'    => $meta_input,
			);

			// Insert the post into the database
			$post_id = wp_insert_post( $dealership_post );
			$field_value = array();
			foreach ( $dealership_mgv as $index => $value ) {
				$field_value[] = array( 'edl_gallery_video' =>  $value );
			}
			update_field( 'edl_video_gallery', $field_value, $post_id );

			$field_value = array();
			foreach ( $dealership_mg as $index => $value ) {
				$value         = str_replace( ' ', '%20', trim( $value ) );
				$field_value[] = array( 'edl_gallery_image' => $this->attachment2post( trim( $value ), $post_id ) );
			}
			update_field( 'edl_image_gallery', $field_value, $post_id );
		}
	}

	public function print_styles() {
		wp_enqueue_style( 'dcbulk', WPD_PLUGIN_URL . 'includes/assets/css/bulk-smart.css' );
		// wp_enqueue_style( 'dcbulk', plugins_url( 'css/bulk-smart.css', DC_PLUGIN_DIR . 'ddd/' ) );
	}

	// register and enqueue needed scripts and styles here
	public function print_scripts() {
		wp_enqueue_script( 'dcbulk', WPD_PLUGIN_URL . 'includes/assets/js/bulk-smart.js' );
		$this->nonce = wp_create_nonce( basename( __FILE__ ) );
		wp_localize_script( 'dcbulk', 'dc_nonce', $this->nonce );
	}

	// initial data run on
	public function init_data() {
		// $this->campaign_list = array_flip ((array) _dc_campaign_list() );
		// $this->campaigns = _dc_get_all_compaign();
		// $this->campaigns_meta = array();
		// foreach ($this->campaigns as $value) {
		// $this->campaigns_meta[$value->term_id] = get_term_meta($value->term_id);
		// }
	}

	public function init() {}

	public function install() {
		// global $wpdb;
		// require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		// $table_name = $wpdb->prefix . 'WPD_sessions';
		// //print_r($table_name);
		//
		// $collate = '';
		// if ( $wpdb->has_cap( 'collation' ) ) {
		// $collate = $wpdb->get_charset_collate();
		// }
		//
		// $tables = "CREATE TABLE {$table_name} (
		// session_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		// session_key char(32) NOT NULL,
		// session_value longtext NOT NULL,
		// session_expiry BIGINT UNSIGNED NOT NULL,
		// PRIMARY KEY  (session_key),
		// UNIQUE KEY session_id (session_id)
		// ) $collate;";
		//
		// dbDelta($tables);
	}

	public function declare_types() {}

	/**
	 * Plugin's interface
	 *
	 * @return void
	 */
	function form() {
		$nonce = $_REQUEST['_wpnonce'];
		if ( wp_verify_nonce( $nonce, 'dealer-import-csv' ) && current_user_can( 'import_dealers' ) ) {
			if ( ! $_POST['options'] ) {
				$_POST['options'] = array();
			}
			$this->post( $_POST['options'] );
		}

		// form HTML {{{
		?>

  <div class="wrap">
  <h2>Import CSV</h2>
		<?php
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'process' ) {
			$count = count( $this->session->get_data( 'wpd_import_qeue' ) );
			if ( $count ) {
				$array = $this->session->get_data( 'wpd_import_qeue' );
				$data  = array_shift( $array );
				$this->session->set_data( 'wpd_import_qeue', $array );
				$this->init_data();
				$this->process( $data );
				print '<div class="bulk processing">Processing:<span></span> ' . $count . ' of ' . $this->session->get_data( 'wpd_import_qeue_total' ) . '</div>';
				// print to js variable
				$js_array = json_encode(
					array(
						'count' => $count,
						'total' => $this->session->get_data( 'wpd_import_qeue_total' ),
					)
				);
				echo "<script type='text/javascript'>\n var bulk = " . $js_array . ";\n </script>";
			} else {
				$this->session->delete_data( 'wpd_import_qeue' );
				$this->session->delete_data( 'wpd_import_qeue_total' );
				$this->session->delete_data( 'wpd_import_qeue_dealership_array' );
				$this->session->delete_data( 'wpd_import_qeue_options' );
			}
		}
		?>

  <form class="add:the-list: validate" method="post" action="/wp-admin/edit.php?post_type=edls&page=smart-import&action=process" enctype="multipart/form-data">
  <!-- Import as draft -->
  <!-- File input -->
  <div><label for="csv_import">Upload file:</label><br/>
	<input type="file" name="csv_import" id="csv_import" value="" aria-required="true" />
  </div>
  <p class="submit">
		<?php
		wp_nonce_field( 'dealer-import-csv' );
		submit_button( 'Import 2', 'primary', 'submit-form', false );
		?>
  </p>
  </form>
  </div><!-- end wrap -->

		<?php
		// end form HTML }}}
	}

	function print_messages() {
		if ( ! empty( $this->log ) ) {
			// messages HTML {{{
			?>
  <div class="wrap">
			<?php if ( ! empty( $this->log['error'] ) ) : ?>

  <div class="error">
				<?php foreach ( $this->log['error'] as $error ) : ?>
	<p><?php echo $error; ?></p>
	<?php endforeach; ?>
  </div>

	<?php endif; ?>

			<?php if ( ! empty( $this->log['notice'] ) ) : ?>

  <div class="updated fade">
				<?php foreach ( $this->log['notice'] as $notice ) : ?>
	<p><?php echo $notice; ?></p>
	<?php endforeach; ?>
  </div>

	<?php endif; ?>
  </div><!-- end wrap -->
			<?php
			// end messages HTML }}}
			$this->log = array();
		}
	}

	/**
	 * Handle POST submission
	 *
	 * @param array $options
	 * @return void
	 */
	function post( $options = array() ) {

		if ( empty( $_FILES['csv_import']['tmp_name'] ) ) {
			$this->log['error'][] = 'No file uploaded, aborting.';
			$this->print_messages();
			return;
		}

		if ( ! current_user_can( 'publish_pages' ) || ! current_user_can( 'publish_posts' ) ) {
			$this->log['error'][] = 'You don\'t have the permissions to publish posts and pages. Please contact the blog\'s administrator.';
			$this->print_messages();
			return;
		}

		$skipped  = 0;
		$imported = 0;
		$comments = 0;

		$row              = 0;
		$time_start       = microtime( true );
		$file             = $_FILES['csv_import']['tmp_name'];
		$big_data_array   = array();
		$dealership_array = array();
		$count            = 0;

		if ( ( $handle = fopen( $file, 'r' ) ) !== false ) {
			while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
				if ( empty( $data[0] ) || is_null( $data ) ) {
					continue;
				}
				if ( $data[1] == 'Title' ) {
					$labels = $data;
					$hash   = count( $labels );
					continue;
				}
				$count++;

				if ( $hash != count( $data ) ) {
					continue;
				}

				$big_data_array[]   = array_combine( $labels, $data );
				$dealership_array[] = (string) str_pad( $data[0], 5, '0', STR_PAD_LEFT );
			}
			fclose( $handle );
		}

		// // update media gallery
		// if(!empty($big_data_array[0]['mediaGallery'])){
		// $field_value = array();
		// foreach ($dealership['mediaGallery'] as $index => $value) {
		// $value = str_replace(' ','%20',trim($value));
		// $field_value[] = array('edl_gallery_image' => $this->attachment2post(trim($value), $post_id));
		// }
		// update_field( 'edl_image_gallery', $field_value, $post_id );
		// }
		// d( $big_data_array[0] );
		// // https://www.hotspring.com/sites/default/files/adtrack_gallery_5405.jpg?itok=oguyUcel
		// foreach ( explode( ',', $big_data_array[0]['Media Gallery'] ) as $key => $value ) {
		// d( str_replace( 'public://', 'https://www.hotspring.com/sites/default/files/', trim( $value ) ) );
		// }
		// exit();
		$smart_qeue = array_chunk( $big_data_array, 50 );
		$data_array = array();
		foreach ( $big_data_array as $data ) {
			$data_array[] = $data[0];
		}

		$this->session->set_data( 'wpd_import_qeue', $smart_qeue );
		$this->session->set_data( 'wpd_import_qeue_total', count( $smart_qeue ) + 1 );
		$this->session->set_data( 'wpd_import_qeue_options', $options );
		$this->session->set_data( 'wpd_import_qeue_dealership_array', $dealership_array );

		$exec_time = microtime( true ) - $time_start;
		if ( file_exists( $file ) ) {
			@unlink( $file );
		}
		if ( $skipped ) {
			$this->log['notice'][] = "<b>Skipped {$skipped} coupons (most likely due to empty title, body and excerpt).</b>";
		}
		$this->print_messages();
	}

	public function loading_functions() {
		require_once WPD_PLUGIN_DIR . 'includes/functions.php';
	}

	public function loading_plugin() {
	}

	public function loading_script() {
	}

	public function declare_actions() {
	}

	public function declare_classes() {
		require_once WPD_PLUGIN_DIR . 'includes/assets/session.php';
	}

	public function define_constants() {
		// $this->define('WPD_PLUGIN_URL', plugins_url('/', __FILE__)  );
		// $this->define('WPD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		// $this->define('WPD_TD', 'dc' );
	}

	private function define( $name, $value ) {
		if ( $name && $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
	}
}
new DealersImport();

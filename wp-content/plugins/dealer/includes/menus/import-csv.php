<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class class_wpd_import_csv {

	var $log = array();

	public function print_messages() {
		if ( ! empty( $this->log ) ) { ?>
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
	</div>
			<?php
			$this->log = array();
		}
	}

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=edls', 'Import CSV', 'Import CSV', 'import_dealers', 'dealer-import-csv', array( $this, 'dealer_import_page' ) );
	}

	public function dealer_import_page() {
		?>
	<div class='wrap'>
	  <div id='icon-options-general' class='icon32'></div>
	  <form method="post" enctype="multipart/form-data">
		<?php
		// settings_fields('dealer_import_section');
		do_settings_sections( 'dealer-import-csv' );
		wp_nonce_field( 'dealer-import-csv' );
		submit_button( 'Import 1', 'primary', 'submit-form', false );
		?>
	  </form>
	</div>
		<?php
	}

	public function register_settings() {
		add_settings_section( 'dealer_import_section', 'Dealers Import CSV', array( $this, 'dealer_settings_section_form_elements' ), 'dealer-import-csv' );

		add_settings_field( 'dealer_csv_file', 'Dealer CSV File', array( $this, 'dealer_settings_field_csv_file' ), 'dealer-import-csv', 'dealer_import_section' );
		register_setting( 'dealer_import_section', 'dealer_csv_file' );
	}

	/**
	 * Delete BOM from UTF-8 file.
	 */
	public function stripBOM( $fname ) {
		$res = fopen( $fname, 'rb' );
		if ( false !== $res ) {
			$bytes = fread( $res, 3 );
			if ( $bytes == pack( 'CCC', 0xef, 0xbb, 0xbf ) ) {
				$this->log['notice'][] = 'Getting rid of byte order mark...';
				fclose( $res );

				$contents = file_get_contents( $fname );
				if ( false === $contents ) {
					trigger_error( 'Failed to get file contents.', E_USER_WARNING );
				}

				$contents = substr( $contents, 3 );
				$success  = file_put_contents( $fname, $contents );
				if ( false === $success ) {
					trigger_error( 'Failed to put file contents.', E_USER_WARNING );
				}
			} else {
				fclose( $res );
			}
		} else {
			$this->log['error'][] = 'Failed to open file, aborting.';
		}
	}

	public function dealer_settings_field_csv_file() {
		print '<input name="dealer_csv_file" id="dealer_csv_file" type="file" value="" aria-required="true" />';
	}

	public function dealer_settings_section_form_elements() {
		print '<p>Please select the file to import and click the Import of the button to start the process of import.</p>';
		$nonce = $_REQUEST['_wpnonce'];
		if ( wp_verify_nonce( $nonce, 'dealer-import-csv' ) && current_user_can( 'import_dealers' ) ) {

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

			require_once WPD_PLUGIN_DIR . 'includes/assets/DataSource.php';

			$time_start = microtime( true );
			$csv        = new File_CSV_DataSource();
			$file       = $_FILES['csv_import']['tmp_name'];
			$this->stripBOM( $file );

			if ( ! $csv->load( $file ) ) {
				$this->log['error'][] = 'Failed to load file, aborting.';
				$this->print_messages();
				return;
			}

			// pad shorter rows with empty values
			$csv->symmetrize();

			// WordPress sets the correct timezone for date functions somewhere
			// in the bowels of wp_insert_post(). We need strtotime() to return
			// correct time before the call to wp_insert_post().
			$tz = get_option( 'timezone_string' );
			if ( $tz && function_exists( 'date_default_timezone_set' ) ) {
				date_default_timezone_set( $tz );
			}

			$skipped  = 0;
			$imported = 0;
			$comments = 0;

			foreach ( $csv->connect() as $data ) {
				d( $data );

				// $matchesHours = array();
				// $storeHours = preg_match_all('((Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday): ((1?[0-9]|2[0-3]):[0-5][0-9] (am|pm))-((1?[0-9]|2[0-3]):[0-5][0-9] (am|pm)))', $data['Store Hours'], $matchesHours, PREG_SET_ORDER);          // if ($post_id = $this->create_post($csv_data, $options)) {
				// $data['storeHours'] = array(
				// 'Monday' => 'Closed',
				// 'Tuesday' => 'Closed',
				// 'Wednesday' => 'Closed',
				// 'Thursday' => 'Closed',
				// 'Friday' => 'Closed',
				// 'Saturday' => 'Closed',
				// 'Sunday' => 'Closed',
				// );
				// foreach ($matchesHours as $key => $value) {
				// $data['storeHours'][$value[1]] = $value[2].'-'.$value['5'];
				// }
				// Available Services
				$dealerServices         = explode( ', ', $data['Available Services at this Hot Tub and Spa Dealership'] );
				$data['dealerServices'] = array();
				foreach ( $dealerServices as $key => $value ) {
					switch ( $value ) {
						case 'Backyard Consultation':
							$data['dealerServices'][] = 'backyard';
							break;
						case 'White Glove Delivery':
							$data['dealerServices'][] = 'glove';
							break;
						case 'Test Soaks':
							  $data['dealerServices'][] = 'test';
							break;
						case 'Service Department':
							  $data['dealerServices'][] = 'service';
							break;
						case 'Water Care & Analysis':
							  $data['dealerServices'][] = 'water';
							break;
						default:
							break;
					}
				}

				// Shopping tools
				$data['shoppingTools'] = explode( ', ', $data['We Also Offer'] );

				// MediaGallery
				$data['mediaGallery'] = explode( '|', $data['Media Gallery'] );
				foreach ( $data['mediaGallery'] as $key => $value ) {
					$data['mediaGallery'][ $key ] = trim( $value );
				}

				$GLOBALS['wpd-background-process']->push_to_queue(
					array(
						'type' => 'csv',
						'data' => $data,
					)
				);
				$imported++;

				if ( file_exists( $file ) ) {
					  @unlink( $file );
				}

				$GLOBALS['wpd-background-process']->save()->dispatch();
				$exec_time = microtime( true ) - $time_start;

				if ( $skipped ) {
					$this->log['notice'][] = "<b>Skipped {$skipped} posts (most likely due to empty title, body and excerpt).</b>";
				}

				$this->log['notice'][] = sprintf( "<b>Imported {$imported} posts and {$comments} comments in %.2f seconds.</b>", $exec_time );
				$this->print_messages();
			}
		}
	}

}
$ff = new class_wpd_import_csv();

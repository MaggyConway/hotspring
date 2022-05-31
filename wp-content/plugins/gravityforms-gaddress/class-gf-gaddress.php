<?php

GFForms::include_addon_framework();

class GFGAddress extends GFAddOn {

	protected $_version = GF_GOOGLE_ADDESS_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'gaddress';
	protected $_path = 'gaddress/setting.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Google Address Add-On';
	protected $_short_title = 'Google Address';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFgfgaddress
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFGAddress();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
		add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2 );
		add_filter( 'gform_enqueue_scripts', array( $this, 'init_scripts' ), 10, 2);
	}

	function init_scripts(){
        wp_register_script( 'gscript_full', $this->get_base_url() . '/js/script-full.js', array( 'jquery' ), false, true );
        wp_register_script( 'gscript', $this->get_base_url() . '/js/script.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&language=en&libraries=places&key=' . get_option( 'dealer_gma_key_web' ), array(), false, true );
	}

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
    	//add key see the dealers settings
		$scripts = array();
		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {
		$styles = array();
		return array_merge( parent::styles(), $styles );
	}

  function setting_to_js($settings){
        //$script = ( isset($settings['zip_only']) )? $this->get_base_url() . '/js/script.js': $this->get_base_url() . '/js/script-full.js';
        if($settings[ 'zip_only' ]) {
            wp_enqueue_script( 'gscript' );
        } else {
            wp_enqueue_script( 'gscript_full' );
        }

		$zo = ( isset($settings['zip_only']) ) ? '_zo' : '' ;

		return '
		
		<script type="text/javascript">
			if( typeof(gaddress_settings' . $zo . ') === "undefined" ){
				var gaddress_settings' . $zo . ' = [];
			}
			gaddress_settings' . $zo . '.push(' . json_encode( $settings, JSON_FORCE_OBJECT ) . ');
		</script>';
  }

	/**
	 * Add the text in the plugin settings to the bottom of the form if enabled for this form.
	 *
	 * @param string $button The string containing the input tag to be filtered.
	 * @param array $form The form currently being displayed.
	 *
	 * @return string
	 */
	function form_submit_button( $button, $form ) {
    if(!$form['gaddress']['hide']){
      return $button;
    }

    $strings['form_id'] = $form['id'];
    $strings['fields'] = array();
		if(!empty($form['gaddress']['zip_only'])){
			$strings['zip_only'] = $form['gaddress']['zip_only'];
		}

    $strings['placeholder'] = !empty($form['gaddress']['placeholder']) ? $form['gaddress']['placeholder'] : 'Please enter your address';

    $fields = GFAPI::get_fields_by_type( $form, 'address' );
    foreach ($fields as $key => $value) {
      $strings['fields'][$key] = array('id' => ''.$value['formId'].'_'.$value['id']);
    }

    $button = $this->setting_to_js($strings) . $button;
		return $button;
	}

	/**
	 * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Google Adress Form Settings', 'gfgaddress' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Hide the address fields on this form', 'gfgaddress' ),
						'type'    => 'checkbox',
						'name'    => 'enabled',
						'tooltip' => esc_html__( 'This is the tooltip', 'gfgaddress' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Hided', 'gfgaddress' ),
								'name'  => 'hide',
							),
							array(
								'label' => esc_html__( 'ZIP Code Only', 'gfgaddress' ),
								'name'  => 'zip_only',
							),
						),
					),
					array(
						'name'    => 'placeholder',
						'label'   => esc_html__( 'Placeholder', 'gfeloqua' ),
						'type'    => 'text',
						'class'   => 'medium wide',
						'tooltip' => esc_html__( 'Enter placeholder for address field. ' ),
            'default_value' => esc_html__( 'Please enter Address', 'gfeloqua' )
					),
				),
			),
		);
	}

	/**
	 * The feedback callback for the 'mytextbox' setting on the plugin settings page and the 'mytext' setting on the form settings page.
	 *
	 * @param string $value The setting value.
	 *
	 * @return bool
	 */
	public function is_valid_setting( $value ) {
		return strlen( $value ) < 10;
	}

}


add_filter( 'gform_validation_message', 'sw_gf_validation_message', 10, 2 );

function sw_gf_validation_message( $validation_message ) {
    add_action( 'wp_footer', 'sw_gf_js_error' );
}

function sw_gf_js_error() {
    ?>
    <script type="text/javascript">
		jQuery(".gfield_error").has("input[type='email'],input[type='text'],input[type='password'],select,textarea").find(".validation_message").each(function() {
	    var e = jQuery(this), fielddesc = jQuery("<div>").append(e.clone()).remove().html();
			e.parent().find("label").after(fielddesc);
	    e.remove();
		});
    </script>
    <?php
}

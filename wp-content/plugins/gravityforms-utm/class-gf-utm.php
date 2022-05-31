<?php

GFForms::include_addon_framework();

class GFUTM extends GFAddOn {

	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'utm';
	protected $_path = 'utm/setting.php';
	protected $_full_path = __FILE__;
	protected $_title = 'UTM Add-On';
	protected $_short_title = 'UTM';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFutm
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFUTM();
		}
		return self::$_instance;
	}

	/**
	 * Handles hooks
	 */
	public function init() {
		parent::init();
		add_filter( 'gform_pre_render', array( $this, 'form_add_field' ) );
		add_filter( 'gform_pre_validation', array( $this, 'form_add_field' ) );
		add_filter( 'gform_pre_submission_filter', array( $this, 'form_add_field' ) );
		add_filter( 'gform_admin_pre_render', array( $this, 'form_add_field' ) );
	}



	public function form_add_field( $form ) {
		$fields_id = $fields_label = [];
		foreach ($form['fields'] as $key => $value) {
			$fields_label[] = $value['label'];
			$fields_id[] = $value['id'];
		}
		if( isset( $form['utm'] ) || !empty( $form['utm'] ) ) {
			foreach ($form['utm'] as $key => $value) {
				if($value && !in_array($key,$fields_label)){
					$fid = 0;
					do {
						$fid++;
						$found = in_array($fid,$fields_id);
					} while ($found);
					$fields_id[] = $fid;

					$props = [
						"type" => "hidden",
						"id" => $fid,
						"label" => $key,
						"adminLabel" => "",
						"isRequired" => false,
						"size" => "medium",
						"errorMessage" => "",
						"inputs" => null,
						"formId" => $form['id'],
						"description" => "",
						"allowsPrepopulate" => true,
						"inputMask" => false,
						"inputMaskValue" => "",
						"inputType" => "",
						"labelPlacement" => "",
						"descriptionPlacement" => "",
						"subLabelPlacement" => "",
						"placeholder" => "",
						"cssClass" => $key,
						"inputName" => $key,
						"visibility" => "visible",
						"noDuplicates" => false,
						"defaultValue" => (strpos($key, 'utm_') !== false) ? 'none' : '',
						"choices" => "",
						"conditionalLogic" => "",
						"productField" => "",
						"multipleFiles" => false,
						"maxFiles" => "",
						"calculationFormula" => "",
						"calculationRounding" => "",
						"enableCalculation" => "",
						"disableQuantity" => false,
						"displayAllCategories" => false,
						"useRichTextEditor" => false,
					];
					$field = GF_Fields::create( $props );
					array_push( $form['fields'], $field );
				}
			}
		}
    return $form;
  }


	/**
	 * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Add a hiden fields', 'utm' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Hide the address fields on this form', 'utm' ),
						'type'    => 'checkbox',
						'name'    => 'enabled',
						'tooltip' => esc_html__( 'This is the tooltip', 'utm' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'marinid', 'utm' ),
								'name'  => 'marinid',
							),
							array(
								'label' => esc_html__( 'utm_campaign', 'utm' ),
								'name'  => 'utm_campaign',
							),
							array(
								'label' => esc_html__( 'utm_source', 'utm' ),
								'name'  => 'utm_source',
							),
							array(
								'label' => esc_html__( 'utm_medium', 'utm' ),
								'name'  => 'utm_medium',
							),
							array(
								'label' => esc_html__( 'utm_term', 'utm' ),
								'name'  => 'utm_term',
							),
							array(
								'label' => esc_html__( 'utm_content', 'utm' ),
								'name'  => 'utm_content',
							),
							array(
								'label' => esc_html__( 'googleclientid', 'utm' ),
								'name'  => 'googleclientid',
							),
							array(
								'label' => esc_html__( 'googletransactionid', 'utm' ),
								'name'  => 'googletransactionid',
              ),
              array(
								'label' => esc_html__( 'textcid', 'utm' ),
								'name'  => 'textcid',
              ),
						),
					),
				),
			),
		);
	}
}

<?php

if ( ! defined('ABSPATH')) exit;  // if direct access

// check if class already exists
if( !class_exists('acf_field_dc_coupons') ) :
class acf_field_dc_coupons extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*/
  function _dealer_field(){
    return array (
    	'key' => 'group_coupons_dealer',
    	'title' => 'Coupons',
    	'fields' => array (
    		array (
    			'key' => 'dcc_coupons',
    			'label' => 'Active coupons',
    			'name' => 'dcc_coupons',
    			'type' => 'dc_coupons',
    			'instructions' => '',
    			'required' => 0,
    			'conditional_logic' => 0,
    			'wrapper' => array (
    				'width' => '',
    				'class' => '',
    				'id' => '',
    			),
    			'font_size' => 14,
    		),
    	),
    	'location' => array (
    		array (
    			array (
    				'param' => 'post_type',
    				'operator' => '==',
    				'value' => 'edls',
    			),
    		),
    	),
    	'menu_order' => 0,
    	'position' => 'normal',
    	'style' => 'default',
    	'label_placement' => 'top',
    	'instruction_placement' => 'label',
    	'hide_on_screen' => '',
    	'active' => 1,
    	'description' => '',
    );
  }

	function __construct( $settings ) {


    acf_add_local_field_group(array($this,'_dealer_field'));
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/

		$this->name = 'dc_coupons';


		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = 'Coupons';


		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'basic';


		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/

		$this->defaults = array(
			'font_size'	=> 14,
		);


		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('FIELD_NAME', 'error');
		*/

		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-FIELD_NAME'),
		);


		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/

		$this->settings = $settings;


		// do not delete!
    	parent::__construct();

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {

		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/

		acf_render_field_setting( $field, array(
			'label'			=> __('Font Size','acf-FIELD_NAME'),
			'instructions'	=> __('Customise the input font size','acf-FIELD_NAME'),
			'type'			=> 'number',
			'name'			=> 'font_size',
			'prepend'		=> 'px',
		));
	}



	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {
		// echo '<pre>';
		// 	print_r( $field );
		// echo '</pre>';

    if( empty($field['value']['value']) ){
      print "You don't have active coupons";
      return;
    }

    $coupons = _get_all_dealer_coupons($field['value']['dealer_id']);

    print '<ul class="acf-checkbox-list acf-bl">';
		print '<input id="default-' . esc_attr($field['name']) . '" name="' . esc_attr($field['name']) . '[0]" type="hidden" value="0">';
    foreach ($coupons as $key => $coupon) {
      $link = '<a href="/wp-admin/post.php?post='.$coupon->ID.'&action=edit">Edit</a>';
			$checked = $field['value']['value'][$coupon->ID]? 'checked="checked"' : '';
      print '<li><label><input id="acf_field-' . $field['name'] . '-coupons-' . $coupon->ID . '" type="checkbox"
      name="'.esc_attr($field['name']) .'['.$coupon->ID.']" value="' . $coupon->ID . '"'.$checked.'>' . $coupon->post_title . ' '.$link.'</label></li>';
    }
    print '</ul>';

	}

  /*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	function update_value( $value, $post_id, $field ) {
    $coupons = _get_all_dealer_coupons($post_id);
		foreach ($coupons as $key => $coupon) {
			$is_active = isset($value[$coupon->ID]);
      update_post_meta( $coupon->ID, 'dcc_active', $is_active );
    }
		return $value;
	}

  /*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	function load_value( $value, $post_id, $field ) {
    $old = $value;
    $coupons = _get_all_dealer_coupons($post_id);
		$value = array();
		foreach ($coupons as $key => $coupon) {
			$is_active = get_post_meta( $coupon->ID, $key = 'dcc_active', $single = false );
			$value[$coupon->ID] = $is_active[0] ? TRUE : FALSE ;
    }
		return array('dealer_id'=>$post_id,'value' => $value);
	}

  /*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/

	function format_value( $value, $post_id, $field ) {

    $coupons = _get_all_dealer_coupons($post_id);
    $result = FALSE;
    foreach ($coupons as $key => $coupon) {
      // print_r($coupon);

			$is_active = get_post_meta( $coupon->ID, $key = 'dcc_active', false );
			$campaign_id = get_field( 'dc_campaign', $coupon->ID );
      $campaign = get_term_by('id', $campaign_id, 'dc_campaign');
			// $campaign_meta = get_term_meta( $campaign_id)
      //campaign data
      $c_headline = get_field( 'dcc_default_headline', $campaign );
      $c_coupon_text = get_field( 'dcc_coupon_text', $campaign );
      $c_img =  get_field( 'dcc_coupon_preview_image', $campaign );
      $c_weight =  get_field( 'dcc_weight', $campaign );

      // $image = get_post_meta( $coupon->ID, $key = 'dcc_coupon_preview_image', $single = false );
      $meta = get_post_meta( $coupon->ID );
      $img = get_field('dcc_coupon_preview_image', $coupon->ID );
      $coupon->headline = !empty($meta['dcc_default_headline'][0])?$meta['dcc_default_headline'][0] : $c_headline;
			$coupon->coupon_text = !empty($meta['dcc_coupon_text'][0])?$meta['dcc_coupon_text'][0] : $c_coupon_text;
      $coupon->weight = !empty($meta['dcc_weight'][0])?$meta['dcc_weight'][0] : $c_weight;
      $coupon->image = !empty($img) ? $img : $c_img;

      $is_active = $is_active[0] ? TRUE : FALSE ;

			if($is_active){
        if(!isset($result->weight) || $coupon->weight > $result->weight )
        $result = $coupon;
      }

    }
    return $result;

    // exit();
    //
		// //$value = 'format_value';
		// // bail early if no value
		// // if( empty($value) ) {
		// // 	return $value;
		// // }
    //
		// // apply setting
		// if( $field['font_size'] > 12 ) {
    //
		// 	// format the value
		// 	// $value = 'something';
    //
		// }
    //
    // print_r('format');
    // print_r($value);
    // print_r($post_id);
    // print_r($field);
    // exit();
    // $value = 'fuuuu';
		// // return
		return $value;
	}

	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	/*

	function input_admin_enqueue_scripts() {

		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];


		// register & include JS
		wp_register_script( 'acf-input-FIELD_NAME', "{$url}assets/js/input.js", array('acf-input'), $version );
		wp_enqueue_script('acf-input-FIELD_NAME');


		// register & include CSS
		wp_register_style( 'acf-input-FIELD_NAME', "{$url}assets/css/input.css", array('acf-input'), $version );
		wp_enqueue_style('acf-input-FIELD_NAME');

	}

	*/


	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	/*

	function input_admin_head() {



	}

	*/


	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/

		/*

   	function input_form_data( $args ) {



   	}

		*/


	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	/*

	function input_admin_footer() {



	}

	*/


	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	/*

	function field_group_admin_enqueue_scripts() {

	}

	*/

	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	/*

	function field_group_admin_head() {

	}

	*/






	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/

	/*

	function validate_value( $valid, $value, $field, $input ){

		// Basic usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = false;
		}


		// Advanced usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = __('The value is too little!','acf-FIELD_NAME'),
		}


		// return
		return $valid;

	}

	*/


	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/

	/*

	function delete_value( $post_id, $key ) {



	}

	*/


	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function load_field( $field ) {

		return $field;

	}

	*/


	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function update_field( $field ) {

		return $field;

	}

	*/


	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/

	/*

	function delete_field( $field ) {



	}

	*/


}
// initialize
new acf_field_dc_coupons( array() );
// class_exists check
endif;

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
  'key' => 'group_coupons_dealer',
  'title' => 'Coupons',
  'fields' => array (
    array (
      'key' => 'dcc_coupons',
      'label' => 'Active coupons',
      'name' => 'dcc_coupons',
      'type' => 'dc_coupons',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'font_size' => 14,
    ),
  ),
  'location' => array (
    array (
      array (
        'param' => 'post_type',
        'operator' => '==',
        'value' => 'edls',
      ),
    ),
  ),
  'menu_order' => 0,
  'position' => 'normal',
  'style' => 'default',
  'label_placement' => 'top',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => 1,
  'description' => '',
));

endif;

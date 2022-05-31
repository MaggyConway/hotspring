<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_wpd_dealer_type{

  public function __construct(){
    add_action( 'init', array( $this, 'new_type'), 0);
    //add_action( 'init', array( $this, 'dealer_posts'), 0);

    add_action( 'restrict_manage_posts', array( $this, 'admin_posts_filter_state_code') );
    //init fields
    $this->init_fields();

    //add custom filters
    add_filter( 'parse_query', array( $this, 'admin_posts_filter_state_code_posts_filter' ) );
    add_filter( 'parse_query', array( $this, 'admin_posts_filter_rsm_filter' ) );
    add_filter( 'parse_query', array( $this, 'admin_posts_filter_dealer_filter' ) );

    add_filter( 'manage_edit-edls_columns', array( $this, 'posts_columns' ), 10, 1 );
    add_action( 'manage_posts_custom_column', array( $this, 'column_page_content' ), 10, 2 );
  }

  public function posts_columns( $columns ) {
  	$new_columns = array(
      'd_address' => __( 'Address', 'edls' ),
    );
    $new_2_columns = array(
      'state' => __( 'State', 'edls' ),
    );
    $num = 2;
    $columns = array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
    return array_merge( $columns, $new_2_columns );
  }

  function column_state_field( $post_id ) {
  	if ( $custom_field_value = get_post_meta( $post_id, 'dealership_state_code', true ) ) {
  		return $custom_field_value;
  	}
  	return '';
  }

  function column_address_field( $post_id ) {
    $get = get_post_meta( $post_id );
    $info = [];

    if ( $dealership_address_1 = get_post_meta( $post_id, 'dealership_address_1', true ) ) {
  		$info[] = $dealership_address_1;
  	}
    if ( $dealership_city = get_post_meta( $post_id, 'dealership_city', true ) ) {
  		$info[] = $dealership_city;
    }
    if ( $dealership_state_code = get_post_meta( $post_id, 'dealership_state_code', true ) ) {
  		$info[] = $dealership_state_code;
    }
    if ( $dealership_zip = get_post_meta( $post_id, 'dealership_zip', true ) ) {
  		$info[] = $dealership_zip;
    }

    return implode( ', ', $info );
  }

  function column_page_content( $column_name, $post_id ) {
  	if ( $column_name == 'state' ) {
  		$column_template_list = $this->column_state_field( $post_id );
  		if ( $column_template_list ) {
  			echo '' . $column_template_list . '';
  		}
    }
    if ( $column_name == 'd_address' ) {
  		$column_template_list = $this->column_address_field( $post_id );
  		if ( $column_template_list ) {
  			echo '' . $column_template_list . '';
  		}
  	}
  }

  // Register new Custom Post Type
  public function new_type() {
    // Set UI labels for Custom Post Type
    $labels = array(
      'name'              => _x( 'Dealers', 'Post Type EDL', 'edls' ),
      'singular_name'     => _x( 'Dealer', 'Post Type Singular Name', 'edls' ),
      'menu_name'         => __( 'Dealers', 'edls' ),
      'name_admin_bar'    => __( 'Dealers', 'edls' ),
      'archives'          => __( 'Dealers Archives', 'edls' ),
      'parent_item_colon' => __( 'Parent Dealer', 'edls' ),
      'all_items'         => __( 'All Dealers', 'edls' ),
      'add_new_item'      => __( 'Add New Dealer', 'edls' ),
      'add_new'           => __( 'Add Dealer', 'edls' ),
      'new_item'          => __( 'New Dealer', 'edls' ),
      'edit_item'         => __( 'Edit Dealer', 'edls' ),
      'update_item'       => __( 'Update Dealer', 'edls' ),
      'view_item'         => __( 'View Dealer', 'edls' ),
      'search_items'      => __( 'Search Dealers', 'edls' ),
      'not_found'           => __( 'Not Found', 'edls' ),
      'not_found_in_trash'  => __( 'Not found in Trash', 'edls' ),
    );
    $capabilities = array(
      'edit_post'             => 'edit_dealer',
      'read_post'             => 'read_dealer',
      'delete_post'           => 'delete_dealer',
      'edit_posts'            => 'edit_dealers',
      'edit_others_posts'     => 'edit_others_dealers',
      'publish_posts'         => 'publish_dealers',
      'read_private_posts'    => 'read_private_dealers',
      'create_posts'          => 'create_dealers',
      'import_dealers'        => 'import_dealers',
    );
    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'Dealers', 'edls' ),
        'description'         => __( 'Dealers profile', 'edls' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        //'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        'supports'            => array( 'title', ),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies'          => array(  ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_icon'           => 'dashicons-groups',
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => array( 'slug' => 'hot-tub-dealers' ),
    		'capability_type'     => [ 'dealer', 'dealers' ],
    		'capabilities'        => $capabilities,
    		'map_meta_cap'        => true,
    );
    // Registering your Custom Post Type
    register_post_type( 'edls', $args );
  }

  // init ACF Fields
  public function init_fields(){
    if ( function_exists( "register_field_group" ) ) {
      register_field_group( array(
        'id'         => 'dealership_edls',
        'title'      => 'Dealership',
        'fields'     => array(
          array(
            'key'           => 'navision_message',
            'label'         => 'For Technical Support, please email <a href="mailto:watkins.support@televerde.com">watkins.support@televerde.com</a>.',
            'name'          => 'navision_message',
            'type'          => 'message',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_id',
            'label'         => 'Dealership Id',
            'name'          => 'dealership_id',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_billto',
            'label'         => 'Billto Dealership Id',
            'name'          => 'dealership_billto',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_name',
            'label'         => 'Dealership Name',
            'name'          => 'dealership_name',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_address_1',
            'label'         => 'Address 1',
            'name'          => 'dealership_address_1',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_address_2',
            'label'         => 'Address 2',
            'name'          => 'dealership_address_2',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_city',
            'label'         => 'City',
            'name'          => 'dealership_city',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_state',
            'label'         => 'State',
            'name'          => 'dealership_state',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_state_code',
            'label'         => 'State Code',
            'name'          => 'dealership_state_code',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_country',
            'label'         => 'Country',
            'name'          => 'dealership_country',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_country_code',
            'label'         => 'Country Code',
            'name'          => 'dealership_country_code',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_zip',
            'label'         => 'Zip',
            'name'          => 'dealership_zip',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_territory_code',
            'label'         => 'Territory Code',
            'name'          => 'dealership_territory_code',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_rsm_id',
            'label'         => 'Rsm Id',
            'name'          => 'dealership_rsm_id',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
          array(
            'key'           => 'dealership_phone',
            'label'         => 'Phone',
            'name'          => 'dealership_phone',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1,
            'wrapper'       => array(
              'width' => '',
              'class' => '',
              'id'    => '',
            ),
          ),
          array(
            'key'               => 'dealership_coordinates',
            'label'             => 'Coordinates',
            'name'              => 'dealership_coordinates',
            'type'              => 'google_map',
            'instructions'      => 'This value is computed from the Address field',
            'required'          => 0,
            'conditional_logic' => 0,
            'wrapper'           => array(
              'width' => '',
              'class' => '',
              'id'    => '',
            ),
            'center_lat'        => '',
            'center_lng'        => '',
            'zoom'              => 17,
            'height'            => '',
          ),
          array(
            'key' => 'dealership_exclude',
            'label' => 'Exclude',
            'name' => 'dealership_exclude',
            'type' => 'true_false',
            'instructions' => 'Exclude from search index, sitemap.xml file as well as add a noindex tag',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
          ),
          array(
            'key'           => 'dealership_record_type',
            'label'         => 'Dealership Record Type',
            'name'          => 'dealership_record_type',
            'type'          => 'text',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
            //'readonly'      => 1
          ),
        ),
        'location'   => array(
          array(
            array(
              'param'    => 'post_type',
              'operator' => '==',
              'value'    => 'edls',
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
      ) );
      register_field_group( array(
        'id'         => 'dealer_edls',
        'title'      => 'Dealer',
        'fields'     => array(
          array(
            'key'           => 'dealer_reference',
            'label'         => 'Dealer Reference',
            'name'          => 'dealer_reference',
            'type'          => 'text',
            'instructions'  => 'Separate multiple parameters with a comma',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
          ),
        ),
        'location'   => array(
          array(
            array(
              'param'    => 'ef_user',
              'operator' => '==',
              'value'    => 'dealer',
              'order_no' => 0,
              'group_no' => 0,
            ),
            array(
              'param'    => 'user_type',
              'operator' => '==',
              'value'    => 'administrator',
              'order_no' => 1,
              'group_no' => 0,
            ),
          ),
          array(
            array(
              'param'    => 'ef_user',
              'operator' => '==',
              'value'    => 'internal',
              'order_no' => 0,
              'group_no' => 1,
            ),
            array(
              'param'    => 'user_type',
              'operator' => '==',
              'value'    => 'administrator',
              'order_no' => 1,
              'group_no' => 1,
            ),
          ),
        ),
        'options'    => array(
          'position'       => 'normal',
          'layout'         => 'default',
          'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
      ) );
      register_field_group( array(
        'id'         => 'flags_edls',
        'title'      => 'Custom field locks',
        'fields'     => array(
          array (
          'key' => 'locking_edls_title',
          'label' => 'Lock Title',
          'name' => 'locking_edls_title',
          'type' => 'true_false',
          'instructions' => 'If this box is checked, the title of this dealer will be locked and the title will not change when new data is Imported.',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array (
          'width' => '',
          'class' => '',
          'id' => '',
          ),
          'message' => '',
          'default_value' => 0,
          'ui' => 0,
          'ui_on_text' => '',
          'ui_off_text' => '',
          ),
          array (
          'key' => 'locking_edls_address',
          'label' => 'Lock Address',
          'name' => 'locking_edls_address',
          'type' => 'true_false',
          'instructions' => 'If this box is checked, the address of this dealer will be locked and the address will not change when new data is imported.',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array (
          'width' => '',
          'class' => '',
          'id' => '',
          ),
          'message' => '',
          'default_value' => 0,
          'ui' => 0,
          'ui_on_text' => '',
          'ui_off_text' => '',
          ),
        ),
        'location'   => array(
          array(
            array(
              'param' => 'current_user_role',
              'operator' => '==',
              'value' => 'administrator',
            ),
            array(
              'param' => 'post_type',
              'operator' => '==',
              'value' => 'edls',
            ),
          ),
          array(
            array(
              'param' => 'current_user_role',
              'operator' => '==',
              'value' => 'dealer_support_staff',
            ),
            array(
              'param' => 'post_type',
              'operator' => '==',
              'value' => 'edls',
            ),
          ),
        ),
        'options'    => array(
          'position'       => 'normal',
          'layout'         => 'default',
          'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
      ));
      register_field_group( array(
        'id'         => 'rsm_edls',
        'title'      => 'Regional Sales Manager',
        'fields'     => array(
          array(
            'key'           => 'rsm_reference',
            'label'         => 'RSM Reference',
            'name'          => 'rsm_reference',
            'type'          => 'text',
            'instructions'  => 'Separate multiple parameters with a comma',
            'default_value' => '',
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'formatting'    => 'none',
            'maxlength'     => '',
          ),
        ),
        'location'   => array(
          array(
            array(
              'param'    => 'ef_user',
              'operator' => '==',
              'value'    => 'rsm',
              'order_no' => 0,
              'group_no' => 1,
            ),
            array(
              'param'    => 'user_type',
              'operator' => '==',
              'value'    => 'administrator',
              'order_no' => 1,
              'group_no' => 1,
            ),
          ),
        ),
        'options'    => array(
          'position'       => 'normal',
          'layout'         => 'default',
          'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
      ) );
    }
    // Field to read only for not admin roles
    add_filter('acf/load_field/key=dealership_id', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_billto', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_name', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_address_1', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_address_2', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_city', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_state', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_state_code', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_country', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_country_code', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_zip', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_territory_code', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_rsm_id', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_phone', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_exclude', array( $this, 'admin_field_only') );
    add_filter('acf/load_field/key=dealership_record_type', array( $this, 'admin_field_only') );


    // Remove google map (google map cannot be read only)
    add_filter('acf/prepare_field/key=dealership_coordinates', array( $this, 'admin_update_field_only') );
    add_filter('acf/prepare_field/key=locking_edls_title', array( $this, 'locking_fields_access') );
    add_filter('acf/prepare_field/key=locking_edls_address', array( $this, 'locking_fields_access') );
    add_filter('acf/prepare_field/key=dealership_exclude', array( $this, 'admin_update_field_only') );
  }

  //cellback field
  public function admin_field_only( $field ) {
  	if ( ! in_array( 'administrator', wp_get_current_user()->roles ) ) {
  		$field ['readonly'] = true;
  	}
  	return $field;
  }

  // Remove field if user haven't the administrator role
  public function admin_update_field_only( $field ) {
  	if ( ! in_array( 'administrator', wp_get_current_user()->roles ) ) {
  		return false;
  	}
  	return $field;
  }

  public function locking_fields_access( $field ) {
    if (  in_array( 'administrator', wp_get_current_user()->roles ) || in_array( 'dealer_support_staff', wp_get_current_user()->roles ) ) {
      return $field;
    }
    return false;
  }

  /**
   * Filters on edit.php page
   * Custom state filter
   */
  public function admin_posts_filter_state_code() {
  	$type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
  	if ( $type = 'edls' ) {
  		//change this to the list of values you want to show
  		//in 'label' => 'value' format
  		$values = array(
  			"AL" => "Alabama",
  			"AK" => "Alaska",
  			"AS" => "American Samoa",
  			"AZ" => "Arizona",
  			"AR" => "Arkansas",
  			"CA" => "California",
  			"CO" => "Colorado",
  			"CT" => "Connecticut",
  			"DE" => "Delaware",
  			"DC" => "District Of Columbia",
  			"FM" => "Federated States Of Micronesia",
  			"FL" => "Florida",
  			"GA" => "Georgia",
  			"GU" => "Guam",
  			"HI" => "Hawaii",
  			"ID" => "Idaho",
  			"IL" => "Illinois",
  			"IN" => "Indiana",
  			"IA" => "Iowa",
  			"KS" => "Kansas",
  			"KY" => "Kentucky",
  			"LA" => "Louisiana",
  			"ME" => "Maine",
  			"MH" => "Marshall Islands",
  			"MD" => "Maryland",
  			"MA" => "Massachusetts",
  			"MI" => "Michigan",
  			"MN" => "Minnesota",
  			"MS" => "Mississippi",
  			"MO" => "Missouri",
  			"MT" => "Montana",
  			"NE" => "Nebraska",
  			"NV" => "Nevada",
  			"NH" => "New Hampshire",
  			"NJ" => "New Jersey",
  			"NM" => "New Mexico",
  			"NY" => "New York",
  			"NC" => "North Carolina",
  			"ND" => "North Dakota",
  			"MP" => "Northern Mariana Islands",
  			"OH" => "Ohio",
  			"OK" => "Oklahoma",
  			"OR" => "Oregon",
  			"PW" => "Palau",
  			"PA" => "Pennsylvania",
  			"PR" => "Puerto Rico",
  			"RI" => "Rhode Island",
  			"SC" => "South Carolina",
  			"SD" => "South Dakota",
  			"TN" => "Tennessee",
  			"TX" => "Texas",
  			"UT" => "Utah",
  			"VT" => "Vermont",
  			"VI" => "Virgin Islands",
  			"VA" => "Virginia",
  			"WA" => "Washington",
  			"WV" => "West Virginia",
  			"WI" => "Wisconsin",
  			"WY" => "Wyoming"
  		);
  		?>
  		<select name="state_code">
  			<option value="">Filter By State</option>
  			<?php
  			$current_v = isset( $_GET['state_code'] ) ? $_GET['state_code'] : '';
  			foreach ( $values as $code => $name ) {
  				printf
  				(
  					'<option value="%s"%s>%s</option>',
  					$code,
  					$code == $current_v ? ' selected="selected"' : '',
  					$name
  				);
  			}
  			?>
  		</select>
  		<?php
  	}
  }

  // filre by state code
  public function admin_posts_filter_state_code_posts_filter( $query ) {
  	global $pagenow;
  	$type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
  	if ( 'edls' == $type
  	     && is_admin()
  	     && $pagenow == 'edit.php'
  	     && isset( $_GET['state_code'] )
  	     && $_GET['state_code'] != ''
  	) {
  		$query->query_vars['meta_key']   = 'dealership_state_code';
  		$query->query_vars['meta_value'] = $_GET['state_code'];
  	}
  }

  // filter for rsm role
  public function admin_posts_filter_rsm_filter( $query ) {
  	global $pagenow;
  	$type    = isset( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
  	$user    = wp_get_current_user();
  	$user_id = $user->ID;
  	if ( in_array( 'rsm', (array) $user->roles )
  	     && $type == 'edls'
  	     && $pagenow == 'edit.php'
  	) {
  		$data = get_user_meta( $user_id, 'rsm_reference', true );
  		$data = explode( ',', $data );
  		$data = array_map( trim, $data );
  		//filter br RSM ID
  		if ( ! empty( $data[0] ) ) {
  			// $query->query_vars['meta_key'] = 'dealership_rsm_id';
  			// $query->query_vars['meta_value'] = $data[0];
  			$query->set( 'meta_query', array(
  				array(
  					'key'     => 'dealership_rsm_id',
  					'value'   => $data,
  					'compare' => 'IN',
  				)
  			) );
  		}
  	}
  }

  // filter for dealer role
  public function admin_posts_filter_dealer_filter( $query ) {
  	global $pagenow;
  	$type    = isset( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
  	$user    = wp_get_current_user();
  	$user_id = $user->ID;
  	if ( in_array( 'dealer', (array) $user->roles )
  	     && $type == 'edls'
  	     && $pagenow == 'edit.php'
  	) {
  		$data = get_user_meta( $user_id, 'dealer_reference', true );
  		$data = explode( ',', $data );
  		$data = array_map( 'trim', $data );
  		//filter br Dealership ID
  		if ( ! empty( $data[0] ) ) {
  			// $query->query_vars['meta_key'] = 'dealership_id';
  			// $query->query_vars['meta_value'] = $data[0];
  			$query->set( 'meta_query', array(
  				'relation' => 'OR',
  				array(
  					'key'     => 'dealership_id',
  					'value'   => $data,
  					'compare' => 'IN',
  				),
  				array(
  				 	'key'     => 'dealership_billto',
  				 	'value'   => $data,
  				 	'compare' => 'IN',
  				),
  			) );
  		}
  	}
  }
}

new class_wpd_dealer_type();

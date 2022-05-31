<?php

GFForms::include_addon_framework();

class GFGT extends GFAddOn {

  protected $_min_gravityforms_version = '1.9';
  protected $_slug                     = 'GT';
  protected $_path                     = 'GT/setting.php';
  protected $_full_path                = __FILE__;
  protected $_title                    = 'GT Add-On';
  protected $_short_title              = 'GT Datalaer';

  private static $_instance = null;

  /**
   * Get an instance of this class.
   *
   * @return GFutm
   */
  public static function get_instance() {
    if ( self::$_instance == null ) {
      self::$_instance = new GFGT();
    }
    return self::$_instance;
  }

  /**
   * Handles hooks
   */
  public function init() {
    parent::init();
    add_action( 'wp_footer', [ $this, 'print_my_inline_script' ] );
  }

  /**
   * Return an array of Google GT Event supporter fields which can be mapped to the Form fields/entry meta.
   *
   * @return array
   */
  public function constituent_field_map() {
    $field_map          = array();
    $constituent_fields = array(
      'zipcode'            => array(),
      'purchase_timeframe' => array(),
      'email_opt_in'       => array(),
    );

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
   * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
   *
   * @return array
   */
  public function form_settings_fields( $form ) {
    return array(
      array(
        'title'  => esc_html__( 'Add to GT dataLayer', 'gfgt' ),
        'fields' => array(
          array(
            'type'    => 'checkbox',
            'name'    => 'send',
            'choices' => array(
              array(
                'label' => 'Send this data to GT',
                'name'  => 'send_this_data'
              )
            )
          ),
          array(
            'name'     => 'event',
            'label'    => esc_html__( 'Event', 'gfgt' ),
            'type'     => 'text',
            'required' => true,
            'class'    => 'medium',
            'tooltip'  => '<h6>' . esc_html__( 'Name', 'gfgt' ) . '</h6>' . esc_html__( 'Custom Event name <a href="https://support.google.com/tagmanager/answer/7679219?hl=en">(info)</a>', 'gfgt' ),
          ),
          array(
            'name'     => 'form_name',
            'label'    => esc_html__( 'Form name', 'gfgt' ),
            'type'     => 'text',
            'required' => true,
            'class'    => 'medium',
            'tooltip'  => '<h6>' . esc_html__( 'Name', 'gfgt' ) . '</h6>' . esc_html__( 'Form name', 'gfgt' ),
          ),
          array(
            'label'   => 'Email Opt In',
            'type'    => 'checkbox',
            'name'    => 'hotspring_opt_in',
            'tooltip' => 'Email Opt-In option is always Yes',
            'choices' => array(
              array(
                'label' => 'Opt-In option is always Yes',
                'name'  => 'email_opt_in',
              ),
            ),
          ),
          array(
            'name'      => 'mappedFields',
            'label'     => esc_html__( 'Additional fields', 'gfgt' ),
            'type'      => 'dynamic_field_map',
            'field_map' => $this->constituent_field_map(),
            'tooltip'   => esc_html__( 'Additional fields added to dataLayer', 'gfgt' ),
          ),
        ),
      ),
    );
  }

  public function print_my_inline_script() {
    if ( isset( $_GET['sid'] ) ) {
      $entry = GFAPI::get_entry( $_GET['sid'] );
      $form  = GFAPI::get_form( $entry['form_id'] );
      $feeds = GFAPI::get_feeds( null, $entry['form_id'] );
      $params = [
        'event'     => $form['GT']['event'],
        'form_name' => $form['GT']['form_name'],
      ];
      if ( $form['GT']['email_opt_in'] == true ) {
        $params['email_opt_in'] = 'Yes';
      }
      if ( $form['GT'] && isset( $form['GT']['event'] ) && ! empty( $form['GT']['event'] )
      && isset($form['GT']['send_this_data']) && $form['GT']['send_this_data'] ) {
        foreach ( $form['GT']['mappedFields'] as $name => $field_id ) {
          $field_value = rgar( $entry, $field_id['value'] );
          if ( ! empty( $field_value ) ) {
            if ( isset( $field_id['custom_key'] ) && ! empty( $field_id['custom_key'] ) ) {
              $params[ $field_id['custom_key'] ] = $field_value;
            } else {
              $params[ $field_id['key'] ] = $field_value;
            }
          }
        }
        $params['email_opt_in'] = ( ! empty( $params['email_opt_in'] ) ) ? 'Yes' : 'No';
        global $dataLayer;
        $dataLayer[] = $params;
      }
    }
  }
}

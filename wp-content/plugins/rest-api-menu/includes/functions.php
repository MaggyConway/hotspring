<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

/**
 * Locate template.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme/wpram/$template_name
 * 2. /themes/theme/wpram
 * 3. /plugins/plugin-templates/templates/$template_name.
 *
 * @since 0.0.1
 *
 * @param 	string 	$template_name			Template to load.
 * @param 	string 	$string $template_path	Path to templates.
 * @param 	string	$default_path			Default path to template files.
 * @return 	string 	Path to the template file.
 */
function wpram_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	// Set variable to search in woocommerce-plugin-templates folder of theme.
  if ( !$template_path ) {
    $template_path = 'wpram/';
  }

	// Set default plugin templates path.
    if ( !$default_path ) {
        $default_path = WPRAM_PLUGIN_DIR . 'templates/'; // Path to the template folder
    }

	// Search template file in theme folder.
	$template = locate_template( array(
		$template_path . $template_name,
		$template_name
	) );

	// Get plugins template file.
  if ( !$template ) {
    $template = $default_path . $template_name;
  }

	return apply_filters( 'wpram_locate_template', $template, $template_name, $template_path, $default_path );

}

/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @since 0.0.1
 *
 * @see wcpt_locate_template()
 *
 * @param string 	$template_name			Template to load.
 * @param array 	$args					Args passed for the template file.
 * @param string 	$string $template_path	Path to templates.
 * @param string	$default_path			Default path to template files.
 */
function wpram_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
	$template_file = wpram_locate_template( $template_name, $tempate_path, $default_path );

  if ( !file_exists($template_file) ) {
    _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '0.0.1');
    return;
  }

  ob_start();
  // extract argumets to template
  if ( isset($args) && is_array($args) ) {
    extract($args);
  }
  include $template_file;
  $buffer = ob_get_clean();
  return $buffer;

}
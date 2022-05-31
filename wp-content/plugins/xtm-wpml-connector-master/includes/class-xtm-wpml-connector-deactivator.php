<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://xtm-intl.com/
 * @since      1.0.0
 *
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/includes
 * @author     XTM International <support@xtm-intl.com>
 */
class Xtm_Wpml_Connector_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        wp_delete_user(Xtm_Wpml_Bridge::PLUGIN_NAME);
    }
}

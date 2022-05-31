<?php

/**
 * Fired during plugin activation
 *
 * @link       https://xtm-intl.com/
 * @since      1.0.0
 *
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/includes
 * @author     XTM International <support@xtm-intl.com>
 */
class Xtm_Wpml_Connector_Activator
{
    const NO_REPLY_XMT_INTL_COM = "no-reply@xmt-intl.com";
    const SITEPRESS_MULTILINGUAL_CMS_SITEPRESS_PHP = 'sitepress-multilingual-cms/sitepress.php';

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        if (is_plugin_active(self::SITEPRESS_MULTILINGUAL_CMS_SITEPRESS_PHP)) {
            self::create_user();
            self::create_table_xtm_cron();
            self::create_table_xtm_projects();
            self::create_table_xtm_callbacks();
        } else {
            wp_die(__('You have to install WPML plugin first', Xtm_Wpml_Bridge::PLUGIN_NAME));
        }

    }

    private static function create_table_xtm_cron()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "xtm_cron";
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT 'new',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `job_id_UNIQUE` (`job_id`)
) ;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_table_xtm_projects()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "xtm_projects";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `api_template_id` int(11) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `source_language` varchar(45) DEFAULT NULL,
  `target_language` varchar(511) DEFAULT NULL,
  `reference` int(11) DEFAULT NULL,
  `wpml_job_id` longtext,
  `status` varchar(45) DEFAULT NULL,
  `items` longtext,
  `created` datetime DEFAULT NULL,
  `api_project_mode` int(11) DEFAULT '0',
  `type` varchar(45) DEFAULT NULL,
  `xtm_link` varchar(255) DEFAULT NULL,
  `client_name` varchar(255)  DEFAULT NULL,
  PRIMARY KEY (`project_id`)
) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_user()
    {
        $user_name = Xtm_Wpml_Bridge::PLUGIN_NAME;
        $user_email = self::NO_REPLY_XMT_INTL_COM;
        $user_id = username_exists($user_name);
        if (!$user_id and email_exists($user_email) == false) {
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
            $user_id = wp_create_user($user_name, $random_password, $user_email);
            $u = new WP_User( $user_id );
            $u->add_role( 'translate' );
        }

        add_option( Xtm_Wpml_Bridge::PLUGIN_USER_ID , $user_id, '', 'yes' );
    }

    private static function create_table_xtm_callbacks()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "xtm_callbacks";
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `wpml_job_id` varchar(511) NOT NULL,
  `xtm_project_id` int(11) NOT NULL,
  `xtm_job_id` int(11)DEFAULT 0,
  `xtm_customer_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
  )
  ;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

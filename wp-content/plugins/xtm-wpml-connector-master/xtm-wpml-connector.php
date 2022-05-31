<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://xtm-intl.com/
 * @since             1.1.0
 * @package           Xtm_Wpml_Connector
 *
 * @wordpress-plugin
 * Plugin Name:       XTM WPML Connector
 * Plugin URI:        https://xtm-intl.com/
 * Description:       XTM Connector for Wordpress
 * Version:           1.1.10
 * Author:            XTM International
 * Author URI:        https://xtm-intl.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       xtm-wpml-connector
 * Domain Path:       /languages
 * Tested with:       WPML Multilingual CMS Version 4.4.12, WPML String Translation Version 3.0.6, WPML Translation Management Version 2.10.7,  WPML Media 2.6.5
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-xtm-wpml-connector-activator.php
 */
function activate_xtm_wpml_connector()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-xtm-wpml-connector-activator.php';
    Xtm_Wpml_Connector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-xtm-wpml-connector-deactivator.php
 */
function deactivate_xtm_wpml_connector()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-xtm-wpml-connector-deactivator.php';
    Xtm_Wpml_Connector_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_xtm_wpml_connector');
register_deactivation_hook(__FILE__, 'deactivate_xtm_wpml_connector');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-xtm-wpml-connector.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_xtm_wpml_connector()
{

    $plugin = new Xtm_Wpml_Connector();
    $plugin->run();
}

run_xtm_wpml_connector();


/**
 * @param WP_REST_Request $request
 * @return bool|WP_Error
 */
function remote_xtm_callback(WP_REST_Request $request)
{
    $bridge = new Xtm_Wpml_Bridge();
    return $bridge->remote_callback($request);
}

/**
 * @param $schedules
 * @return mixed
 */
function cron_time_intervals($schedules)
{
    if (!isset($schedules["1min"])) {
        $schedules["1min"] = [
            'interval' => 60,
            'display'  => __('Once every 1 minute')
        ];
    }
    if (!isset($schedules["15sec"])) {
        $schedules["15sec"] = [
            'interval' => 15,
            'display'  => __('Once every 15 seconds')
        ];
    }
    if (!isset($schedules["30sec"])) {
        $schedules["30sec"] = [
            'interval' => 30,
            'display'  => __('Once every 30 seconds')
        ];
    }
    if (!isset($schedules["3min"])) {
        $schedules["3min"] = [
            'interval' => 180,
            'display'  => __('Once every 180 seconds')
        ];
    }
    return $schedules;
}

add_filter('cron_schedules', 'cron_time_intervals');

function xtm_zen_remove_nbsps($content) {
    return $content;
}
add_filter('content_save_pre', 'xtm_zen_remove_nbsps');

if (!wp_next_scheduled('automatic_send_to_xtm_hook')) {
    wp_schedule_event(time(), '1min', 'automatic_send_to_xtm_hook');
}

if (!wp_next_scheduled('automatic_callbacks_hook')) {
    wp_schedule_event(time(), '15sec', 'automatic_callbacks_hook');
}

add_action('automatic_send_to_xtm_hook', 'automatic_send_to_xtm_function');
add_action('automatic_callbacks_hook', 'automatic_callbacks_function');

if (!wp_next_scheduled('automatic_remove_xtm_projects_hook')) {
    wp_schedule_event(time(), '1min', 'automatic_remove_xtm_projects_hook');
}

add_action('automatic_remove_xtm_projects_hook', 'automatic_remove_from_xtm_function');
/**
 * checking if WPML job exists
 */
function automatic_remove_from_xtm_function()
{
    $job_list_id = Xtm_Provider_Jobs::get_post_ids();
    $wpml_string_list_id = Xtm_Provider_Jobs::get_string_ids();
    $project_list = Xtm_Model_Projects::get_all();
    if (empty($job_list_id) || empty($wpml_string_list_id)) {
        return;
    }

    foreach ($project_list as $project) {
        $project_id = $project['project_id'];
        switch ($project['type']) {
            case "Post" :
                if (!in_array($project['wpml_job_id'], $job_list_id)) {
                    Xtm_Provider_Projects::cancel_project($project_id);
                }
                break;
            case "String" :
                if (!in_array($project['wpml_job_id'], $wpml_string_list_id)) {
                    Xtm_Provider_Projects::cancel_project($project_id);
                }
                break;
        }
    }
}

/**
 *
 */
function automatic_send_to_xtm_function()
{
    $sent_to_xtm = new Xtm_Wpml_Connector_Send_To_XTM();
    $sent_to_xtm->cron_jobs();
}

/**
 *
 */
function automatic_callbacks_function()
{
    $sent_to_xtm = new Xtm_Wpml_Connector_Callbacks();
    $sent_to_xtm->run();
}


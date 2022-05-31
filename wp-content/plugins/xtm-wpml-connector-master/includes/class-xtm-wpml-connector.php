<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://xtm-intl.com/
 * @since      1.0.0
 *
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/includes
 * @author     XTM International <support@xtm-intl.com>
 */
class Xtm_Wpml_Connector
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Xtm_Wpml_Connector_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->plugin_name = 'xtm-wpml-connector';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Xtm_Wpml_Connector_Loader. Orchestrates the hooks of the plugin.
     * - Xtm_Wpml_Connector_i18n. Defines internationalization functionality.
     * - Xtm_Wpml_Connector_Admin. Defines all hooks for the admin area.
     * - Xtm_Wpml_Connector_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-xtm-wpml-connector-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-xtm-wpml-connector-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-xtm-wpml-connector-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-xtm-wpml-connector-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-xtm-wpml-bridge.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-xtm-wpml-connector-send-to-xtm.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-xtm-wpml-connector-callbacks.php';

        /**
         * Helpers
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/helper/class-xtm-wpml-connector-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/helper/class-xtm-wpml-connector-helper-string.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/helper/class-xtm-wpml-connector-helper-post.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/helper/class-xtm-wpml-connector-helper-one-project-creation.php';

        /**
         * List tables
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/list-table/class-xtm-wpml-list-table-jobs.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/list-table/class-xtm-wpml-list-table-projects.php';

        /**
         * Models
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-project.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-callbacks.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-cron.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-icl-strings.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-icl-string-translations.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-icl-translate-job.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-icl-translation-batches.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-icl-translation-status.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-icl-translations.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/model/class-xtm-wpml-connector-model-icl-translate.php';

        /**
         * Providers
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/provider/class-xtm-wpml-connector-provider-jobs.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/provider/class-xtm-wpml-connector-provider-projects.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/provider/class-xtm-wpml-connector-provider-readable-state.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/provider/class-xtm-wpml-connector-provider-zip.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/provider/class-xtm-wpml-connector-provider-translation-jobs-table.php';

        /**
         * Services
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/service/class-xtm-wpml-connector-service-retrieve-translation.php';

        $this->loader = new Xtm_Wpml_Connector_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Xtm_Wpml_Connector_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Xtm_Wpml_Connector_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Xtm_Wpml_Connector_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Add menu item

        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'options_update');
        //$this->loader->add_action('added_option', $plugin_admin, 'check_xtm_connection');

// Add Settings link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

        $this->loader->add_action('wp_ajax__ajax_fetch_job_list', $plugin_admin, 'ajax_fetch_job_list_callback');
        $this->loader->add_action('wp_ajax__ajax_check_xtm_status', $plugin_admin, 'ajax_check_xtm_status');
        $this->loader->add_action('wp_ajax__ajax_xtm_cancel_project', $plugin_admin, 'ajax_xtm_cancel_project');
        $this->loader->add_action('wp_ajax__ajax_fetch_project_list', $plugin_admin,
            'ajax_fetch_project_list_callback');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Xtm_Wpml_Connector_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('rest_api_init', $plugin_public, 'add_remote_xtm_callback');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Xtm_Wpml_Connector_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}

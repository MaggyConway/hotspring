<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://xtm-intl.com/
 * @since      1.0.0
 *
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/admin
 * @author     XTM International <support@xtm-intl.com>
 */
class Xtm_Wpml_Connector_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Xtm_Wpml_Connector_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Xtm_Wpml_Connector_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/xtm-wpml-connector-admin.css', [],
            $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Xtm_Wpml_Connector_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Xtm_Wpml_Connector_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/xtm-wpml-connector-admin.js', ['jquery'],
            $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/xtm-wpml-connector-admin-projects.js',
            ['jquery'],
            $this->version, false);
    }

    /**
     *
     */
    function ajax_fetch_project_list_callback()
    {
        $wpListTable = new WPML_Projects_List_Table();
        $filter = Xtm_Wpml_Connector_Helper::get_filter_project();
        $wpListTable->data = Xtm_Model_Projects::get_all($filter);
        $wpListTable->ajax_response();
    }

    /**
     *
     */
    function ajax_fetch_job_list_callback()
    {
        $_POST = array_merge($_POST, Xtm_Provider_Jobs::get_filter_job());
        $jobs = Xtm_Provider_Jobs::get_jobs();
        $wp_list_table = new WPML_Jobs_List_Table();
        $wp_list_table->data = $jobs['Flat_Data'];
        $wp_list_table->metrics = $jobs['metrics'];
        $wp_list_table->ajax_response();
    }

    /**
     *
     */
    function ajax_xtm_cancel_project()
    {
        $project_id = filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
        $project = Xtm_Model_Projects::get($project_id);
        $bridge = new Xtm_Wpml_Bridge();
        $status_response = $bridge->check_project_raw_status($project);
        $activity = $status_response->project->activity;
        if (Xtm_Provider_Readable_State::XTM_STATE_DELETED == $activity || Xtm_Provider_Readable_State::XTM_STATE_ARCHIVE == $activity) {
            die(json_encode("Project can not be canceled because it is status in XTM is: " . Xtm_Provider_Readable_State::get_readable_state($activity)));
        }

        $cancel_response = Xtm_Provider_Projects::cancel_project($project_id);
        if ($cancel_response) {
            $job = Xtm_Model_Icl_Translate_Job::get($project->wpml_job_id);
            if (!empty($job)) {
                Xtm_Model_Icl_Translation_Status::update(['status' => 1], ['rid' => $job->rid]);
            }
            Xtm_Model_Projects::delete(['project_id' => $project_id]);
        }
        die(json_encode($cancel_response));
    }

    /**
     *
     */
    function ajax_check_xtm_status()
    {
        $bridge = new Xtm_Wpml_Bridge();
        $project_id = filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
        $response = $bridge->check_project_status($project_id);
        echo json_encode($response);
        exit;
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */

    public function add_plugin_admin_menu()
    {
        add_menu_page(
            'XTM Connector',
            'XTM',
            'manage_options',
            'xtm-connector',
            [$this, 'display_plugin_job_page'],
            plugin_dir_url(__FILE__) . 'xtm.png'
        );
        add_submenu_page(
            'xtm-connector',
            'WPML Job List',
            'WPML Job List',
            'manage_options',
            'xtm-connector'
        );

        add_submenu_page(
            'xtm-connector',
            'XTM Project List',
            'XTM Project List',
            'manage_options',
            'xtm-projects',
            [$this, 'display_plugin_xtm_project_page']
        );

        add_submenu_page(
            'xtm-connector',
            'XTM Connector Options Functions Setup',
            'XTM Settings',
            'manage_options',
            $this->plugin_name,
            [$this, 'display_plugin_setup_page']
        );
    }

    /**
     * Add settings action link to the plugins page.
     * @param array $links
     * @return array
     *
     * @since    1.0.0
     */
    public function add_action_links(array $links)
    {
        $settings_link = [
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings',
                $this->plugin_name) . '</a>',
        ];
        return array_merge($settings_link, $links);
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */

    public function display_plugin_setup_page()
    {
        if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
            include_once('partials/xtm-wpml-connector-admin-display.php');
        } else {
            include_once('partials/xtm-wpml-connector-admin-no-wpml.php');
        }
    }

    /**
     *
     */
    public function display_plugin_xtm_project_page()
    {
        include_once('partials/xtm-wpml-connector-projects-xtm-display.php');
        projects_render_list_page();
        projects_ajax_script();
    }

    /**
     *
     */
    public function display_plugin_job_page()
    {
        $jobs = Xtm_Provider_Jobs::get_jobs();
        include_once('partials/xtm-wpml-connector-jobs-wpml-display.php');
        jobs_render_list_page($jobs['Flat_Data'], $jobs['metrics']);
        jobs_ajax_script();
    }

    /**
     * @param $object
     * @param $box
     */
    public function xtm_post_class_meta_box_callback($object, $box)
    {
        include_once('partials/xtm-wpml-connector-admin-meta-box.php');
    }

    /**
     *
     */
    public function xtm_add_post_meta_boxes()
    {
        add_meta_box(
            $this->plugin_name . '-post-class',      // Unique ID
            esc_html__('XTM', Xtm_Wpml_Bridge::PLUGIN_NAME),    // Title
            [$this, 'xtm_post_class_meta_box_callback'],
            'post',         // Admin page (or post type)
            'side',         // Context
            'high'         // Priority
        );
    }

    /**
     *
     */
    public function options_update()
    {
        register_setting($this->plugin_name, $this->plugin_name, [$this, 'validate']);
    }


    /**
     *
     */
    public function check_xtm_connection()
    {
        $bridge = new Xtm_Wpml_Bridge();
        $bridge->find_customer();
    }

    /**
     * Validates params form settings page
     * @param array $input
     * @return array
     */
    public function validate($input)
    {
        $valid = [];
        $valid[Xtm_Wpml_Bridge::XTM_API_URL] = esc_url($input[Xtm_Wpml_Bridge::XTM_API_URL]);
        $valid[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME]
            = sanitize_text_field($input[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME]);
        $valid[Xtm_Wpml_Bridge::XTM_API_USER_ID] = (int)sanitize_text_field($input[Xtm_Wpml_Bridge::XTM_API_USER_ID]);
        $valid[Xtm_Wpml_Bridge::XTM_PROJECT_CUSTOMER_ID]
            = (int)sanitize_text_field($input[Xtm_Wpml_Bridge::XTM_PROJECT_CUSTOMER_ID]);
        $valid[Xtm_Wpml_Bridge::XTM_API_PASSWORD] = sanitize_text_field($input[Xtm_Wpml_Bridge::XTM_API_PASSWORD]);
        $valid[Xtm_Wpml_Bridge::PROJECT_NAME_PREFIX]
            = sanitize_text_field($input[Xtm_Wpml_Bridge::PROJECT_NAME_PREFIX]);
        $valid[Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL]
            = sanitize_email($input[Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL]);
        $valid[Xtm_Wpml_Bridge::API_TEMPLATE_ID]
            = intval($input[Xtm_Wpml_Bridge::API_TEMPLATE_ID]);
        $valid[Xtm_Wpml_Bridge::XTM_AUTOMATICALLY_MOVE_FLAG]
            = filter_var($input[Xtm_Wpml_Bridge::XTM_AUTOMATICALLY_MOVE_FLAG], FILTER_SANITIZE_NUMBER_INT);
        $valid[Xtm_Wpml_Bridge::XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG]
            = filter_var($input[Xtm_Wpml_Bridge::XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG],
            FILTER_SANITIZE_NUMBER_INT);

        $this->validate_language($input, $valid);
        $this->validate_project_modes($input, $valid);

        return $valid;
    }

    /**
     * @param array $input
     * @param array $valid
     */
    private function validate_language(array $input, array &$valid)
    {
        foreach ($input as $key => $value) {
            if (strpos($key, 'lang') !== false) {
                $langArray = explode("lang-", $key);
                $valid['remote_languages_mappings'][$langArray[1]] = $value;
            }
        }
    }

    /**
     * @param array $input
     * @param array $valid
     */
    private function validate_project_modes(array $input, array &$valid)
    {
        $helper = new Xtm_Wpml_Connector_Helper();
        if (in_array($input[Xtm_Wpml_Bridge::API_PROJECT_MODE], array_keys($helper->get_project_modes()))) {
            $valid[Xtm_Wpml_Bridge::API_PROJECT_MODE] = $input[Xtm_Wpml_Bridge::API_PROJECT_MODE];
        }
    }

}

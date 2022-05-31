<?php


/**
 * Class Xtm_Wpml_Bridge
 */
class Xtm_Wpml_Bridge
{
    const INTEGRATION_KEY = '9105e4057d764870912a45d57cf32338';
    const CREATE_PROJECT_FOR_PMMTOM = 'createProjectForPMMTOM';
    const UPDATE_PROJECT_ACTIVITY = 'updateProjectActivity';
    const CHECK_PROJECT_COMPLETION = 'checkProjectCompletion';
    const DOWNLOAD_PROJECT_MTOM = 'downloadProjectMTOM';
    const FIND_CUSTOMER = 'findCustomer';
    const FIND_TEMPLATE = 'findTemplate';
    const GET_XTM_INFO = 'getXTMInfo';
    const XTM_PROJECT_CUSTOMER_ID = 'xtm_project_customer_id';
    const PROJECT_NAME_PREFIX = 'project_name_prefix';
    const XTM_API_URL = 'xtm_api_url';
    const API_TEMPLATE_ID = 'api_template_id';
    const XTM_API_USER_ID = 'xtm_api_user_id';
    const XTM_API_PASSWORD = 'xtm_api_password';
    const XTM_API_CLIENT_NAME = 'xtm_api_client_name';
    const API_PROJECT_MODE = 'api_project_mode';
    const XTM_TEMPLATE_SCOPE_ALL = "ALL";
    const PLUGIN_NAME = 'xtm-wpml-connector';
    const PLUGIN_USER_ID = 'xtm-wpml-user-id';
    const XTM_ACTION_ARCHIVE = 'ARCHIVE';
    const XTM_TRANSLATOR_EMAIL = 'xtm_translator_email';
    const XTM_AUTOMATICALLY_MOVE_FLAG = 'xmt_automatically_flag';
    const XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG = 'xmt_first_available_automatically_flag';
    const WP_JSON_XTM_WPML_CONNECTOR_V_1_REMOTE = 'wp-json/xtm-wpml-connector/v1/remote?';
    const BLOCK_ZIP = 'Block.zip';
    const PAGES_ZIP = 'Pages.zip';
    const POSTS_ZIP = 'Posts.zip';
    const STRINGS_ZIP = 'Strings.zip';

    protected $availableActions = [
        self::CREATE_PROJECT_FOR_PMMTOM,
        self::UPDATE_PROJECT_ACTIVITY,
        self::FIND_TEMPLATE,
        self::DOWNLOAD_PROJECT_MTOM,
        self::CHECK_PROJECT_COMPLETION,
        self::GET_XTM_INFO,
        self::FIND_CUSTOMER,
        self::CHECK_JOB_COMPLETION,
        self::DOWNLOAD_JOB_MTOM,
    ];

    private $plugin_name = self::PLUGIN_NAME;
    const CHECK_JOB_COMPLETION = 'checkJobCompletion';
    const DOWNLOAD_JOB_MTOM = 'downloadJobMTOM';

    /**
     * @param $xml_job_id
     */
    public function add_to_gutenberg_editor($xml_job_id)
    {
        global $wpml_post_translations;
        $translate = Xtm_Model_Icl_Translate::get_by_field('tid', $xml_job_id);
        $builder = new WPML_TM_Page_Builders_Field_Wrapper($translate->field_type);
        if ($builder->get_string_id()) {
            $package = new WPML_Package($builder->get_package_id());
            $strings = $package->get_translated_strings([]);

            global $wpml_translation_job_factory;
            $translationJob = $wpml_translation_job_factory->get_translation_job($xml_job_id, false, 0);
            $translated_post_id = $wpml_post_translations->element_id_in($translationJob->original_doc_id,
                $translationJob->language_code);

            do_action(
                'wpml_page_builder_string_translated',
                'Gutenberg',
                $translated_post_id,
                $translationJob->original_doc_id,
                $strings,
                $translationJob->language_code
            );
            return $wpml_translation_job_factory;
        }
    }

    /**
     * @param int $project_id
     * @param string $activity
     * @return bool
     * @throws Exception
     */
    public function update_project_activity($project_id, $activity = self::XTM_ACTION_ARCHIVE)
    {
        $projectModel = Xtm_Model_Projects::get($project_id);
        if (empty($projectModel->reference)) {
            throw new Exception("No reference");
        }
        try {
            $input = [
                'projects' => ['id' => $projectModel->reference],
                'options'  => ['activity' => $activity]
            ];
            $response = $this->do_request(self::UPDATE_PROJECT_ACTIVITY, $input);

            return $response->projects->result == 1;
        } catch (\SoapFault $fault) {
            error_log($fault->getMessage());
            return false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function download_job_mtom($job_id){
        try {
            $input = [
                'jobs' => ['id' => $job_id],
            ];
            return $this->do_request(self::DOWNLOAD_JOB_MTOM, $input);

        } catch (\SoapFault $fault) {
            error_log($fault->getMessage());
            return false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    public function check_job_completion($job_id)
    {
        try {
            $input = [
                'jobs' => ['id' => $job_id],

            ];
            return $this->do_request(self::CHECK_JOB_COMPLETION, $input);

        } catch (\SoapFault $fault) {
            error_log($fault->getMessage());
            return false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Get XTM templates
     *
     * @return array
     */
    public function get_templates()
    {
        $translator = get_option($this->plugin_name);
        $customerId = $translator[self::XTM_PROJECT_CUSTOMER_ID];
        try {
            if (true === is_null($customerId)) {
                $customerId = $translator[self::XTM_PROJECT_CUSTOMER_ID];
            }

            $input = ['filter' => ['scope' => self::XTM_TEMPLATE_SCOPE_ALL]];
            $output = [];
            $response = $this->do_request(self::FIND_TEMPLATE, $input);
            if (empty($response->templates)) {
                return $output;
            }
            foreach ($this->parse_to_array($response->templates) as $template) {
                if ((!isset($template->customer)) || $template->customer->id == $customerId) {
                    $output[$template->template->id] = $template->template->name;
                }
            }
            return $output;
        } catch (\SoapFault $fault) {
            error_log($fault->getMessage());
            $this->display_error($fault->getMessage());
            return [];
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->display_error($e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve translated project from XTM service.
     * @param $project_id
     * @return bool
     */
    public function retrieve_translation($project_id)
    {
        $project_model = Xtm_Model_Projects::get($project_id);

        try {
            if (!is_writable(get_temp_dir())) {
                throw new Exception('The temporary directory is not writable. Please check settings or the permissions on <i>@name</i> directory.',
                    ['@name' => get_temp_dir()]);
            }
            $reference = $project_model->reference;
            if (empty($reference)) {
                throw new Exception('No reference found');
            }
            $filesMTOM = $this->do_request(self::DOWNLOAD_PROJECT_MTOM, ['project' => ['id' => $reference]]);
            if (empty($filesMTOM->project->jobs)) {
                throw new Exception("Could not get translated files from project
                or project has not been completed.");
            }

            foreach ($this->parse_to_array($filesMTOM->project->jobs) as $xtm_job) {
                $this->propagate_translated_data($project_model, $xtm_job);
            }

            $xml_jobs = json_decode($project_model->wpml_job_id, true);
            global $wpdb, $wpml_post_translations, $wpml_term_translations;
            $complete = (Xtm_Provider_Readable_State::XTM_STATE_FINISHED === $project_model->status);
            $wpml_tm_records = new WPML_TM_Records($wpdb, $wpml_post_translations, $wpml_term_translations);

            foreach ($xml_jobs as $xml_job_id) {
                $save_data_action = new WPML_Save_Translation_Data_Action([
                    'job_id'   => (int)$xml_job_id,
                    'complete' => $complete,
                    'fields'   => []
                ], $wpml_tm_records);
                $save_data_action->save_translation();

                $this->add_to_gutenberg_editor($xml_job_id);
            }


            return true;
        } catch (\SoapFault $fault) {
            error_log($fault->getMessage());
            return Xtm_Wpml_Connector_Helper::display_error_notice(__('Exception during retrieving xtm data'),
                $fault->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            return Xtm_Wpml_Connector_Helper::display_error_notice(__('Exception during retrieving xtm data'),
                $e->getMessage());
        }
    }

    /**
     * @param $project
     * @return mixed|null|string
     * @throws Exception
     */
    public function check_project_raw_status($project)
    {
        if (empty($project->reference)) {
            throw new Exception("No reference");
        }
        $input = ['project' => ['id' => $project->reference]];
        try {
            $response = $this->do_request(self::CHECK_PROJECT_COMPLETION, $input);
            if (empty($response->project)) {
                return null;
            }
        } catch (\SoapFault $fault) {
            error_log($fault->getMessage());
            return Xtm_Wpml_Connector_Helper::display_error_notice(
                __("Unable to connect with XTM via Soap"),
                $fault->getMessage()
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            return Xtm_Wpml_Connector_Helper::display_error_notice(__('Exception during checking xtm status'),
                $e->getMessage());
        }
        return $response;
    }

    /**
     * @param $project_id
     * @return string
     * @throws Exception
     */
    public function check_project_status($project_id)
    {
        $project = Xtm_Model_Projects::get($project_id);
        $response = $this->check_project_raw_status($project);
        $output = $response->project;
        $output->jobs = $this->parse_to_array($response->project->jobs);
        $project_status = $output->status;
        $project_activity = $output->activity;
        if ($project_activity && ($project_status === Xtm_Provider_Readable_State::XTM_STATE_FINISHED)) {
            Xtm_Model_Projects::update(["status" => $project_status], ['project_id' => $project_id]);
        }

        if (
            ($project_status === Xtm_Provider_Readable_State::XTM_STATE_FINISHED) ||
            (
                ("1" === $project->api_project_mode)
                && ($project_status === Xtm_Provider_Readable_State::XTM_STATE_PARTIALLY_FINISHED)
            )
        ) {
            $this->retrieve_translation($project_id);
            if ($project_status === Xtm_Provider_Readable_State::XTM_STATE_FINISHED) {
                $project_activity = $project_status;
            }
        }
        if ($project_activity) {
            Xtm_Model_Projects::update(["status" => $project_activity], ['project_id' => $project_id]);
        }
        return Xtm_Wpml_Connector_Helper::display_success_notice(
            __("The project ID: ") . $project_id,
            Xtm_Provider_Readable_State::get_readable_state($project_activity)
        );

    }

    /**
     * @param WP_REST_Request $request
     * @return WP_Error | bool
     */
    public function remote_callback(WP_REST_Request $request)
    {
        Xtm_Wpml_Connector_Callbacks::create($request);
    }

    /**
     * @param $project
     * @param $project_dir
     * @return bool
     */
    public function request_translation_jobs($project, $project_dir)
    {
        $options = get_option($this->plugin_name);
        $prefix = $options[self::PROJECT_NAME_PREFIX];
        $helper = new Xtm_Wpml_Connector_Helper();

        $translation_files = [];
        if (file_exists($project_dir . "/" . self::STRINGS_ZIP)) {
            $translation_files[] = $this->create_translation_array($project_dir, self::STRINGS_ZIP);
        }
        if (file_exists($project_dir . "/" . self::POSTS_ZIP)) {
            $translation_files[] = $this->create_translation_array($project_dir, self::POSTS_ZIP);
        }

        $map_language_to_xtm_format = [];
        foreach (explode(",", $project->target_language) as $target_language) {
            $mapped_language = $helper->map_language_to_xtm_format($target_language);
            if (!empty($mapped_language)) {
                $map_language_to_xtm_format[] = $mapped_language;
            }
        }

        $projectMTOM = [
            'name'             => ($prefix ? "[$prefix] " : '')
                . trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ',
                    urldecode(preg_replace("/&#?[a-z0-9]+;/i", "",
                        strip_tags($project->label)))))) . ' ' . $project->project_id
            ,
            'sourceLanguage'   => $helper->map_language_to_xtm_format($project->source_language),
            'targetLanguages'  => $map_language_to_xtm_format,
            'translationFiles' => $translation_files,
            'referenceId'      => $project->project_id,
            'customer'         =>
                ['id' => $options[self::XTM_PROJECT_CUSTOMER_ID]],
        ];

        $data = [
            'project_id'  => $project->project_id,
        ];
        $callback_url = site_url()
            . "/index.php/" . self::WP_JSON_XTM_WPML_CONNECTOR_V_1_REMOTE . http_build_query($data);

        $projectMTOM['projectCallback']['projectFinishedCallback'] = $callback_url;
         $projectMTOM['projectCallback']['jobFinishedCallback'] = $callback_url;
        if ($project->api_template_id) {
            $projectMTOM['template'] = ['id' => $project->api_template_id];
        }
        $input = [
            'project' => $projectMTOM,
            'options' => ['autopopulate' => true]
        ];

        $response = $this->do_request(self::CREATE_PROJECT_FOR_PMMTOM, $input);

        if (isset($response->project)) {
            Xtm_Model_Projects::update(
                ['reference' => sanitize_text_field($response->project->projectDescriptor->id)],
                ['project_id' => $project->project_id]
            );
            return true;
        }
        return false;
    }

    /**
     * @param int $project_id
     * @return bool
     */
    public function xtm_request_translation($project_id)
    {
        $project_model = Xtm_Model_Projects::get($project_id);
        /** @var object $project_model */

        try {
            $projectMTOM = $this->create_project_mtom($project_model);
            $data = [
                'project_id'  => $project_model->project_id,
                'wpml_job_id' => $project_model->wpml_job_id
            ];
            $callback_url = site_url()
                . "/index.php/" . self::WP_JSON_XTM_WPML_CONNECTOR_V_1_REMOTE . http_build_query($data);
            $projectMTOM['projectCallback']['projectFinishedCallback'] = $callback_url;
             $projectMTOM['projectCallback']['jobFinishedCallback'] = $callback_url;
            if ($project_model->api_template_id) {
                $projectMTOM['template'] = ['id' => $project_model->api_template_id];
            }
            $input = [
                'project' => $projectMTOM,
                'options' => ['autopopulate' => true]
            ];

            $response = $this->do_request(self::CREATE_PROJECT_FOR_PMMTOM, $input);

            if (isset($response->project)) {
                Xtm_Model_Projects::update(
                    ['reference' => sanitize_text_field($response->project->projectDescriptor->id)],
                    ['project_id' => $project_id]
                );
                return true;
            } else {
                return false;
            }

        } catch (\SoapFault $fault) {
            error_log($fault->getMessage());
            $this->display_error($fault->getMessage());
            return false;
        } catch (Exception $e) {
            $this->display_error($e->getMessage());
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * @param object $project_model
     * @return array
     */
    private function create_project_mtom($project_model)
    {
        $options = get_option($this->plugin_name);
        $helper = $this->get_proper_helper($project_model);
        $prefix = $options[self::PROJECT_NAME_PREFIX];

        return [
          'name' => ($prefix ? "[$prefix] " : '')
            . trim(
              preg_replace(
                '/ +/',
                ' ',
                preg_replace(
                  '/[^A-Za-z0-9 ]/',
                  ' ',
                  urldecode(preg_replace("/&#?[a-z0-9]+;/i", "", strip_tags($project_model->label)))
                )
              )
            )
            ,
          'sourceLanguage' => $helper->map_language_to_xtm_format($project_model->source_language),
          'targetLanguages' => $helper->map_language_to_xtm_format($project_model->target_language),
          'translationFiles' => ($project_model->api_project_mode == 0)
            ? $helper->create_single_xml_file($project_model) :
            $helper->create_multiple_xml_files($project_model),
          'referenceId' => $project_model->project_id,
          'customer' =>
            ['id' => $options[self::XTM_PROJECT_CUSTOMER_ID]],
        ];
    }

    /**
     * Makes all soap request to XTM
     * @param $action
     * @param array $query
     * @return mixed
     */
    private function do_request($action, array $query = [])
    {
        $translator = get_option($this->plugin_name);
        $this->check_request_conditions($action);
        $loginAPI = [
            'loginAPI' => [
                'userId' => $translator[self::XTM_API_USER_ID],
                'password' => $translator[self::XTM_API_PASSWORD],
                'client' => $translator[self::XTM_API_CLIENT_NAME],
                'integrationKey' => self::INTEGRATION_KEY,
            ],
        ];

        $client = new \SoapClient($translator[self::XTM_API_URL]);
        $result = $client->__soapCall(
            $action,
            [array_merge($query, $loginAPI)]
        );

        return $result->return;
    }

    /**
     * @return mixed
     */
    public function find_customer()
    {
        $input = [];
        $translator = get_option($this->plugin_name);
        $id = $translator[self::XTM_PROJECT_CUSTOMER_ID];
        if (!is_null($id)) {
            $input['filter'] = [
                'customers' => [
                    'id' => (int)$id
                ]
            ];
        }
        try {
            return $this->do_request(self::FIND_CUSTOMER, $input);
        } catch (SoapFault $fault) {
            error_log(json_encode($fault));
            return false;
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return false;
        }
    }

    /**
     * @param object $project_model
     * @param $xtm_job
     * @throws Exception
     */
    private function propagate_translated_data($project_model, $xtm_job)
    {
        $service_retrieve_translation = new Xtm_Service_Retrieve_Translation($project_model, $xtm_job);
        $service_retrieve_translation->get_data();
        $service_retrieve_translation->save_translated_data();
    }

    /**
     * @param string $text
     * @param string $code
     */
    private function display_error($text = '', $code = '')
    {
        Xtm_Wpml_Connector_Helper::display_error_notice($text, $code);
    }

    /**
     * @param $action
     * @throws Exception
     */
    private function check_request_conditions($action)
    {
        if (!$this->is_soap_enabled()) {
            error_log("The SOAP extension library is not installed.");
            throw new Exception('The SOAP extension library is not installed.');
        }
        $translator = get_option($this->plugin_name);

        if (!$this->is_wsdl_available($translator[self::XTM_API_URL])) {
            error_log("Could not connect to the XTM SOAP service. Please check settings.");
            throw new Exception('Could not connect to the XTM SOAP service. Please check settings.');
        }

        if (!in_array($action, $this->availableActions)) {
            error_log("XTM SOAP service");
            throw new Exception('Invalid action requested: @action', ['@action' => $action]);
        }
    }

    /**
     * @return bool
     */
    private function is_soap_enabled()
    {
        return class_exists('SoapClient');
    }

    /**
     * @param $wsdl
     * @return bool
     */
    private function is_wsdl_available($wsdl)
    {
        return !!@file_get_contents($wsdl);
    }

    /**
     * @param $item
     * @return array
     */
    private function parse_to_array($item)
    {
        if (is_array($item)) {
            return $item;
        }
        return [$item];
    }

    /**
     * @param $projectModel
     * @return Xtm_Wpml_Connector_Helper_Post|Xtm_Wpml_Connector_Helper_String
     */
    private function get_proper_helper($projectModel)
    {
        switch ($projectModel->type) {
            case 'String' :
                $helper = new Xtm_Wpml_Connector_Helper_String();
                break;
            case 'Post' :
                $helper = new Xtm_Wpml_Connector_Helper_Post();
                break;
            default:
                $helper = new Xtm_Wpml_Connector_Helper_Post();
                break;
        }
        return $helper;
    }

    /**
     * @param $project_dir
     * @return array
     */
    private function create_translation_array($project_dir, $file_name)
    {
        return [
            'fileName'            => $file_name,
            'fileMTOM'            => file_get_contents($project_dir . "/" . $file_name),
            'externalDescriptors' => []
        ];
    }


}

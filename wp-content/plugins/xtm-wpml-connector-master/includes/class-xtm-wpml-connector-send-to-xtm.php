<?php


/**
 * Class Xtm_Wpml_Connector_Send_To_XTM
 */
class Xtm_Wpml_Connector_Send_To_XTM
{
    /**
     * @var
     */
    private $data;

    /**
     * Xtm_Wpml_Connector_Send_To_XTM constructor.
     */
    public function __construct()
    {
        $jobs = Xtm_Provider_Jobs::get_jobs();
        $this->data = $jobs['Flat_Data'];
    }

    /**
     * @param $job_id
     * @param $template_id
     * @param $api_project_mode
     * @return bool
     */
    public function run($job_id, $template_id, $api_project_mode)
    {
        $translation_array = $this->get_translation_array($job_id);
        $xtm_project = $this->create_xtm_project($job_id, $template_id, $api_project_mode, $translation_array);
        $project_exists = Xtm_Model_Projects::get_by_field("wpml_job_id", $xtm_project['wpml_job_id']);
        if (empty($project_exists) || $project_exists->type != $translation_array['type']) {
            Xtm_Model_Projects::insert($xtm_project);
            $bridge = new Xtm_Wpml_Bridge();
            $project_id = Xtm_Model_Projects::insert_id();
            $request_status = $bridge->xtm_request_translation($project_id);
            if (false === $request_status) {
                Xtm_Model_Projects::delete(['project_id' => $project_id]);
                return Xtm_Wpml_Connector_Helper::display_error_notice(__("Can not resolve xtm status. Try again later.",
                    Xtm_Wpml_Bridge::PLUGIN_NAME), $xtm_project['wpml_job_id']);
            }
            Xtm_Model_Icl_Translation_Status::update(['status' => 2],
                ['translation_id' => $translation_array['translation_id']]);
            return $request_status;
        } else {
            echo Xtm_Wpml_Connector_Helper::display_error_notice(
                __("Project exists.", Xtm_Wpml_Bridge::PLUGIN_NAME),
                $xtm_project['wpml_job_id']
            );
            return false;
        }
    }

    /**
     * Returns job id already checked by cron task.
     * @return array
     */
    private function get_cron_job_array_id()
    {
        $all = Xtm_Model_Cron::get_all();
        $output_array = [];
        foreach ($all as $value) {
            $output_array[] = $value['job_id'];
        }
        return $output_array;
    }

    /**
     * Cron job. Sends automatics jobs to xtm.
     */
    public function cron_jobs()
    {
        $options = get_option(Xtm_Wpml_Bridge::PLUGIN_NAME);
        $automatically_move_flag = $options[Xtm_Wpml_Bridge::XTM_AUTOMATICALLY_MOVE_FLAG];
        $first_automatically_move_flag = $options[Xtm_Wpml_Bridge::XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG];
        $user = Xtm_Wpml_Connector_Helper::get_xtm_user();
        $translator_array[] = $user->ID;
        if ($first_automatically_move_flag) {
            $translator_array[] = 0;
        }

        $cron_array = $this->get_cron_job_array_id();
        $jobs_to_send = [];
        foreach ($this->data as $jobs) {
            foreach ($jobs as $translation) {
                $job_id = $translation['id'];

                if ((in_array($job_id, $cron_array))) {
                    continue;
                }
                if ($automatically_move_flag && in_array($translation['translator_id'], $translator_array)) {
                    $jobs_to_send[] =$job_id;
                    Xtm_Model_Cron::insert(['job_id' => $job_id]);
                    Xtm_Model_Cron::update(['status' => 'start'], ['job_id' => $job_id]);
                } else {
                    Xtm_Model_Cron::insert(['job_id' => $job_id]);
                    Xtm_Model_Cron::update(['status' => 'no_automation'], ['job_id' => $job_id]);
                }
            }
        }

        $send_to_xtm_object = new Xtm_Wpml_Connector_Helper_One_Project_Creation();
        $send_to_xtm_object->run_one_project($jobs_to_send,
            $options[Xtm_Wpml_Bridge::API_TEMPLATE_ID],
            0);

        foreach ($jobs_to_send as $job_id) {
            Xtm_Model_Cron::update(['status' => 'send'], ['job_id' => $job_id]);
        }

    }


    /**
     * @param string $job_id
     * @return array
     */
    private function get_translation_array($job_id)
    {
        $translation_array = null;
        foreach ($this->data as $jobs) {
            foreach ($jobs as $translation) {
                if ($translation['id'] == $job_id) {
                    $translation_array = $translation;
                    break;
                }
            }
        }

        return $translation_array;
    }

    /**
     * @param string $job_id
     * @param $template_id
     * @param $api_project_mode
     * @param $translation_array
     * @return array
     */
    private function create_xtm_project($job_id, $template_id, $api_project_mode, array $translation_array)
    {
        $xtm_project = [];
        switch ($translation_array['type']) {
            case "Post":
                $xtm_project['items'] = json_encode($translation_array['elements']);
                $xtm_project['label'] = $translation_array["post_title"];
                $xtm_project['wpml_job_id'] = $translation_array['job_id'];
                break;
            case "String":
                $icl_string = [Xtm_Model_Icl_Strings::get(["id" => $translation_array['string_id']])];
                $xtm_project['label'] = strlen($translation_array["value"]) > 50 ? substr($translation_array["value"],
                        0, 50) . "..." : $translation_array["value"];
                $xtm_project['items'] = json_encode($icl_string);
                $xtm_project['wpml_job_id'] = filter_var($job_id, FILTER_SANITIZE_NUMBER_INT);;
                break;
            default:
                echo Xtm_Wpml_Connector_Helper::display_error_notice("No supported type: " . $translation_array['type']);
                wp_die();
                break;
        }
        $xtm_project['source_language'] = $translation_array['source_language_code'];
        $xtm_project['target_language'] = $translation_array['language_code'];
        $xtm_project['status'] = Xtm_Provider_Readable_State::XTM_STATE_ACTIVE;
        $xtm_project['api_template_id'] = (int)$template_id;
        $xtm_project['api_project_mode'] = (int)$api_project_mode;
        $xtm_project['type'] = $translation_array['type'];
        Xtm_Wpml_Connector_Helper::create_xtm_link($xtm_project);

        return $xtm_project;
    }


}

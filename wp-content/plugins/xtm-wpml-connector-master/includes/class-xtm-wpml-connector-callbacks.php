<?php

/**
 * Class Xtm_Wpml_Connector_Callbacks
 *
 * This class is responsible for obtaining translation from XTM on callbacks
 */
class Xtm_Wpml_Connector_Callbacks
{
    const NOT_FINISHED = 'not_finished';
    const FINISHED = 'FINISHED';
    const IN_PROGRESS = 'in_progress';
    const DOWNLOADED = 'downloaded';
    const PACK_SIZE = 10;
    /**
     *
     */
    const NEW_STATUS = 'new';

    /**
     * @param WP_REST_Request $request
     */
    public static function create(WP_REST_Request $request)
    {
        $data = [];
        $data['project_id'] = $request->get_param('project_id');
        $data['wpml_job_id'] = 0;
        $data['xtm_project_id'] = $request->get_param('xtmProjectId');
        $data['xtm_customer_id'] = $request->get_param('xtmCustomerId');
        $data['xtm_job_id'] = $request->get_param('xtmJobId');
        $data['status'] = self::NEW_STATUS;
        Xtm_Model_Callbacks::insert($data);
    }

    /**
     *
     */
    public function run()
    {
        for ($i = 0; $i < self::PACK_SIZE; $i++) {
            $this->check_callback();
        }
    }

    /**
     *
     */
    private function check_callback()
    {
        $callback = $this->get_last_callback();
        if (empty($callback)) {
            return;
        }
        Xtm_Model_Callbacks::update(['status' => self::IN_PROGRESS], ['id' => $callback->id]);
        $bridge = new Xtm_Wpml_Bridge();
        if (empty($callback->xtm_job_id)) {
            $this->finish_project($callback);
            return;
        }
        $status = $bridge->check_job_completion($callback->xtm_job_id);
        if (self::FINISHED != $status->jobs->status) {
            Xtm_Model_Callbacks::update(['status' => self::NOT_FINISHED], ['id' => $callback->id]);
            return;
        }
        $job_mtom = $bridge->download_job_mtom($callback->xtm_job_id);

        $project_model = Xtm_Model_Projects::get($callback->project_id);
        if ($project_model->status == Xtm_Provider_Readable_State::XTM_STATE_ACTIVE) {
            Xtm_Model_Projects::update(["status" => 'Downloading'], ['project_id' => $callback->project_id]);
        }

        $service_retrieve_translation = new Xtm_Service_Retrieve_Translation($project_model, $job_mtom->jobs);
        $service_retrieve_translation->get_data();
        $icl_translate_ids = $service_retrieve_translation->save_translated_data();

        Xtm_Model_Callbacks::update(['status' => self::DOWNLOADED], ['id' => $callback->id]);

        $icl_translates = Xtm_Model_Icl_Translate::get_all();
        $wmpl_job_id_to_close = [];
        foreach ($icl_translates as $icl_translate) {
            if (in_array($icl_translate['tid'], $icl_translate_ids)) {
                $wmpl_job_id_to_close[] = $icl_translate['job_id'];
            }
        }

        $wmpl_job_id_to_close = array_unique($wmpl_job_id_to_close);
        $this->close_wmpl_jobs($wmpl_job_id_to_close);

        Xtm_Model_Callbacks::update(['status' => 'finished'], ['id' => $callback->id]);
    }

    /**
     * @param $wpml_job_ids
     */
    private function close_wmpl_jobs($wpml_job_ids)
    {
        global $wpdb, $wpml_post_translations, $wpml_term_translations;
        $wpml_tm_records = new WPML_TM_Records($wpdb, $wpml_post_translations, $wpml_term_translations);
        $bridge = new Xtm_Wpml_Bridge();

        foreach ($wpml_job_ids as $wpml_job_id) {
            $save_data_action = new WPML_Save_Translation_Data_Action([
                'job_id'   => (int)$wpml_job_id,
                'complete' => 1,
                'fields'   => []
            ], $wpml_tm_records);
            $save_data_action->save_translation();
            $bridge->add_to_gutenberg_editor($wpml_job_id);
        }
    }

    /**
     * @param $callback
     */
    private function finish_project($callback)
    {
        Xtm_Model_Callbacks::update(['status' => 'checking_project'], ['id' => $callback->id]);
        $filter['status'] = self::NEW_STATUS;
        $filter['xtm_project_id'] = $callback->project_id;
        $new = Xtm_Model_Callbacks::get_all($filter);
        $filter['status'] = self::IN_PROGRESS;
        $in_progress = Xtm_Model_Callbacks::get_all($filter);
        if (!(empty($new) && empty($in_progress))) {
            Xtm_Model_Callbacks::update(['status' => self::NOT_FINISHED], ['id' => $callback->id]);
            return;
        }
        /* quite jobs in project callback */
        //$project_model = Xtm_Model_Projects::get($callback->project_id);
        //$xml_jobs = json_decode($project_model->wpml_job_id, true);
        //$this->close_wmpl_jobs($xml_jobs);

        Xtm_Model_Projects::update(["status" => self::FINISHED], ['project_id' => $callback->project_id]);
        Xtm_Model_Callbacks::update(['status' => self::DOWNLOADED], ['id' => $callback->id]);

    }

    /**
     * First we are taking callbacks with a new status
     *
     * @return object
     */
    private function get_last_callback()
    {
        $callback = Xtm_Model_Callbacks::get_by_field("status", self::NEW_STATUS);
        if (empty($callback)) {
            $callback = Xtm_Model_Callbacks::get_by_field("status", self::NOT_FINISHED);
        }
        if (empty($callback)) {
            $callback = Xtm_Model_Callbacks::get_by_field("status", 'checking_project');
            return $callback;
        }
        return $callback;
    }
}

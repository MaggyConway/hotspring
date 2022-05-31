<?php


/**
 * Class Xtm_Wpml_Connector_One_Project_Creation
 */
class Xtm_Wpml_Connector_Helper_One_Project_Creation extends Xtm_Wpml_Connector_Helper
{
    const POST_PAGE = 'post_page';
    const POST_POST = 'post_post';
    const POST_WP_BLOCK = 'post_wp_block';

    /**
     * @var
     */
    private $data;
    private $template_id;
    private $api_project_mode;

    /**
     * Xtm_Wpml_Connector_Send_To_XTM constructor.
     */
    public function __construct()
    {
        $jobs = Xtm_Provider_Jobs::get_jobs();
        $this->data = $jobs['Flat_Data'];
    }

    /**
     * @param $source_array
     * @param $jobs_array
     * @return array
     */
    public static function get_files_by_language_combination($source_array, $jobs_array)
    {
        $files_by_lang_combination = [];
        foreach ($source_array as $source_language => $files) {
            foreach ($files as $file_id => $target_languages) {
                $job = $jobs_array[$file_id];
                $original_target_language_order = $target_languages;
                $job['original_target_language_order'] = array_reverse($original_target_language_order);
                $target_languages = array_unique($target_languages);
                sort($target_languages);
                $files_by_lang_combination[$source_language][implode(',', $target_languages)][] = $job;
            }
        }
        return $files_by_lang_combination;
    }

    /**
     * @param $jobs
     * @param $template_id
     * @param $api_project_mode
     * @return bool|string |int
     */
    public function run_one_project($jobs, $template_id, $api_project_mode)
    {
        $this->template_id = $template_id;
        $this->api_project_mode = $api_project_mode;
        $jobs_by_source_language = $this->prepare_jobs_source_language($jobs);

        $source_array = [];
        $jobs_array = [];

        foreach ($jobs_by_source_language as $source_lang_code => $source_language_job) {
            $source_jobs = $this->get_jobs($source_language_job);
            foreach ($source_jobs as $job) {
                $source_language_code = $job['source_language_code'];
                $target_language_code = $job['language_code'];
                $id = (!empty($job['original_doc_id'])) ? $job['original_doc_id'] : $job['name'];
                $source_array[$source_language_code][$id][] = $target_language_code;
                if (!empty($jobs_array[$id]) && (!empty($job['elements']))) {
                    foreach ($job['elements'] as $key => &$element) {
                        $element->tid .= ',' . $jobs_array[$id]['elements'][$key]->tid;
                    }
                }
                $job['target_language_mapping'][$job['translation_id']] = $source_language_code;
                if (!empty($jobs_array[$id])) {
                    $job['translation_id'] .= "," . $jobs_array[$id]['translation_id'];
                    $job['id'] .= "," . $jobs_array[$id]['id'];
                }
                $jobs_array[$id] = $job;
            }
        }

        $files_by_lang_combination = self::get_files_by_language_combination($source_array, $jobs_array);

        $project_counter = 0;
        foreach ($files_by_lang_combination as $source_language => $target_lang_jobs) {
            foreach ($target_lang_jobs as $target_language => $jobs_for_combination) {
                if ($this->create_project($jobs_for_combination, $source_language, $target_language)) {
                    $project_counter++;
                }
            }
        }

        return $project_counter;
    }

    /**
     * @param $job_ids
     * @return array
     */
    private function get_jobs($job_ids)
    {
        $translation_array = [];
        foreach ($this->data as $jobs) {
            foreach ($jobs as $translation) {
                if (in_array($translation['id'], $job_ids)) {
                    $translation_array[] = $translation;
                }
            }
        }
        return $translation_array;
    }


    /**
     * @param $jobs
     * @return array
     */
    private function prepare_jobs_source_language($jobs)
    {
        $jobs_by_source_language = [];
        foreach ($jobs as $job_id) {
            $translation_array = $this->get_translation_array($job_id);
            $jobs_by_source_language[$translation_array['source_language_code']][] = $job_id;
        }
        return $jobs_by_source_language;
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
     * @param $jobs
     * @param $source_language
     * @param $target_language
     * @return bool
     */
    private function create_project($jobs, $source_language, $target_language)
    {
        $translator = get_option(Xtm_Wpml_Bridge::PLUGIN_NAME);
        $client_name = $translator[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME];
        $project_dir = get_temp_dir() . '/' . $source_language . '-' . $target_language . '-project-' . $client_name . '/';
        if (file_exists($project_dir)) {
            XTM_Provider_Zip::remove_folder($project_dir);
        }
        if (!file_exists($project_dir)) {
            mkdir($project_dir, 0777);
        }

        $items = $domains = $wpml_jobs = $added_string_list = [];
        foreach ($jobs as $job) {
            $this->collect_wpml_job_id($job, $wpml_jobs);
            if ('String' == $job['type']) {
                $domains = Xtm_Provider_Projects::prepare_strings_array($domains, $job, $added_string_list);
            }
            $counter = [];

            if ($this->is_post_type($job) && (strpos($job['original_post_type'], 'post_') !== false)) {
                $post_type = str_replace('post_', '', $job['original_post_type']);
                Xtm_Provider_Projects::create_xml_page($project_dir, $job, $post_type);
                $counter[$post_type]++;
                $items = $job['original_target_language_order'];
            }

            $translation_ids = explode(",", $job['translation_id']);
            foreach ($translation_ids as $translation_id) {
                Xtm_Model_Icl_Translation_Status::update(['status' => 2],
                    ['translation_id' => $translation_id]);
            }
        }

        if (!empty($domains)) {
            $this->create_domain_zip($project_dir, $domains);
        }

        if (isset($counter)){
            foreach ($counter as $c_post_type => $value ){
                if ($value > 0) {
                    XTM_Provider_Zip::zip_folder($project_dir . "/". $c_post_type . "/", Xtm_Wpml_Bridge::POSTS_ZIP);
                }
            }
        }

        $xtm_project = $this->prepare_project_array($source_language, $target_language);
        Xtm_Wpml_Connector_Helper::create_xtm_link($xtm_project);
        $xtm_project['items'] = json_encode($items);
        sort($wpml_jobs);
        $xtm_project['wpml_job_id'] = json_encode($wpml_jobs);
        Xtm_Model_Projects::insert($xtm_project);
        $bridge = new Xtm_Wpml_Bridge();
        $project_id = Xtm_Model_Projects::insert_id();
        $project = Xtm_Model_Projects::get($project_id);
        $status = $bridge->request_translation_jobs($project, $project_dir);
        if (false === $status) {
            Xtm_Model_Projects::delete(['project_id' => $project_id]);
            echo Xtm_Wpml_Connector_Helper::display_error_notice(__("Can not resolve xtm status. Try again later.",
                Xtm_Wpml_Bridge::PLUGIN_NAME), $xtm_project['wpml_job_id']);
            return false;
        }
        if (file_exists($project_dir)) {
            XTM_Provider_Zip::remove_folder($project_dir);
        }

        return true;
    }


    /**
     * @param $source_language
     * @param $target_language
     * @return array
     */
    private function prepare_project_array($source_language, $target_language)
    {
        $xtm_project = [];
        $xtm_project['label'] = 'Wordpress Project ID ';
        $xtm_project['source_language'] = $source_language;
        $xtm_project['target_language'] = $target_language;
        $xtm_project['status'] = Xtm_Provider_Readable_State::XTM_STATE_ACTIVE;
        $xtm_project['api_template_id'] = (int)$this->template_id;
        $xtm_project['api_project_mode'] = (int)$this->api_project_mode;
        $xtm_project['type'] = 'Multi';
        return $xtm_project;
    }

    /**
     * @param $project_dir
     * @param $domains
     */
    private function create_domain_zip($project_dir, $domains)
    {
        $string_dir = $project_dir . "strings";
        mkdir($string_dir, 0777);
        foreach ($domains as $file_name => $domain) {
            $xmlString = $domain->asXML();
            $xmlString = str_replace('<?xml version="1.0"?>',
                '<?xml version="1.0"?><!DOCTYPE html [ <!ENTITY nbsp "&#160;"><!ENTITY copy "&#169;"><!ENTITY bull "&#8226;"> ]>',
                $xmlString);
            file_put_contents($string_dir . '/' . $file_name . ".xml", $xmlString);
        }
        XTM_Provider_Zip::zip_folder($string_dir, 'Strings.zip');
    }


    /**
     * @param $job
     * @param $wpml_jobs
     */
    private function collect_wpml_job_id($job, &$wpml_jobs)
    {
        foreach (explode(',', $job['id']) as $wpml_job_id) {
            $wpml_jobs[] = filter_var($wpml_job_id, FILTER_SANITIZE_NUMBER_INT);
        }
    }

    /**
     * @param $job
     * @return bool
     */
    private function is_post_type($job)
    {
        return 'Post' == $job['type'];
    }

}

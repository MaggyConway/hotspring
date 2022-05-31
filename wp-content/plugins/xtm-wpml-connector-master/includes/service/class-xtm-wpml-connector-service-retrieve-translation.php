<?php


/**
 * Class Xtm_Service_Retrieve_Translation
 */
class Xtm_Service_Retrieve_Translation
{
    /**
     *
     */
    const FOLDER = '/folder';
    /**
     * @var
     */
    private $project_model;
    /**
     * @var
     */
    private $xtm_job;

    /**
     * @var SimpleXMLElement
     */
    private $xmls;

    /**
     * Xtm_Service_Retrieve_Translation constructor.
     * @param $project_model
     * @param $xtm_job
     */
    public function __construct($project_model, $xtm_job)
    {
        $this->project_model = $project_model;
        $this->xtm_job = $xtm_job;
    }


    /**
     * @param $xtm_target_language
     * @return bool|int|string
     */
    public static function get_wordpress_lang_code($xtm_target_language)
    {
        $translator = get_option(Xtm_Wpml_Bridge::PLUGIN_NAME);

        foreach ($translator['remote_languages_mappings'] as $code => $lang) {
            if ($lang == $xtm_target_language) {
                return $code;
            }
        }
        return false;
    }


    /**
     *
     */
    public function __destruct()
    {
        XTM_Provider_Zip::remove_folder(get_temp_dir() . self::FOLDER);
    }

    /**
     * @throws Exception
     */
    public function get_data()
    {
        $temp_file = get_temp_dir() . '/' . $this->xtm_job->fileName;
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }

        if (file_put_contents($temp_file, $this->xtm_job->fileMTOM) === false) {
            throw new Exception("Could not download a file #@fileId from project #@projectId.",
                ['@fileId' => $this->xtm_job->fileDescriptor->id, '@projectId' => $this->project_model->reference]);
        }
        XTM_Provider_Zip::unpack($temp_file, get_temp_dir() . self::FOLDER);
        $file_paths = $this->get_file_path();
        foreach ($file_paths as $file_path) {
            $translated_text = file_get_contents($file_path);
            $xml = simplexml_load_string($translated_text, null
                , LIBXML_NOCDATA);


            if (false === $xml) {
                throw new Exception("No XML DATA");
            }
            $this->xmls[] = $xml;
        }
    }

    /**
     * @return array
     */
    public function save_translated_data()
    {
        $icl_translate_ids = [];

        foreach ($this->xmls as $xml) {
            foreach ($xml->children() as $xml_job) {
                /** @var SimpleXMLElement $xml_job */
                $type = $xml_job->attributes()->type;
                if ('string' == $type) {
                    $this->save_string($xml_job);
                } else {
                //if ('posts' == $type || 'pages' == $type || 'blocks' == $type) {
                    $icl_translate_ids[] = $this->save_pages($xml_job);

                }

            }
        }

        return $icl_translate_ids;
    }

    /**
     * @param mixed $project_model
     */
    public function setProjectModel($project_model)
    {
        $this->project_model = $project_model;
    }

    /**
     * @param mixed $xtm_job
     */
    public function setXtmjob($xtm_job)
    {
        $this->xtm_job = $xtm_job;
    }

    /**
     * @return array
     */
    private function get_file_path()
    {
        $file_path_array = $this->prepare_file_path_array();
        foreach ($file_path_array as $file_path) {
            if (file_exists($file_path)) {
                return [$file_path];
            }
        }
        $glob_file_path_array[] = glob(get_temp_dir() . self::FOLDER . '/' . $this->xtm_job->targetLanguage . '/' . "*.xml");
        $glob_file_path_array[] = glob(get_temp_dir() . self::FOLDER . '/' . "*.xml");
        foreach ($glob_file_path_array as $glob_file_path) {
            if (!empty($glob_file_path)) {
                return $glob_file_path;
            }
        }

        die('Error! Can not open downloaded file');
    }

    /**
     * @return array
     */
    private function prepare_file_path_array()
    {
        $originalFileName = $this->xtm_job->originalFileName;
        $originalFileNameArray = explode(".", $originalFileName);
        $buildFileName = $originalFileNameArray[0] . "_" . $this->xtm_job->targetLanguage . "." . $originalFileNameArray[1];
        $file_path_array = [];
        $file_path_array[] = get_temp_dir() . self::FOLDER . '/' . $this->xtm_job->targetLanguage . '/' . $this->xtm_job->originalFileName;
        $file_path_array[] = get_temp_dir() . self::FOLDER . '/' . $this->xtm_job->originalFileName;
        $file_path_array[] = get_temp_dir() . self::FOLDER . '/' . $buildFileName;
        $file_path_array[] = get_temp_dir() . self::FOLDER . '/' . $this->xtm_job->targetLanguage . '/' . $buildFileName;
        return $file_path_array;
    }

    /**
     * @param SimpleXMLElement $xml_job
     * @return int
     * @throws Exception
     */
    private function save_pages(SimpleXMLElement $xml_job)
    {
        $xml_jobs = explode(',', $xml_job->attributes()->id);
        $helper = new Xtm_Wpml_Connector_Helper();
        $lang_counter = 0;
        $target_lang_wpml = '';
        foreach (json_decode($this->project_model->items, true) as $key => $target_language) {
            if ($helper->map_language_to_xtm_format($target_language, false) == $this->xtm_job->targetLanguage) {
                $lang_counter = $key;
                $target_lang_wpml = $target_language;
            }
        }

        $xml_job_id = $xml_jobs[$lang_counter];

        if ($xml_job_id === 0) {
            throw new Exception('Cant find job ID ');
        }

        $text = Xtm_Wpml_Connector_Helper_String::string_validation($xml_job);
        Xtm_Model_Icl_Translate::update(
            [
                'field_data_translated' => base64_encode($text),
                'field_finished'        => 1
            ],
            ['tid' => $xml_job_id]
        );
        $translate = Xtm_Model_Icl_Translate::get_by_field('tid', $xml_job_id);
        $builder = new WPML_TM_Page_Builders_Field_Wrapper($translate->field_type);
        //Gutenberg
        if ($builder->get_string_id()){
            icl_add_string_translation($builder->get_string_id(),$target_lang_wpml, $text, 10);
        }

        return $xml_job_id;
    }

    /**
     * @param SimpleXMLElement $xml_job
     * @throws Exception
     */
    private function save_string(SimpleXMLElement $xml_job)
    {
        $text = Xtm_Wpml_Connector_Helper_String::string_validation($xml_job);

        /** @var SimpleXMLElement $xml_job */

        $helper = new Xtm_Wpml_Connector_Helper();

        $target_language = false;
        foreach (explode(",", $this->project_model->target_language) as $key => $target_language) {
            if ($helper->map_language_to_xtm_format($target_language, false) == $this->xtm_job->targetLanguage) {
                break;
            }
        }
        if ($target_language) {
            Xtm_Model_Icl_String_Translations::update(['value' => $text, 'status' => 10],
                ['string_id' => (int)$xml_job->attributes()->id, 'language' => $target_language]);
        } else {
            throw new Exception("Mapping error");
        }
    }
}

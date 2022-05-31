<?php

/**
 * Class Xtm_Wpml_Connector_Helper
 */
class Xtm_Wpml_Connector_Helper
{


    /**
     * Available project modes.
     *
     * @var array
     */
    private $project_modes = [
        0 => 'Single file - translation returned at the end of the project',
        1 => 'Multiple files - translation returned when each file is complete',
        2 => 'Multiple files - translation returned when all files are complete'
    ];

    /**
     * @var string
     */
    private $plugin_name = Xtm_Wpml_Bridge::PLUGIN_NAME;

    /**
     * Prepare link to XTM
     * @param array $xtm_project
     */
    public static function create_xtm_link(&$xtm_project)
    {
        $options = get_option(Xtm_Wpml_Bridge::PLUGIN_NAME);
        $xtm_url = $options[Xtm_Wpml_Bridge::XTM_API_URL];
        $xtm_client = $options[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME];
        $url_array = parse_url($xtm_url);
        $xtm_project['xtm_link'] = $url_array['scheme'] . "://" . $url_array['host'] .
            "/project-manager-gui/external-open-project-editor.action?client=" . $xtm_client . "&pid=";
    }


    /**
     * @param object $project_model
     * @return array
     */
    public function create_multiple_xml_files($project_model)
    {
        $files = [];
        $items = json_decode($project_model->items, true);
        $i = 0;

        foreach ($items as $item) {
            $xml = new \SimpleXMLElement('<' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's></' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's>');
            $str = $this->create_translate_from_string($item);
            if (trim($str)) {
                $this->add_xml_child($xml, $str, $item);

                $files[] = [
                    'fileName'            => $this->filter_file_name($project_model->label, $i),
                    'fileMTOM'            => $xml->asXML(),
                    'externalDescriptors' => []
                ];
                $i++;
            }
        }

        return $files;
    }

    /**
     * @param $options
     * @param $authorization
     */
    public static function display_notices($options, $authorization)
    {
        if (!extension_loaded('soap')) {
            echo Xtm_Wpml_Connector_Helper::display_error_notice(__("The SOAP extension library is not installed.",
                Xtm_Wpml_Bridge::PLUGIN_NAME));
        }
        if (!empty($options[Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL])) {
            $user_result = wp_update_user([
                'ID'         => Xtm_Wpml_Connector_Helper::get_xtm_user()->ID,
                'user_email' => $options[Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL]
            ]);
            if (is_wp_error($user_result)) {
                $error = (!empty(($user_result->errors))) ? array_shift($user_result->errors)[0] : __(" There was an user error ");
                echo Xtm_Wpml_Connector_Helper::display_error_notice($error);
            }
        }

        if ($authorization) {
            echo Xtm_Wpml_Connector_Helper::display_success_notice(__("XTM authorization complete.",
                Xtm_Wpml_Bridge::PLUGIN_NAME));
        } else {
            if (!empty($options[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME])) {
                echo Xtm_Wpml_Connector_Helper::display_error_notice(__("XTM authorization failed. Please check data below.",
                    Xtm_Wpml_Bridge::PLUGIN_NAME));
            }
        }
    }

    /**
     * @return false|WP_User
     */
    public static function get_xtm_user()
    {
        $userdata = WP_User::get_data_by('login', Xtm_Wpml_Bridge::PLUGIN_NAME);

        if (!$userdata) {
            return false;
        }

        $user = new WP_User;
        $user->init($userdata);

        return $user;
    }

    /**
     * @param $project_model
     * @return array
     */
    public function create_single_xml_file($project_model)
    {
        $xml = new \SimpleXMLElement('<' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's></' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's>');
        $items = json_decode($project_model->items, true);
        foreach ($items as $item) {
            $str = $this->create_translate_from_string($item);
            $this->add_xml_child($xml, $str, $item);
        }

        return [
            [
                'fileName'            => $this->filter_file_name($project_model->label, 0),
                'fileMTOM'            => $xml->asXML(),
                'externalDescriptors' => []
            ]
        ];
    }


    /**
     * @param $project_model
     * @param $file
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function get_translated_data($project_model, $file)
    {
        $temp_file = get_temp_dir() . '/' . $file->fileName;
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }

        if (file_put_contents($temp_file, $file->fileMTOM) === false) {
            throw new Exception("Could not download a file #@fileId from project #@projectId.",
                ['@fileId' => $file->fileDescriptor->id, '@projectId' => $project_model->reference]);
        }
        XTM_Provider_Zip::unpack($temp_file, get_temp_dir() . '/folder');
        $file_path = get_temp_dir() . '/folder/' . $file->targetLanguage . '/' . $file->originalFileName;
        $translated_text = file_get_contents($file_path);

        $xml = simplexml_load_string($translated_text, null
            , LIBXML_NOCDATA);

        if (false === $xml) {
            throw new Exception("No XML DATA");
        }
        return $xml;
    }

    /**
     * Read a file from the ZIP archive.
     *
     * @param  string $filePath
     *
     * @return string
     */
    public function read_zip_archive($filePath)
    {
        $zip = @fopen('zip://' . $filePath, 'r');
        if (!$zip) {
            return '';
        }
        $content = '';
        while (!feof($zip)) {
            $content .= fread($zip, 2);
        }
        fclose($zip);

        return $content;
    }


    /**
     * Return all available project modes (with translations).
     *
     * @return array
     */
    public function get_project_modes()
    {
        $out = [];
        foreach ($this->project_modes as $key => $value) {
            $out[$key] = $value;
        }

        return $out;
    }

    /**
     * Return string for error notice
     *
     * @param $message
     * @param string $code
     * @return string
     */
    public static function display_error_notice($message, $code = '')
    {
        $str = '<div class="notice notice-error is-dismissible"><p>';
        if ('' !== $code) {
            $str .= '<code>' . $code . '</code>';
        }
        $str .= $message;
        $str .= '</p></div>';
        return $str;
    }

    /**
     * @param string $text
     * @param string $code
     * @return string
     */
    public static function display_success_notice($text = '', $code = '')
    {
        $str = '<div class="notice notice-success is-dismissible"><p>';
        if ('' !== $code) {
            $str .= '<code>' . $code . '</code>';
        }
        $str .= $text;
        $str .= '</p></div>';
        return $str;
    }


    /**
     * @param string $language_code
     * @return string
     */
    public static function convert_language_to_string($language_code)
    {
        global $sitepress;
        $active_languages = $sitepress->get_active_languages();
        if ($active_languages[$language_code]['display_name']) {
            return esc_html($active_languages[$language_code]['display_name']);
        } else {
            return esc_html($language_code);
        }
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param string $lang
     */
    public function map_language_to_xtm_format($lang, $comment = true)
    {
        $translator = get_option($this->plugin_name);

        if (!empty($translator['remote_languages_mappings'][$lang])) {
            return $translator['remote_languages_mappings'][$lang];
        }
        $xtm_language = $this->get_xtm_languages();
        if (empty($xtm_language[$lang])) {
            return "";
        }
        $lang_key = (count($xtm_language[$lang]) > 1) ? key($xtm_language[$lang][0]) : key($xtm_language[$lang]);
        if ($comment) {
            echo $this->display_error_notice(
              'Please go to XTM -> XTM Settings and confirm language mapping. Default language mapping is used.'
            );
        }

        $translator['remote_languages_mappings'][$lang] = $lang_key;
        update_option($this->plugin_name, $translator);

        return $lang_key;
    }

    /**
     * @return array
     */
    public function get_xtm_languages()
    {
        return json_decode(file_get_contents(plugin_dir_path(plugin_dir_path(dirname(__FILE__))) .
            'public/js/languageList.json'), true);
    }

    /**
     * @return array
     */
    public static function get_filter_project()
    {
        $filter = [];
        $filter_fields = [
            Xtm_Wpml_Bridge::API_PROJECT_MODE => 'project-modes-filter',
            'status'                          => 'status-list-filter',
            'source_language'                 => 'source-language-filter',
            'target_language'                 => 'target-language-filter',
        ];
        foreach ($filter_fields as $key => $value) {
            if (isset($_GET[$value]) && ("" !== $_GET[$value])) {
                $filter[$key] = filter_input(INPUT_GET, $value
                );
            }
        }
        return $filter;
    }

    /**
     * @param object $project_model
     * @param SimpleXMLElement $xml
     */
    protected function save_translated_data($project_model, SimpleXMLElement $xml)
    {
        return;
    }

    /**
     * @param $item
     * @return string
     */
    protected function create_translate_from_string($item)
    {
        return "";
    }

    /**
     * @param SimpleXMLElement $xml
     * @param $str
     * @param $item
     */
    protected function add_xml_child(SimpleXMLElement $xml, $str, $item)
    {

    }


    /**
     * Create filtered name of MTOM file.
     *
     * @param  string $label
     *   The main label for file.
     *
     * @param  string /int $id
     *   File name sufix.
     *
     * @return string
     */
    protected function filter_file_name($label, $id)
    {
        $name = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ',
            urldecode(preg_replace("/&#?[a-z0-9]+;/i", "", strip_tags($label))))));
        return str_replace(['@name', '@id'], [$name, $id], '@name_[@id].xml');
    }

    protected function remove_nbsps($content)
    {
        return $content;
    }

}

<?php

/**
 * Class Xtm_Wpml_Connector_Helper
 */
class Xtm_Wpml_Connector_Helper_String extends Xtm_Wpml_Connector_Helper
{
    /**
     * @param SimpleXMLElement $xml_job
     * @return mixed|string
     */
    public static function string_validation(SimpleXMLElement $xml_job)
    {
        $text = (string)$xml_job;
        $text = str_replace("<![CDATA[", "", $text);
        $text = str_replace("]]>", "", $text);

        $xml_job_id = (int)$xml_job->attributes()->id;

        $translate = Xtm_Model_Icl_Translate::get_by_field('tid', $xml_job_id);
        if (preg_match('/media_(.*?)_alt_text/', $translate->field_type, $match) == 1){
            $text =  str_replace([">", '"', "'"], '', $text);
        };

        preg_match_all('@alt="([^"]+)"@', $text, $match);
        if ($match[1]) {
            $text = str_replace($match[1], str_replace([">", '"', "'"], '', $match[1]), $text);
        }
        return $text;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function create_translate_from_string($item)
    {
        return $item['value'];
    }

    /**
     * @param SimpleXMLElement $xml
     * @param string $str
     * @param array $item
     */
    protected function add_xml_child(SimpleXMLElement $xml, $str, $item)
    {
        $cdata_value = "<![CDATA[" . htmlspecialchars($str) . "]]>";
        $xml->addChild(Xtm_Wpml_Bridge::PLUGIN_NAME, $cdata_value)->addAttribute('id', $item['id']);
    }

    /**
     * @param object $project_model
     * @param SimpleXMLElement $xml
     */
    public function save_translated_data($project_model, SimpleXMLElement $xml)
    {
        foreach ($xml->children() as $xml_job) {
            $text = Xtm_Wpml_Connector_Helper_String::string_validation($xml_job);

            /** @var SimpleXMLElement $xml_job */
            Xtm_Model_Icl_String_Translations::update(['value' => $text, 'status' => 10],
                ['string_id' => (int)$xml_job->attributes()->id, 'language' => $project_model->target_language]);
            $xml_job_id = (int)$project_model->wpml_job_id;
            global $wpdb, $wpml_post_translations, $wpml_term_translations;
            $complete = true;
            $wpml_tm_records = new WPML_TM_Records($wpdb, $wpml_post_translations, $wpml_term_translations);
            $save_data_action = new WPML_Save_Translation_Data_Action([
                'job_id'   => (int)$xml_job_id,
                'complete' => $complete,
                'fields'   => []
            ], $wpml_tm_records);
            $save_data_action->save_translation();
        }
    }
}

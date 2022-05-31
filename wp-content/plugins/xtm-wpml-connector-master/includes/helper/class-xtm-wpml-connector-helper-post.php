<?php

/**
 * Class Xtm_Wpml_Connector_Helper
 */
class Xtm_Wpml_Connector_Helper_Post extends Xtm_Wpml_Connector_Helper
{
    /**
     * @param object $project_model
     * @param SimpleXMLElement $xml
     */
    public function save_translated_data($project_model, SimpleXMLElement $xml)
    {
        foreach ($xml->children() as $xml_job) {
            /** @var SimpleXMLElement $xml_job */
            $xml_job_id = $xml_job->attributes()->id;
            $text = Xtm_Wpml_Connector_Helper_String::string_validation($xml_job);
            Xtm_Model_Icl_Translate::update(
                [
                    'field_data_translated' => base64_encode($text),
                    'field_finished'        => 1
                ],
                ['tid' => (int)$xml_job_id]
            );
        }

        $xml_job_id = (int)$project_model->wpml_job_id;
        global $wpdb, $wpml_post_translations, $wpml_term_translations;
        $complete = (Xtm_Provider_Readable_State::XTM_STATE_FINISHED === $project_model->status);
        $wpml_tm_records = new WPML_TM_Records($wpdb, $wpml_post_translations, $wpml_term_translations);
        $save_data_action = new WPML_Save_Translation_Data_Action([
            'job_id'   => (int)$xml_job_id,
            'complete' => $complete,
            'fields'   => []
        ], $wpml_tm_records);
        $save_data_action->save_translation();
    }

    /**
     * @param array $item
     * @return string
     */
    protected function create_translate_from_string($item)
    {
        $str = '';
        if ('base64' === $item['field_format']) {
            $str = Xtm_Provider_Projects::strip_invalid_xml(nl2br(base64_decode($item['field_data'])));
            return $str;
        }
        return $str;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param string $str
     * @param array $item
     */
    protected function add_xml_child(SimpleXMLElement $xml, $str, $item)
    {
        if (trim($str)) {
            $cdata_value = "<![CDATA[" . htmlspecialchars($str) . "]]>";
            $xml->addChild(Xtm_Wpml_Bridge::PLUGIN_NAME, $cdata_value)->addAttribute('id', $item['tid']);
        }
    }

}

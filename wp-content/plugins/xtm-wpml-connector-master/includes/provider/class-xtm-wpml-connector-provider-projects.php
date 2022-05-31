<?php


/**
 * Class Xtm_Provider_Jobs
 */
class Xtm_Provider_Projects
{
    const IN_PROGRESS = 'in progress';

    const XML_STRING_FILE_PREFIX = "_";
    const XML_POST_FILE_PREFIX = "wp_";

    public static function cancel_project($project_id)
    {
        $project = Xtm_Model_Projects::get($project_id);
        $options = get_option(Xtm_Wpml_Bridge::PLUGIN_NAME);
        $client_name = $options[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME];
        if ($project->client_name !== $client_name) {
            return false;
        }
        if ((int)$project->reference > 0 && in_array($project->status,
                [
                    self::IN_PROGRESS,
                    Xtm_Provider_Readable_State::XTM_STATE_IN_PROGRESS,
                    Xtm_Provider_Readable_State::XTM_STATE_PARTIALLY_FINISHED,
                    Xtm_Provider_Readable_State::XTM_STATE_ACTIVE,
                    Xtm_Provider_Readable_State::XTM_STATE_ERROR
                ]
            )
        ) {
            $bridge = new Xtm_Wpml_Bridge();
            $status = $bridge->update_project_activity($project_id);
            $bridge = new Xtm_Wpml_Bridge();
            $bridge->check_project_status($project_id);
            return $status;
        }
        return false;
    }

    /**
     * @param $value
     * @return string
     */
    public static function strip_invalid_xml($value)
    {
        $ret = "";
        if (empty($value)) {
            return $ret;
        }

        $length = strlen($value);
        for ($i = 0; $i < $length; $i++) {
            $current = ord($value{$i});
            if (($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF))
            ) {
                $ret .= chr($current);
            } else {
                $ret .= " ";
            }
        }
        return $ret;
    }

    /**
     * @param $item
     * @return string
     */
    public static function create_translate_from_string($item)
    {
        $str = '';
        if ('base64' === $item->field_format) {
            $str = Xtm_Provider_Projects::strip_invalid_xml((base64_decode($item->field_data)));
            return $str;
        }
        return $str;
    }

    /**
     * @param $domains
     * @param $job
     * @return mixed
     */
    public static function prepare_strings_array($domains, $job, &$added_list)
    {
        $icl_string = Xtm_Model_Icl_Strings::get(["id" => $job['string_id']]);
        $id = $icl_string->id;
        if (in_array($id, $added_list)) {
            return $domains;
        }

        $domain = self::XML_STRING_FILE_PREFIX . $icl_string->context;
        if (empty($domains[$domain])) {
            $domains[$domain] = new \SimpleXMLElement('<' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's></' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's>');
        }
        $text = htmlspecialchars($icl_string->value);
        $cdata_value = "<![CDATA[" . $text . "]]>";
        $added_list[] = $id;
        $child = $domains[$domain]->addChild(Xtm_Wpml_Bridge::PLUGIN_NAME, $cdata_value);
        /** @var SimpleXMLElement $child */
        $child->addAttribute('id', $id);
        $child->addAttribute('type', 'string');

        return $domains;
    }

    /**
     * @param $temp_dir
     * @param $job
     * @param $type
     */
    public static function create_xml_page($temp_dir, $job, $type)
    {
        $pages_dir = $temp_dir . "/" . $type . "/";
        mkdir($pages_dir, 0777);
        $xml = new \SimpleXMLElement('<' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's></' . Xtm_Wpml_Bridge::PLUGIN_NAME . 's>');
        foreach ($job['elements'] as $element) {
            $str = Xtm_Provider_Projects::create_translate_from_string($element);
            preg_match_all('@alt="([^"]+)"@', $str, $match);

            if ($match[1]) {
                $str = str_replace($match[1], str_replace(["&quot;", "&gt;"], "", $match[1]), $str);
            }

            $cdata_value = "<![CDATA[" . htmlspecialchars($str) . "]]>";
            $child = $xml->addChild(Xtm_Wpml_Bridge::PLUGIN_NAME, $cdata_value);
            $child->addAttribute('id', $element->tid);
            $child->addAttribute('type', $type);
        }
        $xmlString = $xml->asXML();
        $xmlString = str_replace('<?xml version="1.0"?>',
            '<?xml version="1.0"?><!DOCTYPE html [ <!ENTITY nbsp "&#160;"><!ENTITY copy "&#169;"><!ENTITY bull "&#8226;"> ]>',
            $xmlString);
        file_put_contents($pages_dir . '/' . self::XML_POST_FILE_PREFIX . $job['post_title'] . ".xml", $xmlString);
    }
}

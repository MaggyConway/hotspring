<?php

/**
 * Class Xtm_Model_Xtm_Job
 */
class Xtm_Model_Projects extends XTM_Model
{
    /**
     * @var string
     */
    public static $primaryKey = 'project_id';

    /**
     * @param $data
     */
    static public function insert($data)
    {
        $data['created'] = date('Y-m-d H:i:s');
        $options = get_option(Xtm_Wpml_Bridge::PLUGIN_NAME);
        $data['client_name'] = $options[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME];
        parent::insert($data);
    }
}

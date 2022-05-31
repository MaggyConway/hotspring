<?php

/**
 * Class Xtm_Model_Xtm_Job
 */
class Xtm_Model_Icl_Translate_Job extends XTM_Model
{
    protected static $tablePrefix = '';

    /**
     * @var string
     */
    public static $primaryKey = 'job_id';

    public static function get_by_rids($rids)
    {
        $jobs = self::get_all();
        $output_array = [];
        foreach ($jobs as $job){
            if (in_array($job['rid'],$rids)){
                $output_array[] = $job;
            }
        }
        return $output_array;
    }
}




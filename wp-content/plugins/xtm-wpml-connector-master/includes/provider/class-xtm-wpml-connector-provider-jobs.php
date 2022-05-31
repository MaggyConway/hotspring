<?php


/**
 * Class Xtm_Provider_Jobs
 */
class Xtm_Provider_Jobs
{
    /**
     * @return array
     */
    public static function get_post_ids()
    {
        return self::get_ids('post');
    }

    /**
     * @return array
     */
    public static function get_string_ids()
    {
        return self::get_ids('string');
    }

    /**
     * @param string $type
     * @return array
     */
    public static function get_ids($type = '')
    {
        global $wpdb;

        $where = ($type !== '') ? "WHERE element_type_prefix = '" . $type . "'" : '';
        $only_ids_query = "SELECT SQL_CALC_FOUND_ROWS
    jobs.job_id    
/*    ,jobs.translator_id,
    jobs.batch_id,
    jobs.element_type_prefix*/
FROM
    (SELECT 
        s.translator_id,
            j.job_id,
            IF(p.post_type IS NOT NULL, 'post', 'package') AS element_type_prefix,
            s.batch_id
    FROM
        ".$wpdb->prefix."icl_translation_status s
    JOIN ".$wpdb->prefix."icl_translations t ON t.translation_id = s.translation_id
    JOIN ".$wpdb->prefix."icl_translate_job j ON j.rid = s.rid AND j.revision IS NULL
    JOIN ".$wpdb->prefix."icl_translations o ON o.trid = t.trid
        AND o.language_code = t.source_language_code
    JOIN (SELECT 
        ID, post_type
    FROM
        ".$wpdb->prefix."posts UNION ALL SELECT 
        ID, NULL AS post_type
    FROM
        ".$wpdb->prefix."icl_string_packages) p ON o.element_id = p.ID
        AND (o.element_type = CONCAT('post_', p.post_type)
        OR p.post_type IS NULL)
    JOIN ".$wpdb->prefix."icl_translate tr_rows ON tr_rows.job_id = j.job_id
        AND tr_rows.field_type = 'original_id'
        AND tr_rows.field_data = o.element_id
    WHERE
        s.status > 0 AND s.status <> 9 UNION ALL SELECT 
        st.translator_id,
            st.id AS job_id,
            'string' AS element_type_prefix,
            st.batch_id
    FROM
        ".$wpdb->prefix."icl_string_translations st
    JOIN ".$wpdb->prefix."icl_strings s ON s.id = st.string_id) jobs
        INNER JOIN
    ".$wpdb->prefix."icl_translation_batches b ON b.id = jobs.batch_id 
    " . $where . "            
ORDER BY jobs.batch_id DESC , jobs.element_type_prefix , jobs.job_id DESC
LIMIT 0 , 10000";
        $results = $wpdb->get_results($only_ids_query, ARRAY_A);
        return array_column($results, 'job_id');
    }
    /**
     * @param bool $add_local
     * @return array
     */
    public static function get_jobs($add_local = true)
    {
        global $iclTranslationManagement;

        $_POST['pagination_page'] = 1;
        $_POST['pagination_page_size'] = 100000;
        $user = Xtm_Wpml_Connector_Helper::get_xtm_user();

        $translator_array[] = $user->ID;
        if ($add_local) {
            $translator_array[] = 0;
        }
        $wpml_job_model = new XTM_Translation_Jobs_Table($iclTranslationManagement);
        $jobs = $wpml_job_model->get_paginated_jobs();
        //display only xtm jobs
        foreach ($jobs['Flat_Data'] as $jobs_flat) {
            foreach ($jobs_flat as $key => $translation) {
                if (!in_array($translation['translator_id'], $translator_array)) {
                    unset($jobs[$key]);
                }
            }
        }

        $jobs['metrics']['batch_metrics'] = self::get_batch_metric_array();
        return $jobs;
    }

    /**
     * @return array
     */
    private static function get_batch_metric_array()
    {
        $batch_metric_array = [];
        foreach (Xtm_Model_Icl_Translation_Batches::get_all() as $translation_batch) {
            $batch_metric_array[$translation_batch['id']] = $translation_batch;
        }
        return $batch_metric_array;
    }

    /**
     * @return array
     */
    public static function get_filter_job()
    {
        $filter = [];
        $filter_fields = [
            'filter_lang_from'  => 'filter-lang-from',
            'filter_lang_to'    => 'filter-lang-to',
            'filter_job_status' => 'filter-job-status',
        ];
        foreach ($filter_fields as $key => $value) {
            if (isset($_GET[$value])) {
                $filter[$key] = filter_input(INPUT_GET, $value
                );
            }
        }
        return $filter;
    }
}

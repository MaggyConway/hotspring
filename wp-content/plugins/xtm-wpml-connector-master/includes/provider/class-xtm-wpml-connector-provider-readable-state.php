<?php


class Xtm_Provider_Readable_State
{
    const XTM_STATE_FINISHED = 'FINISHED';
    const XTM_STATE_IN_PROGRESS = 'IN_PROGRESS';
    const XTM_STATE_PARTIALLY_FINISHED = 'PARTIALLY_FINISHED';
    const XTM_STATE_ARCHIVE = 'ARCHIVED';
    const XTM_STATE_ERROR = 'ERROR';
    const XTM_STATE_ACTIVE = 'ACTIVE';
    const XTM_STATE_DELETED = 'DELETED';

    /**
     * @param string $action
     * @return string
     */
    public static function get_readable_state($action)
    {
        switch ($action) {
            case self::XTM_STATE_ACTIVE:
                return __('Active');
            case self::XTM_STATE_IN_PROGRESS:
                return __('In progress');
            case self::XTM_STATE_FINISHED:
                return __('Finished');
            case self::XTM_STATE_ERROR:
                return __('Error');
            case self::XTM_STATE_PARTIALLY_FINISHED:
                return __('Partially finished');
            case self::XTM_STATE_ARCHIVE:
                return __('Archived');
            case self::XTM_STATE_DELETED:
                return __('Deleted');
            default:
                return $action;
        }
    }

    /**
     * @return array
     */
    public static function get_status_list()
    {
        $status_array = [
            self::XTM_STATE_ACTIVE,
            self::XTM_STATE_FINISHED,
            self::XTM_STATE_ERROR,
            self::XTM_STATE_ARCHIVE,
            self::XTM_STATE_DELETED
        ];
        $output_array = [];
        foreach ($status_array as $status) {
            $output_array[$status] = self::get_readable_state($status);
        }
        return $output_array;
    }
}

<?php


/**
 * Class XTM_Model
 */
abstract class XTM_Model
{
    const XTM_MODEL = 'xtm_model_';
    protected static $tablePrefix = 'xtm_';
    const GMT = 'GMT';
    /**
     * @var string
     */
    public static $primaryKey = 'id';

    /**
     * @return string
     */
    private static function table()
    {
        global $wpdb;
        $tableName = strtolower(get_called_class());
        $tableName = str_replace(self::XTM_MODEL, static::$tablePrefix, $tableName);
        return $wpdb->prefix . $tableName;
    }

    /**
     * @param $value
     * @return string
     */
    private static function fetch_sql($value)
    {
        global $wpdb;
        $sql = sprintf('SELECT * FROM %s WHERE %s = %%s', self::table(), static::$primaryKey);
        return $wpdb->prepare($sql, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return object
     */
    public static function get_by_field($field, $value)
    {
        global $wpdb;
        $sql = sprintf('SELECT * FROM %s WHERE %s = %%s ', self::table(), $field);

        return $wpdb->get_row($wpdb->prepare($sql, $value));
    }

    /**
     * @param $value
     * @return array|null|object
     */
    public static function get($value)
    {
        global $wpdb;
        return $wpdb->get_row(self::fetch_sql($value));
    }

    /**
     * @param array $filter
     * @return array|null|object
     */
    public static function get_all($filter = null)
    {
        global $wpdb;
        $where = '';
        $str_where = " WHERE ";
        if (!empty($filter)){
            $where = $str_where;
            foreach ($filter as $key => $value) {
                if ($str_where !== $where) {
                    $where .= " AND ";
                }
                $where .= sprintf("%s = '%s'", $key, $value);
            }

        }
        $sql = sprintf('SELECT * FROM %s ', self::table());
        $sql .= $where;
        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * @param $data
     */
    public static function insert($data)
    {
        global $wpdb;
        $wpdb->insert(self::table(), $data);
    }

    /**
     * @param $data
     * @param $where
     */
    public static function update($data, $where)
    {
        global $wpdb;
        $wpdb->update(self::table(), $data, $where);
    }

    /**
     * @param $value
     * @return false|int
     */
    public static function delete($value)
    {
        global $wpdb;
        $sql = sprintf('DELETE FROM %s WHERE %s = %%s', self::table(), static::$primaryKey);
        return $wpdb->query($wpdb->prepare($sql, $value));
    }

    /**
     * @return int
     */
    public static function insert_id()
    {
        global $wpdb;
        return $wpdb->insert_id;
    }

    /**
     * @param $time
     * @return false|string
     */
    public static function timeToDate($time)
    {
        return gmdate('Y-m-d H:i:s', $time);
    }

    /**
     * @return false|string
     */
    public static function now()
    {
        return self::timeToDate(time());
    }

    /**
     * @param $date
     * @return false|int
     */
    public static function dateToTime($date)
    {
        return strtotime($date . ' ' . self::GMT);
    }
}

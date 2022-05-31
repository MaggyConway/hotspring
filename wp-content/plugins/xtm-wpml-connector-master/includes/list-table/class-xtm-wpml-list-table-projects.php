<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://xtm-intl.com/
 * @since      1.0.0
 *
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/admin/partials
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    error_reporting(~E_NOTICE);
}


class WPML_Projects_List_Table extends WP_List_Table
{
    public $data = [];
    const XTM_CHECK_STATUS = 'xtm-check-status';
    const XTM_CANCEL_PROJECT = 'xtm-cancel-project';

    /**
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     */
    public function __construct()
    {
        parent::__construct(
            [
                //singular name of the listed records
                'singular' => 'project_id',
                //plural name of the listed records
                'plural'   => 'projects',
                //does this table support ajax?
                'ajax'     => true
            ]
        );

        $sent_to_xtm = new Xtm_Wpml_Connector_Callbacks();
        $sent_to_xtm->run();
    }

    /**
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param string $column_name The name/slug of the column to be processed
     *
     * @return string Text or HTML to be placed inside the column <td>
     */
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'created':
            case 'project_id':
            case 'source_language':
            case 'target_language':
            case 'label':
                return $item[$column_name];
            default:
                //Show the whole array for troubleshooting purposes
                return print_r($item, true);
        }
    }

    function create_filter($options, $name, $default_value)
    {
        ?>
        <div class="alignleft actions bulkactions">
            <?php
            $move_on_url = '&' . $name . '=';
            if ($options) : ?>
                <select name="<?php echo $name; ?>" class="xtm-filter">
                    <option value=""><?php echo $default_value; ?></option>
                    <?php
                    foreach ($options as $id => $value) :
                        $selected = '';
                        if (filter_input(INPUT_GET, $name) === (string)$id) {
                            $selected = ' selected = "selected"';
                        } ?>
                        <option value="<?php echo $move_on_url . $id; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
        <?php
    }


    function extra_tablenav($which)
    {
        if ($which == "top") : ?>
            <?php
            //$helper = new Xtm_Wpml_Connector_Helper();
            //$this->create_filter($helper->get_project_modes(),'project-modes-filter', __('Filter by Project Modes'));
            $this->create_filter(Xtm_Provider_Readable_State::get_status_list(), 'status-list-filter',
                __('Filter by Status List'));
            global $sitepress;
            $source_language = [];
            $active_languages = $sitepress->get_active_languages();
            foreach ($active_languages as $key => $val) {
                $source_language[$key] = $val['english_name'];
            }
            $this->create_filter($source_language, 'source-language-filter', __('Filter by Source Language'));
            $this->create_filter($source_language, 'target-language-filter', __('Filter by Target Language'));
            ?>
        <?php endif;
    }

    /**
     * @param array $item
     * @return String
     */
    function column_status($item)
    {
        return esc_html(Xtm_Provider_Readable_State::get_readable_state($item['status']));
    }


    /**
     * @param array $item
     * @return String
     */
    function column_job_id($item)
    {
        return esc_html($item['translation_id']);
    }

    function column_reference($item)
    {
        $referencePrepared = esc_html($item['reference']);
        echo "<a target='blank' href='" . $item['xtm_link'] . $referencePrepared . "'>";
        echo $referencePrepared;
        echo "</a>";
    }

    /**
     * @param $item
     */
    function column_action($item)
    {
        if (0 < (int)$item['reference']) {
            submit_button(esc_attr('Check XTM status'), 'button-primary', 'check-xtm-status', false,
                ['id' => 'check-xtm-status-' . $item['project_id'], 'data-id' => $item['project_id']]);
            if (Xtm_Provider_Readable_State::XTM_STATE_FINISHED !== $item['status']) {
                submit_button(esc_attr('Cancel'), 'delete', self::XTM_CANCEL_PROJECT, false,
                    ['id' => 'xtm-cancel-project-' . $item['project_id'], 'data-id' => $item['project_id']]);
            }
        }
    }

    /*
     * @todo Add hiperlink
     */
    function column_wpml_job_id($item)
    {
        return Xtm_Wpml_Connector_Helper::isJson($item['wpml_job_id']) ?
            implode(", ", json_decode($item['wpml_job_id'], true)) : esc_html($item['wpml_job_id']);
    }

    /**
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     *
     * @return string Text to be placed inside the column <td> (movie title only)
     */
    function column_cb($item)
    {
        return sprintf(
            '<input class="project-id-checkbox" type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'],
            /*$2%s*/
            $item['project_id']            //The value of the checkbox should be the record's id
        );
    }

    /**
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::single_row_columns()
     *
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     */
    function get_columns()
    {
        return $columns = [
            'cb'              => '<input type="checkbox" />', //Render a checkbox instead of text
            'project_id'      => __('Project ID'),
//            'label'            => __('Label'),
            'source_language' => __('Source Language'),
            'target_language' => __('Target Language'),
            'status'          => __('Status'),
            'wpml_job_id'     => __('WPML Job Id'),
            'created'         => __('Created'),
//            'api_project_mode' => __('XTM Project Mode'),
            'reference'       => __('XTM Project Id'),
            'action'          => __('Actions'),
        ];
    }

    /**
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */
    function get_sortable_columns()
    {
        return $sortable_columns = [
            'label'           => ['label', false],    //true means it's already sorted
            'created'         => ['created', false],
            'status'          => ['status', false],
            'project_id'      => ['project_id', false],
            'source_language' => ['source_language', false],
            'reference'       => ['reference', false],
            'target_language' => ['target_language', false],
            'wpml_job_id'     => ['wpml_job_id', false],
        ];
    }


    /**
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     */
    function get_bulk_actions()
    {
        $actions = [
            self::XTM_CHECK_STATUS   => esc_html__('Check XTM status'),
            self::XTM_CANCEL_PROJECT => esc_html__('Cancel project'),
        ];

        return $actions;
    }


    public function process_bulk_action()
    {

        $bridge = new Xtm_Wpml_Bridge();
        $projects = filter_input(INPUT_GET, 'project_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        switch ($this->current_action()) {
            case self::XTM_CHECK_STATUS:
                foreach ($projects as $project_id) {
                    $projectModel = Xtm_Model_Projects::get($project_id);
                    /** @var object $projectModel */
                    if ((int)$projectModel->reference > 0) {
                        echo $bridge->check_project_status($project_id);
                    }
                }
                break;
            case self::XTM_CANCEL_PROJECT:
                $success_counter = 0;
                foreach ($projects as $project_id) {
                    if (Xtm_Provider_Projects::cancel_project($project_id)) {
                        $success_counter++;
                    }
                }
                echo Xtm_Wpml_Connector_Helper::display_success_notice(__("Project cancel success",
                    Xtm_Wpml_Bridge::PLUGIN_NAME), $success_counter . "/" . count($projects));
                break;
        }

        return;
    }

    /**
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     */

    /**
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */
    function prepare_items()
    {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 10;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = [$columns, $hidden, $sortable];

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        foreach ($this->data as &$row) {
            $row['source_language'] = Xtm_Wpml_Connector_Helper::convert_language_to_string($row['source_language']);

            $target_languages = explode(',', $row['target_language']);
            foreach ($target_languages as &$target_language) {
                $target_language = Xtm_Wpml_Connector_Helper::convert_language_to_string($target_language);
            }

            $row['target_language'] = implode(', ', $target_languages);

        }
        $data = $this->data;

        function usort_reorder($a, $b)
        {
            //If no sort, default to created
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'created';
            //If no order, default to desc
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
            //Determine sort order
            $result = strnatcasecmp($a[$orderby], $b[$orderby]);
            //Send final sort direction to usort
            return ('asc' === $order) ? $result : -$result;
        }

        usort($data, 'usort_reorder');

        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);

        /**
         * The WP_List_Table does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(
            [
                //WE have to calculate the total number of items
                'total_items' => $total_items,
                //WE have to determine how many items to show on a page
                'per_page'    => $per_page,
                //WE have to calculate the total number of pages
                'total_pages' => ceil($total_items / $per_page),
                // Set ordering values if needed (useful for AJAX)
                'orderby'     => !empty($_REQUEST['orderby']) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'project_id',
                'order'       => !empty($_REQUEST['order']) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'desc'
            ]
        );
    }

    /**
     * Display the table
     * Adds a Nonce field and calls parent's display method
     *
     * @since 3.1.0
     * @access public
     */
    function display()
    {
        wp_nonce_field('ajax-custom-list-nonce', '_ajax_custom_list_nonce');
        echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
        echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
        parent::display();
    }

    /**
     * Handle an incoming ajax request (called from admin-ajax.php)
     *
     * @since 3.1.0
     * @access public
     */
    function ajax_response()
    {
        check_ajax_referer('ajax-custom-list-nonce', '_ajax_custom_list_nonce');
        $this->prepare_items();
        extract($this->_args);
        extract($this->_pagination_args, EXTR_SKIP);
        ob_start();

        if (!empty($_REQUEST['no_placeholder'])) {
            $this->display_rows();
        } else {
            $this->display_rows_or_placeholder();
        }
        $rows = ob_get_clean();
        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();
        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();
        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();
        $response = ['rows' => $rows];
        $response['pagination']['top'] = $pagination_top;
        $response['pagination']['bottom'] = $pagination_bottom;
        $response['column_headers'] = $headers;
        if (isset($total_items)) {
            $response['total_items_i18n'] = sprintf(_n('1 item', '%s items', $total_items),
                number_format_i18n($total_items));
        }
        if (isset($total_pages)) {
            $response['total_pages'] = $total_pages;
            $response['total_pages_i18n'] = number_format_i18n($total_pages);
        }
        die(json_encode($response));
    }
}

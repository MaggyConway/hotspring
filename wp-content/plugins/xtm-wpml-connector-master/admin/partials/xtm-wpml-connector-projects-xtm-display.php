<?php
/** *************************** RENDER PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function projects_render_list_page()
{
    $filter = Xtm_Wpml_Connector_Helper::get_filter_project();
    $project_list_table = new WPML_Projects_List_Table();
    $project_list_table->data = Xtm_Model_Projects::get_all($filter);
    //Fetch, prepare, sort, and filter our data...
    $project_list_table->prepare_items();
    ?>
    <div class="wrap">
        <div id="icon-users" class="icon32"><br/></div>
        <h2><?php _e("XTM WPML Projects", Xtm_Wpml_Bridge::PLUGIN_NAME); ?></h2>

        <form id="project-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <!-- Now we can render the completed list table -->
            <?php $project_list_table->display() ?>
        </form>
    </div>
    <?php
}

/**
 * This function adds the jQuery script to the plugin's page footer
 */
function projects_ajax_script()
{
    $screen = get_current_screen();
    if ('xtm_page_xtm-projects' !== $screen->id) {
        return false;
    }
    ?>
    <script type="text/javascript">
        var orderData = {
            paged: '1',
            order: 'desc',
            orderby: 'project_id'
        };
        function get_data_for_update($) {
            var data = {
                paged: parseInt(orderData.paged) || '1',
                order: orderData.order || 'desc',
                orderby: orderData.orderby || 'project_id'
            };
            var query_window = window.location.search.substring(1);
            $(".xtm-filter").each(function () {
                if ($(this).val() != '') {
                    data[$(this).attr('name')] = list.__query(query_window, $(this).attr('name')) || '';
                }
            });
            return data;
        }
        function check_xtm_status($) {
            $('input[name=check-xtm-status]').each(function () {
                $(this).click(function (event) {
                    event.preventDefault();
                    var  rowID = $(this).data('id');
                    $("#spinner-" +  rowID).hide();
                    $("#check-xtm-status-"+ rowID).prop('disabled', true);
                    $("#xtm-cancel-project-"+ rowID).prop('disabled', true);
                    $(this).after('<div id="spinner-' + rowID + '" class="spinner is-active" style="">');
                    data = [];
                    $.ajax({
                        url: ajaxurl,
                        // Add action and nonce to our collected data
                        data: $.extend(
                            {
                                project_id:  rowID,
                                action: '_ajax_check_xtm_status'
                            },
                            data
                        ),
                        // Handle the successful result
                        success: function (response) {
                            // WP_List_Table::ajax_response() returns json
                            var response_parsed = $.parseJSON(response);
                            $("#project-filter").before(response_parsed);
                            var data = get_data_for_update($);
                            list.update(data);
                            // Add the requested rows
                        }
                    });
                    return false;
                });
            });
        }
        function cancel_project($) {
            $('input[name=xtm-cancel-project]').each(function () {
                $(this).click(function (event) {
                    event.preventDefault();
                    if (false === confirm("Are you sure you want to cancel XTM translation project?")) {
                        return false;
                    }
                    var  rowID = $(this).data('id');
                    $(this).after('<div id="spinner-' +  rowID + '" class="spinner is-active" style="">');
                    $("#check-xtm-status-"+ rowID).prop('disabled', true);
                    $("#xtm-cancel-project-"+ rowID).prop('disabled', true);
                    data = [];
                    $.ajax({
                        url: ajaxurl,
                        // Add action and nonce to our collected data
                        data: $.extend(
                            {
                                project_id:  rowID,
                                action: '_ajax_xtm_cancel_project'
                            },
                            data
                        ),
                        // Handle the successful result
                        success: function (response) {
                            // WP_List_Table::ajax_response() returns json
                            var response_parsed = $.parseJSON(response);
                            switch( response) {
                                case 'true':
                                    alert("Project has been canceled");
                                    break;
                                case 'false':
                                    alert("Connection problem. The project has not been archived");
                                    break;
                                default:
                                    alert(response_parsed);
                            }
                           // (response_parsed) ? alert("Project has been canceled") : alert("Connection problem. The project has not been archived");
                            var data = get_data_for_update($);
                            list.update(data);
                            // Add the requested rows
                        },
                        complete: function () {
                        }
                    });
                    return false;
                });
            });
        }

        (function ($) {
            list = {
                /**
                 * Register our triggers
                 *
                 * We want to capture clicks on specific links, but also value change in
                 * the pagination input field. The links contain all the information we
                 * need concerning the wanted page number or ordering, so we'll just
                 * parse the URL to extract these variables.
                 *
                 * The page number input is trickier: it has no URL so we have to find a
                 * way around. We'll use the hidden inputs added in Jobs_Example_List_Table::display()
                 * to recover the ordering variables, and the default paged input added
                 * automatically by WordPress.
                 */
                init: function () {
                    // This will have its utility when dealing with the page number input
                    var timer;
                    var delay = 500;
                    // Pagination links, sortable link
                    $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function (e) {
                        // We don't want to actually follow these links
                        e.preventDefault();
                        // Simple way: use the URL to extract our needed variables

                        var query_window = window.location.search.substring(1);
                        var query = this.search.substring(1);
                        var data = {
                            paged: list.__query(query, 'paged') || '1',
                            order: list.__query(query, 'order') || 'desc',
                            orderby: list.__query(query, 'orderby') || 'project_id'
                        };
                        $(".xtm-filter").each(function () {
                            if ($(this).val() != '') {
                                data[$(this).attr('name')] = list.__query(query_window, $(this).attr('name')) || '';
                            }
                        });
                        orderData = data;
                        list.update(data);
                    });
                    // Page number input
                    $('input[name=paged]').on('keyup', function (e) {
                        // If user hit enter, we don't want to submit the form
                        // We don't preventDefault() for all keys because it would
                        // also prevent to get the page number!
                        if (13 == e.which)
                            e.preventDefault();
                        // This time we fetch the variables in inputs
                        var data = get_data_for_update($);
                        // Now the timer comes to use: we wait half a second after
                        // the user stopped typing to actually send the call. If
                        // we don't, the keyup event will trigger instantly and
                        // thus may cause duplicate calls before sending the intended
                        // value
                        window.clearTimeout(timer);
                        timer = window.setTimeout(function () {
                            list.update(data);
                        }, delay);
                    });
                },
                /** AJAX call
                 *
                 * Send the call and replace table parts with updated version!
                 *
                 * @param data The data to pass through AJAX
                 */
                update: function (data) {
                    $.ajax({
                        // /wp-admin/admin-ajax.php
                        url: ajaxurl,
                        // Add action and nonce to our collected data
                        data: $.extend(
                            {
                                _ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
                                action: '_ajax_fetch_project_list'
                            },
                            data
                        ),
                        // Handle the successful result
                        success: function (response) {
                            // WP_List_Table::ajax_response() returns json
                            var response_parsed = $.parseJSON(response);
                            // Add the requested rows
                            if (response_parsed.rows.length)
                                $('#the-list').html(response_parsed.rows);
                            // Update column headers for sorting
                            if (response_parsed.column_headers.length)
                                $('thead tr, tfoot tr').html(response_parsed.column_headers);
                            // Update pagination for navigation
                            if (response_parsed.pagination.bottom.length)
                                $('.tablenav.top .tablenav-pages').html($(response_parsed.pagination.top).html());
                            if (response_parsed.pagination.top.length)
                                $('.tablenav.bottom .tablenav-pages').html($(response_parsed.pagination.bottom).html());
                            // Init back our event handlers
                            list.init();
                            check_xtm_status($);
                            cancel_project($);
                        }
                    });
                },
                /**
                 * Filter the URL Query to extract variables
                 *
                 * @see http://css-tricks.com/snippets/javascript/get-url-variables/
                 *
                 * @param    string    query The URL query part containing the variables
                 * @param    string    variable Name of the variable we want to get
                 *
                 * @return   string|boolean The variable value if available, false else.
                 */
                __query: function (query, variable) {
                    var vars = query.split("&");
                    for (var i = 0; i < vars.length; i++) {
                        var pair = vars[i].split("=");
                        if (pair[0] == variable)
                            return pair[1];
                    }
                    return false;
                },
            }
            list.init();
            check_xtm_status($);
            cancel_project($);
            $(".xtm-filter").change(function () {
                var catFilter = "";
                $(".xtm-filter").each(function () {
                    if ($(this).val() != '') {
                        catFilter = catFilter + $(this).val();
                    }
                });
                document.location.href = 'admin.php?page=xtm-projects' + catFilter;
            });
            $("#doaction").click(function () {
                if ($("#bulk-action-selector-top").val() < 0) {
                    alert("Please select bulk action.");
                    return false;
                }
                var $counter = 0;
                $(".project-id-checkbox").each(function () {
                    if ($(this).is(':checked')) {
                        $counter++;
                    }
                });
                if ($counter === 0) {
                    alert("Please select any project");
                    return false;
                }
            });
        })(jQuery);
    </script>
    <?php
}

?>

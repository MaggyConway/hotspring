<?php

/** *************************** RENDER PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */


/**
 * @param array $jobs
 * @param array $metrics
 */
function jobs_render_list_page(array $jobs, array $metrics){

    //Create an instance of our package class...
    $jobsListTable = new WPML_Jobs_List_Table();
    $jobsListTable->data = $jobs;
    $jobsListTable->metrics = $metrics;
    //Fetch, prepare, sort, and filter our data...
    $jobsListTable->prepare_items();

    ?>
    <div class="wrap">
        <div id="icon-users" class="icon32"><br/></div>
        <h2><?php _e('XTM WPML Jobs', Xtm_Wpml_Bridge::PLUGIN_NAME);?></h2>
        <form id="jobs-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $jobsListTable->display() ?>
        </form>
    </div>
    <?php
}

/**
 * This function adds the jQuery script to the plugin's page footer
 */
function jobs_ajax_script() {
    $screen = get_current_screen();
    if ('toplevel_page_xtm-connector' != $screen->id) {
        return false;
    }
    ?>
    <script type="text/javascript">
        (function($) {
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
                init: function() {
                    // This will have its utility when dealing with the page number input
                    var timer;
                    var delay = 500;
                    // Pagination links, sortable link
                    $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
                        // We don't want to actually follow these links
                        e.preventDefault();
                        // Simple way: use the URL to extract our needed variables
                        var query = this.search.substring( 1 );

                        var data = {
                            paged: list.__query( query, 'paged' ) || '1',
                            order: list.__query( query, 'order' ) || 'desc',
                            orderby: list.__query( query, 'orderby' ) || 'batch_id',
                            'filter-job-status' : $("#filter-job-status").val() || '',
                            'filter-lang-from' : $("#filter-job-lang-from").val() || '',
                            'filter-lang-to' : $("#filter-job-lang-to").val() || ''
                        };
                        list.update( data );
                    });
                    // Page number input
                    $('input[name=paged]').on('keyup', function(e) {
                        // If user hit enter, we don't want to submit the form
                        // We don't preventDefault() for all keys because it would
                        // also prevent to get the page number!
                        if ( 13 == e.which )
                            e.preventDefault();
                        // This time we fetch the variables in inputs
                        var data = {
                            paged: parseInt( $('input[name=paged]').val() ) || '1',
                            order: $('input[name=order]').val() || 'desc',
                            orderby: $('input[name=orderby]').val() || 'batch_id',
                            'filter-job-status' : $("#filter-job-status").val() || '',
                            'filter-lang-from' : $("#filter-job-lang-from").val() || '',
                            'filter-lang-to' : $("#filter-job-lang-to").val() || ''

                        };
                        // Now the timer comes to use: we wait half a second after
                        // the user stopped typing to actually send the call. If
                        // we don't, the keyup event will trigger instantly and
                        // thus may cause duplicate calls before sending the intended
                        // value
                        window.clearTimeout( timer );
                        timer = window.setTimeout(function() {
                            list.update( data );
                        }, delay);
                    });
                },
                /** AJAX call
                 *
                 * Send the call and replace table parts with updated version!
                 *
                 * @param    object    data The data to pass through AJAX
                 */
                update: function( data ) {
                    $.ajax({
                        // /wp-admin/admin-ajax.php
                        url: ajaxurl,
                        // Add action and nonce to our collected data
                        data: $.extend(
                            {
                                _ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
                                action: '_ajax_fetch_job_list',
                            },
                            data
                        ),
                        // Handle the successful result
                        success: function( response ) {
                            // WP_List_Table::ajax_response() returns json
                            var response = $.parseJSON( response );
                            // Add the requested rows
                            if ( response.rows.length )
                                $('#the-list').html( response.rows );
                            // Update column headers for sorting
                            if ( response.column_headers.length )
                                $('thead tr, tfoot tr').html( response.column_headers );
                            // Update pagination for navigation
                            if ( response.pagination.bottom.length )
                                $('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
                            if ( response.pagination.top.length )
                                $('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );
                            // Init back our event handlers
                            list.init();
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
                __query: function( query, variable ) {
                    var vars = query.split("&");
                    for ( var i = 0; i <vars.length; i++ ) {
                        var pair = vars[ i ].split("=");
                        if ( pair[0] == variable )
                            return pair[1];
                    }
                    return false;
                }
            }
            list.init();
            $("#filter-job-status, #filter-job-lang-from, #filter-job-lang-to").change(function () {

                var data = {
                    paged: parseInt( $('input[name=paged]').val() ) || '1',
                    order: $('input[name=order]').val() || 'desc',
                    orderby: $('input[name=orderby]').val() || 'batch_id',
                    'filter-job-status' : $("#filter-job-status").val() || '',
                    'filter-lang-from' : $("#filter-job-lang-from").val() || '',
                    'filter-lang-to' : $("#filter-job-lang-to").val() || ''
                };
                list.update(data);
            });
            $("#doaction").click(function () {
                if ($("#bulk-action-selector-top").val() < 0 ){
                    alert("Please select bulk action.");
                    return false;
                }
                var $counter = 0;
                $(".job-id-checkbox").each(function () {
                    if ($(this).is(':checked')) {
                        $counter++;
                    }
                });
                if ($counter === 0 ){
                    alert("Please select any job");
                    return false;
                }
            });
        })(jQuery);
    </script>
    <?php
}
?>


jQuery(document).ready(function ($) {
  //var bulk;
  function getShelfRecursive() {
    $.ajax({
      type: 'GET',
      dataType: 'json',
      crossDomain: true,
      url: ajaxurl, //this is Wordpress variable
      data: {
        action: 'queue_process', //action name, add_action('wp_ajax_[bulk_process]', 'bulk_process');
      },
      success: function (response) {
        bulk.count = bulk.count - 1;
        if (bulk.count <= 0) {
          $('.processing').html('Processing: completed').addClass('completed');
        } else {
          // $('.processing').html(response.message);
          $('.processing').html('Processing:' + bulk.count + ' of ' + bulk.total);

          //console.log('Processing:' + bulk.count + ' of ' + bulk.total);
          getShelfRecursive();
        }
      },
      error: function (response) {
        bulk.count = bulk.count - 1;
        //console.log('bulk error');
        getShelfRecursive();
      }
    });
  }
  getShelfRecursive();



});
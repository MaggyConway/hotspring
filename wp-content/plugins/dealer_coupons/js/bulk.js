jQuery(document).ready(function($) {


  //some process metrics variables, might be usefull for creating nice interface
  var bulk_actions_total = 0;
  var bulk_actions_success = 0;
  var bulk_actions_fail = 0;
  var bulk_actions_current = 0;
  var bulk_actions_remainded = 0;
  var bulk_actions_complete_percentage = 0;
  var bulk_actions_time_start = 0;
  var bulk_actions_time_elapsed = 0;
  var bulk_actions_time_remainded = 0;
  var bulk_actions_time_per_item = 0;

  //This function called as many times as you input
  //it just call php bulk_process() function via ajax
  //recursively until all actions will be done
  function bulk_process() {
    //recalculate most important metric values
    bulk_actions_current = bulk_actions_current + 1;
    bulk_actions_remainded = bulk_actions_remainded - 1;

    var bar = document.querySelector('.bar-fill');
    if( bulk_actions_total >= bulk_actions_current ){
      var width = 0;
      bar.style.width = '0%';
      width = Math.round(bulk_actions_current / (bulk_actions_total / 100));
      bar.style.width = width + '%';
    }


    //check if we are done
    if (bulk_actions_remainded < 0) return bulk_finish();

    //make another request
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: ajaxurl, //this is Wordpress variable
      data: {
        action: 'bulk_process', //action name, add_action('wp_ajax_[bulk_process]', 'bulk_process');
        _ajax_nonce: dc_nonce, //our nonce, defined in bulk_page_handler function
        bulk_nonce: $('bulk_nonce').val(), //and there can be as much data as u want, it will be accessible in php via $_POST
        index: bulk_actions_current,
        //...
        bulk_count: $('bulk_count').val()
      },
      success: function(response) {
        //in both cases of success and failure we call bulk_update function
        //to notify user about changes, also we are going to recursion
        bulk_update(response, response.success); //notice, response.success coming from php, and can be false
        bulk_process();
      },
      error: function(response) {
        bulk_update(response, false); //there is some network error or something else
        bulk_process();
      }
    });
  }

  //this function call after every action is done
  function bulk_update(response, success) {
    calculateMetrics(success);

    $('#bulk_process table').append('<tr><td>' + success + '</td><td>' + bulk_actions_total + '</td><td>' + bulk_actions_success + '</td><td>' + bulk_actions_fail + '</td><td>' + bulk_actions_current + '</td><td>' + bulk_actions_remainded + '</td><td>' + bulk_actions_complete_percentage + '%</td><td>' + bulk_actions_time_start + '</td><td>' + humanReadableTime(bulk_actions_time_elapsed) + '</td><td>' + humanReadableTime(bulk_actions_time_remainded) + '</td><td>' + humanReadableTime(bulk_actions_time_per_item) + '</td><td>' + response.message + '</td></tr>');

    console.log(
      response,
      success,
      bulk_actions_total,
      bulk_actions_success,
      bulk_actions_fail,
      bulk_actions_current,
      bulk_actions_remainded,
      bulk_actions_complete_percentage,
      bulk_actions_time_start,
      bulk_actions_time_elapsed,
      bulk_actions_time_remainded,
      bulk_actions_time_per_item
    );
  }

  //we will call this function at the end of our bulk actions
  function bulk_start() {
    //define start metric values
    bulk_actions_total = 10;
    bulk_actions_remainded = 10;
    bulk_actions_time_start = new Date();

    //start bull process
    bulk_process();

    $('#bulk_form').hide();
    $('#bulk_process').show();
  }

  function bulk_finish() {
    console.log('FINISH');
  }
  bulk_start();
  //here we are attaching to submit button click event and starting bulk process
  // $('#bulk_submit').click(function(event) {
  //   // console.log('blabla');
  //   event.preventDefault();
  //   // console.log('blabla');
  //   //get and validate number of actions
  //   // var count = parseInt($('#bulk_count').val());
  //   // if (!count) {
  //   //   alert('Please enter number');
  //   //   return false;
  //   // }
  //
  //   //define start metric values
  //   bulk_actions_total = count;
  //   bulk_actions_remainded = count;
  //   bulk_actions_time_start = new Date();
  //
  //   //start bull process
  //   bulk_process();
  //
  //   $('#bulk_form').hide();
  //   $('#bulk_process').show();
  //
  //   return false;
  // });

  //bulk_start();

  //helper function, must be called every time to get apropriate metrics
  function calculateMetrics(success) {
    if (success) {
      bulk_actions_success = bulk_actions_success + 1;
    } else {
      bulk_actions_fail = bulk_actions_fail + 1;
    }

    bulk_actions_complete_percentage = Math.round(bulk_actions_current / (bulk_actions_total / 100));
    bulk_actions_time_elapsed = (new Date()).getTime() - bulk_actions_time_start.getTime();
    bulk_actions_time_elapsed = bulk_actions_time_elapsed / 1000;
    bulk_actions_time_per_item = bulk_actions_time_elapsed / bulk_actions_current;
    bulk_actions_time_remainded = bulk_actions_remainded * bulk_actions_time_per_item;
  }

  //helper function to display nice time text
  function humanReadableTime(seconds) {
    // 1, 2, 5 - period label
    var periods = [
      ['&#1089;&#1077;&#1082;&#1091;&#1085;&#1076;&#1072;', '&#1089;&#1077;&#1082;&#1091;&#1088;&#1085;&#1076;&#1099;', '&#1089;&#1077;&#1082;&#1091;&#1085;&#1076;'], //1 second, 2 seconds, 5 seconds
      ['&#1084;&#1080;&#1085;&#1091;&#1090;&#1072;', '&#1084;&#1080;&#1085;&#1091;&#1090;&#1099;', '&#1084;&#1080;&#1085;&#1091;&#1090;'], //min
      ['&#1095;&#1072;&#1089;', '&#1095;&#1072;&#1089;&#1072;', '&#1095;&#1072;&#1089;&#1086;&#1074;'], //hour
      ['&#1076;&#1077;&#1085;&#1100;', '&#1076;&#1085;&#1103;', '&#1076;&#1085;&#1077;&#1081;'], //day
      ['&#1085;&#1077;&#1076;&#1077;&#1083;&#1103;', '&#1085;&#1077;&#1076;&#1077;&#1083;&#1080;', '&#1085;&#1077;&#1076;&#1077;&#1083;&#1100;'], //week
      ['&#1084;&#1077;&#1089;&#1103;&#1094;', '&#1084;&#1077;&#1089;&#1103;&#1094;&#1072;', '&#1084;&#1077;&#1089;&#1103;&#1094;&#1077;&#1074;'], //month
      ['&#1075;&#1086;&#1076;', '&#1075;&#1086;&#1076;&#1072;', '&#1083;&#1077;&#1090;'], //year
    ];

    var lengths = [60, 60, 24, 7, 4.35, 12];

    for (var i = 0; seconds >= lengths[i]; i++) {
      seconds = seconds / lengths[i];
    }

    seconds = Math.round(seconds);

    var cases = [2, 0, 1, 1, 1, 2];
    var text = periods[i][(seconds % 100 > 4 && seconds % 100 < 20) ? 2 : cases[Math.min(seconds % 10, 5)]];
    return seconds + ' ' + text;
  }


});

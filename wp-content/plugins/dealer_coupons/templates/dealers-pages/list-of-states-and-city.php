<?php

if ( ! defined('ABSPATH')) exit;  // if direct access

// $state =  sanitize_text_field($_GET['state']);
// $city = sanitize_text_field($_GET['city']);
// $country = sanitize_text_field($_GET['country']);

$state = sanitize_text_field(get_query_var( 'state', NULL ));
$city = sanitize_text_field(urldecode(get_query_var( 'city', NULL )));
$country = sanitize_text_field(get_query_var( 'country', NULL ));

$wpdp = $GLOBALS['wpdp'];
$list_of_state = $wpdp->get->list_of_state();

if(!isset($list_of_state[$country])){
  $country = NULL;
  unset($_GET['country']);
}
if(!isset($list_of_state[$country][$state])){
  $state = NULL;
  unset($_GET['state']);

  $city = NULL;
  unset($_GET['city']);
}
?>

<div class="dp-states-and-city">
	<?php do_action('dp_header'); ?>
	<?php
if(empty($state) && empty($city)){
  do_action('dp_main');
}
if(!empty($state) && empty($city)) {
  do_action('dp_main_state', $state,$country);
}
if(!empty($state) && !empty($city)) {
  do_action('dp_main_city', $city,$state,$country);
}
    ?>
<?php do_action('dp_footer'); ?>
</div>

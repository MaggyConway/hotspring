<?php

if ( ! defined('ABSPATH')) exit;  // if direct access

$state = sanitize_text_field(get_query_var( 'state', NULL ));
$city = sanitize_text_field(urldecode(get_query_var( 'city', NULL )));
$country = sanitize_text_field(get_query_var( 'country', NULL ));
$pagename = sanitize_text_field(get_query_var( 'pagename', NULL ));
$wpdp = $GLOBALS['wpdp'];

$state_name = $wpdp->get->name_of_state($country,$state);

?>
<div class="dp-header">
</div>

<div class="dp-content-wrapper">

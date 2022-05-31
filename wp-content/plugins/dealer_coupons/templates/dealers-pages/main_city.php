<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
$wpdp = $GLOBALS['wpdp'];
// $result = $wpdp->get->dealers_by_state();

$result = $wpdp->get->dealers_by_csc($city, $state, $country);
?>
<div class="dp-main-city">
<?php
do_action('dp_city_header', $city,$result);
do_action('dp_city_content',$result, $city, $state, $country);
do_action('dp_city_map', $result);
?>
</div>

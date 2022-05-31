<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
$wpdp = $GLOBALS['wpdp'];
$result = $wpdp->get->dealers_by_state();
$state_name = $wpdp->get->name_of_state($country,$state);
?>
<div class="dp-main-state">
<?php
do_action('dp_state_header', $state_name);
do_action('dp_state_content', $result[$country][$state]['city'], $state, $country);
?>
</div>

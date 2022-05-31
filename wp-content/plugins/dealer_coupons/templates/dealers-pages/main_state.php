<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
$wpdp = $GLOBALS['wpdp'];
$result = $wpdp->get->dealers_by_state();
$state_name = $wpdp->get->name_of_state($country,$state);
?>
<style>
.dp-state-content {
    -moz-columns: 3 200px;
    -webkit-columns: 3 200px;
    columns: 3 200px;
    padding-bottom: 1em;
}
.state-header{
  text-align: center;
  margin-bottom: 1rem;
}
.bodyContent .dp-main-state ul, .bodyContent .dp-main-state ol{
  margin-top: 0;
  margin-bottom: 0;
}
</style>
<div class="dp-main-state">
<?php
do_action('dp_state_header', $state_name);
do_action('dp_state_content', $result[$country][$state]['city'], $state, $country);

?>
</div>

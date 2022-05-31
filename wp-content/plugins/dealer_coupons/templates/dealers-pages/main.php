<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
$wpdp = $GLOBALS['wpdp'];
$result = $wpdp->get->dealers_by_state();
?>
<style>
.dp-country {
    -moz-columns: 3 200px;
    -webkit-columns: 3 200px;
    columns: 3 200px;
    padding-bottom: 1em;
}
.country-header{
  text-align: center;
}
</style>
<div class="dp-main">
<?php
//array sort
$result = array('USA'=> $result['USA'],'Canada'=>$result['Canada']);
foreach ($result as $country => $data) {

  do_action('dp_country_header', $country);
  do_action('dp_country_content', $data, $country);

}
?>
</div>

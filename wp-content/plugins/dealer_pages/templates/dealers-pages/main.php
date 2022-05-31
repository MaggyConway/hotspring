<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
$wpdp = $GLOBALS['wpdp'];
$result = $wpdp->get->dealers_by_state();
?>
<div class="dp-main">
<?php
//array sort
foreach($result['Canada'] as $province_key => $province) {
  if (!empty($result['CANADA'][$province_key])) {
    $result['Canada'][$province_key]['city'] = array_merge($result['Canada'][$province_key]['city'], $result['CANADA'][$province_key]['city']);
  }
}
$clean_result = array(
  'USA' => $result['USA'],
  'Canada' => $result['Canada'],
);
foreach ($clean_result as $country => $data) {
  do_action('dp_country_header', $country);
  do_action('dp_country_content', $data, $country);
}
?>
</div>

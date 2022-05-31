<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
// $wpdp = $GLOBALS['wpdp'];
// $result = $wpdp->get->dealers_by_state();
?>
<div class="dp-city-content">
  <ul>
<?php
if(isset($result->post_title)){
  // wp_redirect( $result->guid );
  // exit();
  $result = array($result);
}

foreach ($result as $key => $value) {
  $value->guid = rtrim(get_permalink($value->ID), '/');
  $address = get_field( 'dealership_address_1', $value->ID);
  $did = get_field( 'dealership_id',  $value->ID);
  print '<li><a class="dealer-page-link-cookie" data-dealer-id="' . $did .  '" href="' . $value->guid . '" title="'.$address.'">' . $value->post_title . ' – '.$address.'</a></li>';
}
?>
  </ul>
</div>

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
  $value->guid = get_permalink($value->ID);
  print '<li><a href="' . $value->guid . '">' . $value->post_title . '</a></li>';
}
?>
  </ul>
</div>

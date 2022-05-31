<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
// $wpdp = $GLOBALS['wpdp'];
// $result = $wpdp->get->dealers_by_state();
$pagename = sanitize_text_field(get_query_var( 'pagename', NULL ));
?>
<div class="dp-state-content">
  <ul>
<?php
//print_r($result);
$wpdp = $GLOBALS['wpdp'];
ksort($result);
foreach ($result as $key => $value) {
  $dealers = $wpdp->get->dealers_by_csc($key, $state, $country);

  $count = count($dealers);
  //if dealer only one on city
  if($count == 1){
    $dealer = reset($dealers);
    $dealer->guid = rtrim(get_permalink($dealer->ID), '/');
    print '<li><a href="' . $dealer->guid . '" title="' . $key . ', ' . $state . '. ' . $count .' dealer">' . $key . ', '
    . $state . '</a></li>';
    continue;
  }
  print '<li><a href="/'.$pagename.'/'.$country.'/'.$state.'/'.urlencode($key).'" title="' . $key . ', ' . $state . '. ' . $count .' dealers">'.$key.', '.$state.'</a></li>';
}
?>
  </ul>
</div>

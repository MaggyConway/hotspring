<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
$wpdp = $GLOBALS['wpdp'];
$pagename = sanitize_text_field(get_query_var( 'pagename', NULL ));

?>
<div class="container">
<div class="row">
<div class="country-container col-xs-12">
<div class="dp-country">
  <ul>
<?php
ksort($result);
foreach ($result as $state => $value) {

  print '<li><a href="/'.$pagename.'/'.$country.'/'.$state.'">' . $wpdp->get->name_of_state($country, $state).'</a></li>';

}
?>
  </ul>
  </div>
</div>
</div>
</div>

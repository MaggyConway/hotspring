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

<!--   <ul class="breads">
  <li class="crumbs"><a href="/">Home</a>
    <span class="caret">&gt;</span>
  </li>
  <li class="crumbs"><a href="<?php print '/'.$pagename.'/';?>">Dealers</a><?php if(!empty($state)){ ?><span class="caret">&gt;</span><?php } ?></li>
  <?php if(!empty($state)){ ?>
  <li class="crumbs"><a href="<?php print '/'.$pagename.'/'.$country.'/'.$state.'/';?>"><?php print $state_name;?></a><?php if(!empty($city)){ ?><span class="caret">&gt;</span><?php } ?></li>
  <?php if(!empty($city)){ ?>
  <li class="lastCrumb"><?php print $city;?></li>
  <?php }} ?>
</ul> -->
</div>

<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
$wpdp = $GLOBALS['wpdp'];
$pagename = sanitize_text_field(get_query_var( 'pagename', NULL ));

?>

      <div class="dp-country" id="<?php echo $country; ?>">
        <ul>
          <?php
          foreach ($result as $state => $value) {
            print '<li><a href="/'.$pagename.'/'.$country.'/'.$state.'">' . $wpdp->get->name_of_state($country, $state).'</a></li>';
          }
          ?>
        </ul>
      </div>


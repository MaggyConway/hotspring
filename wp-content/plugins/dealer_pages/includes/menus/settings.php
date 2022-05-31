<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

$active_tab = 'base-options';

switch ($_GET['tab']) {
  case 'base-options':
    $active_tab = 'base-options';
    break;
  case 'main-metatag':
    $active_tab = 'main-metatag';
    break;
  case 'state-metatag':
    $active_tab = 'state-metatag';
    break;
  case 'city-metatag':
    $active_tab = 'city-metatag';
    break;

  default:
    $active_tab = 'base-options';
    break;
}


?>
<div class='wrap'>
  <div id='icon-options-general' class='icon32'></div>
  <h2 class="nav-tab-wrapper">
    <a href="?page=dealer-page-settings&tab=base-options" class="nav-tab <?php if($active_tab == 'base-options'){echo 'nav-tab-active';} ?> ">Base options</a>
    <a href="?page=dealer-page-settings&tab=main-metatag" class="nav-tab <?php if($active_tab == 'main-metatag'){echo 'nav-tab-active';} ?>">Main metatag</a>
    <a href="?page=dealer-page-settings&tab=state-metatag" class="nav-tab <?php if($active_tab == 'state-metatag'){echo 'nav-tab-active';} ?>">State metatag</a>
    <a href="?page=dealer-page-settings&tab=city-metatag" class="nav-tab <?php if($active_tab == 'city-metatag'){echo 'nav-tab-active';} ?>">City metatag</a>
  </h2>

  <form method='post' action='options.php'>
  <?php

  switch ($active_tab) {
    case 'base-options':
      do_settings_sections('dealer-page-settings');
      settings_fields('dealer_page_settings_section');
      break;
    case 'main-metatag':
      do_settings_sections('dealer-page-settings-main');
      settings_fields('dealer_page_settings_section_main_metatag');
      break;
    case 'state-metatag':
      do_settings_sections('dealer-page-settings-state');
      settings_fields('dealer_page_settings_section_state_metatag');
      break;
    case 'city-metatag':
      do_settings_sections('dealer-page-settings-city');
      settings_fields('dealer_page_settings_section_city_metatag');
      break;
    default:
      do_settings_sections('dealer-page-settings');
      settings_fields('dealer_page_settings_section');
      break;
  }
  submit_button();
  ?>
  </form>
</div>

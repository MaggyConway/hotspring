<?php

/**
 * WPDZ Install
 *
 */
if(!defined('ABSPATH'))
  exit;

/**
 * Run the install.
 *
 * @since 1.2.20
 * @return void
 */
function wpdz_install($network_wide) {
  // Create the default settings.
  wpdz_set_default_settings();
}

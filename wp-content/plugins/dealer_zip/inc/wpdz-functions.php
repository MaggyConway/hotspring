<?php

/**
 * Get the default plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function wpdz_get_default_settings() {
  $default_settings = array(
    'salesforce_brand' => 'CS', //Caldera
    'salesforce_country' => 'US', //'US' => 'USA','CA' => 'CANADA',
    'salesforce_key' => '', //see ctr file
    'salesforce_iss' => '3MVG9iTxZANhwHQvcjnT2sSEJPtHuZb_HC3RMz1TQBtA74IwWKu3edf60ZS8v1Kn9F3zSq0iLGZWLlnIYaP9f',
    'salesforce_aud' => 'https://login.salesforce.com',
    'salesforce_prn' => 'wedlapi@watkinsmfg.com',
    'salesforce_endpoint' => 'https://login.salesforce.com/services/oauth2/token',
    'salesforce_dealer' => 'https://masco.my.salesforce.com/services/apexrest/watkins/edl_service'
  );
  return $default_settings;
}

/**
 * Get the current plugin settings.
 *
 * @since 1.0.0
 * @return array $setting The current plugin settings
 */
function wpdz_get_settings() {

  $settings = get_option('wpdz_settings');

  if(!$settings) {
    update_option('wpdz_settings', wpdz_get_default_settings());
    $settings = wpdz_get_default_settings();
  }

  return $settings;
}

/**
 * Get a single value from the default settings.
 *
 * @since 1.0.0
 * @param  string $setting               The value that should be restored
 * @return string $wpdz_default_settings The default setting value
 */
function wpdz_get_setting($setting) {

  $settings = get_option('wpdz_settings');

  if(!$settings) {
    update_option('wpdz_settings', wpdz_get_default_settings());
    $settings = wpdz_get_default_settings();
  }

  return $settings[$setting];
}

/**
 * Set the default plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function wpdz_set_default_settings() {

  $settings = get_option('wpdz_settings');

  if(!$settings) {
    update_option('wpdz_settings', wpdz_get_default_settings());
  }
}

/**
 * Return a list of the store templates.
 *
 * @since 1.2.20
 * @return array $templates The list of default store templates
 */
function wpdz_get_templates() {

}

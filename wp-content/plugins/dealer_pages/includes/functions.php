<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

function dp_list_of_state() {
  return array(
    'USA'    => array(
      'AL' => 'Alabama',
      'AK' => 'Alaska',
      'AS' => 'American Samoa',
      'AZ' => 'Arizona',
      'AR' => 'Arkansas',
      'CA' => 'California',
      'CO' => 'Colorado',
      'CT' => 'Connecticut',
      'DE' => 'Delaware',
      'DC' => 'District Of Columbia',
      'FM' => 'Federated States Of Micronesia',
      'FL' => 'Florida',
      'GA' => 'Georgia',
      'GU' => 'Guam',
      'HI' => 'Hawaii',
      'ID' => 'Idaho',
      'IL' => 'Illinois',
      'IN' => 'Indiana',
      'IA' => 'Iowa',
      'KS' => 'Kansas',
      'KY' => 'Kentucky',
      'LA' => 'Louisiana',
      'ME' => 'Maine',
      'MH' => 'Marshall Islands',
      'MD' => 'Maryland',
      'MA' => 'Massachusetts',
      'MI' => 'Michigan',
      'MN' => 'Minnesota',
      'MS' => 'Mississippi',
      'MO' => 'Missouri',
      'MT' => 'Montana',
      'NE' => 'Nebraska',
      'NV' => 'Nevada',
      'NH' => 'New Hampshire',
      'NJ' => 'New Jersey',
      'NM' => 'New Mexico',
      'NY' => 'New York',
      'NC' => 'North Carolina',
      'ND' => 'North Dakota',
      'MP' => 'Northern Mariana Islands',
      'OH' => 'Ohio',
      'OK' => 'Oklahoma',
      'OR' => 'Oregon',
      'PW' => 'Palau',
      'PA' => 'Pennsylvania',
      'PR' => 'Puerto Rico',
      'RI' => 'Rhode Island',
      'SC' => 'South Carolina',
      'SD' => 'South Dakota',
      'TN' => 'Tennessee',
      'TX' => 'Texas',
      'UT' => 'Utah',
      'VT' => 'Vermont',
      'VI' => 'Virgin Islands',
      'VA' => 'Virginia',
      'WA' => 'Washington',
      'WV' => 'West Virginia',
      'WI' => 'Wisconsin',
      'WY' => 'Wyoming',
    ),
    'Canada' => array(
      'AB' => 'Alberta',
      'BC' => 'British Columbia',
      'LB' => 'Labrador',
      'MB' => 'Manitoba',
      'NB' => 'New Brunswick',
      'NF' => 'Newfoundland',
      'NS' => 'Nova Scotia',
      'NU' => 'Nunavut',
      'NW' => 'North West Terr.',
      'ON' => 'Ontario',
      'PE' => 'Prince Edward Is.',
      'QC' => 'Quebec',
      'SK' => 'Saskatchewen',
      'YU' => 'Yukon',
    ),
  );
}
function dp_list_of_lowerstate() {
  $all_state = array();
  foreach ( dp_list_of_state() as $country_name => $states ) {
    foreach ( $states as $state_code => $state_name ) {
      $all_state[ $country_name ][ $state_code ] = strtolower( $state_name );
    }
  }
  return $all_state;
}

function db_code_of_state( $state_name ) {
  $countries            = dp_list_of_lowerstate();
  $countries_capitalize = dp_list_of_state();
  $state_lower          = str_replace( '-', ' ', strtolower( trim( $state_name ) ) );
  foreach ( $countries as $country_name => $states ) {
    $state_key = array_search( $state_lower, $states );
    if ( ! empty( $state_key ) && ! empty( $states[ $state_key ] ) ) {
      return array(
        'country_name'    => $country_name,
        'state_code'      => $state_key,
        'state_name'      => $states[ $state_key ],
        'capitalize_name' => $countries_capitalize[ $country_name ][ $state_key ],
      );
    }
  }
  return null;
}

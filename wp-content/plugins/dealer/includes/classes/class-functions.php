<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
//
// class class_dp_functions{
//
// 	public function __construct() {
// 		//add_action('add_meta_boxes', array($this, 'meta_boxes_question'));
// 		//add_action('save_post', array($this, 'meta_boxes_question_save'));
// 	}
//
// 	public function jsonld($properties){
// 		return $properties ? '<script type="application/ld+json">' . json_encode($properties) . '</script>' : '';
// 	}
//
//   //get dealers by state
//   public function dealers_by_state(){
//     $query = new WP_Query(array('post_type' => 'edls','posts_per_page' => -1,));
//     $result = array();
//     foreach ( $query->posts as $key => $post ) {
//       $country = get_post_meta( $post->ID, 'dealership_country', true );
//       $state = get_post_meta( $post->ID, 'dealership_state', true );
//       $state_code = get_post_meta( $post->ID, 'dealership_state_code', true );
//       $city = get_post_meta( $post->ID, 'dealership_city', true );
//       if(!isset($result[$country][$state_code])){
//         $result[$country][$state_code] = array(
//           'state' => $state,
//           'city' => array(),
//         );
//       }
//       $result[$country][$state_code]['city'][$city] = $post->ID;
//     }
//     return $result;
//   }
//
//   /**
//    * [dealers_by_csc description]
//    * @param  [type] $city    [description]
//    * @param  [type] $state   [description]
//    * @param  [type] $country [description]
//    * @return [type]          [description]
//    */
//   public function dealers_by_csc($city, $state, $country){
//
//     $args = array(
//       'post_type' => 'edls',
//       'meta_query' => array(
//         'relation' => 'AND',
//         array(
//           'key' => 'dealership_country',
//           'value' => $country
//         ),
//         array(
//           'key' => 'dealership_state_code',
//           'value' => $state
//         ),
//         array(
//           'key' => 'dealership_city',
//           'value' => $city
//         )
//       )
//     );
//     $query = new WP_Query($args);
//     // print_r($query);
//     return $query->posts;
//   }
//
//   public function list_of_state() {
//     return array(
//       'USA' => array(
//         "AL" => "Alabama",
//         "AK" => "Alaska",
//         "AS" => "American Samoa",
//         "AZ" => "Arizona",
//         "AR" => "Arkansas",
//         "CA" => "California",
//         "CO" => "Colorado",
//         "CT" => "Connecticut",
//         "DE" => "Delaware",
//         "DC" => "District Of Columbia",
//         "FM" => "Federated States Of Micronesia",
//         "FL" => "Florida",
//         "GA" => "Georgia",
//         "GU" => "Guam",
//         "HI" => "Hawaii",
//         "ID" => "Idaho",
//         "IL" => "Illinois",
//         "IN" => "Indiana",
//         "IA" => "Iowa",
//         "KS" => "Kansas",
//         "KY" => "Kentucky",
//         "LA" => "Louisiana",
//         "ME" => "Maine",
//         "MH" => "Marshall Islands",
//         "MD" => "Maryland",
//         "MA" => "Massachusetts",
//         "MI" => "Michigan",
//         "MN" => "Minnesota",
//         "MS" => "Mississippi",
//         "MO" => "Missouri",
//         "MT" => "Montana",
//         "NE" => "Nebraska",
//         "NV" => "Nevada",
//         "NH" => "New Hampshire",
//         "NJ" => "New Jersey",
//         "NM" => "New Mexico",
//         "NY" => "New York",
//         "NC" => "North Carolina",
//         "ND" => "North Dakota",
//         "MP" => "Northern Mariana Islands",
//         "OH" => "Ohio",
//         "OK" => "Oklahoma",
//         "OR" => "Oregon",
//         "PW" => "Palau",
//         "PA" => "Pennsylvania",
//         "PR" => "Puerto Rico",
//         "RI" => "Rhode Island",
//         "SC" => "South Carolina",
//         "SD" => "South Dakota",
//         "TN" => "Tennessee",
//         "TX" => "Texas",
//         "UT" => "Utah",
//         "VT" => "Vermont",
//         "VI" => "Virgin Islands",
//         "VA" => "Virginia",
//         "WA" => "Washington",
//         "WV" => "West Virginia",
//         "WI" => "Wisconsin",
//         "WY" => "Wyoming"
//       ),
//       'Canada' => array(
//         "AB"=>"Alberta",
//         "BC"=>"British Columbia",
//         "LB"=>"Labrador",
//         "MB"=>"Manitoba",
//         "NB"=>"New Brunswick",
//         "NF"=>"Newfoundland",
//         "NS"=>"Nova Scotia",
//         "NU"=>"Nunavut",
//         "NW"=>"North West Terr.",
//         "ON"=>"Ontario",
//         "PE"=>"Prince Edward Is.",
//         "QC"=>"Quebec",
//         "SK"=>"Saskatchewen",
//         "YU"=>"Yukon",
//       )
//     );
//
// 	}
//
// 	public function name_of_state($country,$code) {
//     $states = $this->list_of_state();
//
//     return $states[$country][$code];
//   	return isset($states[$country][$code])? $states[$country][$code]: 'Unknown';
// 	}
//
// 	public function get_pages_list() {
// 		$array_pages[''] = __('None', DP_TEXTDOMAIN);
//
// 		foreach( get_pages() as $page )
// 		if ( $page->post_title ) $array_pages[$page->ID] = $page->post_title;
//
// 		return $array_pages;
// 	}
// }
//
// add_filter( 'disable_wpseo_json_ld_search', '__return_true' );
//
// add_filter('wpseo_json_ld_output', 'bybe_remove_yoast_json', 10, 1);
// function bybe_remove_yoast_json($data){
//   $data = array();
//   return $data;
// }

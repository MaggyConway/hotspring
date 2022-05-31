<?php
//
// if ( ! defined('ABSPATH')) exit;  // if direct access
//
// class class_dp_actions{
//
//   public function __construct(){
//     add_action('dp_header', array( $this, 'dp_header' ));
//     add_action('dp_main', array( $this, 'dp_main' ));
//     add_action('dp_main_state', array( $this, 'dp_main_state'), 10,2);
//     add_action('dp_main_city', array( $this, 'dp_main_city'), 10,3);
//     add_action('dp_footer', array( $this, 'dp_footer' ));
//     add_action('dp_country_header', array( $this, 'dp_country_header'),10,1);
//     add_action('dp_city_map', array( $this, 'dp_city_map'),10,1);
//
//     add_action('dp_country_content', array( $this, 'dp_country_content'),10,2);
//     add_action('dp_state_header', array( $this, 'dp_state_header'),10,1);
//     add_action('dp_state_content', array( $this, 'dp_state_content'),10,3);
//     add_action('dp_city_header', array( $this, 'dp_city_header'),10,2);
//     add_action('dp_city_content', array( $this, 'dp_city_content'),10,4);
//
//     add_action( 'wp_head',  array( $this, 'add_meta_tags' ), 10 );
//     add_filter( 'wpseo_title', array( $this, 'generate_custom_title' ), 15 );
//     add_filter( 'wpseo_metadesc', array( $this, 'generate_custom_description' ), 10, 1 );
//
//   }
//
//   public function generate_custom_description($desc) {
//     if(is_object($GLOBALS['post']) && $GLOBALS['post']->post_name == 'find-dealer'){
//       $wpdp = $GLOBALS['wpdp'];
//       $state = sanitize_text_field(get_query_var( 'state', NULL ));
//       $city = sanitize_text_field(urldecode(get_query_var( 'city', NULL )));
//       $country = sanitize_text_field(get_query_var( 'country', NULL ));
//       $output = '';
//       $values = array(
//         'state' => $wpdp->get->name_of_state($country, $state),
//         'state-code' => $state,
//         'city' => $city,
//         'country' => $country,
//       );
//
//       if(empty($state) && empty($city)){
//         $output = get_option('dealer_page_main_metatag_description');
//       }
//       if(!empty($state) && empty($city)) {
//         $output = get_option('dealer_page_state_metatag_description');
//       }
//       if(!empty($state) && !empty($city)) {
//         $output = get_option('dealer_page_city_metatag_description');
//       }
//
//       foreach ($values as $key => $value) {
//           $tagToReplace = "[$key]";
//           $output = str_replace($tagToReplace, $value, $output);
//       }
//       if(!empty($output)){
//         return $output;
//       }
//     }
//     return;
//   }
//   public function generate_custom_title($title) {
//     if($GLOBALS['post']->post_name == 'find-dealer'){
//       $wpdp = $GLOBALS['wpdp'];
//       $state = sanitize_text_field(get_query_var( 'state', NULL ));
//       $city = sanitize_text_field(urldecode(get_query_var( 'city', NULL )));
//       $country = sanitize_text_field(get_query_var( 'country', NULL ));
//       $output = '';
//       $values = array(
//         'state' => $wpdp->get->name_of_state($country, $state),
//         'state-code' => $state,
//         'city' => $city,
//         'country' => $country,
//       );
//
//       if(empty($state) && empty($city)){
//         $output = get_option('dealer_page_main_metatag_title');
//       }
//       if(!empty($state) && empty($city)) {
//         $output = get_option('dealer_page_state_metatag_title');
//       }
//       if(!empty($state) && !empty($city)) {
//         $output = get_option('dealer_page_city_metatag_title');
//       }
//
//       foreach ($values as $key => $value) {
//           $tagToReplace = "[$key]";
//           $output = str_replace($tagToReplace, $value, $output);
//       }
//       if(!empty($output)){
//         return $output;
//       }
//     }
//     return $title;
//   }
//
//   public function add_meta_tags() {
//     if(isset($GLOBALS['post']) && $GLOBALS['post']->post_name != 'find-dealer'){
//       return;
//     }
//     $wpdp = $GLOBALS['wpdp'];
//     $state = sanitize_text_field(get_query_var( 'state', NULL ));
//     $city = sanitize_text_field(urldecode(get_query_var( 'city', NULL )));
//     $country = sanitize_text_field(get_query_var( 'country', NULL ));
//
//     $output = '';
//     $values = array(
//       'state' => '',
//       'state-code' => '',
//       'city' => '',
//       'country' => $country,
//     );
//
//     if(empty($state) && empty($city)){
//       $output = get_option('dealer_page_main_metatags') . "\n";
//     }
//     if(!empty($state) && empty($city)) {
//       $values['state'] = $wpdp->get->name_of_state($country, $state);
//       $values['state_code'] = $state;
//       $values['country'] = $country;
//       $output = get_option('dealer_page_state_metatags') . "\n";
//     }
//     if(!empty($state) && !empty($city)) {
//       $values['state'] = $wpdp->get->name_of_state($country, $state);
//       $values['state_code'] = $wpdp->get->name_of_state($country, $state);
//       $values['country'] = $country;
//       $values['city'] = $city;
//       $output = get_option('dealer_page_city_metatags') . "\n";
//     }
//     foreach ($values as $key => $value) {
//         $tagToReplace = "[$key]";
//         $output = str_replace($tagToReplace, $value, $output);
//     }
//     print $output;
//   }
//
//   public function dp_city_map($result) {
//     require_once( DP_PLUGIN_DIR . 'templates/dealers-pages/map.php');
//   }
//   public function dp_header() {
//     require_once( DP_PLUGIN_DIR . 'templates/dealers-pages/header.php');
//   }
//   public function dp_main() {
//     require_once( DP_PLUGIN_DIR . 'templates/dealers-pages/main.php');
//   }
//   public function dp_main_state($state,$country) {
//     require_once( DP_PLUGIN_DIR . 'templates/dealers-pages/main_state.php');
//   }
//   public function dp_main_city($city,$state,$country) {
//     require_once( DP_PLUGIN_DIR . 'templates/dealers-pages/main_city.php');
//   }
//   public function dp_footer() {
//     require_once( DP_PLUGIN_DIR . 'templates/dealers-pages/footer.php');
//   }
//   public function dp_country_header($name) {
//     if($name == 'USA'){
//       $name = 'the United States';
//     }
//
//     if($name == 'Canada'){
//       print '<div class="country-header" id="canada"><h2 class="pt-2">Hot Tub Dealers in '.$name.'</h2></div>';
//     } else {
//       print '<div class="country-header" id="usa"><h2 class="pt-2">Hot Tub Dealers in '.$name.'</h2></div>';
//     }
//   }
//   public function dp_country_content($result, $country) {
//     require( DP_PLUGIN_DIR . 'templates/dealers-pages/country.php');
//   }
//   public function dp_state_header($state) {
//     print '
//     <div class="state-header">
//         <h1 class="pt-2">Hot Tub Dealers in '.$state.'</h1>
//     </div>
//   ';
//   }
//   public function dp_state_content($result,$state,$country) {
//     require( DP_PLUGIN_DIR . 'templates/dealers-pages/state.php');
//   }
//   public function dp_city_header($city,$result) {
//     $wpdp = $GLOBALS['wpdp'];
//     $state_code = sanitize_text_field(get_query_var( 'state', NULL ));
//     $city = sanitize_text_field(urldecode(get_query_var( 'city', NULL )));
//     $country = sanitize_text_field(get_query_var( 'country', NULL ));
//     $state = $wpdp->get->name_of_state($country, $state_code);
//     print '
//     <div class="city-header text-xs-center">
//         <h2 class="pt-2">Hot Tub Dealers in '.$city.', '.$state.'</h2>
//         <div class="panel-pane pane-custom pane-1">
//           <div class="content">
//             '.get_option('dealer_page_header').'
//           </div>
//         </div>
//     </div>
//   ';
//   }
//   public function dp_city_content($result,$state,$country) {
//     require( DP_PLUGIN_DIR . 'templates/dealers-pages/city.php');
//   }
//
// }
//
// new class_dp_actions();

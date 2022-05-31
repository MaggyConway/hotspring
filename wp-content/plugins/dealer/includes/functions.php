<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

function get_state_list($code = NULL){
	$values = array(
		"AL" => "Alabama",
		"AK" => "Alaska",
		"AS" => "American Samoa",
		"AZ" => "Arizona",
		"AR" => "Arkansas",
		"CA" => "California",
		"CO" => "Colorado",
		"CT" => "Connecticut",
		"DE" => "Delaware",
		"DC" => "District Of Columbia",
		"FM" => "Federated States Of Micronesia",
		"FL" => "Florida",
		"GA" => "Georgia",
		"GU" => "Guam",
		"HI" => "Hawaii",
		"ID" => "Idaho",
		"IL" => "Illinois",
		"IN" => "Indiana",
		"IA" => "Iowa",
		"KS" => "Kansas",
		"KY" => "Kentucky",
		"LA" => "Louisiana",
		"ME" => "Maine",
		"MH" => "Marshall Islands",
		"MD" => "Maryland",
		"MA" => "Massachusetts",
		"MI" => "Michigan",
		"MN" => "Minnesota",
		"MS" => "Mississippi",
		"MO" => "Missouri",
		"MT" => "Montana",
		"NE" => "Nebraska",
		"NV" => "Nevada",
		"NH" => "New Hampshire",
		"NJ" => "New Jersey",
		"NM" => "New Mexico",
		"NY" => "New York",
		"NC" => "North Carolina",
		"ND" => "North Dakota",
		"MP" => "Northern Mariana Islands",
		"OH" => "Ohio",
		"OK" => "Oklahoma",
		"OR" => "Oregon",
		"PW" => "Palau",
		"PA" => "Pennsylvania",
		"PR" => "Puerto Rico",
		"RI" => "Rhode Island",
		"SC" => "South Carolina",
		"SD" => "South Dakota",
		"TN" => "Tennessee",
		"TX" => "Texas",
		"UT" => "Utah",
		"VT" => "Vermont",
		"VI" => "Virgin Islands",
		"VA" => "Virginia",
		"WA" => "Washington",
		"WV" => "West Virginia",
		"WI" => "Wisconsin",
		"WY" => "Wyoming"
	);
  return ($code) ? $values : $values[$code] ;
}

/**
 * Lookup address on google geo coding services
 *
 * @param  (string) $address
 *
 * @return (array) array of lat, lon
 */
function dealer_lookup_address( $address ) {
	$address     = str_replace( " ", "+", urlencode( $address ) );
	$key         = get_option( 'dealer_gma_key_ip' );
	$details_url = "https://maps.googleapis.com/maps/api/geocode/json?key=" . $key . "&address=" . $address . "&language=en&sensor=false";

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $details_url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$rq_result = curl_exec( $ch );
	$response  = json_decode( $rq_result, true );

	// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
	if ( $response['status'] != 'OK' ) {
		return null;
	}
	$geometry = $response['results'][0]['geometry'];
	$result   = array(
		'address' => $response['results'][0]['formatted_address'],
		'lat'     => $geometry['location']['lat'],
		'lng'     => $geometry['location']['lng'],
	);
	//write_log( $address, 'dealer_lookup_address' );

	return $result;
}

/**
 * get dealer post by dealership_id field value
 * @param  string $DealershipId
 * @return [type]               [description]
 */
function _dealer_get_posts_by_dealership_id($DealershipId){
  $args = array(
	'post_type' => 'edls',
	'meta_query' => array(
    array(
      'key' => 'dealership_id',
      'value' => $DealershipId,
    )),
  );
  $query = new WP_Query($args);
  return $query->posts;
}

/**
 * [_dealer_check_posts_by_dealership_id description]
 * @param  [type] $posts        [description]
 * @param  [type] $DealershipId [description]
 * @return [type]               [description]
 */
function _dealer_check_posts_by_dealership_id($posts, $DealershipId){
  if(isset($posts[0]->ID)){
    return $posts;
  }

  $args = array(
	'post_type' => 'edls',
  'post_status' => array('trash'),
	'meta_query' => array(
    array(
      'key' => 'dealership_id',
      'value' => $DealershipId,
    )),
  );

  $query = new WP_Query($args);
  return $query->posts;
}

/**
 * [_get_dealers_list description]
 * @return [type] [description]
 */
function _get_dealers_array(){
  $array = _get_dealers_list();
  $posts_array = array();
  foreach ($array as $post) {
    $dealership_id = get_post_meta( $post->ID, 'dealership_id', true );
    $posts_array[] = (string) $dealership_id;
  }
  return $posts_array;
}

/**
 * [_get_dealers_list description]
 * @return [type] [description]
 */
function _get_dealers_list(){
  $post_type_query  = new WP_Query(
    array (
        'post_type'      => 'edls',
        'posts_per_page' => -1
    )
  );
  // we need the array of posts
  $posts_array = $post_type_query->posts;
  return $posts_array;
}

/**
 * get dealer post by dealership_id field value
 * @param  int $DealershipId [description]
 * @return user obj               [description]
 */
function _dealer_get_user_by_dealership_id($DealershipId){
  $args = array(
    'meta_query' => array(
      array(
        'key'     => 'dealer_reference',
        'value'   => $DealershipId,
        'compare' => 'LIKE'
      )
    )
  );
  $query = new WP_User_Query( $args );
  return $query->results;
}

/**
 * Add dealership_id to field of user
 * @param int $user_id
 * @param string $dealership_id
 */
function _dealer_add_dealer_reference_to_user($user_id, $dealership_id){
  $old_data = get_user_meta( $user_id, 'dealer_reference', true );
  if(empty($old_data)){
    $data = array();
  }else{
    $data = explode (',' , $old_data);
    $data = array_map(trim, $data);
  }
  if(!in_array($dealership_id , $data)){
    array_push($data, $dealership_id);
    update_user_meta($user_id, 'dealer_reference', implode(', ',$data));
    $old_data = get_user_meta( $user_id, 'dealer_reference', true );
  }
}

/**
 * Add dealership_id to field of user
 * @param int $user_id
 * @param string $rsm_id
 */
function _dealer_add_rsm_reference_to_user($user_id, $rsm_id){
  $old_data = get_user_meta( $user_id, 'rsm_reference', true );
  if(empty($old_data)){
    $data = array();
  }else{
    $data = explode (',' , $old_data);
    $data = array_map(trim, $data);
  }
  if(!in_array($rsm_id , $data)){
    array_push($data, $rsm_id);
    update_user_meta($user_id, 'rsm_reference', implode(', ',$data));
    $old_data = get_user_meta( $user_id, 'rsm_reference', true );
  }
}

/**
 * Add dealership_id to field of user
 * @param int $user_id
 * @param string $dealership_id
 */
function _dealer_check_dealer_reference_at_user($user_id, $dealership_id, $dealership_billto){
  $old_data = get_user_meta( $user_id, 'dealer_reference', true );
  if(empty($old_data)){
    $data = array();
  }else{
    $data = explode (',' , $old_data);
    $data = array_map('trim', $data);
  }
  if (in_array($dealership_id , $data) || in_array($dealership_billto, $data)) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Add rsm_id to field of user
 * @param int $user_id
 * @param string $dealership_id
 */
function _dealer_check_rsm_reference_at_user($user_id, $rsm_id){
  $old_data = get_user_meta( $user_id, 'rsm_reference', true );
  if(empty($old_data)){
    $data = array();
  }else{
    $data = explode (',' , $old_data);
    $data = array_map(trim, $data);
  }
  if(in_array($rsm_id , $data)){
    return TRUE;
  }
  return FALSE;
}

/**
 * [_dealer_update_dealership_coordinates description]
 * @param  (int) $post_id    Post id
 * @param  (obj) $dealership dealership object
 * @return
 */
function _dealer_update_dealership_coordinates($post_id, $dealership){
  $address = dealer_lookup_address($dealership->Address1 . ', ' . $dealership->Zip);
  update_post_meta($post_id, 'dealership_coordinates', $address);
}
function put_log_contents( $file, $contents, $mode = false ) {
  $fp = @fopen( $file, 'a' );
  if ( ! $fp ) {
      return false;
  }

  mbstring_binary_safe_encoding();
  $data_length = strlen( $contents );
  $bytes_written = fwrite( $fp, $contents );
  reset_mbstring_encoding();
  fclose( $fp );

  if ( $data_length !== $bytes_written ) {
    return false;
  }

  chmod( $file, $mode );
  return true;
}
function toLog($log, $info = null) {
  $upload_dir = wp_upload_dir();
  $dir = trailingslashit( $upload_dir['basedir'] ) . 'dealer/';
  // $sub = date("Y-m-d");
  $subdir = $dir . "/" . date("Y-m-d") . "/";
  // Create main folder within upload if not exist
  if( !is_dir($subdir) ) {
    mkdir($subdir, 0777, true);
  }
  if(!empty($info)){
    $log = $info . ': ' . $log . "\n";
  }
  put_log_contents( $subdir . 'log.log', $log, 0644 );
}

function add_js() {

    wp_register_script( 'map_init', WPD_PLUGIN_URL . 'includes/assets/js/map.js', [ 'jquery' ] );
    wp_register_script( 'main_map', 'https://maps.googleapis.com/maps/api/js?key=' . get_option('dealer_gma_key_web'), [ 'jquery' ] );
}

add_action( 'wp_enqueue_scripts', 'add_js' );
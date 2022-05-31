<?php
if ( ! defined('ABSPATH')) exit;  // if direct access
/**
 * Create dealer coupon
 * @param  [object] $term      compagin
 * @param  [object] $term_meta compagin meta
 * @param  [int] $dealer_id dealership_id
 * @return [boolean]            [description]
 */
function dc_create_coupon_dealer($term, $term_meta, $dealer_id, $coupon_headline = NULL, $coupon_text = NULL, $is_active = NULL) {
  $is_coupon_active = NULL;

  if (isset($is_active)) {
    $is_coupon_active = empty($is_active) ? 0 : 1;
  }
  else {
    $is_coupon_active = $term_meta['dcc_active'][0];
  }
  $dealer_id = (string) str_pad( $dealer_id, 5, '0', STR_PAD_LEFT );
  $posts = _dealer_get_posts_by_dealership_id($dealer_id);
  if(empty($posts[0])){
    return FALSE;
  }
  if(isset($posts[0]->ID)){
    $post_id = $posts[0]->ID;
  }
  if( _check_dealer_coupons( $dealer_id, $term->term_id ) ){
    return;
  }
  $coupon_post = array(
    'ID' => 0,
    'post_title'    => wp_strip_all_tags($term->name . ' for ' . $posts[0]->post_title),
    'post_content'  => '',
    'post_status'   => 'publish',
    'post_category' => array($term->term_id),
    //'post_author'  => $user_id,
    'post_type' => 'dc_coupon',
    'meta_input'   => array(
      'dc_campaign' => $term->term_id,
      'dc_dealer' => $posts[0]->ID,
      'dcc_default_headline' => $coupon_headline,
      'dcc_coupon_text' => $coupon_text,
      'dcc_legal_text' => '',
      // 'dcc_website' => $term_meta['dcc_website'][0],
      'dcc_coupon_preview_image' => null,
      'dcc_coupon_print_image' => null,
      'dcc_active' => $is_coupon_active,
      'dcc_weight' => $term_meta['dcc_weight'][0],
    ),
  );
  // Insert the post into the database
  $post_id = wp_insert_post($coupon_post);
  if(!empty($post_id)&&!is_object($post_id)){
    wp_set_post_terms( $post_id, array($term->term_id), 'dc_campaign', false );
  }
  return TRUE;
}
/**
 * return array of terms object
 * @return [array] [description]
 */
function _dc_get_all_compaign(){
  $terms  = get_terms( array(
    'taxonomy' => 'dc_campaign',
    'hide_empty' => false,
  ));
  return $terms;
}
/**
 * retern array of compagin list
 * @return [array] [description]
 */
function _dc_campaign_list(){
  $list = array();
  $terms = _dc_get_all_compaign();
  foreach ($terms as $key => $term) {
    $list[$term->term_id]=$term->name;
  }
  return $list;
}
/**
 * return all dealer cupons
 * @param  [int] $dealer_id ID of dealer post
 * @return [array]            [description]
 */
function _get_all_dealer_coupons($dealer_id){
  $args = array(
  'post_type' => 'dc_coupon',
  'posts_per_page' => '-1',
  'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future',),
  'meta_query' => array(
    array(
      'key' => 'dc_dealer',
      'value' => $dealer_id,
    )),
  );
  $query = new WP_Query($args);
  return $query->posts;
}

function _check_dealer_coupons($dealer_id, $compaign_id){
  $coupons = _get_all_dealer_coupons($dealer_id);
  foreach ($coupons as $key => $coupon) {
    $coupon_campaign_id = get_post_meta( $coupon->ID, 'dc_campaign', TRUE );
    if( $coupon_campaign_id == $compaign_id ){
      return TRUE;
    }
  }
  return FALSE;
}

function _dc_get_default_campaign(){
  $terms = _dc_get_all_compaign();
  $deafult = $terms[0];
  $deafult->weight = get_term_meta( $deafult->term_id, 'dcc_weight', TRUE );
  foreach ($terms as $key => $term) {
    $term->weight = get_term_meta( $term->term_id, 'dcc_weight', TRUE );
    if( $term->weight < $deafult->weight ){
      $deafult = $term;
    }
  }
  return $deafult;
}

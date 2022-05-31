<?php
if ( ! defined('ABSPATH')) exit;  // if direct access
class DealerExcludeFromSearch{

	public function __construct(){
    add_action( 'pre_get_posts', array( $this, 'exclude_posts_from_search'), 999 );
    add_action( 'init', array( $this, 'init' ), 1 );
    add_action( 'wp_head', array( $this, 'add_meta_tags' ) , 1 );
    add_filter( 'wpseo_sitemap_entry', array( $this, 'exclude_posts_from_sitemap' ), 1, 3 );
	}

  public function init() {

	}

	//return ids of all exclude dealers
  public function get_all_exclude_ids() {
    $args = array(
    'post_type' => 'edls',
    'posts_per_page' => 500,
    'meta_query' => array(
      array(
          'key' => 'dealership_exclude',
        'value' => 1,
      )),
    );
    $query = new WP_Query($args);
    $result = array();
    foreach ($query->posts as $key => $value) {
      $result[] = $value->ID;
    }
    return $result;
  }

	// search exclude
  public function exclude_posts_from_search( $query ) {
    if ( ! $query->is_admin && $query->is_search() && $query->is_main_query()) {
      // Get an array of all page IDs with `get_all_page_ids()` function
      $query->set( 'post__not_in', array_merge ( $query->get('post__not_in'),$this->get_all_exclude_ids() ) );
      return $query;
    }
	}
  // add noindex metatag
  public function add_meta_tags() {
    global $post;
    if( in_array($post->ID,$this->get_all_exclude_ids()) ){
      print '<meta name="robots" content="noindex, nofollow"/>';
    }
	}

  //sitemap exclude
  public function exclude_posts_from_sitemap( $url, $type, $post) {
  	if ( ($type == 'post') && in_array($post->ID, $this->get_all_exclude_ids()) ) {
      return NULL;
  	}
  	return $url;
	}

}

new DealerExcludeFromSearch();

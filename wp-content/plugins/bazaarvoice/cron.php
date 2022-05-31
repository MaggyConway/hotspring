<?php
require_once( 'inc/wp-async-request.php' );
require_once( 'inc/wp-background-process.php' );

class WP_Bazaarvoice_Process extends WP_Background_Process {

  protected $action = 'bazaarvoice_background_process';
  protected function task( $data ) {
    $info = $this->getBVInfo( $data['bvid'] );
    update_field( 'bazaarvoice_data', json_encode($info), $data['post_id'] );
    return false;
  }

  protected function complete() {
    parent::complete();
  }

  public function getBVInfo( $bazaarvoice_id ){
    $script_url = 'https://hotspring.ugc.bazaarvoice.com/0526-en_us/' . $bazaarvoice_id . '/reviews.djs?format=embeddedhtml';
		// if ($GLOBALS['_SERVER']['SERVER_NAME'] != 'www.hotspring.com') {
		// 	$script_url = 'https://hotspring.ugc.bazaarvoice.com/bvstaging/0526-en_us/' . $bazaarvoice_id . '/reviews.djs?format=embeddedhtml';
		// }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $script_url);
    // To get first 4000 chars.
    curl_setopt($ch, CURLOPT_RANGE, '0-4000');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);

    $result = [];
    // Parse script.
    preg_match_all('/<img src=\\\\\"(.*?)ratingLarge\.png/m', $content, $matches, PREG_SET_ORDER, 0);
    $result['ratingImageUrl'] = $matches[0][1].'ratingLarge.png';
    preg_match_all('/<span class=\\\\"BVRRNumber\\\\">(.*?)<\\\\\/span>/m', $content, $matches, PREG_SET_ORDER, 0);
    $result['totalReviews'] = $matches[0][1];
    preg_match_all('/<span itemprop=\\\\"ratingValue\\\\" class=\\\\"BVRRNumber BVRRRatingNumber\\\\">(.*?)<\\\\\/span>/m', $content, $matches, PREG_SET_ORDER, 0);
    $result['averageRating'] = $matches[0][1];
    // Check if empty.
    if(empty($result['totalReviews'])){
      $result = ['ratingImageUrl' =>'https://hotspring.ugc.bazaarvoice.com/0526-en_us/0/5/ratingLarge.png', 'totalReviews' => 0, 'averageRating' => 0];
    }
    return $result;
  }

}

class WP_Bazaarvoice_Cron {
  /**
	* Class constructor
	*/
	function __construct() {
    $this->includes();
  }

  public function includes() {
    $this->backgroundProcess = new WP_Bazaarvoice_Process();

    add_filter( 'cron_schedules', [ $this, 'cron_schedules' ] );
		//Schedule an action if it's not already scheduled
		if ( ! wp_next_scheduled( 'bazaarvoice_cron_hook' ) ) {
			wp_schedule_event( time(), 'bazaarvoice_hours', 'bazaarvoice_cron_hook' );
		}
    add_action( 'bazaarvoice_cron_hook', [ $this, 'cron_task' ] );
  }

  public function cron_schedules( $schedules ) {
    $schedules['bazaarvoice_hours'] = array(
      'interval' => 43200, // Every 12 hours.
      'display'  => __( 'bazaarvoice (Every 12 hours)' ),
    );
    return $schedules;
  }

  public function cron_task() {
    $models = get_posts(
      array(
       'numberposts' => -1,
       'post_status' => 'any',
       'post_type' => 'model',
      )
    );
    foreach ($models as $model) {
      $bazaarvoice_id = get_field( 'bazaarvoice_id', $model->ID );
      $this->backgroundProcess->push_to_queue( ['bvid' => $bazaarvoice_id, 'post_id' => $model->ID ] );
    }
    $collections = get_posts(
      array(
       'numberposts' => -1,
       'post_status' => 'any',
       'post_type' => 'collections',
      )
    );
    foreach ($collections as $collection) {
      $bazaarvoice_id = get_field( 'bazaarvoice_id', $collection->ID );
      $this->backgroundProcess->push_to_queue( ['bvid' => $bazaarvoice_id, 'post_id' => $collection->ID ] );
    }

    $this->backgroundProcess->save()->dispatch();
  }

}
new WP_Bazaarvoice_Cron();
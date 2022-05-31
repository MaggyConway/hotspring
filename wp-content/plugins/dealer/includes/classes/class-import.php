<?php

if (class_exists('SoapClient')) {
    require_once( WPD_PLUGIN_DIR . 'includes/assets/WSSoapClient.class.php' );
}
require_once( WPD_PLUGIN_DIR . 'includes/assets/wp-async-request.php' );
require_once( WPD_PLUGIN_DIR . 'includes/assets/wp-background-process.php' );


class DealerAccount{
  public $Username;
  public $Email;
  public $Role;
  public $DealerReference;
}

class Dealership{
  public $DealershipId;
  public $BilltoDealershipId;
  public $DealershipName;
  public $Address1;
  public $Address2;
  public $City;
  public $State;
  public $StateCode;
  public $Country;
  public $CountryCode;
  public $Zip;
  public $TerritoryCode;
  public $RsmId;
  public $Phone;
  public $RecordTypeCode;
}

class DealerBackgroundProcess extends WP_Background_Process {

  /**
   * count of queue
   *
   * @return int
   */
  public function count_queue() {
    $count =0;
    // global $wpdb;

    // $table  = $wpdb->options;
    // $column = 'option_name';

    // if ( is_multisite() ) {
    //   $table  = $wpdb->sitemeta;
    //   $column = 'meta_key';
    // }

    // $key = $this->identifier . '_batch_%';

    // $count = $wpdb->get_var( $wpdb->prepare( "
    // SELECT COUNT(*)
    // FROM {$table}
    // WHERE {$column} LIKE %s
    // ", $key ) );

    return $count;
  }
	/**
	 * @var string
	 */
  protected $action = 'dealer_background_process';

  // check user roles
  protected function isStaff( $user ) {
    if(!isset( $user->roles )){
      return false;
    }

    $dealer_roles = get_option('dealer_roles');
    if(!is_array($dealer_roles)){
      $dealer_roles = array();
    }
    foreach ($dealer_roles as $key => $value) {
      if( in_array( $value, $user->roles ) ) {
        return true;
      }
    }
    return false;
  }

  protected function processAccount( $account ) {
    if(isset($account->Email)){
      if ( $user = get_user_by('email', $account->Email) ){
        $user_id = $user->ID;
      }else{
        $random_password = wp_generate_password(12, false );
        $user_id = wp_create_user($account->Email, $random_password, $account->Email);
      }
      //don't rewrite role of user Daniel LeBlanc
      //8 - Daniel.LeBlanc@watkinsmfg.com *
      //134 - Kevin.Mecum@watkinsmfg.com *
      //739 - Philip.Malavenda@watkinsmfg.com *
      //235 - Shelly.Roberts@watkinsmfg.com *
      //203 - rachel.bolen@watkinsmfg.com *
      //33 - caitlin.woelfel@watkinsmfg.com *
      //40 - Catherine.stacks@watkinsmfg.com *
      //858 - Kevin.Graves@watkinsmfg.com
      if( $this->isStaff($user) || in_array($user_id,array(8,134,739,235,203,33,40,858 ))){
        return false;
      }
      $userdata = get_userdata($user_id);
      $user = new WP_User( $user_id );
      switch ($account->Role) {
        case 'Internal':
          $user->set_role('internal');
          //wp_update_user($userdata);
          break;
        case 'Dealer':
          $user->set_role('dealer');
          //wp_update_user($userdata);
          if(!empty($account->DealerReference)){
            _dealer_add_dealer_reference_to_user($user_id, $account->DealerReference);
          }
          break;
        case 'RSM':
          $user->set_role('rsm');
          //wp_update_user($userdata);
          if(!empty($account->DealerReference)){
            _dealer_add_rsm_reference_to_user($user_id, $account->DealerReference);
          }
          break;
        default:
         # code...
         break;
      }
      //wp_update_user($userdata);

    }
    return false;
  }

  protected function processDealership( $dealership ) {
    $post_id = 0;
    $user_id = get_current_user_id();
    $users = _dealer_get_user_by_dealership_id($dealership->DealershipId);
    if($users[0]->ID){
      $user_id = $users[0]->ID;
    }
    $posts = _dealer_get_posts_by_dealership_id($dealership->DealershipId);
    $posts = _dealer_check_posts_by_dealership_id($posts, $dealership->DealershipId);

    if(isset($posts[0]->ID)){
      $post_id = $posts[0]->ID;
    }
    $old_post = get_post($post_id);
    $coordinates = NULL;
    $old_lock_title = get_post_meta( $post_id , 'locking_edls_title', true );
    $old_lock_address = get_post_meta( $post_id , 'locking_edls_address', true );
    $old_coordinates = get_post_meta( $post_id , 'dealership_coordinates', true );
    $old_address = get_post_meta( $post_id , 'dealership_address_1', true );
    // to check if no old value of old_coordinates. For best performance.
    if(empty($old_coordinates) || $dealership->Address1 !== $old_address ){
      $zip = empty($dealership->Zip) ? '' : ', ' .$dealership->Zip;
      $coordinates = dealer_lookup_address($dealership->Address1 . $zip );
    }else{
      $coordinates = $old_coordinates;
    }

    $exclude = FALSE;
    if($dealership->RecordTypeCode == 'BILLONLY'){
      $exclude = TRUE;
    }
    $meta_input = array(
      'dealership_id' => $dealership->DealershipId,
      'dealership_billto' => $dealership->BilltoDealershipId,
      'dealership_rsm_id' => $dealership->RsmId,
      'dealership_phone' => $dealership->Phone,
      'dealership_record_type' => $dealership->RecordTypeCode,
      'dealership_exclude' => $exclude,
    );

    $title = esc_html( $posts[0]->post_title );
    //check if title not locked
    if(!$old_lock_title){
      $title = $dealership->DealershipName;
      $meta_input['dealership_name'] = $dealership->DealershipName;
    }

    //check if address not locked
    if(!$old_lock_address){
      $meta_input = $meta_input + array(
        'dealership_address_1' => $dealership->Address1,
        'dealership_address_2' => $dealership->Address2,
        'dealership_city' => $dealership->City,
        'dealership_state' => $dealership->State,
        'dealership_state_code' => $dealership->StateCode,
        'dealership_country' => $dealership->Country,
        'dealership_country_code' => $dealership->CountryCode,
        'dealership_zip' => $dealership->Zip,
        'dealership_territory_code' => $dealership->TerritoryCode,
        'dealership_coordinates' => $coordinates,
      );
    }
    // default post status
    $post_status = 'publish';
    // try to use old status
    if( isset($old_post->post_status) ){
      $post_status = $old_post->post_status;
      // restore a dealer from the trash
      if( $old_post->post_status == 'trash' ){
        toLog('ID:' . $old_post->ID . '->ID:' . $post_id ,'Restore a dealer from the trash');
        $post_status = 'publish';
      }
    }

    $dealership_post = array(
      'ID' => $post_id,
      'post_title'    => wp_strip_all_tags($title),
      'post_content'  => '',
      'post_status'   => $post_status,
      'post_category' => array(),
      'post_author'  => $user_id,
      'post_type' => 'edls',
      'meta_input'   => $meta_input,
    );
    if($post_id == 0){
      toLog(wp_strip_all_tags($title),'New dealer');
    }
    // Insert the post into the database
    wp_insert_post($dealership_post);
		return false;
	}

  private static function get_existing_attachment_id($original_id, $post_id){
    global $wpdb;
    $sql = "SELECT * FROM ".$wpdb->posts." WHERE `post_type` = 'attachment'  AND `post_title` = '".$original_id."' LIMIT 1";
    $results = $wpdb ->get_results($sql);
    if (isset($results[0]->ID)){
      return $results[0]->ID;
    } else {
      return false;
    }
  }

  //check the lock of post
  protected function isLock($post_id){
    if($lock = get_post_meta( $post_id, '_mg_attachment_lock', false ) && $lock == array(0=>1)){
      return true;
    }
    return false;
  }

  //return attachment id
  protected function attachment2post($remoteUrl, $post_id){
    $original_id = 'original id ' . md5($remoteUrl);
    if($attachment_id = $this->get_existing_attachment_id($original_id)){
      return $attachment_id;
    }

    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $file = array();
    $path = parse_url($remoteUrl, PHP_URL_PATH);
    $name = array_pop(explode('/',$path));
    $file['name'] = $name;
    $file['tmp_name'] = download_url(trim($remoteUrl));

    // do the validation and storage stuff
    $id = media_handle_sideload( $file, $post_id, $original_id );
    $local_url = wp_get_attachment_url( $id );

    // If error storing permanently, unlink
    if ( is_wp_error($id) ) {
      @unlink($file_array['tmp_name']);
    } else {
      // create the thumbnails
      $attach_data = wp_generate_attachment_metadata( $id,  get_attached_file($id));
      wp_update_attachment_metadata( $id,  $attach_data );
    }
    return $id;
  }

  protected function processCsvDealership( $dealership ) {

    $post_id = 0;
    $user_id = get_current_user_id();
    $users = _dealer_get_user_by_dealership_id($dealership['DealershipId']);

    if($users[0]->ID){
      $user_id = $users[0]->ID;
    }
    $posts = _dealer_get_posts_by_dealership_id($dealership['DealershipId']);
    $posts = _dealer_check_posts_by_dealership_id($posts, $dealership['DealershipId']);

    if(isset($posts[0]->ID)){
      $post_id = $posts[0]->ID;
    }

    if($this->isLock($post_id)){
      return true;
    }
    $coordinates = NULL;

    $meta_input = array(
      'about_description' => $dealership['About Dealer'],
      'dealer_email' => $dealership['Dealer Email'],
      'dealer_phone' => $dealership['Phone'],
      'dealer_website' => $dealership['Website'],
      'dealer_services' => $dealership['dealerServices'],
      'store_hour_notes' => $dealership['Store Hour Notes'],
      'mon_fri' => $dealership['storeHours']['Monday'],
      'hours_saturday' => $dealership['storeHours']['Saturday'],
      'hours_sunday' => $dealership['storeHours']['Sunday'],
      'shopping_tools' => count($dealership['shoppingTools']),
    );

    $dealership_post = array(
      'ID' => $post_id,
      'post_title'    => wp_strip_all_tags($dealership['Title']),
      'post_content'  => '',
      'post_status'   => 'publish',
      'post_category' => array(),
      'post_author'  => $user_id,
      'post_type' => 'edls',
      'meta_input'   => $meta_input,
    );

    // Insert the post into the database
    wp_insert_post($dealership_post);

    // lock post
    update_post_meta( $post_id, '_mg_attachment_lock', true );

    $field_value = array();
    // update shopping tools
    foreach ($dealership['shoppingTools'] as $key => $value) {
      $field_value[] = array(
        'tool' => $value,
      );
    }
    update_field( 'shopping_tools', $field_value, $post_id );

    $field_value = array();
    // update dealer services
    foreach ($dealership['dealerServices'] as $key => $value) {
      $field_value[] = array(
        'dealer_service' => $value,
        'service_description' => '',
      );
    }
    update_field( 'dealer_services', $field_value, $post_id );

    // update media gallery
    if(!empty($dealership['mediaGallery'])){
      $field_value = array();
      foreach ($dealership['mediaGallery'] as $index => $value) {
        $value = str_replace(' ','%20',trim($value));
      	$field_value[] = array('edl_gallery_image' => $this->attachment2post(trim($value), $post_id));
      }
      update_field( 'edl_image_gallery', $field_value, $post_id );
    }
    // unlock post
    delete_post_meta($post_id, '_mg_attachment_lock');
    return false;
  }

  /* Load Defaults from options page and user page*/
  protected function update_save($post_id) {
    $post_type = get_post_type($post_id); // Get Post Type From POST ID

  	if ( get_post_status ( $post_id ) == 'draft' || get_post_status ( $post_id ) == 'pending' ) { // Check to see if draft or pending

      //$fields = get_fields($post_id); // Load ACF Fields connected to this POST ID
      $post_author_id = 'user_'.get_post_field( 'post_author', $post_id );
      $fields = array(
        'edl_image_gallery' => 'edl_image_gallery',
        'dealer_services' => 'dealer_services',
        'shopping_tools' => 'shopping_tools'
      );

      if( $fields ) // Check if this post has ACF fields
      {
        foreach( $fields as $field_name => $value ) //Separate fields out using field name as array variable
        {
          $field = get_field_object($field_name, false, array('load_value' => false)); // Get the rest of the array of field objects
          $replace = get_field($field_name, $post_id); // Get current value of variable
          $default_value = get_field($field_name, $post_author_id); // Get the default value set on options page, if there is none, look on the author page
          if(empty($default_value)){
            $default_value = get_field($field_name, 'options');
          }
          if(empty($replace)) { // Check if $replace has a value
            update_field($field_name, $default_value, $post_id); // Updates the value
          }
        }
      }
  	} else {
  	}
  }
  /**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $data ) {
    //write_log($data,'queue task-'.date(DATE_RFC822));
    if($data['type'] == 'account'){
      return $this->processAccount( $data['data'] );
    }
    if($data['type'] == 'dealership'){
      return $this->processDealership( $data['data'] );
    }
    if($data['type'] == 'csv'){
      return $this->processCsvDealership( $data['data'] );
    }
    return false;
	}
  /// Disable old dealers
  public function disableOldDealers(){
    $dealers_list = _get_dealers_list();
    $cur_dealers = [];
    foreach ($dealers_list as $key => $value) {
      $dealership_id = get_post_meta( $value->ID, 'dealership_id', true );
      $cur_dealers[$dealership_id] = $value->ID;
    }

    $file = wp_upload_dir()['basedir'] . '/' . md5( NONCE_SALT . 'lastfeeds.txt' );
    if(!file_exists($file)){
      toLog($file, 'File not exists');
      //write_log('task complete ERROR: file "'.$file.'" not exists','queue');
      return;
    }

    $feed_dealers = unserialize( file_get_contents($file) );
    if( !is_array($feed_dealers) ){
      toLog($file, 'File not array');
      //write_log('task complete ERROR: file not array','queue');
      return;
    }

    $dealers_to_remove = [];
    foreach ($cur_dealers as $key => $value) {
      if(!isset($feed_dealers[(string)$key])){
        $dealers_to_remove[$key] = $value;
        toLog($value, 'Move to trash');
        wp_trash_post($value);
      }
    }

    //remove file
    toLog($file, 'Feed file removed');
    unlink($file);
  }

  /**
   * Complete
   *
   * Override if applicable, but ensure that the below actions are
   * performed, or, call parent::complete().
   */
  protected function complete() {
    //write_log('task complete','queue');
    $this->disableOldDealers();
  	parent::complete();
  	// Show notice to user or perform some other arbitrary task...
  }
}

$GLOBALS['wpd-background-process'] = new DealerBackgroundProcess();

class DealerImporterSoapPage {
  private $connection;
	private $request;
	public $unsavedUsers;
	public $unsavedDealerships;
	public $unmappedDealerships;

	public function __construct() {
    add_action('admin_menu', array( $this, 'admin_menu') );
	}
  public function admin_menu(){
    $page = add_submenu_page('edit.php?post_type=edls', 'Import', 'Import', 'manage_options', 'import', array($this, 'form'));
  }
    /**
   * Handle POST submission
   *
   * @param array $options
   * @return void
   */
  function post($options = array()){
    $importer = New DealerImporterSoap('Hotspring');
    $importer->getAccounts();
    $importer->getDealerships();
    $importer->process();
  }

  function form(){
    $nonce = $_REQUEST['_wpnonce'];
    if ( wp_verify_nonce( $nonce, 'dealer-import' ) && current_user_can( 'import_dealers' ) ) {
      if(!$_POST['options']){
        $_POST['options'] = array();
      }
      $this->post($_POST['options']);
    }

    // form HTML {{{
    ?>

    <div class="wrap">
    <h2>Import SOAP</h2>
    <?php
    // global $DealerBackgroundProcess;
    // $count_queue = $DealerBackgroundProcess->count_queue();
    // if($count_queue){
    //   print '<p>Count of the queues is ' . $count_queue . '</p>';
    // }
    // if(isset($_GET['action']) && $_GET['action'] == 'process' )
    // {

    // }
    ?>

    <form class="add:the-list: validate" method="post" action="/wp-admin/edit.php?post_type=edls&page=import&action=process" enctype="multipart/form-data">
        <!-- Import as draft -->
        <!-- File input -->
        <div>
        <!-- <label for="csv_import">Upload file:</label><br/> -->
            <!-- <input type="file" name="csv_import" id="csv_import" value="" aria-required="true" /> -->

        </div>
        <p class="submit"><?php wp_nonce_field( 'dealer-import' ); submit_button( 'Import', 'primary', 'submit-form', false );?></p>
    </form>
    </div><!-- end wrap -->

    <?php
    // end form HTML }}}

  }

}
new DealerImporterSoapPage();

//custom class
class DealerImporterSoap {
  private $connection;
	private $request;
	private $excludedDealerships;
	public $unsavedUsers;
	public $unsavedDealerships;
	public $unmappedDealerships;

	public function __construct( $brand = 'Hotspring' ) {
    ini_set('default_socket_timeout', 60);

    $this->connection = new WSSoapClient(DEALE_RDIS_WSDL,
      array(
        'classmap' => array(
          'AccountData' => 'DealerAccount',
          'DealershipData' => 'Dealership',
        ),
      )
    );
		$this->connection->__setLocation(DEALE_RDIS_LOCATION);
		$this->connection->__setUsernameToken(DEALE_RDIS_NAME, DEALE_RDIS_PASS, "PasswordText");
		$this->request = new stdClass();
		$this->request->brand = $brand;
		$this->unsavedUsers = array();
		$this->unmappedDealerships = array();

    // Exclude particular dealers
    $this->excludedDealerships = array(
      //'04305', // Durango Outdoor living
    );

    //global $DealerBackgroundProcess;
    $this->backgroundProcess = $GLOBALS['wpd-background-process'];
    add_action('admin_menu', array( $this, 'admin_menu') );
  }

  public function getDealerships() {
    $this->unsavedDealerships = array();
    // $this->unsavedDealerships['03932'] = (object) array(
    //   'DealershipId' => '03932',
    //   'BilltoDealershipId' => '',
    //   'DealershipName' => 'Robertson Billiards',
    //   'Address1' => '1721 N. Franklin St.',
    //   'Address2' => '',
    //   'City' => 'Tampa',
    //   'State' => 'Florida',
    //   'StateCode' => 'FL',
    //   'Country' => 'USA',
    //   'CountryCode' => 'US',
    //   'Zip' => '33602',
    //   'TerritoryCode' => '10',
    //   'RsmId' => 'RSM10',
    //   'Phone' => '813-229-2778',
    //   'RecordTypeCode' => 'BILLMAIN',
    // );
    // $this->unsavedDealerships['03942'] = (object) array(
    //   'DealershipId' => '03942',
    //   'BilltoDealershipId' => '',
    //   'DealershipName' => 'Aquatic Pools And Hot Tubs',
    //   'Address1' => '2312 Palmer Hwy',
    //   'Address2' => '',
    //   'City' => 'Texas',
    //   'State' => 'Texas',
    //   'StateCode' => 'TX',
    //   'Country' => 'USA',
    //   'CountryCode' => 'US',
    //   'Zip' => '77590',
    //   'TerritoryCode' => '15',
    //   'RsmId' => 'RSM15',
    //   'Phone' => '409-986-7600',
    //   'RecordTypeCode' => 'BILLONLY',
    // );
    try {
      $response = $this->connection->GetDealerships($this->request);
      foreach ($response->GetDealershipsResult->DealershipData as $dealershipData) {
        $this->unsavedDealerships[$dealershipData->DealershipId] = $dealershipData;
      }
    } catch (Exception $e) {
      //write_log($e,'ERRRRROR! SOAP->GetDealerships');
    }
    //save dealers feed to file
    try {
      //global $wp_filesystem;
      $file = wp_upload_dir()['basedir'] . '/'.md5( NONCE_SALT . 'lastfeeds.txt' );
      file_put_contents($file, serialize($this->unsavedDealerships));
      toLog($file, 'Feed file saved');
    } catch (Exception $e) {
      //write_log($e,'ERRRRROR! filesave');
    }
	}
  public function getAccounts() {
    $this->unsavedUsers = array();
    // $this->unsavedUsers['Aaron.Minner@watkinsmfg.com'] = (object) array(
    //   'Username' => 'Aaron.Minner@watkinsmfg.com',
    //   'Email' => 'Aaron.Minner@watkinsmfg.com',
    //   'Role' => 'Internal',
    //   'DealerReference' => '',
    // );
    // $this->unsavedUsers['ALANA@ROBERTSONBILLIARDS.COM'] = (object) array(
    //   'Username' => 'ALANA@ROBERTSONBILLIARDS.COM',
    //   'Email' => 'ALANA@ROBERTSONBILLIARDS.COM',
    //   'Role' => 'Dealer',
    //   'DealerReference' => '03932',
    // );
    // $this->unsavedUsers['justinyeager1@gmail.com'] = (object) array(
    //   'Username' => 'justinyeager1@gmail.com',
    //   'Email' => 'justinyeager1@gmail.com',
    //   'Role' => 'Dealer',
    //   'DealerReference' => '03942',
    // );
    try {
      $response = $this->connection->GetAccounts($this->request);
      foreach ($response->GetAccountsResult->AccountData as $accountData) {
        $this->unsavedUsers[$accountData->Email] = $accountData;
      }
    } catch (Exception $e) {
      //write_log($e,'ERRRRROR! SOAP->GetAccounts');
    }
	}
  public function getUnSavedAccounts(){
    return $this->unsavedUsers;
  }
  /**
   * save and start processing
   *
   */
  public function process(){
    $this->feedLog();
    $this->processAccounts();
    $this->excludeDealerships();
    $this->processDealerships();
    $this->backgroundProcess->save()->dispatch();
  }

  public function put_contents( $file, $contents, $mode = false ) {
    $fp = @fopen( $file, 'wb' );

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

  private function feedLog() {

    $upload_dir = wp_upload_dir();
    $dir = trailingslashit( $upload_dir['basedir'] ) . 'dealer/';
    // $sub = date("Y-m-d");
    $subdir = $dir . "/" . date("Y-m-d") . "/";
    // Create main folder within upload if not exist
    if( !is_dir($subdir) ) {
      mkdir($subdir, 0777, true);
    }

    // Save file and set permission to 0644
    $feed_user_filename = wp_unique_filename( $subdir, 'user_feed.log');
    $feed_dealer_filename = wp_unique_filename( $subdir, 'dealer_feed.log');

    $feed_user_filename_data = wp_unique_filename( $subdir, 'user_feed.data');
    $feed_dealer_filename_data = wp_unique_filename( $subdir, 'dealer_feed.data');

    $this->put_contents( $subdir . $feed_user_filename, print_r($this->unsavedUsers, true), 0644 );
    $this->put_contents( $subdir . $feed_dealer_filename, print_r($this->unsavedDealerships, true), 0644 );

    $this->put_contents( $subdir . $feed_user_filename_data, serialize($this->unsavedUsers), 0644 );
    $this->put_contents( $subdir . $feed_dealer_filename_data, serialize($this->unsavedDealerships), 0644 );

  }

  /**
   * Process Accounts
   */
  private function processAccounts(){
    foreach ($this->unsavedUsers as $key => $value) {
      $this->backgroundProcess->push_to_queue(array(
        'type' => 'account',
        'data' => $value)
      );
      //write_log($value,'push_to_queue Accounts');
    }
  }

  /**
   * Process Dealerships
   */
  private function processDealerships(){
    foreach ($this->unsavedDealerships as $key => $value) {
      $this->backgroundProcess->push_to_queue(array(
        'type' => 'dealership',
        'data' => $value,
        )
      );
      //write_log($value,'push_to_queue Dealerships');
    }
  }

  /**
   * Exclude some dealerships from being imported
   */
  private function excludeDealerships() {
    if (empty($this->unsavedDealerships) || empty($this->excludedDealerships)) {
      return;
    }
    foreach ($this->excludedDealerships as $dealership_id) {
      if (!empty($this->unsavedDealerships[$dealership_id])) {
        unset($this->unsavedDealerships[$dealership_id]);
      }
    }
  }
}

<?php
class MDWatchdogAddon extends MDAddOn {
  private static $_instance = null;
  public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new MDWatchdogAddon();
		}
		return self::$_instance;
	}
	private function install() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'watchdog';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `$table_name` (
			`wid` int(11) NOT NULL auto_increment,
			`uid` int(11) NOT NULL default '0',
			`type` varchar(16) NOT NULL default '',
			`message` longtext NOT NULL,
			`variables` longtext NOT NULL,
			`severity` tinyint(3) unsigned NOT NULL default '0',
			`link` varchar(255) NOT NULL default '',
			`location` text NOT NULL,
			`referer` varchar(128) NOT NULL default '',
			`hostname` varchar(128) NOT NULL default '',
			`timestamp` int(11) NOT NULL default '0',
			PRIMARY KEY  (`wid`),
			KEY `type` (`type`)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		add_option( 'develpress_db_version', 1 );
	}

	private function install_data() {
		global $wpdb;

		// $welcome_name = 'Mr. WordPress';
		// $welcome_text = 'Congratulations, you just completed the installation!';

		// $table_name = $wpdb->prefix . 'liveshoutbox';

		// $wpdb->insert(
		// 	$table_name,
		// 	array(
		// 		'time' => current_time( 'mysql' ),
		// 		'name' => $welcome_name,
		// 		'text' => $welcome_text,
		// 	)
		// );
	}
  public function init() {
		parent::init();
		require_once( WPMD_PLUGIN_DIR . 'addons/watchdog/functions.php' );
		register_activation_hook( __FILE__, [ $this, 'install' ] );
		register_activation_hook( __FILE__, [ $this, 'install_data' ] );
	}
}

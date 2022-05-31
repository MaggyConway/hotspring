<?php
function watchdog( $type, $message, $variables = array(), $severity = 'info', $link = null ) {
	global $wpdb, $wp;
  $current_url = home_url( add_query_arg( array(), $wp->request ) );
  $current_user = wp_get_current_user();

	$log_entry = array(
		'type'        => $type,
		'message'     => $message,
		'variables'   => $variables,
		'severity'    => $severity,
		'link'        => $link,
		'user'        => $current_user->user_login,
		'uid'         => $current_user->ID,
		'request_uri' => $current_url,
		'referer'     => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',
		'ip'          => ip_address(),
		// Request time isn't accurate for long processes, use time() instead.
		'timestamp'   => time(),
	);

	$table_name = $wpdb->prefix . 'watchdog';
	$wpdb->insert( $table_name, $log_entry );
}

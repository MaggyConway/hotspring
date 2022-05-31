<?php
// Add notification
function add_notification( $notification ){
	if( is_string($notification) ){
		$GLOBALS['notifications'][] = [ 'text' => $notification ];
 	} else {
		$GLOBALS['notifications'][] = $notification;
	 }
}
add_action( 'add_notification', 'add_notification' );

// do_action( 'add_notification', ['text' => 'test1'] );
// do_action( 'add_notification', ['text' => 'test2'] );
// do_action( 'add_notification', 'H5 Headline Notification Bar (48–80) Lorem ipsum dolor sit amet. <a href="#">H5 Headline Text Link (1-24)</a>' );
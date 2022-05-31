<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

$state   = sanitize_text_field( get_query_var( 'state', null ) );
$city    = sanitize_text_field( urldecode( get_query_var( 'city', null ) ) );
$country = sanitize_text_field( get_query_var( 'country', null ) );

$wpdp          = $GLOBALS['wpdp'];
$list_of_state = $wpdp->get->list_of_state();

// fix for compatibility of old site, it allows you to open pages without country prefix,
// like this (/hot-tub-dealers/colorado/)
if ( ! empty( $state ) && empty( $country ) ) {
	$info = $wpdp->get->code_of_state( $state );
	if ( ! empty( $info['country_name'] ) && ! empty( $info['state_code'] ) ) {
		$country = $info['country_name'];
		$state   = $info['state_code'];
	}
}

if ( ! isset( $list_of_state[ $country ] ) ) {
	$country = null;
	unset( $_GET['country'] );
}
if ( ! isset( $list_of_state[ $country ][ $state ] ) ) {
	$state = null;
	unset( $_GET['state'] );

	$city = null;
	unset( $_GET['city'] );
}
?>

<div class="dp-states-and-city">
	<?php do_action( 'dp_header' ); ?>
	<?php
	if ( empty( $state ) && empty( $city ) ) {
		do_action( 'dp_main' );
	}
	if ( ! empty( $state ) && empty( $city ) ) {
		do_action( 'dp_main_state', $state, $country );
	}
	if ( ! empty( $state ) && ! empty( $city ) ) {
		do_action( 'dp_main_city', $city, $state, $country );
	}
	?>
	<?php do_action( 'dp_footer' ); ?>
</div>

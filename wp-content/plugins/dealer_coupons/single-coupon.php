<?php
//no cache
nocache_headers();
$post_id = get_the_ID();
$dealer_id =  get_field( 'dc_dealer' );
$campaign_id =  get_field( 'dc_campaign' );
$campaign = get_term_by('id', $campaign_id, 'dc_campaign');

$dcc_active =  get_field( 'dcc_active' );

if( !$dcc_active ) {
  status_header( 404 );
  include( get_query_template( '404' ) );
  exit();
}
//campaign data
$c_headline = get_field( 'dcc_default_headline', $campaign );
$c_coupon_text = get_field( 'dcc_coupon_text', $campaign );
$c_dont_show_text = get_field( 'dcc_dont_show_info', $campaign );

$c_img =  get_field( 'dcc_coupon_print_image', $campaign );
$c_legal_text =  get_field( 'dcc_legal_text', $campaign );
$c_css = get_field( 'dcc_style_css', $campaign );

//coupon data
$headline = get_field( 'dcc_default_headline', $post_id );
$coupon_text = get_field( 'dcc_coupon_text', $post_id );
$img =  get_field( 'dcc_coupon_print_image', $post_id );
$legal_text =  get_field( 'dcc_legal_text', $post_id );
$css = get_field( 'dcc_style_css', $post_id);

$dont_show_text = get_field( 'dcc_dont_show_info', $post_id );

//if coupon data is empty override it to campaign data
$headline = empty($headline) ? $c_headline : $headline;
$coupon_text = empty($coupon_text) ? $c_coupon_text : $coupon_text;
$dont_show_text = empty($dont_show_text) ? $c_dont_show_text : $dont_show_text;

$img = empty($img) ? $c_img : $img;

$legal_text = empty($legal_text) ? $c_legal_text : $legal_text;
$css = empty($css) ? $c_css : $css;


?><!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="nofollow, noindex" />


	<!-- Favicon -->
	<?php
	//variables
	$favIcon        = get_field( 'favIcon', 'option' );
	$favApple       = get_field( 'favApple', 'option' );
	$fav32          = get_field( 'fav32', 'option' );
	$fav16          = get_field( 'fav16', 'option' );
	$favSafari      = get_field( 'favSafari', 'option' );
	$favSafariColor = get_field( 'favSafariColor', 'option' );
	?>

	<link rel="icon" href="<?php echo $favIcon; ?>" type="image/x-icon"/>
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $favApple; ?>">
	<link rel="icon" type="image/png" href="<?php echo $fav32; ?>" sizes="32x32">
	<link rel="icon" type="image/png" href="<?php echo $fav16; ?>" sizes="16x16">

	<link rel="mask-icon" href="<?php echo $favSafari; ?>" color="<?php echo $favSafariColor; ?>">
	<meta name="theme-color" content="#ffffff">
	<style type="text/css">
  <?php echo $css; ?>
	</style>
	<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>

<div id="postID" style="display:none"><?php echo gt_get_the_ID(); ?></div>

<header class="header page-header">
	<div class="header-container elementor-sticky--active bg-light">
		<?php get_template_part( 'parts/nav', 'topbar' ); ?>
	</div>
</header>
<?php


//dealer info
$dealer = get_post( $dealer_id );
$dealer_title = $dealer->post_title;
$dealership_address_1 = get_post_meta( $dealer_id, 'dealership_address_1', true );
$dealership_city = get_post_meta( $dealer_id, 'dealership_city', true );
$dealership_state_code = get_post_meta( $dealer_id, 'dealership_state_code', true );
$dealership_zip = get_post_meta( $dealer_id, 'dealership_zip', true );
$dealership_country_code = get_post_meta( $dealer_id, 'dealership_country_code', true );
$dealership_website = get_field('dealer_website', $dealer_id);
$dealership_phone = get_field('dealer_phone', $dealer_id);


$address = '<div class="dealer-address-wrapper">';
$address .= '<img width="145" src="/wp-content/uploads/2018/06/medium-logo-2.png">';
$address = $address .'<div class="dealer-title">'.$dealer_title.'</div>';
if ( $dealership_address_1 ) {
  $address = $address . '<span>' . $dealership_address_1 . '</span></br>';
}
if ( $dealership_city ) {
  $address = $address . '<span>' . $dealership_city . '</span>, ';
}
if ( $dealership_state_code ) {
  $address = $address . '<span>' . $dealership_state_code . '</span> ';
}
if ( $dealership_zip ) {
  $address = $address . '<span>' . $dealership_zip . '</span>';
}
if ( $dealership_phone ) {
	$address = $address . '</br><span>' . $dealership_phone . '</span>';
}
if ( $dealership_website ) {
	$address = $address . '</br><span>' . $dealership_website . '</span>';
}

$address = $address . '</div>';
?>
<?php if( !$dont_show_text ):?>
	<div class="coupon-wrapper">
		<div class="coupon-title text-center"><p><strong>Print this page</strong> and bring on your visit to <strong><?php print $dealer_title; ?></strong></p></div>
  	<div class="image-wrapper"><img src="<?php print $img['url'];?>"/>
      <div class="coupon-body-wrapper">
    	  <div class="headline">
					<?php
						// Limit to 20 chars.
						if (strlen(headline) > 20) {
							$pos = strpos($headline, ' ', 20);
							$headline = substr($headline, 0, $pos);
						}
						print $headline;
					?>
				</div>
    	  <div class="coupon-text"><?php print $coupon_text;?></div>
    	  <div class="dealer-address"><?php print $address;?></div>
      </div>
    </div>
  	<div class="legal">
			<?php print $legal_text;?>
			<span class="coupon-current-date"><?php date_default_timezone_set('America/Los_Angeles'); print date('m/d/Y g:i:s A');?></span>
		</div>
  </div>
<?php else:?>
	<div class="coupon-wrapper">
		<div class="coupon-title text-center"><p><strong>Print this page</strong> and bring on your visit to <strong><?php print $dealer_title; ?></strong></p></div>
  	<div class="image-wrapper"><img src="<?php print $img['url'];?>"/>
      <div class="coupon-body-wrapper">

    	  <div class="dealer-address"><?php print $address;?></div>
      </div>
    </div>
  	<div class="legal">
			<?php print $legal_text;?>
			<span class="coupon-current-date"><?php date_default_timezone_set('America/Los_Angeles');  print date('m/d/Y g:i:s A');?></span>
		</div>
  </div>
<?php endif;
wp_footer(); ?>
</body>
</html>

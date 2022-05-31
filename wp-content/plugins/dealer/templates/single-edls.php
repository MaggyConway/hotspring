<?php
	get_header();
	//the_post();
  $location  = get_field( 'dealership_coordinates' );
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha256-NuCn4IvuZXdBaFKJOAcsU2Q3ZpwbdFisd5dux4jkQ5w=" crossorigin="anonymous" />
<section class="intro">
  <?php get_template_part('fragments/page-top-image'); ?>
  <div class="vertical-aligned">
    <div class="shell">
      <h1><?php the_title(); ?></h1>
    </div><!-- /.shell -->
  </div><!-- /.verticaly-aligned -->
</section><!-- /.intro -->

<div class="main">
		<div class="shell">

      <div class="posts-section">
        <div class="col one-half">
          <!-- info -->
          <?php require_once('dealer-info.php'); ?>
          <!-- info -->
        </div>
        <div class="col one-half">
          <!-- map -->
          <?php require_once('dealer-map.php'); ?>
          <!-- map -->
        </div>
        <?php /* if ( get_field( 'about_description' ) ){?>
        <div class="col one">
          <h2>About</h2>
          <div class="about"><?php print nl2br(htmlspecialchars(get_field( 'about_description' ))); ?></div>
        </div>
        <?php } ?>
        <?php if ( get_field( 'we_also_offer' ) ){?>
        <div class="col one">
          <h2>We also offer</h2>
          <div class="we-offer"><?php print nl2br(htmlspecialchars(get_field( 'we_also_offer' ))); ?></div>
        </div>
        <?php } */ ?>
				<div class="col one">
          <ul class="dealer-links">
						<li><a class="dealer-btn big-btn" href="/download-brochure/">Download Brochure</a></li>
						<li><a class="dealer-btn big-btn" href="https://info.fantasy-spas.com/pricing">Get Pricing</a></li>
					</ul>
				</div>
			</div>

		</div><!-- /.shell -->
</div><!-- /.main -->

<?php
wp_enqueue_script('main_map');
wp_enqueue_script('map_init');
?>

<?php get_footer();
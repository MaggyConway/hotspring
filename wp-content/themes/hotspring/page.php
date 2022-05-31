<?php
/**
 * Template Name: Page (Default)
 * Description: Page template.
 *
 */

get_header();

the_post();
?>
<div <?php post_class( 'container' ); ?> id="post-<?php the_ID(); ?>">
	<?php
		the_content();
	?>
</div>
<?php
get_footer();

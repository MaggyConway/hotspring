<?php
/**
 * Template Name: Blog Index
 * Description: The template for displaying the Blog index /blog.
 *
 */

get_header();

$page_id = get_option( 'page_for_posts' );
?>

<div <?php post_class( 'container' ); ?> id="post-<?php the_ID(); ?>">
	<?php
		echo apply_filters( 'the_content', get_post_field( 'post_content', $page_id ) );
	?>
</div>
<?php
get_footer();

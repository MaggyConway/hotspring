<?php
/**
 * The Template for displaying collection.
 */

get_header();

get_template_part( 'template-parts/breadcrumb', 'collection' );

the_post();

the_content();

get_footer();

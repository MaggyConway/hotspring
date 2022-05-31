<?php get_header(); ?>

<div id="content" class="container">

    <div id="inner-content" class="row">

        <main id="main" class="col-lg-6 offset-lg-3 col-md-6 offset-md-3 col-sm-12 section-indent pb-0" role="main">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/loop', 'single-faq' ); ?>

			<?php endwhile; else : ?>

			<?php endif; ?>

        </main> <!-- end #main -->

    </div> <!-- end #inner-content -->

</div> <!-- end #content -->

<?php get_footer(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( '' ); ?> role="article">

	<header class="article-header">
		<h1 class="entry-title single-title text-center">
			<?php the_title(); ?>
		</h1>
	</header>
	<!-- end article header -->
	<section class="entry-content">
		<div class="d-flex justify-content-end">
			<div class="share">
				<div class="a2a_kit a2a_kit_size_32 a2a_default_style" data-a2a-url="<?php the_permalink() ?>" data-a2a-title="<?php the_title(); ?>">
					<a class="a2a_dd" href="https://www.addtoany.com/share"></a>
					<a class="a2a_button_facebook"></a>
					<a class="a2a_button_twitter"></a>
					<a class="a2a_button_google_plus"></a>
					<a class="a2a_button_linkedin"></a>
					<a class="a2a_button_pinterest"></a>
					<a class="a2a_button_print"></a>
					<a class="a2a_button_reddit"></a>
					<a class="a2a_button_email"></a>
					<a class="a2a_button_print"></a>
				</div>
				<script async src="https://static.addtoany.com/menu/page.js"></script>
			</div>
		</div>
		<?php the_post_thumbnail('full'); ?>
		<?php the_content(); ?>
	</section>
	<!-- end article section -->

</article>
<!-- end article -->
<?php
$faq = [
	"@context" => "https://schema.org",
	"@type" => "FAQPage",
	"mainEntity" => [
		"@type" => "Question",
		"name" => get_the_title(),
		"acceptedAnswer" => [[
				"@type" => "Answer",
				"text" => strip_tags(get_the_content())
		]]
	],
];
print '<script type="application/ld+json">' . json_encode($faq) . '</script>';

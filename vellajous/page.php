<?php
/**
 * The template for displaying all pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vellajous
 */

get_header();
?>

<main id="primary" class="site-main vellajous-standard-page">
	<div class="container">
		<?php
		while (have_posts()) :
			the_post();

			get_template_part('template-parts/content', 'page');

			if (comments_open() || get_comments_number()) :
				comments_template();
			endif;

		endwhile;
		?>
	</div>
</main><!-- #main -->

<?php
get_footer();

<?php
/**
 * Single.
 *
 * @package Almasa
 */

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
	?>
	<main id="main" class="almasa-main almasa-shell almasa-fallback-article">
		<?php
		while ( have_posts() ) {
			the_post();
			the_title( '<h1>', '</h1>' );
			the_content();
		}
		?>
	</main>
	<?php
}

get_footer();

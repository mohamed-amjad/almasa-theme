<?php
/**
 * Archive.
 *
 * @package Almasa
 */

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {
	?>
	<main id="main" class="almasa-main almasa-shell almasa-fallback-article">
		<?php the_archive_title( '<h1>', '</h1>' ); ?>
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_title( '<h2><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
			}
		}
		?>
	</main>
	<?php
}

get_footer();

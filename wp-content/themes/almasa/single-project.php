<?php
/**
 * Single project — Elementor Theme Builder first.
 *
 * @package Almasa
 */

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
	get_template_part( 'template-parts/projects/single' );
}

get_footer();

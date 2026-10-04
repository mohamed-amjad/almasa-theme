<?php
/**
 * Theme supports, menus, image sizes.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * After setup.
 */
function almasa_setup() {
	load_theme_textdomain( 'almasa', ALMASA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 160,
		'width'       => 160,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'elementor' );

	register_nav_menus( array(
		'primary' => __( 'القائمة الرئيسية', 'almasa' ),
		'footer'  => __( 'قائمة التذييل', 'almasa' ),
	) );

	add_image_size( 'almasa-project-card', 960, 720, true );
	add_image_size( 'almasa-project-hero', 1920, 1080, true );
}
add_action( 'after_setup_theme', 'almasa_setup' );

/**
 * Flag views that open with a full-bleed dark hero, so the fixed header starts transparent.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function almasa_hero_body_class( $classes ) {
	$themed_page = is_page() && ! almasa_uses_elementor_layout( get_queried_object_id() );
	if ( $themed_page || is_singular( 'project' ) || is_post_type_archive( 'project' ) || is_404() ) {
		$classes[] = 'almasa-has-hero';
	}
	return $classes;
}
add_filter( 'body_class', 'almasa_hero_body_class' );

/**
 * Content width.
 */
function almasa_content_width() {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'almasa_content_width', 0 );

<?php
/**
 * Styles and scripts.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a theme asset.
 *
 * @param string $path Path relative to the theme root.
 * @return string
 */
function almasa_asset_version( $path ) {
	$file = ALMASA_DIR . '/' . ltrim( $path, '/' );
	return file_exists( $file ) ? ALMASA_VERSION . '.' . filemtime( $file ) : ALMASA_VERSION;
}

/**
 * Front-end assets.
 */
function almasa_enqueue_assets() {
	wp_enqueue_style(
		'almasa-fonts',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'almasa-tokens', ALMASA_URI . '/assets/css/tokens.css', array(), almasa_asset_version( 'assets/css/tokens.css' ) );
	wp_enqueue_style( 'almasa-base', ALMASA_URI . '/assets/css/base.css', array( 'almasa-tokens', 'almasa-fonts' ), almasa_asset_version( 'assets/css/base.css' ) );
	// Load after Elementor's frontend CSS so equal-specificity theme rules (e.g. cover images) win over `.elementor img`.
	$site_deps = array( 'almasa-base' );
	if ( wp_style_is( 'elementor-frontend', 'registered' ) ) {
		$site_deps[] = 'elementor-frontend';
	}
	wp_enqueue_style( 'almasa-site', ALMASA_URI . '/assets/css/site.css', $site_deps, almasa_asset_version( 'assets/css/site.css' ) );
	wp_enqueue_style( 'almasa-motion', ALMASA_URI . '/assets/css/motion.css', array( 'almasa-site' ), almasa_asset_version( 'assets/css/motion.css' ) );

	wp_enqueue_script( 'almasa-theme', ALMASA_URI . '/assets/js/theme.js', array(), almasa_asset_version( 'assets/js/theme.js' ), true );
	wp_localize_script( 'almasa-theme', 'almasaForm', array(
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'phonePattern' => ALMASA_PHONE_PATTERN,
		'messages'     => almasa_contact_messages(),
	) );
}
add_action( 'wp_enqueue_scripts', 'almasa_enqueue_assets' );

/**
 * Editor / Elementor preview tokens.
 */
function almasa_enqueue_editor_assets() {
	wp_enqueue_style( 'almasa-tokens', ALMASA_URI . '/assets/css/tokens.css', array(), almasa_asset_version( 'assets/css/tokens.css' ) );
}
add_action( 'enqueue_block_editor_assets', 'almasa_enqueue_editor_assets' );
add_action( 'elementor/editor/after_enqueue_styles', 'almasa_enqueue_editor_assets' );

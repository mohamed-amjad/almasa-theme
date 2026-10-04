<?php
/**
 * Almasa theme bootstrap.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

define( 'ALMASA_VERSION', '1.2.0' );
define( 'ALMASA_DIR', get_template_directory() );
define( 'ALMASA_URI', get_template_directory_uri() );

$almasa_includes = array(
	'setup.php',
	'enqueue.php',
	'post-types.php',
	'acf.php',
	'elementor.php',
	'project-gallery.php',
	'media.php',
	'contact-form.php',
	'social.php',
	'helpers.php',
	'security.php',
);

foreach ( $almasa_includes as $almasa_file ) {
	require_once ALMASA_DIR . '/inc/' . $almasa_file;
}

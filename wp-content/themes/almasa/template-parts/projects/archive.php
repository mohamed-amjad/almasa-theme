<?php
/**
 * Fallback project archive.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
?>
<main id="main" class="almasa-main">
	<?php
	get_template_part( 'template-parts/sections/page-hero', null, array(
		'title'    => __( 'مشاريعنا', 'almasa' ),
		'image_id' => almasa_page_thumb_id( 'مشاريعنا' ),
	) );

	if ( have_posts() ) {
		get_template_part( 'template-parts/sections/projects', null, array(
			'projects' => $wp_query->posts,
			'head'     => false,
			'layout'   => 'grid',
		) );
		get_template_part( 'template-parts/sections/gallery', null, array( 'limit' => 12 ) );
	}

	get_template_part( 'template-parts/sections/contact' );
	?>
</main>

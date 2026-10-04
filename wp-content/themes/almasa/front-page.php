<?php
/**
 * Front page — designed theme layout unless Elementor owns the page.
 *
 * @package Almasa
 */

get_header();
?>
<main id="main" class="almasa-main">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			if ( almasa_uses_elementor_layout() ) {
				the_content();
			} else {
				get_template_part( 'template-parts/sections/hero' );
				get_template_part( 'template-parts/sections/marquee' );
				get_template_part( 'template-parts/sections/intro' );
				get_template_part( 'template-parts/sections/services' );
				get_template_part( 'template-parts/sections/projects' );
				get_template_part( 'template-parts/sections/gallery', null, array( 'limit' => 9 ) );
				get_template_part( 'template-parts/sections/locations', null, array( 'maps' => true ) );
				get_template_part( 'template-parts/sections/contact' );
			}
		}
	}
	?>
</main>
<?php
get_footer();

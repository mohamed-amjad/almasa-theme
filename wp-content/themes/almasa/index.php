<?php
/**
 * Main query loop — Elementor-ready.
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
			the_content();
		}
	}
	?>
</main>
<?php
get_footer();

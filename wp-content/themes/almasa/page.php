<?php
/**
 * Page — Elementor or themed sections by page title.
 *
 * @package Almasa
 */

get_header();

$sections_by_title = array(
	'من نحن'     => array(
		'intro'    => array(),
		'values'   => array(),
		'why'      => array(),
		'marquee'  => array(),
		'services' => array(),
		'contact'  => array(),
	),
	'خدماتنا'    => array(
		'services' => array( 'head' => false, 'layout' => 'grid' ),
		'contact'  => array(),
	),
	'مشاريعنا'   => array(
		'projects' => array( 'head' => false, 'layout' => 'grid' ),
		'gallery'  => array( 'limit' => 12 ),
		'contact'  => array(),
	),
	'تواصل معنا' => array(
		'contact-form' => array(),
		'locations'    => array( 'maps' => true ),
	),
);
?>
<main id="main" class="almasa-main">
	<?php
	while ( have_posts() ) {
		the_post();
		if ( almasa_uses_elementor_layout() ) {
			the_content();
			continue;
		}

		$title = get_the_title();

		get_template_part( 'template-parts/sections/page-hero', null, array(
			'title'    => $title,
			'image_id' => (int) get_post_thumbnail_id(),
		) );

		if ( get_the_content() ) {
			echo '<div class="almasa-section"><div class="almasa-shell almasa-prose almasa-page-content">';
			the_content();
			echo '</div></div>';
		}

		if ( isset( $sections_by_title[ $title ] ) ) {
			foreach ( $sections_by_title[ $title ] as $section => $section_args ) {
				get_template_part( 'template-parts/sections/' . $section, null, $section_args );
			}
		}
	}
	?>
</main>
<?php
get_footer();

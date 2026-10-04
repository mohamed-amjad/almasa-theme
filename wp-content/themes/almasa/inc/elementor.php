<?php
/**
 * Elementor theme locations and defaults.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Theme Builder locations.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager Manager.
 */
function almasa_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_location( 'header' );
	$elementor_theme_manager->register_location( 'footer' );
	$elementor_theme_manager->register_location( 'single' );
	$elementor_theme_manager->register_location( 'archive' );
}
add_action( 'elementor/theme/register_locations', 'almasa_register_elementor_locations' );

/**
 * Allow Elementor on all public types used by the site.
 *
 * @param array $cpt List.
 * @return array
 */
function almasa_elementor_cpts( $cpt ) {
	$cpt[] = 'project';
	$cpt[] = 'service';
	$cpt[] = 'page';
	return array_unique( $cpt );
}
add_filter( 'elementor_pro/utils/get_public_post_types', 'almasa_elementor_cpts' );
add_filter( 'elementor/utils/get_public_post_types', 'almasa_elementor_cpts' );

/**
 * Enable Elementor on structured types.
 */
function almasa_elementor_cpt_support() {
	$support = get_option( 'elementor_cpt_support' );
	if ( ! is_array( $support ) ) {
		$support = array( 'page', 'post' );
	}
	$needed  = array( 'page', 'post', 'project', 'service' );
	$missing = array_diff( $needed, $support );
	if ( empty( $missing ) ) {
		return;
	}
	update_option( 'elementor_cpt_support', array_values( array_unique( array_merge( $support, $needed ) ) ) );
}
add_action( 'after_switch_theme', 'almasa_elementor_cpt_support' );
add_action( 'init', 'almasa_elementor_cpt_support', 20 );

/**
 * "الماسة" widget category.
 *
 * @param \Elementor\Elements_Manager $elements_manager Manager.
 */
function almasa_elementor_category( $elements_manager ) {
	$elements_manager->add_category( 'almasa', array(
		'title' => 'الماسة',
		'icon'  => 'eicon-site-identity',
	) );

	// Elementor has no API for category order; move ours to the top of the panel (after favorites).
	$reorder = \Closure::bind( function () {
		if ( ! is_array( $this->categories ) || ! isset( $this->categories['almasa'] ) ) {
			return;
		}
		$almasa = array( 'almasa' => $this->categories['almasa'] );
		unset( $this->categories['almasa'] );
		$head             = array_slice( $this->categories, 0, isset( $this->categories['favorites'] ) ? 1 : 0, true );
		$this->categories = $head + $almasa + $this->categories;
	}, $elements_manager, get_class( $elements_manager ) );
	if ( $reorder ) {
		$reorder();
	}
}
add_action( 'elementor/elements/categories_registered', 'almasa_elementor_category' );

/**
 * Register Almasa widgets.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
 */
function almasa_register_elementor_widgets( $widgets_manager ) {
	require_once ALMASA_DIR . '/inc/elementor/class-almasa-widget.php';
	require_once ALMASA_DIR . '/inc/elementor/widgets-sections.php';
	require_once ALMASA_DIR . '/inc/elementor/widgets-layout.php';

	$classes = array(
		'Almasa_Widget_Hero',
		'Almasa_Widget_Page_Hero',
		'Almasa_Widget_Marquee',
		'Almasa_Widget_Intro',
		'Almasa_Widget_Services',
		'Almasa_Widget_Projects',
		'Almasa_Widget_Gallery',
		'Almasa_Widget_Locations',
		'Almasa_Widget_Contact',
		'Almasa_Widget_Contact_Form',
		'Almasa_Widget_Values',
		'Almasa_Widget_Why',
		'Almasa_Widget_Header',
		'Almasa_Widget_Footer',
	);
	foreach ( $classes as $class ) {
		$widgets_manager->register( new $class() );
	}
}
add_action( 'elementor/widgets/register', 'almasa_register_elementor_widgets' );

/**
 * Elementor templates (Templates → Saved Templates) usable as header / footer.
 *
 * @return array<int,string> ID => title.
 */
function almasa_elementor_template_choices() {
	$choices = array( 0 => 'تصميم الثيم الافتراضي' );
	$posts   = get_posts( array(
		'post_type'      => 'elementor_library',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => '_elementor_template_type',
				'value'   => array( 'kit' ),
				'compare' => 'NOT IN',
			),
		),
	) );
	foreach ( $posts as $post ) {
		$choices[ $post->ID ] = $post->post_title;
	}
	return $choices;
}

/**
 * Customizer: pick the Elementor template used as the site header / footer.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function almasa_layout_customize( $wp_customize ) {
	$wp_customize->add_section( 'almasa_layout', array(
		'title'       => 'الهيدر والفوتر',
		'description' => 'اختر قالب Elementor (القوالب ← القوالب المحفوظة) الذي يظهر كهيدر أو فوتر في كل الصفحات.',
		'priority'    => 30,
	) );
	$choices = almasa_elementor_template_choices();
	foreach ( array(
		'header' => 'قالب الهيدر',
		'footer' => 'قالب الفوتر',
	) as $slot => $label ) {
		$wp_customize->add_setting( 'almasa_' . $slot . '_template', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'almasa_' . $slot . '_template', array(
			'label'   => $label,
			'section' => 'almasa_layout',
			'type'    => 'select',
			'choices' => $choices,
		) );
	}
}
add_action( 'customize_register', 'almasa_layout_customize' );

/**
 * Print the site header / footer: Elementor Pro location → selected Elementor
 * template → theme template part.
 *
 * @param string $slot header|footer.
 */
function almasa_render_site_part( $slot ) {
	if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $slot ) ) {
		return;
	}
	// Saved templates (incl. the header/footer themselves) are previewed/edited without site chrome.
	if ( is_singular( 'elementor_library' ) ) {
		return;
	}
	$template_id = (int) get_theme_mod( 'almasa_' . $slot . '_template', 0 );
	if ( $template_id && class_exists( '\Elementor\Plugin' ) && 'publish' === get_post_status( $template_id ) ) {
		$html = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
		if ( '' !== trim( (string) $html ) ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor-rendered markup.
			return;
		}
	}
	get_template_part( 'template-parts/' . $slot . '/' . $slot );
}

/**
 * First widget type of an Elementor document.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function almasa_elementor_first_widget( $post_id ) {
	$data = json_decode( (string) get_post_meta( $post_id, '_elementor_data', true ), true );
	while ( is_array( $data ) && $data ) {
		$first = reset( $data );
		if ( isset( $first['elType'] ) && 'widget' === $first['elType'] ) {
			return isset( $first['widgetType'] ) ? (string) $first['widgetType'] : '';
		}
		$data = isset( $first['elements'] ) ? $first['elements'] : array();
	}
	return '';
}

/**
 * Elementor pages that open with an Almasa hero sit under the transparent header.
 *
 * @param array $classes Classes.
 * @return array
 */
function almasa_elementor_hero_body_class( $classes ) {
	if ( is_singular() && almasa_uses_elementor_layout( get_queried_object_id() ) ) {
		if ( in_array( almasa_elementor_first_widget( get_queried_object_id() ), array( 'almasa-hero', 'almasa-page-hero' ), true ) ) {
			$classes[] = 'almasa-has-hero';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'almasa_elementor_hero_body_class' );

/**
 * Default canvas-friendly body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function almasa_body_classes( $classes ) {
	$classes[] = 'almasa-theme';
	if ( is_rtl() ) {
		$classes[] = 'almasa-rtl';
	}
	return $classes;
}
add_filter( 'body_class', 'almasa_body_classes' );

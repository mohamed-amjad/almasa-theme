<?php
/**
 * Helpers.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template-part argument with a fallback. A key that is present wins even when
 * empty (Elementor widgets clear a text field to hide it).
 *
 * @param array  $args    Template args.
 * @param string $key     Key.
 * @param mixed  $default Fallback when the key is absent.
 * @return mixed
 */
function almasa_arg( $args, $key, $default = '' ) {
	return is_array( $args ) && array_key_exists( $key, $args ) ? $args[ $key ] : $default;
}

/**
 * URL argument, falling back to a page permalink (by title) when absent or empty.
 *
 * @param array  $args       Template args.
 * @param string $key        Key.
 * @param string $page_title Fallback page title.
 * @return string
 */
function almasa_arg_url( $args, $key, $page_title ) {
	$url = is_array( $args ) && ! empty( $args[ $key ] ) ? (string) $args[ $key ] : '';
	return '' !== $url ? $url : almasa_page_url( $page_title );
}

/**
 * ACF-aware meta getter.
 *
 * @param string $name    Field name.
 * @param int    $post_id Post ID.
 * @return mixed
 */
function almasa_get_field( $name, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, $post_id );
		if ( null !== $value && false !== $value && '' !== $value ) {
			return $value;
		}
	}
	return get_post_meta( $post_id, $name, true );
}

/**
 * Normalize Egyptian mobile for tel: href.
 *
 * @param string $phone Raw phone.
 * @return string
 */
function almasa_tel_href( $phone ) {
	$digits = preg_replace( '/\D+/', '', (string) $phone );
	if ( '' === $digits ) {
		return '';
	}
	if ( 0 === strpos( $digits, '00' ) ) {
		$digits = substr( $digits, 2 );
	}
	if ( 0 === strpos( $digits, '0' ) ) {
		return 'tel:+2' . $digits;
	}
	return 'tel:' . $digits;
}

/**
 * @param int $post_id Project ID.
 * @return int[]
 */
function almasa_project_gallery_ids( $post_id ) {
	return almasa_get_gallery_ids( $post_id );
}

/**
 * Whether a post is an Elementor document with saved layout.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function almasa_is_elementor_canvas( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return false;
	}
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Whether a post should render its Elementor layout instead of the theme sections.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function almasa_uses_elementor_layout( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! almasa_is_elementor_canvas( $post_id ) ) {
		return false;
	}
	$data = json_decode( (string) get_post_meta( $post_id, '_elementor_data', true ), true );
	return ! empty( $data ) || isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Permalink for a published page by title.
 *
 * @param string $title Page title.
 * @return string
 */
function almasa_page_url( $title ) {
	$query = new WP_Query( array(
		'post_type'      => 'page',
		'title'          => $title,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	) );
	$url = $query->have_posts() ? get_permalink( $query->posts[0] ) : '';
	wp_reset_postdata();
	return $url ? $url : home_url( '/' );
}

/**
 * Featured/ordered projects.
 *
 * @param int  $limit    Count.
 * @param bool $featured Featured only.
 * @return WP_Post[]
 */
function almasa_get_projects( $limit = 12, $featured = false ) {
	$meta_query = array();
	if ( $featured ) {
		$meta_query[] = array(
			'key'   => 'featured_project',
			'value' => '1',
		);
	}

	$query = new WP_Query( array(
		'post_type'      => 'project',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'meta_key'       => 'project_order',
		'orderby'        => array(
			'meta_value_num' => 'ASC',
			'date'           => 'DESC',
		),
		'meta_query'     => $meta_query,
	) );

	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * @return WP_Post[]
 */
function almasa_get_services() {
	$query = new WP_Query( array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * ID of a published page, by title.
 *
 * @param string $title Page title.
 * @return int
 */
function almasa_page_id( $title ) {
	$query = new WP_Query( array(
		'post_type'      => 'page',
		'title'          => $title,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'fields'         => 'ids',
	) );
	return $query->posts ? (int) $query->posts[0] : 0;
}

/**
 * Featured image ID of a published page, by title.
 *
 * @param string $title Page title.
 * @return int
 */
function almasa_page_thumb_id( $title ) {
	$page_id = almasa_page_id( $title );
	return $page_id ? (int) get_post_thumbnail_id( $page_id ) : 0;
}

/**
 * About section small image: first project thumbnail that differs from the main image.
 *
 * @param int $main_id Main image ID.
 * @return int
 */
function almasa_intro_side_image_id( $main_id ) {
	foreach ( almasa_get_projects( 50 ) as $project ) {
		$thumb = (int) get_post_thumbnail_id( $project );
		if ( $thumb && $thumb !== (int) $main_id ) {
			return $thumb;
		}
	}
	return 0;
}

/**
 * Contact CTA background: "تواصل معنا" featured image, or "الرئيسية" on the contact page itself.
 *
 * @param int $post_id Page being displayed/edited.
 * @return int
 */
function almasa_contact_bg_id( $post_id ) {
	$contact_page = almasa_page_id( 'تواصل معنا' );
	return $contact_page && (int) $post_id === $contact_page ? almasa_page_thumb_id( 'الرئيسية' ) : almasa_page_thumb_id( 'تواصل معنا' );
}

/**
 * Post currently viewed, or edited in Elementor (editor page, preview iframe, editor AJAX).
 *
 * @return int
 */
function almasa_context_post_id() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	foreach ( array( 'editor_post_id', 'initial_document_id', 'elementor-preview' ) as $key ) {
		if ( ! empty( $_REQUEST[ $key ] ) ) {
			return absint( $_REQUEST[ $key ] );
		}
	}
	if ( is_admin() && ! empty( $_GET['post'] ) ) {
		return absint( $_GET['post'] );
	}
	// phpcs:enable
	$id = (int) get_queried_object_id();
	return $id ? $id : (int) get_the_ID();
}

/**
 * Hero slideshow attachments (media library flag), falling back to project thumbnails.
 *
 * @return int[]
 */
function almasa_hero_image_ids() {
	$ids = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 8,
		'meta_key'       => 'almasa_hero',
		'meta_value'     => '1',
		'orderby'        => array(
			'menu_order' => 'ASC',
			'ID'         => 'ASC',
		),
		'fields'         => 'ids',
	) );

	if ( $ids ) {
		return array_map( 'intval', $ids );
	}

	$fallback = array();
	foreach ( almasa_get_projects( 6 ) as $project ) {
		$thumb = (int) get_post_thumbnail_id( $project );
		if ( $thumb ) {
			$fallback[] = $thumb;
		}
	}
	return $fallback;
}

/**
 * Default gallery tabs: one per project that has gallery images.
 *
 * @return array<int, array{key:string, title:string, ids:int[]}>
 */
function almasa_gallery_tabs() {
	$tabs = array();
	foreach ( almasa_get_projects( 50 ) as $project ) {
		$ids = almasa_project_gallery_ids( $project->ID );
		if ( $ids ) {
			$tabs[] = array(
				'key'   => (string) $project->ID,
				'title' => get_the_title( $project ),
				'ids'   => array_map( 'intval', $ids ),
			);
		}
	}
	return $tabs;
}

/**
 * Flatten tabs into gallery items, interleaved so "الكل" mixes every tab.
 * Skips tabs without a title/images and attachments that no longer exist.
 *
 * @param array $tabs Tabs { key, title, ids[] }.
 * @return array<int, array{id:int, group:string, caption:string}>
 */
function almasa_interleave_gallery_tabs( array $tabs ) {
	$buckets = array();
	foreach ( $tabs as $tab ) {
		$ids = array_values( array_filter( array_map( 'intval', (array) ( $tab['ids'] ?? array() ) ), 'wp_attachment_is_image' ) );
		if ( $ids && '' !== trim( (string) ( $tab['title'] ?? '' ) ) ) {
			$buckets[] = array(
				'group'   => (string) $tab['key'],
				'caption' => trim( (string) $tab['title'] ),
				'ids'     => $ids,
			);
		}
	}

	$items = array();
	$round = 0;
	do {
		$added = false;
		foreach ( $buckets as $bucket ) {
			if ( isset( $bucket['ids'][ $round ] ) ) {
				$items[] = array(
					'id'      => $bucket['ids'][ $round ],
					'group'   => $bucket['group'],
					'caption' => $bucket['caption'],
				);
				$added = true;
			}
		}
		++$round;
	} while ( $added );

	return $items;
}

/**
 * Split the site name into a lead phrase (first two words) and the remainder.
 *
 * @return array{0:string,1:string}
 */
function almasa_split_site_name() {
	$words = preg_split( '/\s+/u', trim( (string) get_bloginfo( 'name' ) ) );
	if ( count( $words ) <= 2 ) {
		return array( implode( ' ', $words ), '' );
	}
	return array(
		implode( ' ', array_slice( $words, 0, 2 ) ),
		implode( ' ', array_slice( $words, 2 ) ),
	);
}

/**
 * Slider controls (progress + prev/next). Behaviour lives in theme.js [data-almasa-slider].
 *
 * @param bool $on_dark Light controls for dark sections.
 */
function almasa_slider_controls( $on_dark = false ) {
	?>
	<div class="almasa-slider__controls<?php echo $on_dark ? ' almasa-slider__controls--dark' : ''; ?>" data-slider-controls>
		<div class="almasa-slider__progress" aria-hidden="true"><span data-slider-bar></span></div>
		<div class="almasa-slider__arrows">
			<button type="button" class="almasa-slider__btn" data-slider-prev aria-label="<?php esc_attr_e( 'السابق', 'almasa' ); ?>">
				<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
			</button>
			<button type="button" class="almasa-slider__btn" data-slider-next aria-label="<?php esc_attr_e( 'التالي', 'almasa' ); ?>">
				<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M15 5l-7 7 7 7"/></svg>
			</button>
		</div>
	</div>
	<?php
}

/**
 * @param string $type almasa_location|almasa_contact|almasa_reason
 * @return WP_Post[]
 */
function almasa_get_structured( $type ) {
	$query = new WP_Query( array(
		'post_type'      => $type,
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

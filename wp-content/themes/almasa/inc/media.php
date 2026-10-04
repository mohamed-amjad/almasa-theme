<?php
/**
 * Media library flag: show attachment in the homepage hero slideshow.
 *
 * Meta key: almasa_hero (1). Order follows the attachment "menu_order".
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param array   $fields Fields.
 * @param WP_Post $post   Attachment.
 * @return array
 */
function almasa_attachment_fields( $fields, $post ) {
	if ( ! wp_attachment_is_image( $post->ID ) ) {
		return $fields;
	}

	$checked = get_post_meta( $post->ID, 'almasa_hero', true ) ? ' checked' : '';

	$fields['almasa_hero'] = array(
		'label' => 'عرض في الهيرو',
		'input' => 'html',
		'html'  => '<label><input type="checkbox" name="attachments[' . absint( $post->ID ) . '][almasa_hero]" value="1"' . $checked . '> ضمن صور واجهة الصفحة الرئيسية</label>',
	);

	$fields['almasa_hero_order'] = array(
		'label' => 'ترتيب الهيرو',
		'input' => 'text',
		'value' => (string) (int) $post->menu_order,
	);

	return $fields;
}
add_filter( 'attachment_fields_to_edit', 'almasa_attachment_fields', 10, 2 );

/**
 * @param array $post       Attachment data.
 * @param array $attachment Submitted fields.
 * @return array
 */
function almasa_attachment_fields_save( $post, $attachment ) {
	if ( ! current_user_can( 'edit_post', $post['ID'] ) ) {
		return $post;
	}

	if ( ! empty( $attachment['almasa_hero'] ) ) {
		update_post_meta( $post['ID'], 'almasa_hero', 1 );
	} else {
		delete_post_meta( $post['ID'], 'almasa_hero' );
	}

	if ( isset( $attachment['almasa_hero_order'] ) ) {
		$post['menu_order'] = (int) $attachment['almasa_hero_order'];
	}

	return $post;
}
add_filter( 'attachment_fields_to_save', 'almasa_attachment_fields_save', 10, 2 );

/**
 * Some uploads keep spaces in their file names; unencoded spaces break CSS `url()`
 * (Elementor media previews, background images).
 *
 * @param string $url Attachment URL.
 * @return string
 */
function almasa_encode_attachment_url( $url ) {
	return str_replace( ' ', '%20', (string) $url );
}
add_filter( 'wp_get_attachment_url', 'almasa_encode_attachment_url' );

/**
 * Intermediate sizes rebuild the URL from the raw file name.
 *
 * @param array|false $image [ url, width, height, is_intermediate ].
 * @return array|false
 */
function almasa_encode_attachment_src( $image ) {
	if ( is_array( $image ) && isset( $image[0] ) ) {
		$image[0] = almasa_encode_attachment_url( $image[0] );
	}
	return $image;
}
add_filter( 'wp_get_attachment_image_src', 'almasa_encode_attachment_src' );

/**
 * srcset is built from the metadata file names and must match the (encoded) src.
 *
 * @param array $meta Attachment metadata.
 * @return array
 */
function almasa_encode_srcset_meta( $meta ) {
	if ( ! empty( $meta['file'] ) ) {
		$meta['file'] = almasa_encode_attachment_url( $meta['file'] );
	}
	foreach ( (array) ( $meta['sizes'] ?? array() ) as $size => $data ) {
		if ( ! empty( $data['file'] ) ) {
			$meta['sizes'][ $size ]['file'] = almasa_encode_attachment_url( $data['file'] );
		}
	}
	return $meta;
}
add_filter( 'wp_calculate_image_srcset_meta', 'almasa_encode_srcset_meta' );

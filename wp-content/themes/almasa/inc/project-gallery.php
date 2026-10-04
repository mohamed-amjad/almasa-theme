<?php
/**
 * Project gallery metabox (attachment IDs). Works without ACF Pro.
 *
 * Meta key: project_gallery — same key if ACF Gallery is added later.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register metabox.
 */
function almasa_gallery_metabox() {
	add_meta_box(
		'almasa_project_gallery',
		'معرض المشروع',
		'almasa_gallery_metabox_render',
		'project',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'almasa_gallery_metabox' );

/**
 * @param WP_Post $post Post.
 */
function almasa_gallery_metabox_render( $post ) {
	wp_nonce_field( 'almasa_save_gallery', 'almasa_gallery_nonce' );
	$ids = almasa_get_gallery_ids( $post->ID );
	$ids_csv = implode( ',', $ids );
	?>
	<p>اختر صورًا من مكتبة الوسائط. الصورة البارزة تُدار من المربع الجانبي.</p>
	<input type="hidden" id="almasa-gallery-ids" name="almasa_gallery_ids" value="<?php echo esc_attr( $ids_csv ); ?>" />
	<p>
		<button type="button" class="button" id="almasa-gallery-select">اختيار الصور</button>
	</p>
	<ul id="almasa-gallery-preview" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;margin:0;padding:0;">
		<?php
		foreach ( $ids as $id ) {
			$thumb = wp_get_attachment_image( (int) $id, 'thumbnail' );
			if ( $thumb ) {
				echo '<li>' . $thumb . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
		?>
	</ul>
	<script>
	(function(){
		var btn = document.getElementById('almasa-gallery-select');
		if (!btn || typeof wp === 'undefined' || !wp.media) return;
		btn.addEventListener('click', function(e){
			e.preventDefault();
			var frame = wp.media({ title: 'معرض المشروع', multiple: true, library: { type: 'image' } });
			frame.on('select', function(){
				var ids = frame.state().get('selection').map(function(att){ return att.id; });
				document.getElementById('almasa-gallery-ids').value = ids.join(',');
				var preview = document.getElementById('almasa-gallery-preview');
				preview.innerHTML = '';
				frame.state().get('selection').each(function(att){
					var url = att.get('sizes') && att.get('sizes').thumbnail ? att.get('sizes').thumbnail.url : att.get('url');
					var li = document.createElement('li');
					var img = document.createElement('img');
					img.src = url;
					img.width = 80;
					img.height = 80;
					li.appendChild(img);
					preview.appendChild(li);
				});
			});
			frame.open();
		});
	})();
	</script>
	<?php
}

/**
 * Enqueue media on project screens.
 *
 * @param string $hook Hook.
 */
function almasa_gallery_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || 'project' !== $screen->post_type ) {
		return;
	}
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'almasa_gallery_admin_assets' );

/**
 * Save gallery IDs.
 *
 * @param int $post_id Post ID.
 */
function almasa_save_gallery( $post_id ) {
	if ( ! isset( $_POST['almasa_gallery_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['almasa_gallery_nonce'] ) ), 'almasa_save_gallery' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'project' !== get_post_type( $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['almasa_gallery_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['almasa_gallery_ids'] ) ) : '';
	$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
	update_post_meta( $post_id, 'project_gallery', $ids );
}
add_action( 'save_post_project', 'almasa_save_gallery' );

/**
 * @param int $post_id Project ID.
 * @return int[]
 */
function almasa_get_gallery_ids( $post_id ) {
	$ids = get_post_meta( $post_id, 'project_gallery', true );
	if ( ! is_array( $ids ) ) {
		return array();
	}
	return array_values( array_filter( array_map( 'absint', $ids ) ) );
}

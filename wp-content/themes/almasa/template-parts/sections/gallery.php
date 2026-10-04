<?php
/**
 * Photo gallery — equal tiles of project gallery images with filters + lightbox.
 *
 * Args: tabs (array of { key, title, ids[] } — Elementor-managed filter tabs; default = one tab
 * per project from its gallery), project_id (int, single project only), limit (initial visible
 * count), head (bool), kicker, title, filters (bool), all_text, more_text.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$tabs         = isset( $args['tabs'] ) && is_array( $args['tabs'] ) ? $args['tabs'] : null;
$project_id   = (int) almasa_arg( $args, 'project_id', 0 );
$limit        = max( 1, (int) almasa_arg( $args, 'limit', 12 ) );
$show_head    = (bool) almasa_arg( $args, 'head', true );
$kicker       = (string) almasa_arg( $args, 'kicker', __( 'من مواقع العمل', 'almasa' ) );
$title        = (string) almasa_arg( $args, 'title', __( 'معرض الصور', 'almasa' ) );
$show_filters = (bool) almasa_arg( $args, 'filters', true );
$all_text     = (string) almasa_arg( $args, 'all_text', __( 'الكل', 'almasa' ) );
$more_text    = (string) almasa_arg( $args, 'more_text', __( 'عرض المزيد من الصور', 'almasa' ) );

if ( null === $tabs ) {
	$tabs = $project_id ? array(
		array(
			'key'   => (string) $project_id,
			'title' => get_the_title( $project_id ),
			'ids'   => almasa_project_gallery_ids( $project_id ),
		),
	) : almasa_gallery_tabs();
}

$items   = almasa_interleave_gallery_tabs( $tabs );
$filters = array();
foreach ( $items as $item ) {
	if ( ! isset( $filters[ $item['group'] ] ) ) {
		$filters[ $item['group'] ] = array(
			'title' => $item['caption'],
			'count' => 0,
		);
	}
	++$filters[ $item['group'] ]['count'];
}

if ( ! $items ) {
	return;
}
?>
<section class="almasa-section almasa-gallery-section" id="gallery" data-almasa-gallery data-limit="<?php echo esc_attr( (string) $limit ); ?>">
	<div class="almasa-shell">
		<?php if ( $show_head ) : ?>
			<div class="almasa-gallery-head">
				<?php if ( '' !== $kicker ) : ?>
					<p class="almasa-kicker" data-reveal><?php echo esc_html( $kicker ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $title ) : ?>
					<h2 class="almasa-section-title" data-reveal style="--reveal-delay:80ms"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $show_filters && count( $filters ) > 1 ) : ?>
					<div class="almasa-filters" role="group" aria-label="<?php esc_attr_e( 'تصفية حسب المشروع', 'almasa' ); ?>" data-reveal style="--reveal-delay:140ms">
						<button type="button" class="almasa-filter is-active" data-almasa-filter="all" aria-pressed="true">
							<?php echo esc_html( $all_text ); ?> <span><?php echo esc_html( (string) count( $items ) ); ?></span>
						</button>
						<?php foreach ( $filters as $group => $filter ) : ?>
							<button type="button" class="almasa-filter" data-almasa-filter="<?php echo esc_attr( (string) $group ); ?>" aria-pressed="false">
								<?php echo esc_html( $filter['title'] ); ?> <span><?php echo esc_html( (string) $filter['count'] ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ul class="almasa-photos">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$full  = wp_get_attachment_image_url( $item['id'], 'full' );
				$title = $item['caption'];
				?>
				<li class="almasa-photos__item" data-group="<?php echo esc_attr( (string) $item['group'] ); ?>">
					<a class="almasa-photos__link" href="<?php echo esc_url( $full ); ?>" data-almasa-lightbox data-caption="<?php echo esc_attr( $title ); ?>">
						<?php
						echo wp_get_attachment_image( $item['id'], 'almasa-project-card', false, array(
							'alt'     => $title,
							'loading' => 'lazy',
							'sizes'   => '(min-width: 64rem) 33vw, (min-width: 36rem) 50vw, 100vw',
						) );
						?>
						<span class="almasa-photos__overlay" aria-hidden="true">
							<span class="almasa-photos__caption"><?php echo esc_html( $title ); ?></span>
							<span class="almasa-photos__zoom">
								<svg viewBox="0 0 24 24" width="20" height="20"><path fill="none" stroke="currentColor" stroke-width="2" d="M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm9 16-4-4M11 8v6M8 11h6"/></svg>
							</span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="almasa-gallery-more">
			<button type="button" class="almasa-btn almasa-btn--outline" data-almasa-more hidden><?php echo esc_html( $more_text ); ?></button>
		</div>
	</div>
</section>

<?php
/**
 * Why choose us — almasa_reason CPT (title + excerpt, ordered by menu_order).
 *
 * Args (optional overrides): kicker, title, image (int ID), show_image (bool).
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$reasons = almasa_get_structured( 'almasa_reason' );
if ( ! $reasons ) {
	return;
}
$kicker = (string) almasa_arg( $args, 'kicker', __( 'ما يميزنا', 'almasa' ) );
$title  = (string) almasa_arg( $args, 'title', __( 'لماذا تختارنا', 'almasa' ) );
$image  = 0;
if ( almasa_arg( $args, 'show_image', true ) ) {
	$image = ! empty( $args['image'] ) ? (int) $args['image'] : almasa_page_thumb_id( 'خدماتنا' );
}
?>
<section class="almasa-section almasa-why" id="why">
	<div class="almasa-shell">
		<?php if ( '' !== $kicker || '' !== $title ) : ?>
			<div class="almasa-section-head">
				<div>
					<?php if ( '' !== $kicker ) : ?>
						<p class="almasa-kicker almasa-kicker--on-dark" data-reveal><?php echo esc_html( $kicker ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $title ) : ?>
						<h2 class="almasa-section-title" data-reveal style="--reveal-delay:80ms"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="almasa-why__grid<?php echo $image ? ' almasa-why__grid--media' : ''; ?>">
			<?php if ( $image ) : ?>
				<figure class="almasa-why__media" data-reveal>
					<?php echo wp_get_attachment_image( $image, 'large', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(min-width: 64rem) 30vw, 100vw' ) ); ?>
				</figure>
			<?php endif; ?>
			<ol class="almasa-why__list">
				<?php foreach ( $reasons as $n => $reason ) : ?>
					<li class="almasa-reason" data-reveal style="--reveal-delay:<?php echo esc_attr( (string) ( $n * 90 ) ); ?>ms">
						<span class="almasa-reason__num"><?php echo esc_html( str_pad( (string) ( $n + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div>
							<h3><?php echo esc_html( get_the_title( $reason ) ); ?></h3>
							<?php if ( has_excerpt( $reason ) ) : ?>
								<p><?php echo esc_html( get_the_excerpt( $reason ) ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>

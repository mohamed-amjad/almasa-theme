<?php
/**
 * Services — image cards from the service CPT (featured image per service).
 *
 * Args: layout ('slider'|'grid'), head (bool), kicker, title, show_link (bool),
 * link_text, link_url, autoplay (ms, 0 = off).
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$services = almasa_get_services();
if ( ! $services ) {
	return;
}
$show_head = (bool) almasa_arg( $args, 'head', true );
$is_slider = 'grid' !== almasa_arg( $args, 'layout', 'slider' );
$kicker    = (string) almasa_arg( $args, 'kicker', __( 'ماذا نقدم', 'almasa' ) );
$title     = (string) almasa_arg( $args, 'title', __( 'خدماتنا', 'almasa' ) );
$link_text = (string) almasa_arg( $args, 'link_text', __( 'كل الخدمات', 'almasa' ) );
$link_url  = (bool) almasa_arg( $args, 'show_link', is_front_page() ) && '' !== $link_text ? almasa_arg_url( $args, 'link_url', 'خدماتنا' ) : '';
$autoplay  = (int) almasa_arg( $args, 'autoplay', 5000 );
?>
<section class="almasa-section almasa-services" id="services">
	<div class="almasa-shell">
		<?php if ( $show_head && ( '' !== $kicker || '' !== $title || $link_url ) ) : ?>
			<div class="almasa-section-head">
				<div>
					<?php if ( '' !== $kicker ) : ?>
						<p class="almasa-kicker almasa-kicker--on-dark" data-reveal><?php echo esc_html( $kicker ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $title ) : ?>
						<h2 class="almasa-section-title" data-reveal style="--reveal-delay:80ms"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
				</div>
				<?php if ( $link_url ) : ?>
					<a class="almasa-text-link almasa-text-link--light" href="<?php echo esc_url( $link_url ); ?>" data-reveal><?php echo esc_html( $link_text ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $is_slider ) : ?>
			<div class="almasa-slider almasa-slider--services" data-almasa-slider data-autoplay="<?php echo esc_attr( (string) $autoplay ); ?>" data-reveal>
				<ol class="almasa-slider__track" data-slider-track>
		<?php else : ?>
				<ol class="almasa-services__grid">
		<?php endif; ?>
			<?php foreach ( $services as $n => $service ) : ?>
				<?php
				$thumb   = (int) get_post_thumbnail_id( $service );
				$summary = almasa_get_field( 'service_summary', $service->ID );
				?>
				<li class="almasa-service-card<?php echo $thumb ? '' : ' almasa-service-card--plain'; ?>" data-reveal style="--reveal-delay:<?php echo esc_attr( (string) ( $n * 90 ) ); ?>ms">
					<?php if ( $thumb ) : ?>
						<div class="almasa-service-card__media">
							<?php echo wp_get_attachment_image( $thumb, 'almasa-project-card', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(min-width: 64rem) 33vw, (min-width: 48rem) 50vw, 90vw' ) ); ?>
						</div>
					<?php endif; ?>
					<div class="almasa-service-card__body">
						<span class="almasa-service-card__index"><?php echo esc_html( str_pad( (string) ( $n + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( get_the_title( $service ) ); ?></h3>
						<?php if ( $summary ) : ?>
							<p><?php echo esc_html( $summary ); ?></p>
						<?php endif; ?>
						<span class="almasa-service-card__line" aria-hidden="true"></span>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php if ( $is_slider ) : ?>
				<?php almasa_slider_controls( true ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>

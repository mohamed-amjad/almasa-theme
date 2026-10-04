<?php
/**
 * Projects — equal cards as a slider (home) or grid (projects page / archive).
 *
 * Args: projects (WP_Post[]), limit (int), featured (bool), layout ('slider'|'grid'),
 * head (bool), kicker, title, show_link (bool), link_text, link_url, autoplay (ms, 0 = off).
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$projects = isset( $args['projects'] ) ? $args['projects'] : almasa_get_projects( max( 1, (int) almasa_arg( $args, 'limit', 12 ) ), (bool) almasa_arg( $args, 'featured', false ) );
if ( ! $projects ) {
	return;
}
$show_head = (bool) almasa_arg( $args, 'head', true );
$is_slider = 'grid' !== almasa_arg( $args, 'layout', 'slider' );
$kicker    = (string) almasa_arg( $args, 'kicker', __( 'أعمالنا', 'almasa' ) );
$title     = (string) almasa_arg( $args, 'title', __( 'مشاريعنا', 'almasa' ) );
$link_text = (string) almasa_arg( $args, 'link_text', __( 'كل المشاريع', 'almasa' ) );
$link_url  = (bool) almasa_arg( $args, 'show_link', is_front_page() ) && '' !== $link_text ? almasa_arg_url( $args, 'link_url', 'مشاريعنا' ) : '';
$autoplay  = (int) almasa_arg( $args, 'autoplay', 6000 );
?>
<section class="almasa-section almasa-projects" id="projects">
	<div class="almasa-shell">
		<?php if ( $show_head && ( '' !== $kicker || '' !== $title || $link_url ) ) : ?>
			<div class="almasa-section-head">
				<div>
					<?php if ( '' !== $kicker ) : ?>
						<p class="almasa-kicker" data-reveal><?php echo esc_html( $kicker ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $title ) : ?>
						<h2 class="almasa-section-title" data-reveal style="--reveal-delay:80ms"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
				</div>
				<?php if ( $link_url ) : ?>
					<a class="almasa-text-link" href="<?php echo esc_url( $link_url ); ?>" data-reveal><?php echo esc_html( $link_text ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $is_slider ) : ?>
			<div class="almasa-slider almasa-slider--projects" data-almasa-slider data-autoplay="<?php echo esc_attr( (string) $autoplay ); ?>" data-reveal>
				<div class="almasa-slider__track" data-slider-track>
		<?php else : ?>
				<div class="almasa-cards-grid">
		<?php endif; ?>
			<?php
			foreach ( $projects as $index => $project ) {
				$card_args = array(
					'project' => $project,
					'index'   => $index,
				);
				if ( array_key_exists( 'card_cta', $args ) ) {
					$card_args['cta'] = $args['card_cta'];
				}
				get_template_part( 'template-parts/projects/card', null, $card_args );
			}
			?>
		</div>
		<?php if ( $is_slider ) : ?>
				<?php almasa_slider_controls(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>

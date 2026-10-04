<?php
/**
 * Home hero — crossfading slideshow of media flagged "عرض في الهيرو".
 *
 * Args (all optional, Elementor widget overrides): slides (int[]), interval (ms),
 * eyebrow, title_lead, title_rest, lead, primary_text, primary_url, secondary_text, secondary_url.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$slides = ! empty( $args['slides'] ) ? array_map( 'intval', (array) $args['slides'] ) : almasa_hero_image_ids();
list( $name_lead, $name_rest ) = almasa_split_site_name();

$eyebrow        = (string) almasa_arg( $args, 'eyebrow', __( 'مرحبًا بكم في', 'almasa' ) );
$title_lead     = (string) almasa_arg( $args, 'title_lead', $name_lead );
$title_rest     = (string) almasa_arg( $args, 'title_rest', $name_rest );
$lead           = (string) almasa_arg( $args, 'lead', get_bloginfo( 'description' ) );
$primary_text   = (string) almasa_arg( $args, 'primary_text', __( 'استكشف مشاريعنا', 'almasa' ) );
$secondary_text = (string) almasa_arg( $args, 'secondary_text', __( 'تواصل معنا', 'almasa' ) );
$interval       = max( 2000, (int) almasa_arg( $args, 'interval', 6000 ) );
?>
<section class="almasa-hero" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" data-almasa-hero data-interval="<?php echo esc_attr( (string) $interval ); ?>">
	<div class="almasa-hero__slides" aria-hidden="true">
		<?php foreach ( $slides as $i => $attachment_id ) : ?>
			<div class="almasa-hero__slide<?php echo 0 === $i ? ' is-active' : ''; ?>" data-almasa-slide>
				<?php
				echo wp_get_attachment_image(
					$attachment_id,
					'full',
					false,
					array(
						'alt'           => '',
						'loading'       => 0 === $i ? 'eager' : 'lazy',
						'fetchpriority' => 0 === $i ? 'high' : 'low',
						'decoding'      => 'async',
						'sizes'         => '100vw',
					)
				);
				?>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="almasa-hero__veil" aria-hidden="true"></div>
	<div class="almasa-hero__lines" aria-hidden="true"></div>

	<div class="almasa-shell almasa-hero__content">
		<?php if ( '' !== $eyebrow ) : ?>
			<p class="almasa-eyebrow" data-reveal><span class="almasa-eyebrow__bar" aria-hidden="true"></span><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $title_lead || '' !== $title_rest ) : ?>
			<h1 class="almasa-hero__title" data-reveal style="--reveal-delay:120ms">
				<?php if ( '' !== $title_lead ) : ?>
					<span class="almasa-hero__title-lead"><?php echo esc_html( $title_lead ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $title_rest ) : ?>
					<span class="almasa-hero__title-rest"><?php echo esc_html( $title_rest ); ?></span>
				<?php endif; ?>
			</h1>
		<?php endif; ?>
		<?php if ( '' !== $lead ) : ?>
			<p class="almasa-hero__lead" data-reveal style="--reveal-delay:240ms"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $primary_text || '' !== $secondary_text ) : ?>
			<div class="almasa-hero__actions" data-reveal style="--reveal-delay:360ms">
				<?php if ( '' !== $primary_text ) : ?>
					<a class="almasa-btn almasa-btn--gold almasa-btn--lg" href="<?php echo esc_url( almasa_arg_url( $args, 'primary_url', 'مشاريعنا' ) ); ?>">
						<?php echo esc_html( $primary_text ); ?>
						<svg class="almasa-btn__icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M15 5l-7 7 7 7"/></svg>
					</a>
				<?php endif; ?>
				<?php if ( '' !== $secondary_text ) : ?>
					<a class="almasa-btn almasa-btn--glass almasa-btn--lg" href="<?php echo esc_url( almasa_arg_url( $args, 'secondary_url', 'تواصل معنا' ) ); ?>"><?php echo esc_html( $secondary_text ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

</section>

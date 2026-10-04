<?php
/**
 * Locations (almasa_location CPT).
 *
 * Args: head (bool), maps (bool — embed a map per location), kicker, title, zoom (int).
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$locations = almasa_get_structured( 'almasa_location' );
if ( ! $locations ) {
	return;
}
$show_head = (bool) almasa_arg( $args, 'head', true );
$with_maps = (bool) almasa_arg( $args, 'maps', false );
$kicker    = (string) almasa_arg( $args, 'kicker', __( 'أين تجدنا', 'almasa' ) );
$title     = (string) almasa_arg( $args, 'title', __( 'مواقعنا', 'almasa' ) );
$zoom      = min( 20, max( 3, (int) almasa_arg( $args, 'zoom', 15 ) ) );
?>
<section class="almasa-section almasa-locations<?php echo $with_maps ? ' almasa-locations--maps' : ''; ?>" id="locations">
	<div class="almasa-shell">
		<?php if ( $show_head && ( '' !== $kicker || '' !== $title ) ) : ?>
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
		<div class="almasa-locations__grid">
			<?php foreach ( $locations as $n => $location ) : ?>
				<?php
				$address = (string) almasa_get_field( 'address', $location->ID );
				$query   = trim( (string) almasa_get_field( 'map_query', $location->ID ) );
				if ( '' === $query ) {
					$query = trim( get_the_title( $location ) . ' ' . preg_replace( '/\s+/u', ' ', $address ) );
				}
				?>
				<article class="almasa-place" data-reveal style="--reveal-delay:<?php echo esc_attr( (string) ( $n * 120 ) ); ?>ms">
					<?php if ( $with_maps ) : ?>
						<div class="almasa-place__map">
							<iframe
								src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $query ) . '&hl=ar&z=' . $zoom . '&output=embed' ); ?>"
								title="<?php echo esc_attr( sprintf( 'خريطة %s', get_the_title( $location ) ) ); ?>"
								loading="lazy"
								referrerpolicy="no-referrer-when-downgrade"
								allowfullscreen></iframe>
						</div>
					<?php endif; ?>
					<div class="almasa-place__body">
						<span class="almasa-place__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $n + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="almasa-place__pin" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
						</span>
						<h3><?php echo esc_html( get_the_title( $location ) ); ?></h3>
						<?php if ( $address ) : ?>
							<p><?php echo nl2br( esc_html( $address ) ); ?></p>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

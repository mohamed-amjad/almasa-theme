<?php
/**
 * Vision & mission — ACF fields on the "من نحن" page (about_vision, about_mission).
 *
 * Args (optional overrides): vision_title, mission_title, vision_text, mission_text.
 * Texts fall back to the ACF fields when absent or empty.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$about_id = almasa_page_id( 'من نحن' );

$vision  = trim( (string) almasa_arg( $args, 'vision_text', '' ) );
$mission = trim( (string) almasa_arg( $args, 'mission_text', '' ) );
if ( '' === $vision && $about_id ) {
	$vision = (string) almasa_get_field( 'about_vision', $about_id );
}
if ( '' === $mission && $about_id ) {
	$mission = (string) almasa_get_field( 'about_mission', $about_id );
}

$items = array_filter( array(
	array( (string) almasa_arg( $args, 'vision_title', 'الرؤية' ), $vision, 'M12 5C6 5 2 12 2 12s4 7 10 7 10-7 10-7-4-7-10-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8z' ),
	array( (string) almasa_arg( $args, 'mission_title', 'الرسالة' ), $mission, 'M12 2 3 7v6c0 5 3.8 8.7 9 9 5.2-.3 9-4 9-9V7l-9-5zm-1.2 14.2-3.5-3.5 1.4-1.4 2.1 2.1 4.9-4.9 1.4 1.4-6.3 6.3z' ),
), static function ( $item ) {
	return '' !== trim( $item[1] );
} );
if ( ! $items ) {
	return;
}
?>
<section class="almasa-section almasa-values" id="values">
	<div class="almasa-shell almasa-values__grid">
		<?php foreach ( array_values( $items ) as $n => $item ) : ?>
			<article class="almasa-value<?php echo $n ? ' almasa-value--gold' : ''; ?>" data-reveal style="--reveal-delay:<?php echo esc_attr( (string) ( $n * 120 ) ); ?>ms">
				<span class="almasa-value__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="26" height="26"><path fill="currentColor" d="<?php echo esc_attr( $item[2] ); ?>"/></svg>
				</span>
				<?php if ( '' !== $item[0] ) : ?>
					<h2 class="almasa-value__title"><?php echo esc_html( $item[0] ); ?></h2>
				<?php endif; ?>
				<p class="almasa-value__text"><?php echo esc_html( $item[1] ); ?></p>
			</article>
		<?php endforeach; ?>
	</div>
</section>

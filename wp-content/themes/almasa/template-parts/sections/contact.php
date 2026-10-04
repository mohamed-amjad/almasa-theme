<?php
/**
 * Contact CTA — full-bleed image ("تواصل معنا" page featured image) + engineers' phones.
 *
 * Args (optional overrides): kicker, head (bool), title, lead, image (int ID), show_image (bool).
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$contacts = almasa_get_structured( 'almasa_contact' );
if ( ! $contacts ) {
	return;
}
$bg = 0;
if ( almasa_arg( $args, 'show_image', true ) ) {
	if ( ! empty( $args['image'] ) ) {
		$bg = (int) $args['image'];
	} else {
		$bg = almasa_contact_bg_id( (int) get_queried_object_id() );
	}
}
$kicker = (string) almasa_arg( $args, 'kicker', __( 'اتصل مباشرة', 'almasa' ) );
$title  = almasa_arg( $args, 'head', true ) ? (string) almasa_arg( $args, 'title', __( 'تواصل معنا', 'almasa' ) ) : '';
$lead   = (string) almasa_arg( $args, 'lead', get_bloginfo( 'description' ) );
?>
<section class="almasa-contact<?php echo $bg ? '' : ' almasa-contact--plain'; ?>" id="contact">
	<?php if ( $bg ) : ?>
		<div class="almasa-contact__media" aria-hidden="true">
			<?php echo wp_get_attachment_image( $bg, 'full', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '100vw' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="almasa-contact__veil" aria-hidden="true"></div>

	<div class="almasa-shell almasa-contact__inner">
		<div class="almasa-contact__text">
			<?php if ( '' !== $kicker ) : ?>
				<p class="almasa-kicker almasa-kicker--on-dark" data-reveal><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h2 class="almasa-contact__title" data-reveal style="--reveal-delay:80ms"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $lead ) : ?>
				<p class="almasa-contact__lead" data-reveal style="--reveal-delay:160ms"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</div>

		<ul class="almasa-contact__list">
			<?php foreach ( $contacts as $n => $contact ) : ?>
				<?php
				$phone = (string) almasa_get_field( 'phone', $contact->ID );
				$href  = almasa_tel_href( $phone );
				?>
				<li data-reveal style="--reveal-delay:<?php echo esc_attr( (string) ( 200 + $n * 100 ) ); ?>ms">
					<?php if ( $href ) : ?>
						<a class="almasa-contact__card" href="<?php echo esc_url( $href ); ?>">
					<?php else : ?>
						<div class="almasa-contact__card">
					<?php endif; ?>
						<span class="almasa-contact__icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
						</span>
						<span class="almasa-contact__who">
							<span class="almasa-contact__name"><?php echo esc_html( get_the_title( $contact ) ); ?></span>
							<?php if ( $phone ) : ?>
								<span class="almasa-contact__tel" dir="ltr"><?php echo esc_html( $phone ); ?></span>
							<?php endif; ?>
						</span>
						<?php if ( $href ) : ?>
							<span class="almasa-contact__go" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18"><path fill="none" stroke="currentColor" stroke-width="2" d="M15 5l-7 7 7 7"/></svg></span>
						<?php endif; ?>
					<?php echo $href ? '</a>' : '</div>'; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<?php
/**
 * Site footer.
 *
 * Args (optional overrides): menu (nav menu ID; default "footer" location), tagline (bool),
 * nav_title, locations_title, contact_title, social (bool), copyright.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$nav_title       = (string) almasa_arg( $args, 'nav_title', __( 'تصفح', 'almasa' ) );
$locations_title = (string) almasa_arg( $args, 'locations_title', __( 'مواقعنا', 'almasa' ) );
$contact_title   = (string) almasa_arg( $args, 'contact_title', __( 'تواصل', 'almasa' ) );
$copyright       = trim( (string) almasa_arg( $args, 'copyright', '' ) );
$menu_id         = (int) almasa_arg( $args, 'menu', 0 );
$menu_args       = array(
	'container'   => false,
	'fallback_cb' => false,
);
if ( $menu_id ) {
	$menu_args['menu'] = $menu_id;
} else {
	$menu_args['theme_location'] = 'footer';
}
?>
<footer class="almasa-site-footer" role="contentinfo">
	<div class="almasa-shell almasa-footer-grid">
		<div class="almasa-footer-brand">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			}
			?>
			<p class="almasa-footer-name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<?php if ( almasa_arg( $args, 'tagline', true ) && get_bloginfo( 'description' ) ) : ?>
				<p class="almasa-footer-tag"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
			<?php endif; ?>
			<?php if ( almasa_arg( $args, 'social', false ) ) : ?>
				<?php almasa_social_list( 'almasa-social--footer' ); ?>
			<?php endif; ?>
		</div>
		<nav class="almasa-footer-nav" aria-label="<?php esc_attr_e( 'قائمة التذييل', 'almasa' ); ?>">
			<?php if ( '' !== $nav_title ) : ?>
				<p class="almasa-footer-title"><?php echo esc_html( $nav_title ); ?></p>
			<?php endif; ?>
			<?php wp_nav_menu( $menu_args ); ?>
		</nav>
		<div class="almasa-footer-col">
			<?php if ( '' !== $locations_title ) : ?>
				<p class="almasa-footer-title"><?php echo esc_html( $locations_title ); ?></p>
			<?php endif; ?>
			<?php
			foreach ( almasa_get_structured( 'almasa_location' ) as $location ) {
				echo '<p class="almasa-footer-item"><strong>' . esc_html( get_the_title( $location ) ) . '</strong>';
				echo nl2br( esc_html( (string) almasa_get_field( 'address', $location->ID ) ) );
				echo '</p>';
			}
			?>
		</div>
		<div class="almasa-footer-col">
			<?php if ( '' !== $contact_title ) : ?>
				<p class="almasa-footer-title"><?php echo esc_html( $contact_title ); ?></p>
			<?php endif; ?>
			<?php
			foreach ( almasa_get_structured( 'almasa_contact' ) as $contact ) {
				$phone = (string) almasa_get_field( 'phone', $contact->ID );
				$href  = almasa_tel_href( $phone );
				echo '<p class="almasa-footer-item"><strong>' . esc_html( get_the_title( $contact ) ) . '</strong>';
				if ( $href ) {
					echo '<a class="almasa-footer-tel" dir="ltr" href="' . esc_url( $href ) . '">' . esc_html( $phone ) . '</a>';
				}
				echo '</p>';
			}
			?>
		</div>
	</div>
	<div class="almasa-footer-copy">
		<div class="almasa-shell almasa-footer-copy__inner">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( '' !== $copyright ? $copyright : get_bloginfo( 'name' ) ); ?></p>
			<a class="almasa-to-top" href="#top" data-almasa-top aria-label="<?php esc_attr_e( 'العودة للأعلى', 'almasa' ); ?>">
				<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
			</a>
		</div>
	</div>
</footer>

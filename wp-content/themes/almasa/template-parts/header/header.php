<?php
/**
 * Site header — fixed, transparent over heroes, solid on scroll.
 *
 * Args (optional overrides): menu (nav menu ID; default "primary" location),
 * phone (bool), cta_text, cta_url.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$contacts    = almasa_get_structured( 'almasa_contact' );
$first_phone = $contacts && almasa_arg( $args, 'phone', true ) ? (string) almasa_get_field( 'phone', $contacts[0]->ID ) : '';
$cta_text    = (string) almasa_arg( $args, 'cta_text', __( 'تواصل معنا', 'almasa' ) );
$menu_id     = (int) almasa_arg( $args, 'menu', 0 );
$menu_args   = array(
	'container'   => false,
	'fallback_cb' => false,
);
if ( $menu_id ) {
	$menu_args['menu'] = $menu_id;
} else {
	$menu_args['theme_location'] = 'primary';
}
?>
<header class="almasa-site-header" role="banner" data-almasa-header>
	<div class="almasa-shell almasa-site-header__inner">
		<div class="almasa-brand">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<a class="almasa-brand__text" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
			}
			?>
		</div>

		<nav id="almasa-primary-nav" class="almasa-nav" aria-label="<?php esc_attr_e( 'القائمة الرئيسية', 'almasa' ); ?>" data-almasa-nav>
			<?php wp_nav_menu( $menu_args ); ?>
			<div class="almasa-nav__footer">
				<?php foreach ( $contacts as $contact ) : ?>
					<?php
					$phone = (string) almasa_get_field( 'phone', $contact->ID );
					$href  = almasa_tel_href( $phone );
					if ( ! $href ) {
						continue;
					}
					?>
					<a class="almasa-nav__tel" href="<?php echo esc_url( $href ); ?>">
						<span><?php echo esc_html( get_the_title( $contact ) ); ?></span>
						<strong dir="ltr"><?php echo esc_html( $phone ); ?></strong>
					</a>
				<?php endforeach; ?>
			</div>
		</nav>

		<div class="almasa-site-header__actions">
			<?php if ( $first_phone && almasa_tel_href( $first_phone ) ) : ?>
				<a class="almasa-header-phone" href="<?php echo esc_url( almasa_tel_href( $first_phone ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $contacts[0] ) ); ?>">
					<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
					<span dir="ltr"><?php echo esc_html( $first_phone ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( '' !== $cta_text ) : ?>
				<a class="almasa-btn almasa-btn--gold almasa-header-cta" href="<?php echo esc_url( almasa_arg_url( $args, 'cta_url', 'تواصل معنا' ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
			<?php endif; ?>
			<button class="almasa-nav-toggle" type="button" data-almasa-nav-toggle aria-expanded="false" aria-controls="almasa-primary-nav">
				<span class="almasa-nav-toggle__bars" aria-hidden="true"><span></span><span></span><span></span></span>
				<span class="screen-reader-text"><?php esc_html_e( 'القائمة', 'almasa' ); ?></span>
			</button>
		</div>
	</div>
</header>

<?php
/**
 * Social media links — Customizer (المظهر ← تخصيص ← السوشيال ميديا).
 *
 * Networks with a saved URL render as links; until any URL is saved, all
 * networks render as placeholder icons.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Supported networks: key => [label, SVG path (24×24)].
 *
 * @return array<string,array{0:string,1:string}>
 */
function almasa_social_networks() {
	return array(
		'facebook'  => array( 'فيسبوك', 'M14 8h3V4h-3c-2.8 0-4 1.7-4 4.3V10H7v4h3v8h4v-8h3l1-4h-4V8.6c0-.4.3-.6.6-.6z' ),
		'instagram' => array( 'إنستجرام', 'M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 2a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM17.5 5.5a1 1 0 1 1 0 2 1 1 0 0 1 0-2z' ),
		'whatsapp'  => array( 'واتساب', 'M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 2a8 8 0 1 1-4.1 14.9l-.3-.2-2.9.8.8-2.8-.2-.3A8 8 0 0 1 12 4zm-3.2 4c-.2 0-.5 0-.7.3-.3.3-1 1-1 2.3s1 2.7 1.2 2.9c.1.2 2 3.1 4.9 4.3 2.4.9 2.9.8 3.4.7.5-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.4l-2-1c-.3-.1-.5-.1-.7.2l-.9 1.1c-.2.2-.3.2-.6.1-.3-.2-1.2-.4-2.3-1.4-.8-.8-1.4-1.7-1.6-2-.1-.3 0-.4.1-.6l.5-.5.3-.5c.1-.2 0-.4 0-.5l-.9-2.2c-.2-.5-.5-.5-.7-.5h-.5z' ),
		'tiktok'    => array( 'تيك توك', 'M16.5 3c.3 2.2 1.6 3.6 3.8 3.8v3.1c-1.4.1-2.6-.3-3.8-1v6.1c0 3.4-2.5 6-5.9 6-3.2 0-5.6-2.5-5.6-5.6 0-3.4 3-6 6.5-5.5v3.2c-1.6-.4-3.2.6-3.2 2.3 0 1.4 1.1 2.4 2.4 2.4 1.5 0 2.5-1 2.5-2.9V3h3.3z' ),
		'youtube'   => array( 'يوتيوب', 'M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.6 12a31 31 0 0 0 .4 3.8 3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1c.4-1.2.4-3.8.4-3.8s0-2.6-.4-3.8zM10 15V9l5.2 3L10 15z' ),
		'linkedin'  => array( 'لينكد إن', 'M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9.5h4V21H3V9.5zm6.5 0h3.8v1.6h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4V9.5z' ),
		'x'         => array( 'إكس', 'M17.8 3h3.1l-6.8 7.8L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.3-8.3L2 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z' ),
	);
}

/**
 * Customizer section + one URL setting per network.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function almasa_social_customize( $wp_customize ) {
	$wp_customize->add_section( 'almasa_social', array(
		'title'       => 'السوشيال ميديا',
		'description' => 'الصق رابط كل صفحة كاملًا. قبل إضافة أي رابط تظهر كل الأيقونات بدون روابط؛ بعد الإضافة تظهر الشبكات التي لها رابط فقط. لواتساب استخدم رابط wa.me.',
		'priority'    => 35,
	) );
	foreach ( almasa_social_networks() as $key => $network ) {
		$wp_customize->add_setting( 'almasa_social_' . $key, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'almasa_social_' . $key, array(
			'label'   => $network[0],
			'section' => 'almasa_social',
			'type'    => 'url',
		) );
	}
}
add_action( 'customize_register', 'almasa_social_customize' );

/**
 * Saved social links.
 *
 * @return array<int,array{key:string,label:string,url:string,icon:string}>
 */
function almasa_social_links() {
	$links = array();
	foreach ( almasa_social_networks() as $key => $network ) {
		$url = trim( (string) get_theme_mod( 'almasa_social_' . $key, '' ) );
		if ( '' !== $url ) {
			$links[] = array(
				'key'   => $key,
				'label' => $network[0],
				'url'   => $url,
				'icon'  => $network[1],
			);
		}
	}
	return $links;
}

/**
 * Placeholder icons (no URL) shown until at least one link is saved.
 *
 * @return array<int,array{key:string,label:string,url:string,icon:string}>
 */
function almasa_social_placeholders() {
	$items = array();
	foreach ( almasa_social_networks() as $key => $network ) {
		$items[] = array(
			'key'   => $key,
			'label' => $network[0],
			'url'   => '',
			'icon'  => $network[1],
		);
	}
	return $items;
}

/**
 * Print the social icon list. Saved links render as links; with none saved,
 * every network renders as a non-clickable placeholder icon.
 *
 * @param string $class Extra class on the wrapper.
 */
function almasa_social_list( $class = '' ) {
	$links = almasa_social_links();
	if ( ! $links ) {
		$links = almasa_social_placeholders();
	}
	?>
	<ul class="almasa-social <?php echo esc_attr( $class ); ?>">
		<?php foreach ( $links as $link ) : ?>
			<li>
				<?php if ( $link['url'] ) : ?>
					<a class="almasa-social__link almasa-social__link--<?php echo esc_attr( $link['key'] ); ?>" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $link['label'] ); ?>">
						<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="<?php echo esc_attr( $link['icon'] ); ?>"/></svg>
					</a>
				<?php else : ?>
					<span class="almasa-social__link almasa-social__link--<?php echo esc_attr( $link['key'] ); ?> is-placeholder" title="<?php echo esc_attr( $link['label'] ); ?>" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="<?php echo esc_attr( $link['icon'] ); ?>"/></svg>
					</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

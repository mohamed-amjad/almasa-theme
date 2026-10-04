<?php
/**
 * Inner page hero — featured image background, breadcrumb, title.
 *
 * Args: title (string), image_id (int), kicker (string), crumbs (bool), home_text (string).
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$title     = (string) almasa_arg( $args, 'title', get_the_title() );
$image_id  = (int) almasa_arg( $args, 'image_id', 0 );
$kicker    = (string) almasa_arg( $args, 'kicker', '' );
$home_text = (string) almasa_arg( $args, 'home_text', __( 'الرئيسية', 'almasa' ) );
?>
<header class="almasa-page-hero<?php echo $image_id ? '' : ' almasa-page-hero--plain'; ?>">
	<?php if ( $image_id ) : ?>
		<div class="almasa-page-hero__media" aria-hidden="true">
			<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '100vw' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="almasa-page-hero__veil" aria-hidden="true"></div>
	<div class="almasa-shell almasa-page-hero__inner">
		<?php if ( almasa_arg( $args, 'crumbs', true ) && '' !== $home_text ) : ?>
			<nav class="almasa-crumbs" aria-label="<?php esc_attr_e( 'مسار التنقل', 'almasa' ); ?>" data-reveal>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $home_text ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html( $title ); ?></span>
			</nav>
		<?php endif; ?>
		<?php if ( '' !== $kicker ) : ?>
			<p class="almasa-kicker almasa-kicker--on-dark" data-reveal style="--reveal-delay:60ms"><?php echo esc_html( $kicker ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $title ) : ?>
			<h1 class="almasa-page-hero__title" data-reveal style="--reveal-delay:120ms"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
	</div>
</header>

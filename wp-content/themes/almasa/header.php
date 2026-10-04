<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> id="top">
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'تخطي إلى المحتوى', 'almasa' ); ?></a>
<?php almasa_render_site_part( 'header' ); ?>

<?php
/**
 * 404.
 *
 * @package Almasa
 */

get_header();
?>
<main id="main" class="almasa-main">
	<?php
	get_template_part( 'template-parts/sections/page-hero', null, array(
		'title' => __( 'الصفحة غير موجودة', 'almasa' ),
	) );
	?>
	<div class="almasa-section">
		<div class="almasa-shell almasa-404">
			<p class="almasa-404__code" aria-hidden="true">404</p>
			<a class="almasa-btn almasa-btn--gold" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'العودة للرئيسية', 'almasa' ); ?></a>
		</div>
	</div>
</main>
<?php
get_footer();

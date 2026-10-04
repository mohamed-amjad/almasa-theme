<?php
/**
 * Project card (equal 4:3 tile used in sliders and grids).
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$project = isset( $args['project'] ) ? $args['project'] : null;
if ( ! $project instanceof WP_Post ) {
	return;
}
$index   = isset( $args['index'] ) ? (int) $args['index'] : 0;
$work    = almasa_get_field( 'work_type', $project->ID );
$company = almasa_get_field( 'associated_company', $project->ID );
$loc     = almasa_get_field( 'project_location', $project->ID );
$thumb   = (int) get_post_thumbnail_id( $project );
$photos  = count( almasa_project_gallery_ids( $project->ID ) );
$cta     = (string) almasa_arg( $args, 'cta', __( 'تفاصيل المشروع', 'almasa' ) );
?>
<article class="almasa-card<?php echo $thumb ? '' : ' almasa-card--pattern'; ?>" data-reveal style="--reveal-delay:<?php echo esc_attr( (string) ( $index * 100 ) ); ?>ms">
	<a class="almasa-card__link" href="<?php echo esc_url( get_permalink( $project ) ); ?>">
		<?php if ( $thumb ) : ?>
			<div class="almasa-card__media">
				<?php
				echo wp_get_attachment_image( $thumb, 'almasa-project-card', false, array(
					'alt'     => get_the_title( $project ),
					'loading' => 'lazy',
					'sizes'   => '(min-width: 64rem) 33vw, (min-width: 40rem) 50vw, 90vw',
				) );
				?>
			</div>
		<?php else : ?>
			<div class="almasa-card__pattern" aria-hidden="true"><span class="almasa-card__gem"></span></div>
		<?php endif; ?>
		<div class="almasa-card__shade" aria-hidden="true"></div>

		<div class="almasa-card__top">
			<span class="almasa-card__num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
			<?php if ( $photos ) : ?>
				<span class="almasa-chip almasa-chip--glass">
					<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path fill="currentColor" d="M4 5h3l2-2h6l2 2h3a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm8 3.5a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9z"/></svg>
					<?php echo esc_html( sprintf( '%d صورة', $photos ) ); ?>
				</span>
			<?php endif; ?>
		</div>

		<div class="almasa-card__body">
			<?php if ( $work ) : ?>
				<span class="almasa-chip almasa-chip--gold"><?php echo esc_html( $work ); ?></span>
			<?php endif; ?>
			<h3 class="almasa-card__title"><?php echo esc_html( get_the_title( $project ) ); ?></h3>
			<?php
			$bits = array_filter( array( $loc, $company ) );
			if ( $bits ) {
				echo '<p class="almasa-card__meta">' . esc_html( implode( ' — ', $bits ) ) . '</p>';
			}
			?>
			<?php if ( '' !== $cta ) : ?>
				<span class="almasa-card__cta">
					<?php echo esc_html( $cta ); ?>
					<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M15 5l-7 7 7 7"/></svg>
				</span>
			<?php endif; ?>
		</div>
	</a>
</article>

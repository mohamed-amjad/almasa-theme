<?php
/**
 * Fallback single project.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;
?>
<main id="main" class="almasa-main">
	<?php
	while ( have_posts() ) {
		the_post();
		$project_id = get_the_ID();
		$work       = almasa_get_field( 'work_type' );
		$loc        = almasa_get_field( 'project_location' );
		$company    = almasa_get_field( 'associated_company' );
		$subtitle   = almasa_get_field( 'project_subtitle' );
		$desc       = almasa_get_field( 'project_description' );
		$gallery    = almasa_project_gallery_ids( $project_id );
		$thumb      = (int) get_post_thumbnail_id();
		$works      = almasa_page_url( 'مشاريعنا' );
		?>
		<article class="almasa-project-single">
			<?php
			get_template_part( 'template-parts/sections/page-hero', null, array(
				'title'    => get_the_title(),
				'image_id' => $thumb,
				'kicker'   => $work ? $work : '',
			) );
			?>

			<div class="almasa-section almasa-project-single__intro">
				<div class="almasa-shell almasa-project-single__grid">
					<dl class="almasa-facts" data-reveal>
						<?php if ( $work ) : ?>
							<div><dt><?php esc_html_e( 'نوع العمل', 'almasa' ); ?></dt><dd><?php echo esc_html( $work ); ?></dd></div>
						<?php endif; ?>
						<?php if ( $loc ) : ?>
							<div><dt><?php esc_html_e( 'الموقع', 'almasa' ); ?></dt><dd><?php echo esc_html( $loc ); ?></dd></div>
						<?php endif; ?>
						<?php if ( $company ) : ?>
							<div><dt><?php esc_html_e( 'الجهة', 'almasa' ); ?></dt><dd><?php echo esc_html( $company ); ?></dd></div>
						<?php endif; ?>
						<?php if ( $gallery ) : ?>
							<div><dt><?php esc_html_e( 'صور المشروع', 'almasa' ); ?></dt><dd><?php echo esc_html( (string) count( $gallery ) ); ?></dd></div>
						<?php endif; ?>
					</dl>
					<?php
					$show_desc    = $desc && $desc !== $company;
					$show_content = '' !== trim( (string) get_the_content() );
					if ( $subtitle || $show_desc || $show_content ) :
						?>
						<div class="almasa-project-single__text" data-reveal style="--reveal-delay:120ms">
							<?php if ( $subtitle ) : ?>
								<p class="almasa-about__lead"><?php echo esc_html( $subtitle ); ?></p>
							<?php endif; ?>
							<?php
							if ( $show_desc ) {
								echo '<div class="almasa-prose">' . wp_kses_post( wpautop( $desc ) ) . '</div>';
							}
							if ( $show_content ) {
								echo '<div class="almasa-prose">';
								the_content();
								echo '</div>';
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php
			if ( $gallery ) {
				get_template_part( 'template-parts/sections/gallery', null, array(
					'project_id' => $project_id,
					'limit'      => 12,
				) );
			}

			$others = array_values( array_filter( almasa_get_projects( 10 ), static function ( $p ) use ( $project_id ) {
				return $p->ID !== $project_id;
			} ) );
			if ( $others ) {
				?>
				<section class="almasa-section almasa-more-projects">
					<div class="almasa-shell">
						<div class="almasa-section-head">
							<div>
								<p class="almasa-kicker" data-reveal><?php esc_html_e( 'استكشف أيضًا', 'almasa' ); ?></p>
								<h2 class="almasa-section-title" data-reveal><?php esc_html_e( 'مشاريع أخرى', 'almasa' ); ?></h2>
							</div>
							<?php if ( $works ) : ?>
								<a class="almasa-text-link" href="<?php echo esc_url( $works ); ?>"><?php esc_html_e( 'كل المشاريع', 'almasa' ); ?></a>
							<?php endif; ?>
						</div>
						<div class="almasa-cards-grid almasa-cards-grid--pair">
							<?php
							foreach ( array_slice( $others, 0, 2 ) as $i => $other ) {
								get_template_part( 'template-parts/projects/card', null, array(
									'project' => $other,
									'index'   => $i + 1,
								) );
							}
							?>
						</div>
					</div>
				</section>
				<?php
			}

			get_template_part( 'template-parts/sections/contact' );
			?>
		</article>
		<?php
	}
	?>
</main>

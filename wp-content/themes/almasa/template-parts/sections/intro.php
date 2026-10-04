<?php
/**
 * About — site identity, figures derived from content, management contacts.
 *
 * Images: "من نحن" page featured image + first project thumbnail.
 *
 * Args (optional overrides): kicker, title, lead, main_image, side_image (int IDs),
 * badge, checklist, figures, team (bool), label_projects, label_services, label_locations,
 * team_label, button (bool), button_text, button_url.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$projects  = almasa_get_projects( 50 );
$services  = almasa_get_services();
$locations = almasa_get_structured( 'almasa_location' );
$contacts  = almasa_get_structured( 'almasa_contact' );

$main_img = ! empty( $args['main_image'] ) ? (int) $args['main_image'] : almasa_page_thumb_id( 'من نحن' );
$side_img = ! empty( $args['side_image'] ) ? (int) $args['side_image'] : almasa_intro_side_image_id( $main_img );

$kicker      = (string) almasa_arg( $args, 'kicker', __( 'من نحن', 'almasa' ) );
$title       = (string) almasa_arg( $args, 'title', get_bloginfo( 'name' ) );
$lead        = (string) almasa_arg( $args, 'lead', get_bloginfo( 'description' ) );
$team_label  = (string) almasa_arg( $args, 'team_label', __( 'المهندسون', 'almasa' ) );
$button_text = (string) almasa_arg( $args, 'button_text', __( 'المزيد عن الشركة', 'almasa' ) );
$show_button = (bool) almasa_arg( $args, 'button', is_front_page() ) && '' !== $button_text;

$figures = array();
if ( almasa_arg( $args, 'figures', true ) ) {
	$figures = array_filter( array(
		array( count( $projects ), (string) almasa_arg( $args, 'label_projects', 'مشاريع' ) ),
		array( count( $services ), (string) almasa_arg( $args, 'label_services', 'مجالات عمل' ) ),
		array( count( $locations ), (string) almasa_arg( $args, 'label_locations', 'مقرات' ) ),
	), static function ( $figure ) {
		return $figure[0] > 0 && '' !== $figure[1];
	} );
}
?>
<section class="almasa-section almasa-about" id="intro">
	<div class="almasa-shell almasa-about__grid">
		<div class="almasa-about__media" data-reveal>
			<?php if ( $main_img ) : ?>
				<figure class="almasa-about__img almasa-about__img--main">
					<?php echo wp_get_attachment_image( $main_img, 'large', false, array( 'loading' => 'lazy', 'sizes' => '(min-width: 64rem) 36rem, 90vw' ) ); ?>
				</figure>
			<?php endif; ?>
			<?php if ( $side_img ) : ?>
				<figure class="almasa-about__img almasa-about__img--side">
					<?php echo wp_get_attachment_image( $side_img, 'almasa-project-card', false, array( 'loading' => 'lazy', 'sizes' => '(min-width: 64rem) 20rem, 50vw' ) ); ?>
				</figure>
			<?php endif; ?>
			<?php if ( almasa_arg( $args, 'badge', true ) && has_custom_logo() ) : ?>
				<div class="almasa-about__badge" aria-hidden="true">
					<?php echo wp_get_attachment_image( (int) get_theme_mod( 'custom_logo' ), 'medium', false, array( 'alt' => '' ) ); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="almasa-about__text">
			<?php if ( '' !== $kicker ) : ?>
				<p class="almasa-kicker" data-reveal><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h2 class="almasa-section-title" data-reveal style="--reveal-delay:80ms"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $lead ) : ?>
				<p class="almasa-about__lead" data-reveal style="--reveal-delay:160ms"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>

			<?php if ( $services && almasa_arg( $args, 'checklist', true ) ) : ?>
				<ul class="almasa-checklist" data-reveal style="--reveal-delay:220ms">
					<?php foreach ( $services as $service ) : ?>
						<li><?php echo esc_html( get_the_title( $service ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $figures ) : ?>
				<dl class="almasa-figures" data-reveal style="--reveal-delay:280ms">
					<?php foreach ( $figures as $figure ) : ?>
						<div>
							<dt><?php echo esc_html( $figure[1] ); ?></dt>
							<dd data-almasa-count="<?php echo esc_attr( (string) $figure[0] ); ?>"><?php echo esc_html( str_pad( (string) $figure[0], 2, '0', STR_PAD_LEFT ) ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php if ( $contacts && almasa_arg( $args, 'team', true ) ) : ?>
				<div class="almasa-about__team" data-reveal style="--reveal-delay:340ms">
					<?php if ( '' !== $team_label ) : ?>
						<p class="almasa-about__team-label"><?php echo esc_html( $team_label ); ?></p>
					<?php endif; ?>
					<ul>
						<?php foreach ( $contacts as $contact ) : ?>
							<li><?php echo esc_html( get_the_title( $contact ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $show_button ) : ?>
				<a class="almasa-btn almasa-btn--dark" href="<?php echo esc_url( almasa_arg_url( $args, 'button_url', 'من نحن' ) ); ?>" data-reveal style="--reveal-delay:400ms">
					<?php echo esc_html( $button_text ); ?>
					<svg class="almasa-btn__icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M15 5l-7 7 7 7"/></svg>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

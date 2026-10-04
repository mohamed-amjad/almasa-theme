<?php
/**
 * Contact page — engineers' phones + request form (inc/contact-form.php).
 *
 * Args (optional overrides): kicker, title, lead, social (bool), social_label,
 * form_title, form_hint, submit_text.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$contacts     = almasa_get_structured( 'almasa_contact' );
$services     = almasa_get_services();
$status       = isset( $_GET['almasa_form'] ) ? sanitize_key( wp_unslash( $_GET['almasa_form'] ) ) : '';
$kicker       = (string) almasa_arg( $args, 'kicker', __( 'اتصل مباشرة', 'almasa' ) );
$title        = (string) almasa_arg( $args, 'title', __( 'فريقنا في خدمتك', 'almasa' ) );
$lead         = (string) almasa_arg( $args, 'lead', get_bloginfo( 'description' ) );
$social_label = (string) almasa_arg( $args, 'social_label', __( 'تابعنا على', 'almasa' ) );
$form_title   = (string) almasa_arg( $args, 'form_title', __( 'أرسل طلبك', 'almasa' ) );
$form_hint    = (string) almasa_arg( $args, 'form_hint', __( 'املأ البيانات وسيتواصل معك فريقنا. الحقول المعلّمة بـ * مطلوبة.', 'almasa' ) );
$submit_text  = (string) almasa_arg( $args, 'submit_text', '' );
if ( '' === trim( $submit_text ) ) {
	$submit_text = __( 'إرسال الطلب', 'almasa' );
}
?>
<section class="almasa-section almasa-contact-page" id="contact-form">
	<div class="almasa-shell almasa-contact-page__grid">
		<aside class="almasa-contact-info" data-reveal>
			<?php if ( '' !== $kicker ) : ?>
				<p class="almasa-kicker almasa-kicker--on-dark"><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h2 class="almasa-contact-info__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $lead ) : ?>
				<p class="almasa-contact-info__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<ul class="almasa-contact__list">
				<?php foreach ( $contacts as $contact ) : ?>
					<?php
					$phone = (string) almasa_get_field( 'phone', $contact->ID );
					$href  = almasa_tel_href( $phone );
					if ( ! $href ) {
						continue;
					}
					?>
					<li>
						<a class="almasa-contact__card" href="<?php echo esc_url( $href ); ?>">
							<span class="almasa-contact__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
							</span>
							<span class="almasa-contact__who">
								<span class="almasa-contact__name"><?php echo esc_html( get_the_title( $contact ) ); ?></span>
								<span class="almasa-contact__tel" dir="ltr"><?php echo esc_html( $phone ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( almasa_arg( $args, 'social', true ) ) : ?>
				<div class="almasa-contact-info__social">
					<?php if ( '' !== $social_label ) : ?>
						<p class="almasa-contact-info__label"><?php echo esc_html( $social_label ); ?></p>
					<?php endif; ?>
					<?php almasa_social_list(); ?>
				</div>
			<?php endif; ?>
		</aside>

		<div class="almasa-form-card" data-reveal style="--reveal-delay:120ms">
			<?php if ( '' !== $form_title ) : ?>
				<h2 class="almasa-form-card__title"><?php echo esc_html( $form_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $form_hint ) : ?>
				<p class="almasa-form-card__hint"><?php echo esc_html( $form_hint ); ?></p>
			<?php endif; ?>

			<div class="almasa-form-status<?php echo 'sent' === $status ? ' is-success' : ( 'error' === $status ? ' is-error' : '' ); ?>" role="status" aria-live="polite" data-form-status<?php echo $status ? '' : ' hidden'; ?>>
				<?php
				if ( 'sent' === $status ) {
					echo esc_html( almasa_contact_messages()['sent'] );
				} elseif ( 'error' === $status ) {
					echo esc_html( almasa_contact_messages()['check_fields'] );
				}
				?>
			</div>

			<form class="almasa-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-almasa-form>
				<input type="hidden" name="action" value="almasa_contact">
				<?php wp_nonce_field( 'almasa_contact', 'almasa_contact_nonce' ); ?>
				<div class="almasa-form__hp" aria-hidden="true">
					<label for="almasa-company-site">Website</label>
					<input type="text" id="almasa-company-site" name="company_site" tabindex="-1" autocomplete="off">
				</div>

				<div class="almasa-form__row">
					<div class="almasa-field">
						<label for="almasa-name"><?php esc_html_e( 'الاسم', 'almasa' ); ?> <span aria-hidden="true">*</span></label>
						<input type="text" id="almasa-name" name="name" required minlength="3" maxlength="80" autocomplete="name" aria-describedby="almasa-name-error">
						<p class="almasa-field__error" id="almasa-name-error" data-error-for="name"></p>
					</div>
					<div class="almasa-field">
						<label for="almasa-phone"><?php esc_html_e( 'رقم الهاتف', 'almasa' ); ?> <span aria-hidden="true">*</span></label>
						<input type="tel" id="almasa-phone" name="phone" required inputmode="tel" autocomplete="tel" dir="ltr" placeholder="01XXXXXXXXX" aria-describedby="almasa-phone-error">
						<p class="almasa-field__error" id="almasa-phone-error" data-error-for="phone"></p>
					</div>
				</div>

				<div class="almasa-form__row">
					<div class="almasa-field">
						<label for="almasa-email"><?php esc_html_e( 'البريد الإلكتروني', 'almasa' ); ?> <small><?php esc_html_e( '(اختياري)', 'almasa' ); ?></small></label>
						<input type="email" id="almasa-email" name="email" autocomplete="email" dir="ltr" aria-describedby="almasa-email-error">
						<p class="almasa-field__error" id="almasa-email-error" data-error-for="email"></p>
					</div>
					<div class="almasa-field">
						<label for="almasa-service"><?php esc_html_e( 'الخدمة المطلوبة', 'almasa' ); ?></label>
						<select id="almasa-service" name="service" aria-describedby="almasa-service-error">
							<option value=""><?php esc_html_e( 'اختر الخدمة', 'almasa' ); ?></option>
							<?php foreach ( $services as $service ) : ?>
								<option value="<?php echo esc_attr( (string) $service->ID ); ?>"><?php echo esc_html( get_the_title( $service ) ); ?></option>
							<?php endforeach; ?>
							<option value="other"><?php esc_html_e( 'أخرى', 'almasa' ); ?></option>
						</select>
						<p class="almasa-field__error" id="almasa-service-error" data-error-for="service"></p>
					</div>
				</div>

				<div class="almasa-field">
					<label for="almasa-message"><?php esc_html_e( 'تفاصيل الطلب', 'almasa' ); ?> <span aria-hidden="true">*</span></label>
					<textarea id="almasa-message" name="message" rows="5" required minlength="10" maxlength="2000" aria-describedby="almasa-message-error"></textarea>
					<p class="almasa-field__error" id="almasa-message-error" data-error-for="message"></p>
				</div>

				<button type="submit" class="almasa-btn almasa-btn--gold almasa-btn--lg almasa-form__submit" data-form-submit>
					<span data-submit-label><?php echo esc_html( $submit_text ); ?></span>
					<span class="almasa-spinner" aria-hidden="true"></span>
				</button>
			</form>
		</div>
	</div>
</section>

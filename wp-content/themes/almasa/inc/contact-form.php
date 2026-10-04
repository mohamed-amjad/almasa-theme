<?php
/**
 * Contact form: validation, storage (almasa_request CPT), admin inbox, email notice.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Requests inbox CPT (admin only, no front end).
 */
function almasa_register_request_cpt() {
	register_post_type(
		'almasa_request',
		array(
			'labels'              => array(
				'name'               => 'طلبات التواصل',
				'singular_name'      => 'طلب',
				'edit_item'          => 'تفاصيل الطلب',
				'all_items'          => 'كل الطلبات',
				'menu_name'          => 'الطلبات',
				'search_items'       => 'بحث في الطلبات',
				'not_found'          => 'لا توجد طلبات بعد',
				'not_found_in_trash' => 'لا توجد طلبات في سلة المهملات',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 4,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'almasa_register_request_cpt' );

/**
 * Convert Arabic-Indic / Persian digits to ASCII.
 *
 * @param string $value Input.
 * @return string
 */
function almasa_ascii_digits( $value ) {
	return strtr( (string) $value, array(
		'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
		'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
		'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
		'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
	) );
}

/**
 * Validation messages — shared by the server and the client script (enqueue.php).
 *
 * @return array<string,string>
 */
function almasa_contact_messages() {
	return array(
		'name_short'    => 'من فضلك اكتب الاسم (3 أحرف على الأقل).',
		'name_long'     => 'الاسم طويل جدًا.',
		'phone_empty'   => 'من فضلك اكتب رقم الهاتف.',
		'phone_invalid' => 'رقم الهاتف غير صحيح. مثال: 01012345678',
		'email_invalid' => 'البريد الإلكتروني غير صحيح.',
		'service'       => 'اختر خدمة من القائمة.',
		'message_short' => 'اكتب تفاصيل طلبك (10 أحرف على الأقل).',
		'message_long'  => 'الرسالة طويلة جدًا (الحد 2000 حرف).',
		'check_fields'  => 'من فضلك راجع الحقول المطلوبة.',
		'sent'          => 'تم استلام طلبك بنجاح، سنتواصل معك قريبًا.',
		'failed'        => 'حدث خطأ أثناء الإرسال. حاول مرة أخرى أو اتصل بنا مباشرة.',
		'sending'       => 'جارٍ الإرسال…',
	);
}

/**
 * Phone pattern (Egyptian mobile/landline or international), shared with JS.
 */
const ALMASA_PHONE_PATTERN = '^(?:\+?20|0020)?0?1[0125]\d{8}$|^(?:\+?20|0)\d{8,9}$|^\+\d{10,15}$';

/**
 * Validate a submission.
 *
 * @param array $raw Raw input.
 * @return array{data: array, errors: array<string,string>}
 */
function almasa_contact_validate( $raw ) {
	$name    = sanitize_text_field( wp_unslash( $raw['name'] ?? '' ) );
	$phone   = preg_replace( '/[\s\-().]/', '', almasa_ascii_digits( sanitize_text_field( wp_unslash( $raw['phone'] ?? '' ) ) ) );
	$email   = sanitize_email( wp_unslash( $raw['email'] ?? '' ) );
	$service = sanitize_text_field( wp_unslash( $raw['service'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $raw['message'] ?? '' ) );

	$errors = array();
	$msg    = almasa_contact_messages();

	$name_len = mb_strlen( $name );
	if ( $name_len < 3 ) {
		$errors['name'] = $msg['name_short'];
	} elseif ( $name_len > 80 ) {
		$errors['name'] = $msg['name_long'];
	}

	if ( '' === $phone ) {
		$errors['phone'] = $msg['phone_empty'];
	} elseif ( ! preg_match( '/' . ALMASA_PHONE_PATTERN . '/', $phone ) ) {
		$errors['phone'] = $msg['phone_invalid'];
	}

	$raw_email = trim( (string) wp_unslash( $raw['email'] ?? '' ) );
	if ( '' !== $raw_email && ! is_email( $email ) ) {
		$errors['email'] = $msg['email_invalid'];
	}

	$service_label = '';
	if ( '' !== $service ) {
		if ( 'other' === $service ) {
			$service_label = 'أخرى';
		} elseif ( ctype_digit( $service ) && 'service' === get_post_type( (int) $service ) ) {
			$service_label = get_the_title( (int) $service );
		} else {
			$errors['service'] = $msg['service'];
		}
	}

	$msg_len = mb_strlen( $message );
	if ( $msg_len < 10 ) {
		$errors['message'] = $msg['message_short'];
	} elseif ( $msg_len > 2000 ) {
		$errors['message'] = $msg['message_long'];
	}

	return array(
		'data'   => array(
			'name'    => $name,
			'phone'   => $phone,
			'email'   => is_email( $email ) ? $email : '',
			'service' => $service_label,
			'message' => $message,
		),
		'errors' => $errors,
	);
}

/**
 * Handle a submission (AJAX or regular POST).
 *
 * @return array{ok: bool, message: string, errors?: array}
 */
function almasa_contact_process() {
	if ( ! isset( $_POST['almasa_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['almasa_contact_nonce'] ) ), 'almasa_contact' ) ) {
		return array(
			'ok'      => false,
			'message' => 'انتهت صلاحية الصفحة. حدّث الصفحة وحاول مرة أخرى.',
		);
	}

	// Honeypot: humans never see this field.
	if ( ! empty( $_POST['company_site'] ) ) {
		return array(
			'ok'      => true,
			'message' => almasa_contact_messages()['sent'],
		);
	}

	$result = almasa_contact_validate( $_POST );
	if ( $result['errors'] ) {
		return array(
			'ok'      => false,
			'message' => almasa_contact_messages()['check_fields'],
			'errors'  => $result['errors'],
		);
	}

	$ip_key = 'almasa_contact_' . md5( ( $_SERVER['REMOTE_ADDR'] ?? '' ) . wp_salt( 'nonce' ) );
	if ( get_transient( $ip_key ) ) {
		return array(
			'ok'      => false,
			'message' => 'تم إرسال طلب منذ لحظات. انتظر قليلًا ثم حاول مرة أخرى.',
		);
	}

	$data    = $result['data'];
	$post_id = wp_insert_post( array(
		'post_type'    => 'almasa_request',
		'post_status'  => 'publish',
		'post_title'   => $data['name'] . ' — ' . $data['phone'],
		'post_content' => $data['message'],
		'meta_input'   => array(
			'request_name'    => $data['name'],
			'request_phone'   => $data['phone'],
			'request_email'   => $data['email'],
			'request_service' => $data['service'],
			'request_page'    => esc_url_raw( wp_get_referer() ? wp_get_referer() : '' ),
			'_almasa_unread'  => 1,
		),
	), true );

	if ( is_wp_error( $post_id ) ) {
		return array(
			'ok'      => false,
			'message' => almasa_contact_messages()['failed'],
		);
	}

	set_transient( $ip_key, 1, 30 );

	$lines = array(
		'الاسم: ' . $data['name'],
		'الهاتف: ' . $data['phone'],
		'البريد: ' . ( $data['email'] ? $data['email'] : '—' ),
		'الخدمة: ' . ( $data['service'] ? $data['service'] : '—' ),
		'',
		$data['message'],
		'',
		admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $data['email'] ) {
		$headers[] = 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>';
	}
	wp_mail( get_option( 'admin_email' ), 'طلب تواصل جديد — ' . get_bloginfo( 'name' ), implode( "\n", $lines ), $headers );

	return array(
		'ok'      => true,
		'message' => almasa_contact_messages()['sent'],
	);
}

/**
 * AJAX endpoint.
 */
function almasa_contact_ajax() {
	$response = almasa_contact_process();
	if ( $response['ok'] ) {
		wp_send_json_success( $response );
	}
	wp_send_json_error( $response, 422 );
}
add_action( 'wp_ajax_almasa_contact', 'almasa_contact_ajax' );
add_action( 'wp_ajax_nopriv_almasa_contact', 'almasa_contact_ajax' );

/**
 * No-JS fallback: regular POST to admin-post.php, then redirect back.
 */
function almasa_contact_post() {
	$response = almasa_contact_process();
	$back     = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back     = remove_query_arg( 'almasa_form', $back );
	wp_safe_redirect( add_query_arg( 'almasa_form', $response['ok'] ? 'sent' : 'error', $back ) . '#contact-form' );
	exit;
}
add_action( 'admin_post_almasa_contact', 'almasa_contact_post' );
add_action( 'admin_post_nopriv_almasa_contact', 'almasa_contact_post' );

/**
 * Admin list columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function almasa_request_columns( $columns ) {
	return array(
		'cb'              => $columns['cb'],
		'title'           => 'الاسم / الهاتف',
		'request_service' => 'الخدمة',
		'request_email'   => 'البريد',
		'request_status'  => 'الحالة',
		'date'            => 'التاريخ',
	);
}
add_filter( 'manage_almasa_request_posts_columns', 'almasa_request_columns' );

/**
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function almasa_request_column_content( $column, $post_id ) {
	if ( 'request_service' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, 'request_service', true ) );
	} elseif ( 'request_email' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, 'request_email', true ) );
	} elseif ( 'request_status' === $column ) {
		echo get_post_meta( $post_id, '_almasa_unread', true ) ? '<strong style="color:#b32d2e">جديد</strong>' : 'تمت القراءة';
	}
}
add_action( 'manage_almasa_request_posts_custom_column', 'almasa_request_column_content', 10, 2 );

/**
 * Read-only details box; opening a request marks it read.
 */
function almasa_request_metabox() {
	add_meta_box( 'almasa_request_details', 'بيانات الطلب', 'almasa_request_metabox_render', 'almasa_request', 'normal', 'high' );
	remove_meta_box( 'submitdiv', 'almasa_request', 'side' );
}
add_action( 'add_meta_boxes_almasa_request', 'almasa_request_metabox' );

/**
 * @param WP_Post $post Request.
 */
function almasa_request_metabox_render( $post ) {
	delete_post_meta( $post->ID, '_almasa_unread' );
	$phone = (string) get_post_meta( $post->ID, 'request_phone', true );
	$email = (string) get_post_meta( $post->ID, 'request_email', true );
	$rows  = array(
		'الاسم'   => esc_html( (string) get_post_meta( $post->ID, 'request_name', true ) ),
		'الهاتف'  => $phone ? '<a href="' . esc_url( almasa_tel_href( $phone ) ) . '" dir="ltr">' . esc_html( $phone ) . '</a>' : '—',
		'البريد'  => $email ? '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>' : '—',
		'الخدمة'  => esc_html( (string) get_post_meta( $post->ID, 'request_service', true ) ) ?: '—',
		'التاريخ' => esc_html( get_the_date( 'Y/m/d — H:i', $post ) ),
		'الرسالة' => nl2br( esc_html( $post->post_content ) ),
	);
	echo '<table class="widefat striped"><tbody>';
	foreach ( $rows as $label => $value ) {
		echo '<tr><th style="width:120px">' . esc_html( $label ) . '</th><td>' . wp_kses_post( $value ) . '</td></tr>';
	}
	echo '</tbody></table>';
	echo '<p><a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=almasa_request' ) ) . '">رجوع لكل الطلبات</a> ';
	echo '<a class="button-link-delete" style="margin-inline-start:12px" href="' . esc_url( get_delete_post_link( $post->ID ) ) . '">نقل إلى سلة المهملات</a></p>';
}

/**
 * Unread count bubble on the admin menu.
 */
function almasa_request_menu_bubble() {
	global $menu;
	$unread = get_posts( array(
		'post_type'      => 'almasa_request',
		'post_status'    => 'publish',
		'meta_key'       => '_almasa_unread',
		'meta_value'     => '1',
		'fields'         => 'ids',
		'posts_per_page' => 99,
	) );
	if ( ! $unread || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=almasa_request' === $item[2] ) {
			$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . count( $unread ) . '</span></span>';
		}
	}
}
add_action( 'admin_menu', 'almasa_request_menu_bubble', 99 );

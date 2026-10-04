<?php
/**
 * One-time content seed. Run: wp eval-file inc/seed-once.php
 *
 * @package Almasa
 */

if ( ! defined( 'WP_CLI' ) && php_sapi_name() !== 'cli' ) {
	return;
}

if ( get_option( 'almasa_content_seeded' ) ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::success( 'Content already seeded.' );
	}
	return;
}

/**
 * Register an existing uploads file as an attachment.
 *
 * @param string $abs Absolute path.
 * @return int
 */
function almasa_seed_attachment( $abs ) {
	if ( ! file_exists( $abs ) ) {
		return 0;
	}

	$uploads  = wp_upload_dir();
	$filename = wp_basename( $abs );
	$rel      = ltrim( str_replace( '\\', '/', substr( $abs, strlen( WP_CONTENT_DIR . '/uploads' ) ) ), '/' );
	$url      = trailingslashit( $uploads['baseurl'] ) . $rel;
	$filetype = wp_check_filetype( $filename, null );

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
			'post_status'    => 'inherit',
			'guid'           => $url,
		),
		$abs
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$meta = wp_generate_attachment_metadata( $attachment_id, $abs );
	if ( $meta ) {
		wp_update_attachment_metadata( $attachment_id, $meta );
	}

	return (int) $attachment_id;
}

/**
 * @param string $dir Directory.
 * @return int[]
 */
function almasa_seed_dir( $dir ) {
	$ids   = array();
	$files = glob( $dir . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE );
	if ( ! $files ) {
		return $ids;
	}
	natsort( $files );
	foreach ( $files as $file ) {
		$id = almasa_seed_attachment( $file );
		if ( $id ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

update_option( 'blogname', 'شركة الماسة للمقاولات العامة والتوريدات العمومية' );
update_option( 'blogdescription', 'أعمال إنشاءات متكاملة من الخرسانات حتى التشطيب' );

$services = array(
	'أعمال الإنشاءات',
	'أعمال الخرسانات المسلحة',
	'أعمال التشطيبات',
	'أعمال المقاولات المتكاملة',
);
$order    = 1;
foreach ( $services as $title ) {
	$existing = get_page_by_title( $title, OBJECT, 'service' );
	if ( $existing ) {
		continue;
	}
	wp_insert_post( array(
		'post_type'   => 'service',
		'post_title'  => $title,
		'post_status' => 'publish',
		'menu_order'  => $order,
	) );
	++$order;
}

$locations = array(
	array(
		'title'   => 'كفر الشيخ',
		'address' => "محافظة كفر الشيخ\nأبراج الجبالي",
	),
	array(
		'title'   => 'العلمين',
		'address' => "محافظة مطروح\nالعلمين\nشارع الغزالة\nبجوار فرع فودافون",
	),
);
$order = 1;
foreach ( $locations as $row ) {
	$id = wp_insert_post( array(
		'post_type'   => 'almasa_location',
		'post_title'  => $row['title'],
		'post_status' => 'publish',
		'menu_order'  => $order,
	) );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, 'address', $row['address'] );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'address', $row['address'], $id );
		}
	}
	++$order;
}

$contacts = array(
	array(
		'title' => 'المهندس / محمد عبدالمطلب',
		'phone' => '01068309062',
	),
	array(
		'title' => 'المهندس / عثمان عماد يوسف',
		'phone' => '01065084349',
	),
);
$order = 1;
foreach ( $contacts as $row ) {
	$id = wp_insert_post( array(
		'post_type'   => 'almasa_contact',
		'post_title'  => $row['title'],
		'post_status' => 'publish',
		'menu_order'  => $order,
	) );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, 'phone', $row['phone'] );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'phone', $row['phone'], $id );
		}
	}
	++$order;
}

$upload_09 = WP_CONTENT_DIR . '/uploads/2026/09';
$gallery_1 = almasa_seed_dir( $upload_09 . '/1' );
$gallery_2 = almasa_seed_dir( $upload_09 . '/2' );
$gallery_3 = almasa_seed_dir( $upload_09 . '/3' );

$projects = array(
	array(
		'title'    => 'محلات تجارية بالحي اللاتيني',
		'work'     => 'أعمال تشطيبات',
		'company'  => 'مشروع خاص بشركة ريدكون للتعمير',
		'order'    => 1,
		'featured' => 1,
		'gallery'  => $gallery_1,
	),
	array(
		'title'    => 'الحي اللاتيني بالعلمين',
		'work'     => 'أعمال تشطيبات',
		'company'  => 'شركة ريدكون، جاما، أوراسكوم',
		'order'    => 2,
		'featured' => 1,
		'gallery'  => $gallery_3,
	),
	array(
		'title'    => 'تشطيب الفيلات بقرية سول',
		'work'     => 'أعمال خرسانات مسلحة وتشطيبات',
		'company'  => 'مشروع خاص بشركة ريدكون للتعمير',
		'order'    => 3,
		'featured' => 0,
		'gallery'  => $gallery_2,
	),
);

foreach ( $projects as $row ) {
	$id = wp_insert_post( array(
		'post_type'    => 'project',
		'post_title'   => $row['title'],
		'post_status'  => 'publish',
		'post_content' => '',
	) );
	if ( is_wp_error( $id ) || ! $id ) {
		continue;
	}
	update_post_meta( $id, 'work_type', $row['work'] );
	update_post_meta( $id, 'associated_company', $row['company'] );
	update_post_meta( $id, 'project_description', $row['company'] );
	update_post_meta( $id, 'featured_project', $row['featured'] );
	update_post_meta( $id, 'project_order', $row['order'] );
	update_post_meta( $id, 'project_gallery', $row['gallery'] );
	if ( function_exists( 'update_field' ) ) {
		update_field( 'work_type', $row['work'], $id );
		update_field( 'associated_company', $row['company'], $id );
		update_field( 'project_description', $row['company'], $id );
		update_field( 'featured_project', $row['featured'], $id );
		update_field( 'project_order', $row['order'], $id );
	}
	if ( ! empty( $row['gallery'][0] ) ) {
		set_post_thumbnail( $id, (int) $row['gallery'][0] );
	}
}

$page_titles = array(
	'الرئيسية',
	'من نحن',
	'خدماتنا',
	'مشاريعنا',
	'مواقعنا',
	'تواصل معنا',
);
$page_ids = array();
foreach ( $page_titles as $title ) {
	$found = get_page_by_title( $title, OBJECT, 'page' );
	if ( $found ) {
		$page_ids[ $title ] = $found->ID;
		continue;
	}
	$page_ids[ $title ] = wp_insert_post( array(
		'post_type'    => 'page',
		'post_title'   => $title,
		'post_status'  => 'publish',
		'post_content' => '',
	) );
}

if ( ! empty( $page_ids['الرئيسية'] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', (int) $page_ids['الرئيسية'] );
}

$menu_name = 'القائمة الرئيسية';
$menu      = wp_get_nav_menu_object( $menu_name );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
} else {
	$menu_id = (int) $menu->term_id;
}

$locations = get_theme_mod( 'nav_menu_locations' );
if ( ! is_array( $locations ) ) {
	$locations = array();
}
$locations['primary'] = $menu_id;
$locations['footer']  = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

$items = wp_get_nav_menu_items( $menu_id );
if ( empty( $items ) ) {
	$n = 1;
	foreach ( $page_titles as $title ) {
		if ( empty( $page_ids[ $title ] ) ) {
			continue;
		}
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => $title,
			'menu-item-object'    => 'page',
			'menu-item-object-id' => (int) $page_ids[ $title ],
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-position'  => $n,
		) );
		++$n;
	}
}

update_option( 'almasa_content_seeded', 1 );

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::success( 'Almasa content seeded.' );
}

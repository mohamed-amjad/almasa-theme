<?php
/**
 * Custom post types.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register CPTs.
 */
function almasa_register_post_types() {
	register_post_type(
		'project',
		array(
			'labels'              => array(
				'name'               => 'المشاريع',
				'singular_name'      => 'مشروع',
				'add_new'            => 'إضافة مشروع',
				'add_new_item'       => 'إضافة مشروع جديد',
				'edit_item'          => 'تحرير المشروع',
				'new_item'           => 'مشروع جديد',
				'view_item'          => 'عرض المشروع',
				'search_items'       => 'بحث في المشاريع',
				'not_found'          => 'لا توجد مشاريع',
				'not_found_in_trash' => 'لا توجد مشاريع في سلة المهملات',
				'all_items'          => 'كل المشاريع',
				'menu_name'          => 'المشاريع',
			),
			'public'              => true,
			'has_archive'         => true,
			'show_in_rest'        => true,
			'show_in_nav_menus'   => true,
			'menu_icon'           => 'dashicons-building',
			'menu_position'       => 5,
			'rewrite'             => array(
				'slug'       => 'projects',
				'with_front' => false,
			),
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
		)
	);

	register_post_type(
		'service',
		array(
			'labels'            => array(
				'name'          => 'الخدمات',
				'singular_name' => 'خدمة',
				'add_new_item'  => 'إضافة خدمة',
				'edit_item'     => 'تحرير الخدمة',
				'all_items'     => 'كل الخدمات',
				'menu_name'     => 'الخدمات',
			),
			'public'            => true,
			'has_archive'       => true,
			'show_in_rest'      => true,
			'menu_icon'         => 'dashicons-hammer',
			'menu_position'     => 6,
			'rewrite'           => array(
				'slug'       => 'services',
				'with_front' => false,
			),
			'supports'          => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'show_in_nav_menus' => true,
		)
	);

	register_post_type(
		'almasa_location',
		array(
			'labels'              => array(
				'name'          => 'المواقع',
				'singular_name' => 'موقع',
				'add_new_item'  => 'إضافة موقع',
				'edit_item'     => 'تحرير الموقع',
				'menu_name'     => 'المواقع',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_icon'           => 'dashicons-location',
			'menu_position'       => 7,
			'supports'            => array( 'title', 'page-attributes', 'custom-fields' ),
		)
	);

	register_post_type(
		'almasa_contact',
		array(
			'labels'              => array(
				'name'          => 'جهات الاتصال',
				'singular_name' => 'جهة اتصال',
				'add_new_item'  => 'إضافة جهة اتصال',
				'edit_item'     => 'تحرير جهة الاتصال',
				'menu_name'     => 'جهات الاتصال',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_icon'           => 'dashicons-phone',
			'menu_position'       => 8,
			'supports'            => array( 'title', 'page-attributes', 'custom-fields' ),
		)
	);

	register_post_type(
		'almasa_reason',
		array(
			'labels'              => array(
				'name'          => 'لماذا تختارنا',
				'singular_name' => 'سبب',
				'add_new_item'  => 'إضافة سبب',
				'edit_item'     => 'تحرير السبب',
				'menu_name'     => 'لماذا تختارنا',
			),
			'description'         => 'بنود قسم "لماذا تختارنا" في صفحة من نحن. العنوان + المقتطف.',
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_icon'           => 'dashicons-star-filled',
			'menu_position'       => 9,
			'supports'            => array( 'title', 'excerpt', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'almasa_register_post_types' );

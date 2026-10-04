<?php
/**
 * ACF local field groups + JSON save path.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Save/load ACF JSON in the theme.
 *
 * @param string $path Default path.
 * @return string
 */
function almasa_acf_json_save_point( $path ) {
	return ALMASA_DIR . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'almasa_acf_json_save_point' );

/**
 * @param array $paths Load paths.
 * @return array
 */
function almasa_acf_json_load_point( $paths ) {
	unset( $paths[0] );
	$paths[] = ALMASA_DIR . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'almasa_acf_json_load_point' );

/**
 * Local fields (ACF Free–compatible types).
 */
function almasa_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'      => 'group_almasa_project',
		'title'    => 'بيانات المشروع',
		'fields'   => array(
			array(
				'key'   => 'field_almasa_project_subtitle',
				'label' => 'عنوان فرعي',
				'name'  => 'project_subtitle',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_almasa_work_type',
				'label'        => 'نوع العمل',
				'name'         => 'work_type',
				'type'         => 'text',
				'instructions' => 'مثال: أعمال تشطيبات',
			),
			array(
				'key'          => 'field_almasa_project_location',
				'label'        => 'الموقع',
				'name'         => 'project_location',
				'type'         => 'text',
				'instructions' => 'يُملأ فقط إذا كان الموقع مؤكدًا. لا تُختلق بيانات.',
			),
			array(
				'key'   => 'field_almasa_associated_company',
				'label' => 'الشركة / السياق',
				'name'  => 'associated_company',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_almasa_project_description',
				'label' => 'وصف منظم',
				'name'  => 'project_description',
				'type'  => 'textarea',
				'rows'  => 4,
			),
			array(
				'key'           => 'field_almasa_featured_project',
				'label'         => 'مشروع مميز',
				'name'          => 'featured_project',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 0,
			),
			array(
				'key'           => 'field_almasa_project_order',
				'label'         => 'ترتيب العرض',
				'name'          => 'project_order',
				'type'          => 'number',
				'default_value' => 10,
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'project',
				),
			),
		),
		'position' => 'acf_after_title',
		'style'    => 'default',
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_almasa_location',
		'title'    => 'عنوان الموقع',
		'fields'   => array(
			array(
				'key'   => 'field_almasa_address',
				'label' => 'العنوان',
				'name'  => 'address',
				'type'  => 'textarea',
				'rows'  => 4,
			),
			array(
				'key'          => 'field_almasa_map_query',
				'label'        => 'بحث الخريطة (اختياري)',
				'name'         => 'map_query',
				'type'         => 'text',
				'instructions' => 'اسم المكان أو الإحداثيات منسوخة من خرائط Google لتحديد الدبوس بدقة. إن تُرك فارغًا يُستخدم العنوان.',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'almasa_location',
				),
			),
		),
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_almasa_contact',
		'title'    => 'الهاتف',
		'fields'   => array(
			array(
				'key'          => 'field_almasa_phone',
				'label'        => 'رقم الهاتف',
				'name'         => 'phone',
				'type'         => 'text',
				'instructions' => 'أرقام فقط أو بالصيغة المحلية. يُعرض كرابط tel:',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'almasa_contact',
				),
			),
		),
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_almasa_service',
		'title'    => 'ملخص الخدمة',
		'fields'   => array(
			array(
				'key'          => 'field_almasa_service_summary',
				'label'        => 'ملخص (اختياري)',
				'name'         => 'service_summary',
				'type'         => 'textarea',
				'instructions' => 'اتركه فارغًا حتى يتوفر نص معتمد.',
				'rows'         => 3,
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'service',
				),
			),
		),
	) );

	$about_id = almasa_page_id( 'من نحن' );
	if ( $about_id ) {
		acf_add_local_field_group( array(
			'key'      => 'group_almasa_about',
			'title'    => 'الرؤية والرسالة',
			'fields'   => array(
				array(
					'key'   => 'field_almasa_about_vision',
					'label' => 'الرؤية',
					'name'  => 'about_vision',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'   => 'field_almasa_about_mission',
					'label' => 'الرسالة',
					'name'  => 'about_mission',
					'type'  => 'textarea',
					'rows'  => 3,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'page',
						'operator' => '==',
						'value'    => (string) $about_id,
					),
				),
			),
			'position' => 'acf_after_title',
		) );
	}
}
add_action( 'acf/init', 'almasa_register_acf_fields' );

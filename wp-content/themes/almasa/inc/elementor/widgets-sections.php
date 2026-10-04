<?php
/**
 * Almasa section widgets (Elementor). Dynamic data (projects, services, phones,
 * locations, galleries) still comes from the dashboard; widgets control copy,
 * layout options and styling.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/* ---------------------------------------------------------------- Hero */

class Almasa_Widget_Hero extends Almasa_Widget {
	protected $template = 'template-parts/sections/hero';

	public function get_name() {
		return 'almasa-hero';
	}

	public function get_title() {
		return 'الماسة — الهيرو الرئيسي';
	}

	public function get_icon() {
		return 'eicon-slider-full-screen';
	}

	protected function register_controls() {
		list( $name_lead, $name_rest ) = almasa_split_site_name();

		$this->content_section( 'content', 'المحتوى' );
		$this->text( 'eyebrow', 'السطر الصغير', 'مرحبًا بكم في' );
		$this->text( 'title_lead', 'العنوان — السطر الأول', $name_lead );
		$this->toggle( 'show_title_rest', 'إظهار السطر الثاني' );
		$this->text( 'title_rest', 'العنوان — السطر الثاني', $name_rest, array( 'condition' => array( 'show_title_rest' => 'yes' ) ) );
		$this->toggle( 'show_lead', 'إظهار الوصف' );
		$this->textarea( 'lead', 'الوصف', get_bloginfo( 'description' ), array( 'condition' => array( 'show_lead' => 'yes' ) ) );
		$this->end_controls_section();

		$this->content_section( 'buttons', 'الأزرار' );
		$this->text( 'primary_text', 'الزر الذهبي', 'استكشف مشاريعنا', array( 'description' => 'امسح النص لإخفاء الزر.' ) );
		$this->link( 'primary_url', 'رابط الزر الذهبي', 'مشاريعنا' );
		$this->text( 'secondary_text', 'الزر الشفاف', 'تواصل معنا', array( 'description' => 'امسح النص لإخفاء الزر.' ) );
		$this->link( 'secondary_url', 'رابط الزر الشفاف', 'تواصل معنا' );
		$this->end_controls_section();

		$this->content_section( 'slides_section', 'صور الخلفية' );
		$this->add_control( 'slides', array(
			'label'       => 'الصور',
			'type'        => Controls_Manager::GALLERY,
			'default'     => array_values( array_filter( array_map( array( __CLASS__, 'media_default' ), almasa_hero_image_ids() ), static function ( $m ) {
				return '' !== $m['url'];
			} ) ),
			'description' => 'لو مسحت كل الصور يرجع العرض للصور المعلّمة «عرض في الهيرو» في مكتبة الوسائط.',
		) );
		$this->number( 'interval', 'مدة كل صورة (ثانية)', 6, 2, 20 );
		$this->end_controls_section();

		$this->style_controls( array(
			'kicker' => '.almasa-eyebrow',
			'title'  => '.almasa-hero__title',
			'text'   => '.almasa-hero__lead',
		) );
	}

	protected function template_args( array $s ) {
		$a = array(
			'eyebrow'        => self::str( $s, 'eyebrow' ),
			'primary_text'   => self::str( $s, 'primary_text' ),
			'primary_url'    => self::url( $s, 'primary_url' ),
			'secondary_text' => self::str( $s, 'secondary_text' ),
			'secondary_url'  => self::url( $s, 'secondary_url' ),
			'interval'       => max( 2, (int) ( $s['interval'] ?? 6 ) ) * 1000,
		);
		if ( '' !== self::str( $s, 'title_lead' ) ) {
			$a['title_lead'] = self::str( $s, 'title_lead' );
		}
		if ( ! self::on( $s, 'show_title_rest' ) ) {
			$a['title_rest'] = '';
		} elseif ( '' !== self::str( $s, 'title_rest' ) ) {
			$a['title_rest'] = self::str( $s, 'title_rest' );
		}
		if ( ! self::on( $s, 'show_lead' ) ) {
			$a['lead'] = '';
		} elseif ( '' !== self::str( $s, 'lead' ) ) {
			$a['lead'] = self::str( $s, 'lead' );
		}
		$slides = array_filter( array_map( 'intval', wp_list_pluck( (array) ( $s['slides'] ?? array() ), 'id' ) ) );
		if ( $slides ) {
			$a['slides'] = array_values( $slides );
		}
		return $a;
	}
}

/* ---------------------------------------------------------------- Page hero */

class Almasa_Widget_Page_Hero extends Almasa_Widget {
	protected $template = 'template-parts/sections/page-hero';

	public function get_name() {
		return 'almasa-page-hero';
	}

	public function get_title() {
		return 'الماسة — رأس الصفحة';
	}

	public function get_icon() {
		return 'eicon-header';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$page_id = almasa_context_post_id();
		$this->text( 'title', 'العنوان', $page_id ? get_the_title( $page_id ) : '' );
		$this->text( 'kicker', 'عنوان صغير فوق العنوان', '' );
		$this->image( 'image', 'صورة الخلفية', $page_id ? (int) get_post_thumbnail_id( $page_id ) : 0 );
		$this->toggle( 'crumbs', 'إظهار مسار التنقل' );
		$this->text( 'home_text', 'اسم الرئيسية في المسار', 'الرئيسية', array( 'condition' => array( 'crumbs' => 'yes' ) ) );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-page-hero',
			'kicker' => '.almasa-page-hero .almasa-kicker',
			'title'  => '.almasa-page-hero__title',
		) );
	}

	protected function template_args( array $s ) {
		$image = self::media_id( $s, 'image' );
		$a     = array(
			'image_id'  => $image ? $image : (int) get_post_thumbnail_id( get_the_ID() ),
			'kicker'    => self::str( $s, 'kicker' ),
			'crumbs'    => self::on( $s, 'crumbs' ),
			'home_text' => self::str( $s, 'home_text' ),
		);
		if ( '' !== self::str( $s, 'title' ) ) {
			$a['title'] = self::str( $s, 'title' );
		}
		return $a;
	}
}

/* ---------------------------------------------------------------- Marquee */

class Almasa_Widget_Marquee extends Almasa_Widget {
	protected $template = 'template-parts/sections/marquee';

	public function get_name() {
		return 'almasa-marquee';
	}

	public function get_title() {
		return 'الماسة — شريط الخدمات المتحرك';
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'الإعدادات' );
		$this->data_note( 'الخدمات', self::titles( almasa_get_services() ), 'service' );
		$this->add_control( 'speed', array(
			'label'     => 'مدة الدورة (ثانية)',
			'type'      => Controls_Manager::NUMBER,
			'min'       => 8,
			'max'       => 120,
			'default'   => 36,
			'selectors' => array( '{{WRAPPER}} .almasa-marquee__track' => 'animation-duration: {{VALUE}}s;' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array(
			'label' => 'الشريط',
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'bg', array(
			'label'     => 'لون الخلفية',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-marquee' => 'background-color: {{VALUE}};' ),
		) );
		$this->add_control( 'color', array(
			'label'     => 'لون النص',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-marquee li' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'typo',
			'selector' => '{{WRAPPER}} .almasa-marquee li',
		) );
		$this->end_controls_section();
	}

	protected function template_args( array $s ) {
		return array();
	}
}

/* ---------------------------------------------------------------- About / intro */

class Almasa_Widget_Intro extends Almasa_Widget {
	protected $template = 'template-parts/sections/intro';

	public function get_name() {
		return 'almasa-intro';
	}

	public function get_title() {
		return 'الماسة — من نحن';
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->text( 'kicker', 'العنوان الصغير', 'من نحن' );
		$this->text( 'title', 'العنوان', get_bloginfo( 'name' ) );
		$this->toggle( 'show_lead', 'إظهار الوصف' );
		$this->textarea( 'lead', 'الوصف', get_bloginfo( 'description' ), array( 'condition' => array( 'show_lead' => 'yes' ) ) );
		$this->end_controls_section();

		$main = almasa_page_thumb_id( 'من نحن' );
		$this->content_section( 'media', 'الصور' );
		$this->image( 'main_image', 'الصورة الكبيرة', $main );
		$this->image( 'side_image', 'الصورة الصغيرة', almasa_intro_side_image_id( $main ) );
		$this->toggle( 'badge', 'إظهار شارة اللوجو' );
		$this->end_controls_section();

		$this->content_section( 'blocks', 'العناصر' );
		$this->toggle( 'checklist', 'قائمة الخدمات' );
		$this->toggle( 'figures', 'الأرقام' );
		$this->text( 'label_projects', 'اسم رقم المشاريع', 'مشاريع', array( 'condition' => array( 'figures' => 'yes' ) ) );
		$this->text( 'label_services', 'اسم رقم الخدمات', 'مجالات عمل', array( 'condition' => array( 'figures' => 'yes' ) ) );
		$this->text( 'label_locations', 'اسم رقم المقرات', 'مقرات', array( 'condition' => array( 'figures' => 'yes' ) ) );
		$this->toggle( 'team', 'أسماء المهندسين' );
		$this->text( 'team_label', 'عنوان المهندسين', 'المهندسون', array( 'condition' => array( 'team' => 'yes' ) ) );
		$this->toggle( 'button', 'إظهار الزر' );
		$this->text( 'button_text', 'نص الزر', 'المزيد عن الشركة', array( 'condition' => array( 'button' => 'yes' ) ) );
		$this->link( 'button_url', 'رابط الزر', 'من نحن', array( 'condition' => array( 'button' => 'yes' ) ) );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-about',
			'kicker' => '.almasa-kicker',
			'title'  => '.almasa-section-title',
			'text'   => '.almasa-about__lead',
		) );
	}

	protected function template_args( array $s ) {
		$a = array(
			'kicker'          => self::str( $s, 'kicker' ),
			'main_image'      => self::media_id( $s, 'main_image' ),
			'side_image'      => self::media_id( $s, 'side_image' ),
			'badge'           => self::on( $s, 'badge' ),
			'checklist'       => self::on( $s, 'checklist' ),
			'figures'         => self::on( $s, 'figures' ),
			'label_projects'  => self::str( $s, 'label_projects' ),
			'label_services'  => self::str( $s, 'label_services' ),
			'label_locations' => self::str( $s, 'label_locations' ),
			'team'            => self::on( $s, 'team' ),
			'team_label'      => self::str( $s, 'team_label' ),
			'button'          => self::on( $s, 'button' ),
			'button_text'     => self::str( $s, 'button_text' ),
			'button_url'      => self::url( $s, 'button_url' ),
		);
		if ( '' !== self::str( $s, 'title' ) ) {
			$a['title'] = self::str( $s, 'title' );
		}
		if ( ! self::on( $s, 'show_lead' ) ) {
			$a['lead'] = '';
		} elseif ( '' !== self::str( $s, 'lead' ) ) {
			$a['lead'] = self::str( $s, 'lead' );
		}
		return $a;
	}
}

/* ---------------------------------------------------------------- Shared head controls */

trait Almasa_Section_Head {
	protected function head_controls( $kicker, $title, $link_text = '', $link_page = '' ) {
		$this->toggle( 'head', 'إظهار رأس القسم' );
		$this->text( 'kicker', 'العنوان الصغير', $kicker, array( 'condition' => array( 'head' => 'yes' ) ) );
		$this->text( 'title', 'العنوان', $title, array( 'condition' => array( 'head' => 'yes' ) ) );
		if ( '' !== $link_text ) {
			$this->toggle( 'show_link', 'إظهار رابط «الكل»', true, array( 'condition' => array( 'head' => 'yes' ) ) );
			$this->text( 'link_text', 'نص الرابط', $link_text, array( 'condition' => array( 'head' => 'yes', 'show_link' => 'yes' ) ) );
			$this->link( 'link_url', 'الرابط', $link_page, array( 'condition' => array( 'head' => 'yes', 'show_link' => 'yes' ) ) );
		}
	}

	protected function head_args( array $s, $with_link = false ) {
		$a = array(
			'head'   => self::on( $s, 'head' ),
			'kicker' => self::str( $s, 'kicker' ),
			'title'  => self::str( $s, 'title' ),
		);
		if ( $with_link ) {
			$a['show_link'] = self::on( $s, 'show_link' );
			$a['link_text'] = self::str( $s, 'link_text' );
			$a['link_url']  = self::url( $s, 'link_url' );
		}
		return $a;
	}
}

/* ---------------------------------------------------------------- Services */

class Almasa_Widget_Services extends Almasa_Widget {
	use Almasa_Section_Head;

	protected $template = 'template-parts/sections/services';

	public function get_name() {
		return 'almasa-services';
	}

	public function get_title() {
		return 'الماسة — الخدمات';
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->data_note( 'الخدمات', self::titles( almasa_get_services() ), 'service' );
		$this->select( 'layout', 'طريقة العرض', array(
			'slider' => 'سلايدر',
			'grid'   => 'جريد (2 × 2)',
		), 'slider' );
		$this->number( 'autoplay', 'التقليب التلقائي (ثانية، 0 = إيقاف)', 5, 0, 30, 1, array( 'condition' => array( 'layout' => 'slider' ) ) );
		$this->head_controls( 'ماذا نقدم', 'خدماتنا', 'كل الخدمات', 'خدماتنا' );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-services',
			'kicker' => '.almasa-kicker',
			'title'  => '.almasa-section-title',
			'text'   => '.almasa-service-card h3',
		) );
	}

	protected function template_args( array $s ) {
		return array_merge( $this->head_args( $s, true ), array(
			'layout'   => 'grid' === ( $s['layout'] ?? '' ) ? 'grid' : 'slider',
			'autoplay' => max( 0, (int) ( $s['autoplay'] ?? 5 ) ) * 1000,
		) );
	}
}

/* ---------------------------------------------------------------- Projects */

class Almasa_Widget_Projects extends Almasa_Widget {
	use Almasa_Section_Head;

	protected $template = 'template-parts/sections/projects';

	public function get_name() {
		return 'almasa-projects';
	}

	public function get_title() {
		return 'الماسة — المشاريع';
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->data_note( 'المشاريع', self::titles( almasa_get_projects( 50 ) ), 'project' );
		$this->select( 'layout', 'طريقة العرض', array(
			'slider' => 'سلايدر',
			'grid'   => 'جريد',
		), 'slider' );
		$this->number( 'limit', 'عدد المشاريع', 12, 1, 50 );
		$this->toggle( 'featured', 'المشاريع المميزة فقط', false );
		$this->number( 'autoplay', 'التقليب التلقائي (ثانية، 0 = إيقاف)', 6, 0, 30, 1, array( 'condition' => array( 'layout' => 'slider' ) ) );
		$this->text( 'card_cta', 'نص رابط الكارت', 'تفاصيل المشروع' );
		$this->head_controls( 'أعمالنا', 'مشاريعنا', 'كل المشاريع', 'مشاريعنا' );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-projects',
			'kicker' => '.almasa-kicker',
			'title'  => '.almasa-section-title',
			'text'   => '.almasa-card__title',
		) );
	}

	protected function template_args( array $s ) {
		return array_merge( $this->head_args( $s, true ), array(
			'layout'   => 'grid' === ( $s['layout'] ?? '' ) ? 'grid' : 'slider',
			'limit'    => max( 1, (int) ( $s['limit'] ?? 12 ) ),
			'featured' => self::on( $s, 'featured' ),
			'autoplay' => max( 0, (int) ( $s['autoplay'] ?? 6 ) ) * 1000,
			'card_cta' => self::str( $s, 'card_cta' ),
		) );
	}
}

/* ---------------------------------------------------------------- Gallery */

class Almasa_Widget_Gallery extends Almasa_Widget {
	use Almasa_Section_Head;

	protected $template = 'template-parts/sections/gallery';

	public function get_name() {
		return 'almasa-gallery';
	}

	public function get_title() {
		return 'الماسة — معرض الصور';
	}

	public function get_icon() {
		return 'eicon-photo-library';
	}

	protected function register_controls() {
		$this->content_section( 'tabs_section', 'التابات والصور' );
		$this->add_control( 'tabs_note', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => 'كل تابة تظهر كزر في الفلتر، و«الكل» يعرض صور كل التابات. اضغط «إضافة عنصر» لتابة جديدة، اكتب اسمها واختر صورها.',
			'content_classes' => 'elementor-descriptor',
		) );

		$tab = new Repeater();
		$tab->add_control( 'tab_title', array(
			'label'       => 'اسم التابة',
			'type'        => Controls_Manager::TEXT,
			'default'     => 'تابة جديدة',
			'label_block' => true,
		) );
		$tab->add_control( 'tab_images', array(
			'label' => 'الصور',
			'type'  => Controls_Manager::GALLERY,
		) );

		$this->add_control( 'tabs', array(
			'label'       => 'التابات',
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $tab->get_controls(),
			'default'     => self::default_tabs(),
			'title_field' => '{{{ tab_title }}}',
		) );
		$this->end_controls_section();

		$this->content_section( 'content', 'العرض' );
		$this->number( 'limit', 'عدد الصور الظاهرة أولًا', 12, 1, 60 );
		$this->text( 'more_text', 'نص زر المزيد', 'عرض المزيد من الصور' );
		$this->head_controls( 'من مواقع العمل', 'معرض الصور' );
		$this->toggle( 'filters', 'إظهار الفلتر', true, array( 'condition' => array( 'head' => 'yes' ) ) );
		$this->text( 'all_text', 'اسم فلتر الكل', 'الكل', array( 'condition' => array( 'head' => 'yes', 'filters' => 'yes' ) ) );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-gallery-section',
			'kicker' => '.almasa-kicker',
			'title'  => '.almasa-section-title',
		) );
	}

	/**
	 * Repeater default mirrors the current project galleries, so the widget starts with the existing tabs.
	 *
	 * @return array
	 */
	protected static function default_tabs() {
		return array_map( static function ( $tab ) {
			return array(
				'_id'        => 'project' . $tab['key'],
				'tab_title'  => $tab['title'],
				'tab_images' => array_values( array_filter( array_map( array( __CLASS__, 'media_default' ), $tab['ids'] ), static function ( $m ) {
					return '' !== $m['url'];
				} ) ),
			);
		}, almasa_gallery_tabs() );
	}

	protected function template_args( array $s ) {
		$more = self::str( $s, 'more_text' );
		$tabs = array();
		foreach ( (array) ( $s['tabs'] ?? array() ) as $i => $tab ) {
			$tabs[] = array(
				'key'   => ! empty( $tab['_id'] ) ? (string) $tab['_id'] : 'tab' . $i,
				'title' => (string) ( $tab['tab_title'] ?? '' ),
				'ids'   => wp_list_pluck( (array) ( $tab['tab_images'] ?? array() ), 'id' ),
			);
		}
		return array_merge( $this->head_args( $s ), array(
			'tabs'      => $tabs,
			'limit'     => max( 1, (int) ( $s['limit'] ?? 12 ) ),
			'filters'   => self::on( $s, 'filters' ),
			'all_text'  => self::str( $s, 'all_text' ),
			'more_text' => '' !== $more ? $more : __( 'عرض المزيد من الصور', 'almasa' ),
		) );
	}
}

/* ---------------------------------------------------------------- Locations */

class Almasa_Widget_Locations extends Almasa_Widget {
	use Almasa_Section_Head;

	protected $template = 'template-parts/sections/locations';

	public function get_name() {
		return 'almasa-locations';
	}

	public function get_title() {
		return 'الماسة — مواقعنا والخرائط';
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->data_note( 'المواقع', self::titles( almasa_get_structured( 'almasa_location' ) ), 'almasa_location' );
		$this->toggle( 'maps', 'إظهار الخرائط' );
		$this->number( 'zoom', 'تقريب الخريطة', 15, 3, 20, 1, array( 'condition' => array( 'maps' => 'yes' ) ) );
		$this->head_controls( 'أين تجدنا', 'مواقعنا' );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-locations',
			'kicker' => '.almasa-kicker',
			'title'  => '.almasa-section-title',
			'text'   => '.almasa-place p',
		) );
	}

	protected function template_args( array $s ) {
		return array_merge( $this->head_args( $s ), array(
			'maps' => self::on( $s, 'maps' ),
			'zoom' => (int) ( $s['zoom'] ?? 15 ),
		) );
	}
}

/* ---------------------------------------------------------------- Contact CTA */

class Almasa_Widget_Contact extends Almasa_Widget {
	protected $template = 'template-parts/sections/contact';

	public function get_name() {
		return 'almasa-contact';
	}

	public function get_title() {
		return 'الماسة — اتصل بنا (أرقام المهندسين)';
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->data_note( 'أرقام المهندسين', self::contact_lines(), 'almasa_contact' );
		$this->text( 'kicker', 'العنوان الصغير', 'اتصل مباشرة' );
		$this->toggle( 'head', 'إظهار العنوان' );
		$this->text( 'title', 'العنوان', 'تواصل معنا', array( 'condition' => array( 'head' => 'yes' ) ) );
		$this->toggle( 'show_lead', 'إظهار الوصف' );
		$this->textarea( 'lead', 'الوصف', get_bloginfo( 'description' ), array( 'condition' => array( 'show_lead' => 'yes' ) ) );
		$this->toggle( 'show_image', 'صورة خلفية' );
		$this->image( 'image', 'الصورة', almasa_contact_bg_id( almasa_context_post_id() ), array( 'condition' => array( 'show_image' => 'yes' ) ) );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-contact',
			'kicker' => '.almasa-kicker',
			'title'  => '.almasa-contact__title',
			'text'   => '.almasa-contact__lead',
		) );
	}

	protected function template_args( array $s ) {
		$a = array(
			'kicker'     => self::str( $s, 'kicker' ),
			'head'       => self::on( $s, 'head' ),
			'title'      => self::str( $s, 'title' ),
			'show_image' => self::on( $s, 'show_image' ),
			'image'      => self::media_id( $s, 'image' ),
		);
		if ( ! self::on( $s, 'show_lead' ) ) {
			$a['lead'] = '';
		} elseif ( '' !== self::str( $s, 'lead' ) ) {
			$a['lead'] = self::str( $s, 'lead' );
		}
		return $a;
	}
}

/* ---------------------------------------------------------------- Contact form */

class Almasa_Widget_Contact_Form extends Almasa_Widget {
	protected $template = 'template-parts/sections/contact-form';

	public function get_name() {
		return 'almasa-contact-form';
	}

	public function get_title() {
		return 'الماسة — فورم التواصل';
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	protected function register_controls() {
		$this->content_section( 'info', 'صندوق الأرقام' );
		$this->data_note( 'أرقام المهندسين', self::contact_lines(), 'almasa_contact' );
		$this->text( 'kicker', 'العنوان الصغير', 'اتصل مباشرة' );
		$this->text( 'title', 'العنوان', 'فريقنا في خدمتك' );
		$this->toggle( 'show_lead', 'إظهار الوصف' );
		$this->textarea( 'lead', 'الوصف', get_bloginfo( 'description' ), array( 'condition' => array( 'show_lead' => 'yes' ) ) );
		$this->toggle( 'social', 'أيقونات السوشيال ميديا' );
		$this->text( 'social_label', 'عنوان السوشيال', 'تابعنا على', array( 'condition' => array( 'social' => 'yes' ) ) );
		$this->end_controls_section();

		$this->content_section( 'form', 'الفورم' );
		$this->text( 'form_title', 'عنوان الفورم', 'أرسل طلبك' );
		$this->textarea( 'form_hint', 'تعليمات', 'املأ البيانات وسيتواصل معك فريقنا. الحقول المعلّمة بـ * مطلوبة.' );
		$this->text( 'submit_text', 'نص زر الإرسال', 'إرسال الطلب' );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-contact-page',
			'kicker' => '.almasa-contact-info .almasa-kicker',
			'title'  => '.almasa-contact-info__title',
			'text'   => '.almasa-contact-info__lead',
		) );

		$this->start_controls_section( 'boxes', array(
			'label' => 'الصناديق',
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'info_bg', array(
			'label'     => 'خلفية صندوق الأرقام',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-contact-info' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'form_bg', array(
			'label'     => 'خلفية الفورم',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-form-card' => 'background: {{VALUE}};' ),
		) );
		$this->end_controls_section();
	}

	protected function template_args( array $s ) {
		$a = array(
			'kicker'       => self::str( $s, 'kicker' ),
			'title'        => self::str( $s, 'title' ),
			'social'       => self::on( $s, 'social' ),
			'social_label' => self::str( $s, 'social_label' ),
			'form_title'   => self::str( $s, 'form_title' ),
			'form_hint'    => self::str( $s, 'form_hint' ),
			'submit_text'  => self::str( $s, 'submit_text' ),
		);
		if ( ! self::on( $s, 'show_lead' ) ) {
			$a['lead'] = '';
		} elseif ( '' !== self::str( $s, 'lead' ) ) {
			$a['lead'] = self::str( $s, 'lead' );
		}
		return $a;
	}
}

/* ---------------------------------------------------------------- Vision & mission */

class Almasa_Widget_Values extends Almasa_Widget {
	protected $template = 'template-parts/sections/values';

	public function get_name() {
		return 'almasa-values';
	}

	public function get_title() {
		return 'الماسة — الرؤية والرسالة';
	}

	public function get_icon() {
		return 'eicon-columns';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$about = almasa_page_id( 'من نحن' );
		$this->text( 'vision_title', 'عنوان الرؤية', 'الرؤية' );
		$this->textarea( 'vision_text', 'نص الرؤية', $about ? (string) almasa_get_field( 'about_vision', $about ) : '', array( 'rows' => 5 ) );
		$this->text( 'mission_title', 'عنوان الرسالة', 'الرسالة' );
		$this->textarea( 'mission_text', 'نص الرسالة', $about ? (string) almasa_get_field( 'about_mission', $about ) : '', array( 'rows' => 5 ) );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'  => '.almasa-values',
			'title' => '.almasa-value__title',
			'text'  => '.almasa-value__text',
		) );
	}

	protected function template_args( array $s ) {
		return array(
			'vision_title'  => self::str( $s, 'vision_title' ),
			'vision_text'   => self::str( $s, 'vision_text' ),
			'mission_title' => self::str( $s, 'mission_title' ),
			'mission_text'  => self::str( $s, 'mission_text' ),
		);
	}
}

/* ---------------------------------------------------------------- Why us */

class Almasa_Widget_Why extends Almasa_Widget {
	protected $template = 'template-parts/sections/why';

	public function get_name() {
		return 'almasa-why';
	}

	public function get_title() {
		return 'الماسة — لماذا تختارنا';
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->data_note( 'أسباب الاختيار', self::titles( almasa_get_structured( 'almasa_reason' ) ), 'almasa_reason' );
		$this->text( 'kicker', 'العنوان الصغير', 'ما يميزنا' );
		$this->text( 'title', 'العنوان', 'لماذا تختارنا' );
		$this->toggle( 'show_image', 'إظهار الصورة' );
		$this->image( 'image', 'الصورة', almasa_page_thumb_id( 'خدماتنا' ), array( 'condition' => array( 'show_image' => 'yes' ) ) );
		$this->end_controls_section();

		$this->style_controls( array(
			'root'   => '.almasa-why',
			'kicker' => '.almasa-kicker',
			'title'  => '.almasa-section-title',
			'text'   => '.almasa-reason h3',
		) );
	}

	protected function template_args( array $s ) {
		return array(
			'kicker'     => self::str( $s, 'kicker' ),
			'title'      => self::str( $s, 'title' ),
			'show_image' => self::on( $s, 'show_image' ),
			'image'      => self::media_id( $s, 'image' ),
		);
	}
}

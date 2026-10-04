<?php
/**
 * Almasa header / footer widgets, used inside the Elementor header & footer templates.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Almasa_Widget_Header extends Almasa_Widget {
	protected $template = 'template-parts/header/header';

	public function get_name() {
		return 'almasa-header';
	}

	public function get_title() {
		return 'الماسة — الهيدر';
	}

	public function get_icon() {
		return 'eicon-header';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->add_control( 'note', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => 'اللوجو من المظهر ← تخصيص ← هوية الموقع، والروابط من المظهر ← القوائم.',
			'content_classes' => 'elementor-descriptor',
		) );
		$this->select( 'menu', 'القائمة', $this->menu_options( 'القائمة الرئيسية (الافتراضي)' ), '0' );
		$this->toggle( 'phone', 'إظهار رقم الهاتف' );
		$this->text( 'cta_text', 'نص الزر', 'تواصل معنا', array( 'description' => 'امسح النص لإخفاء الزر.' ) );
		$this->link( 'cta_url', 'رابط الزر', 'تواصل معنا' );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array(
			'label' => 'الهيدر',
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_responsive_control( 'logo_height', array(
			'label'      => 'ارتفاع اللوجو',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'rem' ),
			'range'      => array(
				'px'  => array( 'min' => 30, 'max' => 160 ),
				'rem' => array( 'min' => 2, 'max' => 10, 'step' => 0.25 ),
			),
			'selectors'  => array( '{{WRAPPER}} .almasa-brand img' => 'max-height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_control( 'scrolled_bg', array(
			'label'     => 'خلفية الهيدر بعد النزول',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-site-header.is-scrolled::before' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'link_color', array(
			'label'     => 'لون الروابط',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-nav a' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'link_typo',
			'label'    => 'خط الروابط',
			'selector' => '{{WRAPPER}} .almasa-nav .menu > li > a',
		) );
		$this->end_controls_section();
	}

	protected function template_args( array $s ) {
		return array(
			'menu'     => (int) ( $s['menu'] ?? 0 ),
			'phone'    => self::on( $s, 'phone' ),
			'cta_text' => self::str( $s, 'cta_text' ),
			'cta_url'  => self::url( $s, 'cta_url' ),
		);
	}
}

class Almasa_Widget_Footer extends Almasa_Widget {
	protected $template = 'template-parts/footer/footer';

	public function get_name() {
		return 'almasa-footer';
	}

	public function get_title() {
		return 'الماسة — الفوتر';
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	protected function register_controls() {
		$this->content_section( 'content', 'المحتوى' );
		$this->add_control( 'note', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => 'المواقع والأرقام من لوحة التحكم، والروابط من المظهر ← القوائم.',
			'content_classes' => 'elementor-descriptor',
		) );
		$this->toggle( 'tagline', 'إظهار وصف الموقع' );
		$this->toggle( 'social', 'أيقونات السوشيال ميديا', false );
		$this->select( 'menu', 'القائمة', $this->menu_options( 'قائمة الفوتر (الافتراضي)' ), '0' );
		$this->text( 'nav_title', 'عنوان الروابط', 'تصفح' );
		$this->text( 'locations_title', 'عنوان المواقع', 'مواقعنا' );
		$this->text( 'contact_title', 'عنوان التواصل', 'تواصل' );
		$this->text( 'copyright', 'نص حقوق النشر', get_bloginfo( 'name' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array(
			'label' => 'الفوتر',
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_responsive_control( 'logo_height', array(
			'label'      => 'ارتفاع اللوجو',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'rem' ),
			'range'      => array(
				'px'  => array( 'min' => 40, 'max' => 240 ),
				'rem' => array( 'min' => 2.5, 'max' => 15, 'step' => 0.25 ),
			),
			'selectors'  => array( '{{WRAPPER}} .almasa-footer-brand img' => 'max-height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_control( 'bg', array(
			'label'     => 'لون الخلفية',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-site-footer' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'title_color', array(
			'label'     => 'لون العناوين',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-footer-title' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'text_color', array(
			'label'     => 'لون النص',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .almasa-site-footer, {{WRAPPER}} .almasa-site-footer a' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();
	}

	protected function template_args( array $s ) {
		return array(
			'menu'            => (int) ( $s['menu'] ?? 0 ),
			'tagline'         => self::on( $s, 'tagline' ),
			'social'          => self::on( $s, 'social' ),
			'nav_title'       => self::str( $s, 'nav_title' ),
			'locations_title' => self::str( $s, 'locations_title' ),
			'contact_title'   => self::str( $s, 'contact_title' ),
			'copyright'       => self::str( $s, 'copyright' ),
		);
	}
}

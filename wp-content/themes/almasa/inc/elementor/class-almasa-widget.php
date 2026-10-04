<?php
/**
 * Base for Almasa Elementor widgets: each widget renders a theme template part
 * (template-parts/…) with its settings mapped to template args, so the Elementor
 * layout and the PHP fallback share one markup source.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

abstract class Almasa_Widget extends Widget_Base {

	/**
	 * Template part slug, e.g. 'template-parts/sections/hero'.
	 *
	 * @var string
	 */
	protected $template = '';

	public function get_categories() {
		return array( 'almasa' );
	}

	public function get_keywords() {
		return array( 'almasa', 'الماسة' );
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Settings → template args.
	 *
	 * @param array $s Display settings.
	 * @return array
	 */
	abstract protected function template_args( array $s );

	protected function render() {
		get_template_part( $this->template, null, $this->template_args( $this->get_settings_for_display() ) );
	}

	/* ---------- control helpers ---------- */

	protected function content_section( $id, $label ) {
		$this->start_controls_section( $id, array(
			'label' => $label,
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
	}

	protected function text( $id, $label, $default = '', $args = array() ) {
		$this->add_control( $id, array_merge( array(
			'label'       => $label,
			'type'        => Controls_Manager::TEXT,
			'default'     => $default,
			'label_block' => true,
		), $args ) );
	}

	protected function textarea( $id, $label, $default = '', $args = array() ) {
		$this->add_control( $id, array_merge( array(
			'label'   => $label,
			'type'    => Controls_Manager::TEXTAREA,
			'default' => $default,
			'rows'    => 3,
		), $args ) );
	}

	protected function toggle( $id, $label, $on = true, $args = array() ) {
		$this->add_control( $id, array_merge( array(
			'label'        => $label,
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'نعم',
			'label_off'    => 'لا',
			'return_value' => 'yes',
			'default'      => $on ? 'yes' : '',
		), $args ) );
	}

	/**
	 * URL control prefilled with a page permalink (empty value falls back to it on render).
	 *
	 * @param string $page_title Page whose permalink is the default.
	 */
	protected function link( $id, $label, $page_title = '', $args = array() ) {
		$this->add_control( $id, array_merge( array(
			'label'       => $label,
			'type'        => Controls_Manager::URL,
			'options'     => false,
			'label_block' => true,
			'default'     => array( 'url' => '' !== $page_title ? almasa_page_url( $page_title ) : '' ),
		), $args ) );
	}

	/**
	 * Media control prefilled with the image the section currently shows.
	 *
	 * @param int $default_id Attachment ID used when the control is left untouched.
	 */
	protected function image( $id, $label, $default_id = 0, $args = array() ) {
		$this->add_control( $id, array_merge( array(
			'label'       => $label,
			'type'        => Controls_Manager::MEDIA,
			'default'     => self::media_default( $default_id ),
			'description' => 'لو حذفت الصورة يرجع القسم للصورة الأصلية.',
		), $args ) );
	}

	/**
	 * Read-only list of dashboard records feeding the widget, with an edit link.
	 *
	 * @param string   $label     Heading, e.g. «الخدمات».
	 * @param string[] $items     Current record labels.
	 * @param string   $post_type Post type edited from the dashboard.
	 */
	protected function data_note( $label, array $items, $post_type ) {
		$html = '<strong>' . esc_html( $label ) . '</strong> — تأتي من لوحة التحكم.';
		if ( $items ) {
			$html .= '<ul style="margin:.5em 0;padding-inline-start:1.2em;list-style:disc">';
			foreach ( $items as $item ) {
				$html .= '<li>' . esc_html( $item ) . '</li>';
			}
			$html .= '</ul>';
		}
		$html .= '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . $post_type ) ) . '" target="_blank">تعديل ' . esc_html( $label ) . ' ←</a>';

		$this->add_control( 'note', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => $html,
			'content_classes' => 'elementor-descriptor',
		) );
	}

	/**
	 * Only the editor panel needs the record lists; skip the queries on normal page views.
	 */
	protected static function is_editor_request() {
		return is_admin() || wp_doing_ajax() || isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	protected static function titles( array $posts ) {
		return self::is_editor_request() ? array_map( 'get_the_title', $posts ) : array();
	}

	protected static function contact_lines() {
		if ( ! self::is_editor_request() ) {
			return array();
		}
		return array_map( static function ( $contact ) {
			return trim( get_the_title( $contact ) . ' — ' . almasa_get_field( 'phone', $contact->ID ), ' —' );
		}, almasa_get_structured( 'almasa_contact' ) );
	}

	protected static function media_default( $id ) {
		$id  = (int) $id;
		$url = $id ? wp_get_attachment_image_url( $id, 'large' ) : '';
		return $url ? array( 'id' => $id, 'url' => $url ) : array( 'id' => '', 'url' => '' );
	}

	protected function number( $id, $label, $default, $min = 0, $max = 100, $step = 1, $args = array() ) {
		$this->add_control( $id, array_merge( array(
			'label'   => $label,
			'type'    => Controls_Manager::NUMBER,
			'default' => $default,
			'min'     => $min,
			'max'     => $max,
			'step'    => $step,
		), $args ) );
	}

	protected function select( $id, $label, array $options, $default, $args = array() ) {
		$this->add_control( $id, array_merge( array(
			'label'   => $label,
			'type'    => Controls_Manager::SELECT,
			'options' => $options,
			'default' => $default,
		), $args ) );
	}

	/**
	 * Nav menus as select options (0 = theme location default).
	 *
	 * @param string $default_label Label for the default option.
	 * @return array
	 */
	protected function menu_options( $default_label ) {
		$options = array( '0' => $default_label );
		foreach ( wp_get_nav_menus() as $menu ) {
			$options[ (string) $menu->term_id ] = $menu->name;
		}
		return $options;
	}

	/**
	 * Common "Style" tab: section background/padding + heading/kicker/text colors & type.
	 *
	 * @param array $sel Selectors: root, title, kicker, text (any may be omitted).
	 */
	protected function style_controls( array $sel ) {
		$this->start_controls_section( 'almasa_style_section', array(
			'label' => 'القسم',
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		if ( ! empty( $sel['root'] ) ) {
			$this->add_control( 'almasa_bg', array(
				'label'     => 'لون الخلفية',
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} ' . $sel['root'] => 'background-color: {{VALUE}};' ),
			) );
			$this->add_responsive_control( 'almasa_padding', array(
				'label'      => 'المسافة الداخلية',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', 'vw' ),
				'selectors'  => array( '{{WRAPPER}} ' . $sel['root'] => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};' ),
				'allowed_dimensions' => array( 'top', 'bottom' ),
			) );
		}
		$this->end_controls_section();

		$parts = array(
			'kicker' => 'العنوان الصغير',
			'title'  => 'العنوان',
			'text'   => 'النص',
		);
		foreach ( $parts as $key => $label ) {
			if ( empty( $sel[ $key ] ) ) {
				continue;
			}
			$this->start_controls_section( 'almasa_style_' . $key, array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			) );
			$this->add_control( 'almasa_' . $key . '_color', array(
				'label'     => 'اللون',
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} ' . $sel[ $key ] => 'color: {{VALUE}};' ),
			) );
			$this->add_group_control( Group_Control_Typography::get_type(), array(
				'name'     => 'almasa_' . $key . '_typo',
				'selector' => '{{WRAPPER}} ' . $sel[ $key ],
			) );
			$this->end_controls_section();
		}
	}

	/* ---------- setting readers ---------- */

	protected static function on( array $s, $key ) {
		return isset( $s[ $key ] ) && 'yes' === $s[ $key ];
	}

	protected static function url( array $s, $key ) {
		return isset( $s[ $key ]['url'] ) ? trim( (string) $s[ $key ]['url'] ) : '';
	}

	protected static function media_id( array $s, $key ) {
		return isset( $s[ $key ]['id'] ) ? (int) $s[ $key ]['id'] : 0;
	}

	protected static function str( array $s, $key ) {
		return isset( $s[ $key ] ) ? trim( (string) $s[ $key ] ) : '';
	}
}

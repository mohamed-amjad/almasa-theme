<?php
/**
 * Light hardening. Do not break Elementor or REST.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

remove_action( 'wp_head', 'wp_generator' );

<?php
/**
 * WPBakery Visual Composer Compatibility — Singleton
 *
 * Integrates Opulentia with WPBakery Page Builder:
 * - Graceful coexistence: detects WPBakery before loading
 * - Suppresses theme dynamic CSS when builder is active on a page
 * - Adds body classes for builder detection
 * - Provides theme-styled rows, columns, and modules
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Visual_Composer class.
 */
class Opulentia_Visual_Composer {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor — registers hooks.
	 */
	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'init' ), 20 );
	}

	/**
	 * Initialize WPBakery compatibility.
	 */
	public function init() {
		if ( ! $this->is_vc_active() ) {
			return;
		}

		// Suppress theme dynamic CSS when WPBakery is active on the page.
		add_filter( 'opulentia_dynamic_css_enabled', array( $this, 'maybe_suppress_theme_css' ) );

		// Add WPBakery-specific body classes.
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		// Ensure full-width layouts work with WPBakery rows.
		add_action( 'wp', array( $this, 'support_full_width' ) );
	}

	/**
	 * Check if WPBakery Page Builder is active.
	 *
	 * @return bool
	 */
	private function is_vc_active() {
		if ( defined( 'WPB_VC_VERSION' ) ) {
			return true;
		}

		if ( function_exists( 'vc_is_active' ) && vc_is_active() ) {
			return true;
		}

		if ( class_exists( 'Vc_Manager' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Suppress theme dynamic CSS when WPBakery is editing or displaying.
	 *
	 * @param bool $enabled Whether dynamic CSS is enabled.
	 * @return bool
	 */
	public function maybe_suppress_theme_css( $enabled ) {
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			return false;
		}

		// Suppress on pages that contain WPBakery rows.
		if ( is_singular() ) {
			$post = get_post();
			if ( $post && preg_match( '/vc_row/', $post->post_content ) ) {
				return false;
			}
		}

		return $enabled;
	}

	/**
	 * Add full-width support for WPBakery rows.
	 *
	 * When a page is built with WPBakery, removes theme content
	 * constraints so rows can span the full viewport width.
	 */
	public function support_full_width() {
		if ( ! is_singular() ) {
			return;
		}

		$post = get_post();
		if ( ! $post || ! preg_match( '/vc_row/', $post->post_content ) ) {
			return;
		}

		add_filter( 'Opulentia_layout_content_layout', '__return_false' );
	}

	/**
	 * Add WPBakery-specific body classes.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function body_classes( $classes ) {
		$classes[] = 'opulentia-vc-compat';

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			$classes[] = 'vc-inline-editor-active';
		}

		return $classes;
	}
}

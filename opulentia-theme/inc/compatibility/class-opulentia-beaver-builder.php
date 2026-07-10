<?php
/**
 * Beaver Builder Compatibility — Singleton
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Beaver_Builder class.
 */
class Opulentia_Beaver_Builder {

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
		add_action( 'wp', array( $this, 'support_full_width' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Add full-width support for Beaver Builder.
	 */
	public function support_full_width() {
		if ( ! class_exists( 'FLBuilderModel' ) ) {
			return;
		}
		if ( \FLBuilderModel::is_builder_enabled() ) {
			add_filter( 'Opulentia_layout_content_layout', '__return_false' );
		}
	}

	/**
	 * Add Beaver Builder body class.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( class_exists( 'FLBuilderModel' ) && \FLBuilderModel::is_builder_enabled() ) {
			$classes[] = 'fl-builder-active';
		}
		return $classes;
	}
}

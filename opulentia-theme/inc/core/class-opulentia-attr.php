<?php
/**
 * Opulentia HTML Attribute Builder
 *
 * Singleton that builds and filters HTML attribute strings.
 * Patterned after Astra_Attr for compatibility and extensibility.
 *
 * @package Opulentia
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Opulentia_Attr' ) ) {

	/**
	 * Opulentia_Attr class.
	 */
	class Opulentia_Attr {

		/**
		 * Singleton instance.
		 *
		 * @var self|null
		 */
		private static $instance;

		/**
		 * Returns the singleton instance.
		 *
		 * @return self
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Private constructor (singleton).
		 */
		private function __construct() {}

		/**
		 * Build an HTML attribute string from a context and optional attributes.
		 *
		 * The contextual filter is of the form `Opulentia_attr_{context}_output`.
		 *
		 * @since 1.0.0
		 *
		 * @param  string $context    Context identifier for filter naming.
		 * @param  array  $attributes Optional. Extra attributes to merge with defaults.
		 * @param  array  $args       Optional. Custom data to pass to filter.
		 * @return string             HTML attribute string.
		 */
		public function Opulentia_attr( $context, $attributes = array(), $args = array() ) {
			$attributes = $this->Opulentia_parse_attr( $context, $attributes, $args );
			$output     = '';

			foreach ( $attributes as $key => $value ) {
				if ( ! $value ) {
					continue;
				}

				if ( true === $value ) {
					$output .= esc_html( $key ) . ' ';
				} else {
					$output .= sprintf( '%s="%s" ', esc_html( $key ), esc_attr( $value ) );
				}
			}

			$output = apply_filters( "Opulentia_attr_{$context}_output", $output, $attributes, $context, $args );

			return trim( $output );
		}

		/**
		 * Parse attributes array with defaults for a given context.
		 *
		 * The contextual filter is of the form `Opulentia_attr_{context}`.
		 *
		 * @since 1.0.0
		 *
		 * @param  string $context    Context identifier.
		 * @param  array  $attributes Optional. Extra attributes to merge.
		 * @param  array  $args       Optional. Custom data to pass to filter.
		 * @return array              Merged and filtered attributes.
		 */
		public function Opulentia_parse_attr( $context, $attributes = array(), $args = array() ) {
			$defaults = array(
				'class' => sanitize_html_class( $context ),
			);

			$attributes = wp_parse_args( $attributes, $defaults );

			return apply_filters( "Opulentia_attr_{$context}", $attributes, $context, $args );
		}
	}

	// Kick off the singleton.
	Opulentia_Attr::get_instance();
}

// -----------------------------------------------------------------------------
// Global Helper Function
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_attr' ) ) {
	/**
	 * Convenience wrapper — build an HTML attribute string.
	 *
	 * @param  string $context    Context identifier.
	 * @param  array  $attributes Optional attributes.
	 * @param  array  $args       Optional args.
	 * @return string             HTML attribute string.
	 */
	function Opulentia_attr( $context, $attributes = array(), $args = array() ) {
		return Opulentia_Attr::get_instance()->Opulentia_attr( $context, $attributes, $args );
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class Opulentia_Custom_Fonts {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_fonts' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_custom_fonts' ), 5 );
	}

	public function enqueue_custom_fonts() {
		$body_font    = get_theme_mod( 'Opulentia_body_font_family', 'Inter' );
		$heading_font = get_theme_mod( 'Opulentia_heading_font_family', 'Playfair Display' );
		$fonts        = array_unique( array_filter( array( $body_font, $heading_font ) ) );
		$family_args  = array();

		foreach ( $fonts as $font ) {
			if ( in_array( $font, array( 'Inter', 'Playfair Display', 'inherit', 'initial' ), true ) ) {
				continue;
			}
			$family_args[] = str_replace( ' ', '+', $font ) . ':300,400,500,600,700,800,900&display=swap';
		}

		if ( empty( $family_args ) ) {
			return; }

		$url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $family_args );
		wp_enqueue_style( 'Opulentia-custom-fonts', $url, array(), Opulentia_VERSION );
	}
}

Opulentia_Custom_Fonts::get_instance();

<?php
/**
 * Dynamic CSS — Navigation
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate navigation dynamic CSS.
 *
 * @return string CSS.
 */
function Opulentia_dynamic_navigation_css() {
	$css = '';

	$font_family    = Opulentia_get_option( 'typo-nav-family', '' );
	$font_weight    = Opulentia_get_option( 'typo-nav-weight', '500' );
	$font_size      = Opulentia_get_option( 'typo-nav-size', '14' );
	$transform      = Opulentia_get_option( 'typo-nav-transform', 'uppercase' );
	$letter_spacing = Opulentia_get_option( 'typo-nav-spacing', '1' );

	if ( $font_family ) {
		$css .= ".main-navigation a { font-family: '{$font_family}', sans-serif; }";
	}
	if ( $font_weight ) {
		$css .= ".main-navigation a { font-weight: {$font_weight}; }";
	}
	if ( $font_size ) {
		$css .= ".main-navigation a { font-size: {$font_size}px; }";
	}
	if ( $transform ) {
		$css .= ".main-navigation a { text-transform: {$transform}; }";
	}
	if ( $letter_spacing ) {
		$css .= ".main-navigation a { letter-spacing: {$letter_spacing}px; }";
	}

	return $css;
}

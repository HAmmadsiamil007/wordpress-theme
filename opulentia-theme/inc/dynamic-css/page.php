<?php
/**
 * Page Dynamic CSS
 *
 * Generates inline CSS for individual pages based on customizer settings.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate page dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_page_css() {
	$css = '';

	// Default page title styling.
	$title_color = get_theme_mod( 'page_title_color', '' );
	if ( ! empty( $title_color ) ) {
		$css .= ".page-header__title,\n";
		$css .= ".page-title {\n";
		$css .= "    color: {$title_color};\n";
		$css .= "}\n";
	}

	$title_size = get_theme_mod( 'page_title_size', '' );
	if ( ! empty( $title_size ) ) {
		$css .= ".page-header__title,\n";
		$css .= ".page-title {\n";
		$css .= "    font-size: {$title_size}px;\n";
		$css .= "}\n";
	}

	// Page header styling.
	$header_bg = get_theme_mod( 'page_header_bg', '' );
	if ( ! empty( $header_bg ) ) {
		$css .= ".page-header {\n";
		$css .= "    background-color: {$header_bg};\n";
		$css .= "}\n";
	}

	$header_padding = get_theme_mod( 'page_header_padding', '' );
	if ( ! empty( $header_padding ) ) {
		$css .= ".page-header {\n";
		$css .= "    padding-top: {$header_padding}px;\n";
		$css .= "    padding-bottom: {$header_padding}px;\n";
		$css .= "}\n";
	}

	// Page content padding.
	$content_padding = get_theme_mod( 'page_content_padding', '' );
	if ( ! empty( $content_padding ) ) {
		$css .= ".page-content {\n";
		$css .= "    padding-top: {$content_padding}px;\n";
		$css .= "    padding-bottom: {$content_padding}px;\n";
		$css .= "}\n";
	}

	// Full-width template: remove max-width constraint.
	$is_fullwidth = get_theme_mod( 'page_fullwidth', false );
	if ( $is_fullwidth ) {
		$css .= ".page-template-full-width .site-content,\n";
		$css .= ".page-template-full-width .page-content {\n";
		$css .= "    max-width: none;\n";
		$css .= "}\n";
	}

	// Page featured image styling.
	$image_radius = get_theme_mod( 'page_image_radius', '' );
	if ( ! empty( $image_radius ) ) {
		$css .= ".page-featured-image img {\n";
		$css .= "    border-radius: {$image_radius}px;\n";
		$css .= "}\n";
	}

	$image_margin = get_theme_mod( 'page_image_margin', '' );
	if ( ! empty( $image_margin ) ) {
		$css .= ".page-featured-image {\n";
		$css .= "    margin-bottom: {$image_margin}px;\n";
		$css .= "}\n";
	}

	return $css;
}

<?php
/**
 * 404 Dynamic CSS
 *
 * Generates inline CSS for the 404 error page based on customizer settings.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate 404 page dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_404_css() {
	$css = '';

	// 404 page container centering.
	$container_max_width = get_theme_mod( '404_container_width', '' );
	if ( ! empty( $container_max_width ) ) {
		$css .= ".error-404 {\n";
		$css .= "    max-width: {$container_max_width}px;\n";
		$css .= "}\n";
	}

	$container_padding = get_theme_mod( '404_container_padding', '100' );
	if ( '100' !== $container_padding ) {
		$css .= ".error-404 {\n";
		$css .= "    padding-top: {$container_padding}px;\n";
		$css .= "    padding-bottom: {$container_padding}px;\n";
		$css .= "}\n";
	}

	// 404 title size (large).
	$title_size = get_theme_mod( '404_title_size', '120' );
	if ( '120' !== $title_size ) {
		$css .= ".error-404__title {\n";
		$css .= "    font-size: {$title_size}px;\n";
		$css .= "}\n";
	}

	$title_color = get_theme_mod( '404_title_color', '' );
	if ( ! empty( $title_color ) ) {
		$css .= ".error-404__title {\n";
		$css .= "    color: {$title_color};\n";
		$css .= "}\n";
	}

	// 404 message styling.
	$message_color = get_theme_mod( '404_message_color', '' );
	if ( ! empty( $message_color ) ) {
		$css .= ".error-404__message {\n";
		$css .= "    color: {$message_color};\n";
		$css .= "}\n";
	}

	$message_size = get_theme_mod( '404_message_size', '' );
	if ( ! empty( $message_size ) ) {
		$css .= ".error-404__message {\n";
		$css .= "    font-size: {$message_size}px;\n";
		$css .= "}\n";
	}

	// 404 search form.
	$form_width = get_theme_mod( '404_form_width', '' );
	if ( ! empty( $form_width ) ) {
		$css .= ".error-404 .search-form {\n";
		$css .= "    max-width: {$form_width}px;\n";
		$css .= "}\n";
	}

	$form_border = get_theme_mod( '404_form_border_color', '' );
	if ( ! empty( $form_border ) ) {
		$css .= ".error-404 .search-form .search-field {\n";
		$css .= "    border-color: {$form_border};\n";
		$css .= "}\n";
	}

	// 404 back to home button.
	$btn_bg = get_theme_mod( '404_button_bg', '' );
	if ( ! empty( $btn_bg ) ) {
		$css .= ".error-404__button,\n";
		$css .= ".error-404 .home-button {\n";
		$css .= "    background-color: {$btn_bg};\n";
		$css .= "}\n";
	}

	$btn_color = get_theme_mod( '404_button_color', '' );
	if ( ! empty( $btn_color ) ) {
		$css .= ".error-404__button,\n";
		$css .= ".error-404 .home-button {\n";
		$css .= "    color: {$btn_color};\n";
		$css .= "}\n";
	}

	$btn_hover_bg = get_theme_mod( '404_button_hover_bg', '' );
	if ( ! empty( $btn_hover_bg ) ) {
		$css .= ".error-404__button:hover,\n";
		$css .= ".error-404 .home-button:hover {\n";
		$css .= "    background-color: {$btn_hover_bg};\n";
		$css .= "}\n";
	}

	return $css;
}

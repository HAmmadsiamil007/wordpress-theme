<?php
/**
 * Search Dynamic CSS
 *
 * Generates inline CSS for search results page based on customizer settings.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate search results dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_search_css() {
	$css = '';

	// Search results page title.
	$title_color = get_theme_mod( 'search_title_color', '' );
	if ( ! empty( $title_color ) ) {
		$css .= ".search-header__title {\n";
		$css .= "    color: {$title_color};\n";
		$css .= "}\n";
	}

	$title_size = get_theme_mod( 'search_title_size', '' );
	if ( ! empty( $title_size ) ) {
		$css .= ".search-header__title {\n";
		$css .= "    font-size: {$title_size}px;\n";
		$css .= "}\n";
	}

	// Search result item styling.
	$result_bg = get_theme_mod( 'search_result_bg', '' );
	if ( ! empty( $result_bg ) ) {
		$css .= ".search-result-item {\n";
		$css .= "    background-color: {$result_bg};\n";
		$css .= "}\n";
	}

	$result_border = get_theme_mod( 'search_result_border', '' );
	if ( ! empty( $result_border ) ) {
		$css .= ".search-result-item {\n";
		$css .= "    border-color: {$result_border};\n";
		$css .= "}\n";
	}

	$result_radius = get_theme_mod( 'search_result_radius', '8' );
	if ( '8' !== $result_radius ) {
		$css .= ".search-result-item {\n";
		$css .= "    border-radius: {$result_radius}px;\n";
		$css .= "}\n";
	}

	// Search result highlight (mark tag).
	$highlight_bg = get_theme_mod( 'search_highlight_bg', '' );
	if ( ! empty( $highlight_bg ) ) {
		$css .= ".search-result-item mark {\n";
		$css .= "    background-color: {$highlight_bg};\n";
		$css .= "}\n";
	}

	$highlight_color = get_theme_mod( 'search_highlight_color', '' );
	if ( ! empty( $highlight_color ) ) {
		$css .= ".search-result-item mark {\n";
		$css .= "    color: {$highlight_color};\n";
		$css .= "}\n";
	}

	// No results message styling.
	$no_results_color = get_theme_mod( 'search_no_results_color', '' );
	if ( ! empty( $no_results_color ) ) {
		$css .= ".search-no-results__message {\n";
		$css .= "    color: {$no_results_color};\n";
		$css .= "}\n";
	}

	// Search form styling within results.
	$form_width = get_theme_mod( 'search_form_width', '' );
	if ( ! empty( $form_width ) ) {
		$css .= ".search-results .search-form {\n";
		$css .= "    max-width: {$form_width}px;\n";
		$css .= "}\n";
	}

	$form_border = get_theme_mod( 'search_form_border_color', '' );
	if ( ! empty( $form_border ) ) {
		$css .= ".search-results .search-form .search-field {\n";
		$css .= "    border-color: {$form_border};\n";
		$css .= "}\n";
	}

	$form_bg = get_theme_mod( 'search_form_bg', '' );
	if ( ! empty( $form_bg ) ) {
		$css .= ".search-results .search-form .search-field {\n";
		$css .= "    background-color: {$form_bg};\n";
		$css .= "}\n";
	}

	return $css;
}

<?php
/**
 * Archive Dynamic CSS
 *
 * Generates inline CSS for archive pages based on customizer settings.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate archive page dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_archive_css() {
	$css = '';

	// Archive header padding.
	$header_padding = get_theme_mod( 'archive_header_padding', '60' );
	if ( '60' !== $header_padding ) {
		$css .= ".archive-header {\n";
		$css .= "    padding-top: {$header_padding}px;\n";
		$css .= "    padding-bottom: {$header_padding}px;\n";
		$css .= "}\n";
	}

	// Archive title styling.
	$title_color = get_theme_mod( 'archive_title_color', '' );
	if ( ! empty( $title_color ) ) {
		$css .= ".archive-header__title {\n";
		$css .= "    color: {$title_color};\n";
		$css .= "}\n";
	}

	$title_size = get_theme_mod( 'archive_title_size', '' );
	if ( ! empty( $title_size ) ) {
		$css .= ".archive-header__title {\n";
		$css .= "    font-size: {$title_size}px;\n";
		$css .= "}\n";
	}

	// Archive description styling.
	$desc_color = get_theme_mod( 'archive_desc_color', '' );
	if ( ! empty( $desc_color ) ) {
		$css .= ".archive-header__description {\n";
		$css .= "    color: {$desc_color};\n";
		$css .= "}\n";
	}

	// Archive layout: grid columns.
	$grid_columns = (int) get_theme_mod( 'archive_grid_columns', 2 );
	$grid_columns = max( 1, min( 4, $grid_columns ) );

	if ( 2 !== $grid_columns ) {
		$css .= ".archive-posts-grid {\n";
		$css .= "    grid-template-columns: repeat({$grid_columns}, 1fr);\n";
		$css .= "}\n";
	}

	// Category/tag badge styling.
	$badge_bg = get_theme_mod( 'archive_badge_bg', '' );
	if ( ! empty( $badge_bg ) ) {
		$css .= ".archive-badge {\n";
		$css .= "    background-color: {$badge_bg};\n";
		$css .= "}\n";
	}

	$badge_color = get_theme_mod( 'archive_badge_color', '' );
	if ( ! empty( $badge_color ) ) {
		$css .= ".archive-badge {\n";
		$css .= "    color: {$badge_color};\n";
		$css .= "}\n";
	}

	$badge_radius = get_theme_mod( 'archive_badge_radius', '4' );
	if ( '4' !== $badge_radius ) {
		$css .= ".archive-badge {\n";
		$css .= "    border-radius: {$badge_radius}px;\n";
		$css .= "}\n";
	}

	return $css;
}

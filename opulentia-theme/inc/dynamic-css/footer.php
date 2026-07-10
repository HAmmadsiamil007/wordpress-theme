<?php
/**
 * Footer Dynamic CSS
 *
 * Generates inline CSS for footer-related elements
 * based on customizer settings — widget grid columns,
 * newsletter section, trust badges, footer rows, bottom bar.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate footer dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_footer_css() {
	$css = '';

	// Footer background.
	$footer_bg = get_theme_mod( 'color_footer_bg', '' );
	if ( ! empty( $footer_bg ) ) {
		$css .= ".site-footer {\n";
		$css .= "    --color-footer-bg: {$footer_bg};\n";
		$css .= "    background-color: {$footer_bg};\n";
		$css .= "}\n";
	}

	// Footer widget grid column widths.
	$columns        = (int) get_theme_mod( 'footer_columns', 4 );
	$columns        = max( 2, min( 5, $columns ) );
	$show_brand     = get_theme_mod( 'footer_show_brand', true );
	$widget_columns = $show_brand ? $columns : $columns;

	// Calculate column fractions (brand takes 2fr if shown, rest are 1fr).
	$total_fr = $show_brand ? ( $widget_columns - 1 + 2 ) : $widget_columns;
	$brand_fr = $show_brand ? '2fr' : '0';
	$col_fr   = $show_brand ? '1fr' : ( $widget_columns > 0 ? ( 100 / $widget_columns ) . '%' : '1fr' );

	$grid_template = $show_brand ? "{$brand_fr} " . str_repeat( "{$col_fr} ", $widget_columns - 1 ) : str_repeat( "{$col_fr} ", $widget_columns );
	$grid_template = trim( $grid_template );

	if ( ! empty( $grid_template ) ) {
		$css .= ".footer-widget-grid {\n";
		$css .= "    grid-template-columns: {$grid_template};\n";
		$css .= "}\n";
	}

	// Newsletter section background.
	$newsletter_bg = get_theme_mod( 'color_newsletter_bg', '' );
	if ( ! empty( $newsletter_bg ) ) {
		$css .= ".footer-newsletter {\n";
		$css .= "    background: {$newsletter_bg};\n";
		$css .= "}\n";
	}

	// Trust badges section background.
	$trust_badges_bg = get_theme_mod( 'color_trust_badges_bg', '' );
	if ( ! empty( $trust_badges_bg ) ) {
		$css .= ".footer-trust-badges {\n";
		$css .= "    background: {$trust_badges_bg};\n";
		$css .= "}\n";
	}

	// Footer row above background.
	$footer_above_bg = get_theme_mod( 'color_footer_above_bg', '' );
	if ( ! empty( $footer_above_bg ) ) {
		$css .= ".footer-row--above {\n";
		$css .= "    background-color: {$footer_above_bg};\n";
		$css .= "}\n";
	}

	// Footer bottom bar background.
	$footer_bottom_bg = get_theme_mod( 'color_footer_bottom_bg', '' );
	if ( ! empty( $footer_bottom_bg ) ) {
		$css .= ".footer-bottom {\n";
		$css .= "    background-color: {$footer_bottom_bg};\n";
		$css .= "}\n";
	}

	return $css;
}

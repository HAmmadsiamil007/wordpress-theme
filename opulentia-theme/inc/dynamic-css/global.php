<?php
/**
 * Global Dynamic CSS — CSS Variables from Customizer Settings
 *
 * Generates :root CSS custom properties based on theme_mod values.
 * All defaults match the Opulentia design tokens from style.css.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate global CSS variable declarations.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_global_css() {
	$css = '';

	// ---- Color Palette ----
	$primary_dark   = get_theme_mod( 'color_primary_dark', '#1a1a1a' );
	$secondary_dark = get_theme_mod( 'color_secondary_dark', '#111111' );
	$accent         = get_theme_mod( 'color_accent', '#b8860b' );
	$accent_hover   = get_theme_mod( 'color_accent_hover', '#d4a843' );
	$gold           = get_theme_mod( 'color_gold', '#c9a96e' );
	$gold_hover     = get_theme_mod( 'color_gold_hover', '#b8944f' );
	$light_gold     = get_theme_mod( 'color_light_gold', '#e8d5a3' );
	$light_gray     = get_theme_mod( 'color_light_gray', '#2a2a2a' );
	$medium_gray    = get_theme_mod( 'color_medium_gray', '#999999' );
	$text_color     = get_theme_mod( 'color_text', '#f5f5f5' );
	$border_color   = get_theme_mod( 'color_border', '#333333' );

	// ---- Element Colors ----
	$link_color      = get_theme_mod( 'color_link', '#c9a96e' );
	$link_hover      = get_theme_mod( 'color_link_hover', '#d4a843' );
	$button_bg       = get_theme_mod( 'color_button_bg', '#c9a96e' );
	$button_text     = get_theme_mod( 'color_button_text', '#ffffff' );
	$button_hover_bg = get_theme_mod( 'color_button_hover_bg', '#b8944f' );

	$css .= "    --color-primary-dark: {$primary_dark};\n";
	$css .= "    --color-secondary-dark: {$secondary_dark};\n";
	$css .= "    --color-accent: {$accent};\n";
	$css .= "    --color-accent-hover: {$accent_hover};\n";
	$css .= "    --color-gold: {$gold};\n";
	$css .= "    --color-gold-hover: {$gold_hover};\n";
	$css .= "    --color-light-gold: {$light_gold};\n";
	$css .= "    --color-off-white: {$light_gray};\n";
	$css .= "    --color-light-gray: {$light_gray};\n";
	$css .= "    --color-medium-gray: {$medium_gray};\n";
	$css .= "    --color-text: {$text_color};\n";
	$css .= "    --color-text-muted: {$medium_gray};\n";
	$css .= "    --color-border: {$border_color};\n";

	// ---- Element Colors ----
	$css .= "    --color-link: {$link_color};\n";
	$css .= "    --color-link-hover: {$link_hover};\n";
	$css .= "    --color-button-bg: {$button_bg};\n";
	$css .= "    --color-button-text: {$button_text};\n";
	$css .= "    --color-button-hover-bg: {$button_hover_bg};\n";

	// ---- Derived Colors ----
	$white = '#ffffff';
	$css  .= "    --color-white: {$white};\n";

	// ---- Typography ----
	$heading_font = get_theme_mod( 'typography_heading', 'Playfair Display' );
	$body_font    = get_theme_mod( 'typography_body', 'Inter' );

	$heading_font_stack = "'{$heading_font}', Georgia, serif";
	$body_font_stack    = "'{$body_font}', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";

	$css .= "    --font-heading: {$heading_font_stack};\n";
	$css .= "    --font-body: {$body_font_stack};\n";

	// ---- Layout ----
	$container_max    = get_theme_mod( 'layout_container_max', '1200px' );
	$container_narrow = get_theme_mod( 'layout_container_narrow', '900px' );

	$css .= "    --container-max: {$container_max};\n";
	$css .= "    --container-narrow: {$container_narrow};\n";

	// ---- Spacing ----
	$section_padding_top    = get_theme_mod( 'layout_section_padding_top', 80 );
	$section_padding_bottom = get_theme_mod( 'layout_section_padding_bottom', 80 );

	$css .= "    --section-padding-top: {$section_padding_top}px;\n";
	$css .= "    --section-padding-bottom: {$section_padding_bottom}px;\n";

	return $css;
}

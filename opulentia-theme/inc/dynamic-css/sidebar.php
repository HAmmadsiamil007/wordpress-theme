<?php
/**
 * Dynamic CSS — Sidebar
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate sidebar dynamic CSS.
 *
 * @return string CSS.
 */
function Opulentia_dynamic_sidebar_css() {
	$css = '';

	$sidebar_width = Opulentia_get_option( 'sidebar_width', 300 );
	$sidebar_bg    = Opulentia_get_option( 'sidebar_bg_color', '' );

	if ( $sidebar_width ) {
		$css .= ".sidebar { width: {$sidebar_width}px; }";
		$css .= ".content-sidebar-layout { grid-template-columns: 1fr {$sidebar_width}px; }";
	}
	if ( $sidebar_bg ) {
		$css .= ".sidebar { background-color: {$sidebar_bg}; }";
	}

	return $css;
}

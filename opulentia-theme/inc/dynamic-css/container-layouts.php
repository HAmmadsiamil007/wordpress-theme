<?php
/**
 * Dynamic CSS — Container Layouts
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate container layout CSS.
 *
 * When the Site Layouts module is active, delegates to the module.
 * Falls back to legacy settings only when the module is not loaded.
 *
 * @return string CSS.
 */
function Opulentia_dynamic_container_css() {
	// If the Site Layouts module is loaded, delegate to it.
	if ( class_exists( 'Opulentia_Site_Layouts' ) ) {
		return Opulentia_Site_Layouts::get_instance()->get_site_layout_css();
	}

	// Legacy fallback (only used if module is not active).
	$css    = '';
	$layout = Opulentia_get_option( 'layout_content_layout', 'boxed' );

	if ( 'full-width' === $layout ) {
		$css .= '
        .site-content .container {
            max-width: 100%;
            padding: 0 40px;
        }
        ';
	}

	return $css;
}

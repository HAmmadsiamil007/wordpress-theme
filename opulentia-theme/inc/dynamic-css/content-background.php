<?php
/**
 * Dynamic CSS — Content Background
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate content background dynamic CSS.
 *
 * @return string CSS.
 */
function Opulentia_dynamic_content_background_css() {
    $css = '';

    $content_bg = Opulentia_get_option( 'color_content_bg', '' );
    $box_bg     = Opulentia_get_option( 'color_box_bg', '' );

    if ( $content_bg ) {
        $css .= ".site-content { background-color: {$content_bg}; }";
    }
    if ( $box_bg ) {
        $css .= ".boxed-content { background-color: {$box_bg}; }";
    }

    return $css;
}

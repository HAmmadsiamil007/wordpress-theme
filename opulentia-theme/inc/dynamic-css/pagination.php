<?php
/**
 * Dynamic CSS — Pagination
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate pagination dynamic CSS.
 *
 * @return string CSS.
 */
function Opulentia_dynamic_pagination_css() {
    $css = '';

    $bg_color   = Opulentia_get_option( 'color_pagination_bg', '' );
    $text_color = Opulentia_get_option( 'color_pagination_text', '' );
    $active_bg  = Opulentia_get_option( 'color_pagination_active_bg', '' );
    $radius     = Opulentia_get_option( 'pagination_radius', '4' );

    if ( $bg_color ) {
        $css .= ".pagination__link { background-color: {$bg_color}; }";
    }
    if ( $text_color ) {
        $css .= ".pagination__link { color: {$text_color}; }";
    }
    if ( $active_bg ) {
        $css .= ".pagination__link--active { background-color: {$active_bg}; border-color: {$active_bg}; }";
    }
    if ( $radius ) {
        $css .= ".pagination__link { border-radius: {$radius}px; }";
    }

    return $css;
}

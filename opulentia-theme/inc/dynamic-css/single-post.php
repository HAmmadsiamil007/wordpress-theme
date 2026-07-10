<?php
/**
 * Dynamic CSS — Single Post Styles
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate single post dynamic CSS.
 *
 * @return string CSS.
 */
function Opulentia_dynamic_single_post_css() {
    $css = '';

    $show_author = Opulentia_get_option( 'blog-single-show-author', true );
    if ( ! $show_author ) {
        $css .= '.single-post .author-box { display: none; }';
    }

    $show_related = Opulentia_get_option( 'blog-single-show-related', true );
    if ( ! $show_related ) {
        $css .= '.single-post .related-posts { display: none; }';
    }

    $show_nav = Opulentia_get_option( 'blog-single-show-navigation', true );
    if ( ! $show_nav ) {
        $css .= '.single-post .post-navigation { display: none; }';
    }

    return $css;
}

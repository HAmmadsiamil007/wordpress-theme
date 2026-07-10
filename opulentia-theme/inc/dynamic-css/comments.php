<?php
/**
 * Dynamic CSS — Comments
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate comments dynamic CSS.
 *
 * @return string CSS.
 */
function Opulentia_dynamic_comments_css() {
    $css = '';

    $comment_bg   = Opulentia_get_option( 'color_comment_bg', '' );
    $comment_border = Opulentia_get_option( 'color_comment_border', '' );

    if ( $comment_bg ) {
        $css .= ".comment { background-color: {$comment_bg}; }";
    }
    if ( $comment_border ) {
        $css .= ".comment { border-color: {$comment_border}; }";
    }

    return $css;
}

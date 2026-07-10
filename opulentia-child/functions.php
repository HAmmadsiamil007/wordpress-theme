<?php
/**
 * Opulentia Child Theme Functions
 *
 * @package Opulentia_Child
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue parent theme styles first, then child overrides.
 */
function opulentia_child_enqueue_styles() {
    wp_enqueue_style(
        'opulentia-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get( 'Version' )
    );

    wp_enqueue_style(
        'opulentia-child-style',
        get_stylesheet_uri(),
        array( 'opulentia-parent-style' ),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'opulentia_child_enqueue_styles' );

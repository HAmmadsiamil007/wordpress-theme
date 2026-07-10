<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function opulentia_update_2_0_0() {
    $settings = get_option( Opulentia_SETTINGS, array() );

    if ( ! is_array( $settings ) || empty( $settings ) ) {
        $settings = array();
    }

    if ( ! isset( $settings['version'] ) ) {
        $settings['version'] = '2.0.0';
    }

    if ( ! isset( $settings['container-width'] ) ) {
        $settings['container-width'] = 1200;
    }

    if ( ! isset( $settings['site-layout'] ) ) {
        $settings['site-layout'] = 'full-width';
    }

    if ( ! isset( $settings['sidebar-layout'] ) ) {
        $settings['sidebar-layout'] = 'right';
    }

    if ( ! isset( $settings['header-layout'] ) ) {
        $settings['header-layout'] = 'standard';
    }

    if ( ! isset( $settings['footer-layout'] ) ) {
        $settings['footer-layout'] = 'boxed';
    }

    if ( ! isset( $settings['blog-layout'] ) ) {
        $settings['blog-layout'] = 'grid';
    }

    if ( ! isset( $settings['blog-columns'] ) ) {
        $settings['blog-columns'] = 3;
    }

    if ( ! isset( $settings['primary-color'] ) ) {
        $settings['primary-color'] = '#b8860b';
    }

    if ( ! isset( $settings['accent-color'] ) ) {
        $settings['accent-color'] = '#c9a96e';
    }

    $dynamic_css_cache = get_transient( 'Opulentia_dynamic_css' );
    if ( false !== $dynamic_css_cache ) {
        delete_transient( 'Opulentia_dynamic_css' );
    }

    update_option( Opulentia_SETTINGS, $settings );

    return true;
}

<?php
/**
 * Header Dynamic CSS
 *
 * Generates inline CSS for header-related elements
 * based on customizer settings.
 * Supports all 5 layout presets, row visibility, sticky/transparent states.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate header dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_header_css() {
    $css = '';

    // Sticky header.
    $sticky = Opulentia_get_option( 'header-sticky', true );
    if ( ! $sticky ) {
        $css .= '.site-header{position:relative;}';
    }

    // Transparent header.
    $transparent = Opulentia_get_option( 'header-transparent', false );
    if ( $transparent ) {
        $css .= '.site-header--transparent{background-color:transparent;position:absolute;width:100%;}';
        $css .= '.site-header--transparent.scrolled{background-color:rgba(26,26,26,0.95);position:fixed;}';
    }

    // Header background (when not transparent).
    $header_bg = Opulentia_get_option( 'color-header-bg', '#1a1a1a' );
    if ( $header_bg ) {
        $css .= '.site-header{background-color:' . esc_attr( $header_bg ) . ';}';
    }

    // Header scroll background.
    $scroll_bg = Opulentia_get_option( 'color-header-scroll-bg', 'rgba(17, 17, 17, 0.95)' );
    if ( $scroll_bg ) {
        $css .= '.site-header.scrolled{background-color:' . esc_attr( $scroll_bg ) . ';}';
    }

    // Top bar background.
    $top_bar_bg = Opulentia_get_option( 'color-header-top-bar-bg', '#111111' );
    if ( $top_bar_bg ) {
        $css .= '.header-row--above{background-color:' . esc_attr( $top_bar_bg ) . ';}';
    }

    // Header layout-specific spacing.
    $layout = Opulentia_get_option( 'header-layout', 'standard' );
    if ( 'centered' === $layout ) {
        $css .= '.site-header--centered .header-main__inner{padding-top:24px;}';
        $css .= '.site-header--centered .header-col--nav-full{margin-top:8px;}';
    }
    if ( 'minimal' === $layout ) {
        $css .= '.site-header--minimal .header-main__inner{padding-top:8px;padding-bottom:8px;}';
    }
    if ( 'stacked' === $layout ) {
        $css .= '.site-header--stacked .header-stacked-row--top{padding-bottom:8px;border-bottom:1px solid #333;}';
        $css .= '.site-header--stacked .header-stacked-row--bottom{padding-top:8px;}';
    }

    // Responsive: hide rows per device.
    $css .= Opulentia_get_header_row_visibility_css();

    return $css;
}

/**
 * Generate CSS for per-row device visibility.
 *
 * Reads header-row-*-visibility settings and outputs media queries
 * to hide rows on specific devices.
 *
 * @return string CSS.
 */
function Opulentia_get_header_row_visibility_css() {
    $css = '';
    $rows = array( 'above', 'main', 'below' );
    $breakpoints = array(
        'tablet' => Opulentia_get_tablet_breakpoint(),
        'mobile' => Opulentia_get_mobile_breakpoint(),
    );

    foreach ( $rows as $row ) {
        $setting = 'header-row-' . $row . '-visibility';
        $visibility = Opulentia_get_option( $setting, array(
            'desktop' => true,
            'tablet'  => true,
            'mobile'  => true,
        ) );

        foreach ( $breakpoints as $device => $bp ) {
            if ( isset( $visibility[ $device ] ) && ! $visibility[ $device ] ) {
                $css .= '@media (max-width:' . $bp . 'px){';
                $css .= '.hide-' . $device . '-' . $row . ' .header-row--' . $row . ',';
                $css .= '.hide-' . $device . '-' . $row . ' .header-main{display:none !important;}';
                $css .= '}';
            }
        }
    }

    return $css;
}

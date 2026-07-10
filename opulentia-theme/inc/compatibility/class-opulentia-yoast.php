<?php
/**
 * Yoast SEO Compatibility — Singleton
 *
 * Integrates Yoast SEO breadcrumbs into the Opulentia theme.
 * Replaces the default theme breadcrumbs when Yoast is active.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Yoast class.
 */
class Opulentia_Yoast {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Returns the singleton instance.
     *
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor — registers hooks.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    /**
     * Initialize Yoast SEO compatibility.
     *
     * Adds theme support for Yoast breadcrumbs. The actual breadcrumb
     * rendering is handled by the Breadcrumbs module which auto-detects
     * Yoast and delegates to yoast_breadcrumb() when appropriate.
     */
    public function init() {
        if ( ! $this->is_yoast_active() ) {
            return;
        }

        // Add Yoast breadcrumb support to the theme for the customizer setting.
        add_theme_support( 'yoast-seo-breadcrumbs' );

        // Delegate breadcrumb display to the Breadcrumbs module.
        // The module's auto-display hooks into Opulentia_content_top
        // and handles Yoast/Rank Math detection internally.
        remove_action( 'Opulentia_content_top', array( $this, 'maybe_display_breadcrumbs' ) );
    }

    /**
     * Check if Yoast SEO is active.
     *
     * @return bool
     */
    private function is_yoast_active() {
        return defined( 'WPSEO_VERSION' ) || defined( 'WPSEO_PREMIUM_VERSION' );
    }


}

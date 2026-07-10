<?php
/**
 * Beaver Themer Compatibility — Singleton
 *
 * Integrates Opulentia with Beaver Themer's theme builder:
 * - Registers theme support for headers, footers, and parts
 * - Replaces theme header/footer when Beaver Themer templates are assigned
 * - Registers Opulentia theme hook locations for parts
 * - Adds body classes for builder detection
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Beaver_Themer class.
 */
class Opulentia_Beaver_Themer {

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
        add_action( 'after_setup_theme', array( $this, 'init' ), 20 );
    }

    /**
     * Initialize Beaver Themer integration.
     */
    public function init() {
        if ( ! $this->is_beaver_themer_active() ) {
            return;
        }

        // Declare theme support for Beaver Themer.
        add_theme_support( 'fl-theme-builder-headers' );
        add_theme_support( 'fl-theme-builder-footers' );
        add_theme_support( 'fl-theme-builder-parts' );

        // Replace theme header/footer with Themer templates.
        add_action( 'wp', array( $this, 'handle_header_footer' ) );

        // Register theme hook locations for parts.
        add_filter( 'fl_theme_builder_part_hooks', array( $this, 'register_part_hooks' ) );

        // Add body classes.
        add_filter( 'body_class', array( $this, 'body_classes' ) );
    }

    /**
     * Check if Beaver Themer is active.
     *
     * @return bool
     */
    private function is_beaver_themer_active() {
        return class_exists( 'FLThemeBuilderLoader' )
            || class_exists( 'FLThemeBuilderLayoutData' );
    }

    /**
     * Handle header/footer replacement on frontend.
     *
     * If Beaver Themer has header/footer templates assigned for the current page,
     * removes the theme's native output and renders the Themer version instead.
     */
    public function handle_header_footer() {
        if ( is_admin() || ! class_exists( 'FLThemeBuilderLayoutData' ) ) {
            return;
        }

        // Replace header if Themer has a matching template.
        $header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();
        if ( ! empty( $header_ids ) ) {
            add_filter( 'opulentia_header_enabled', '__return_false' );
            add_action( 'Opulentia_header_before', array( $this, 'render_themer_header' ) );
        }

        // Replace footer if Themer has a matching template.
        $footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();
        if ( ! empty( $footer_ids ) ) {
            add_filter( 'opulentia_footer_enabled', '__return_false' );
            add_action( 'Opulentia_footer_after', array( $this, 'render_themer_footer' ) );
        }
    }

    /**
     * Render Beaver Themer header.
     */
    public function render_themer_header() {
        if ( class_exists( 'FLThemeBuilderLayoutRenderer' ) ) {
            echo '<header id="opulentia-bb-header" class="opulentia-bb-header">';
            FLThemeBuilderLayoutRenderer::render_header();
            echo '</header>';
        }
    }

    /**
     * Render Beaver Themer footer.
     */
    public function render_themer_footer() {
        if ( class_exists( 'FLThemeBuilderLayoutRenderer' ) ) {
            echo '<footer id="opulentia-bb-footer" class="opulentia-bb-footer">';
            FLThemeBuilderLayoutRenderer::render_footer();
            echo '</footer>';
        }
    }

    /**
     * Register Opulentia theme hook locations for Beaver Themer parts.
     *
     * Maps Opulentia action hooks to Beaver Themer so users can inject
     * custom content at specific locations via the Themer UI.
     *
     * @param array $hooks Registered part hooks.
     * @return array
     */
    public function register_part_hooks( $hooks ) {
        $hooks[] = array(
            'label' => __( 'Opulentia Theme', 'opulentia' ),
            'hooks' => array(
                'Opulentia_body_top'                 => __( 'Body Top', 'opulentia' ),
                'Opulentia_header_before'            => __( 'Before Header', 'opulentia' ),
                'Opulentia_header_after'             => __( 'After Header', 'opulentia' ),
                'Opulentia_primary_content_before'   => __( 'Before Content', 'opulentia' ),
                'Opulentia_primary_content_after'    => __( 'After Content', 'opulentia' ),
                'Opulentia_footer_before'            => __( 'Before Footer', 'opulentia' ),
                'Opulentia_footer_after'             => __( 'After Footer', 'opulentia' ),
                'Opulentia_body_bottom'              => __( 'Body Bottom', 'opulentia' ),
            ),
        );

        return $hooks;
    }

    /**
     * Add Beaver Themer body classes.
     *
     * @param array $classes Body classes.
     * @return array
     */
    public function body_classes( $classes ) {
        $classes[] = 'opulentia-bb-themer-compat';

        if ( class_exists( 'FLThemeBuilderLayoutData' ) ) {
            $header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();
            if ( ! empty( $header_ids ) ) {
                $classes[] = 'fl-themer-header-active';
            }

            $footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();
            if ( ! empty( $footer_ids ) ) {
                $classes[] = 'fl-themer-footer-active';
            }
        }

        return $classes;
    }
}

<?php
/**
 * Divi Builder Compatibility — Singleton
 *
 * Integrates Opulentia with the Divi Builder (Elegant Themes):
 * - Graceful coexistence: checks for Divi classes before enqueuing
 * - Suppresses theme dynamic CSS when Divi Builder is editing
 * - Ensures smooth Content Builder integration
 * - Handles Divi theme builder overrides for header/footer
 * - Prevents CSS conflicts between theme and Divi
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Divi class.
 */
class Opulentia_Divi {

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
     * Initialize Divi compatibility.
     */
    public function init() {
        if ( ! $this->is_divi_active() ) {
            return;
        }

        // Suppress theme dynamic CSS when Divi Builder is active on the page.
        add_filter( 'opulentia_dynamic_css_enabled', array( $this, 'maybe_suppress_theme_css' ) );

        // Disable theme actions that conflict with Divi Theme Builder.
        add_action( 'template_redirect', array( $this, 'handle_theme_builder' ), 0 );

        // Add Divi-specific body classes.
        add_filter( 'body_class', array( $this, 'body_classes' ) );

        // Ensure Divi builder modules load properly.
        add_action( 'et_builder_ready', array( $this, 'on_builder_ready' ) );
    }

    /**
     * Check if Divi is active as theme or plugin.
     *
     * @return bool
     */
    private function is_divi_active() {
        // Divi theme.
        if ( function_exists( 'et_setup_theme' ) ) {
            return true;
        }

        // Divi Builder plugin.
        if ( class_exists( 'ET_Builder_Plugin' ) || defined( 'ET_BUILDER_PLUGIN_VERSION' ) ) {
            return true;
        }

        // Divi module base class check.
        if ( class_exists( 'ET_Builder_Module' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Suppress theme dynamic CSS when Divi Builder is editing.
     *
     * Divi Builder has its own CSS system. When the builder is active
     * on a page, we suppress theme-generated inline CSS to avoid
     * specificity conflicts and visual duplication.
     *
     * @param bool $enabled Whether dynamic CSS is enabled.
     * @return bool
     */
    public function maybe_suppress_theme_css( $enabled ) {
        if ( function_exists( 'et_core_is_builder_used_on_current_request' ) ) {
            if ( et_core_is_builder_used_on_current_request() ) {
                return false;
            }
        }

        return $enabled;
    }

    /**
     * Handle Divi Theme Builder overrides.
     *
     * When Divi Theme Builder assigns custom headers, footers, or
     * body templates, we let Divi handle them instead of the theme.
     */
    public function handle_theme_builder() {
        // Check if Divi Theme Builder is overriding the header.
        if ( function_exists( 'et_theme_builder_is_layout_active' ) ) {
            // If a Divi theme builder layout is active for header/footer,
            // suppress our theme header/footer hooks.
            if ( et_theme_builder_is_layout_active() ) {
                add_filter( 'opulentia_header_enabled', '__return_false' );
                add_filter( 'opulentia_footer_enabled', '__return_false' );
            }
        }
    }

    /**
     * Add Divi-specific body classes.
     *
     * @param array $classes Body classes.
     * @return array
     */
    public function body_classes( $classes ) {
        $classes[] = 'opulentia-divi-compat';

        if ( function_exists( 'et_core_is_builder_used_on_current_request' ) ) {
            if ( et_core_is_builder_used_on_current_request() ) {
                $classes[] = 'opulentia-divi-builder-active';
            }
        }

        return $classes;
    }

    /**
     * Fires when the Divi builder engine is fully loaded.
     *
     * Hook into this to register custom Divi modules or extend
     * builder functionality specific to the Opulentia theme.
     */
    public function on_builder_ready() {
        // Future: Register custom Divi modules here if needed.
    }
}

<?php
/**
 * Dynamic CSS Engine — Singleton
 *
 * The central engine that:
 * 1. Loads all sub-module CSS generators (global, header, footer, blog, typography, woocommerce)
 * 2. Compiles them into a single CSS string
 * 3. Caches the output via WordPress transients
 * 4. Outputs the CSS inline in <head> via wp_head hook
 * 5. Invalidates the cache when customizer settings are saved
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Dynamic_CSS class.
 */
class Opulentia_Dynamic_CSS {

    /**
     * Transient key for cached CSS.
     */
    const CACHE_KEY = 'Opulentia_dynamic_css';

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
        // Output dynamic CSS in the head, after main stylesheet.
        add_action( 'wp_head', array( $this, 'output_dynamic_css' ), 99 );

        // Invalidate cache when customizer settings are saved.
        add_action( 'customize_save_after', array( $this, 'invalidate_cache' ) );

        // Also invalidate when theme mods are updated via switch_theme.
        add_action( 'switch_theme', array( $this, 'invalidate_cache' ) );
    }

    /**
     * Output the dynamic CSS inline in <head>.
     *
     * The CSS is passed through the 'Opulentia_dynamic_css_output' filter
     * for minification (handled by Opulentia_Performance::minify_css()).
     */
    public function output_dynamic_css() {
        $css = $this->get_cached_css();

        if ( empty( $css ) ) {
            return;
        }

        // Apply performance minification filter.
        $css = apply_filters( 'Opulentia_dynamic_css_output', $css );

        echo "<style id='opulentia-dynamic-css' type='text/css'>\n";
        echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo "</style>\n";
    }

    /**
     * Get the cached CSS, generating it if the cache is stale or empty.
     *
     * @return string Compiled CSS string.
     */
    private function get_cached_css() {
        $cached = get_transient( self::CACHE_KEY );

        if ( false !== $cached && ! empty( $cached ) ) {
            return $cached;
        }

        $css = $this->compile_css();

        // Cache for 24 hours. Invalidated on customizer save.
        set_transient( self::CACHE_KEY, $css, DAY_IN_SECONDS );

        return $css;
    }

    /**
     * Compile all sub-module CSS generators into a single string.
     *
     * @return string Compiled CSS.
     */
    private function compile_css() {
        $css = "/* Opulentia Dynamic CSS — Generated from Customizer Settings */\n";

        // Global CSS & palette variables — always output.
        $global_css = function_exists( 'Opulentia_dynamic_global_css' ) ? Opulentia_dynamic_global_css() : '';
        $css .= ":root {\n";
        $css .= $global_css;
        $css .= $this->get_global_palette_css();
        $css .= "}\n\n";

        // General spacing — apply section padding to major sections.
        $spacing_css = $this->get_spacing_css();
        if ( ! empty( $spacing_css ) ) {
            $css .= "/* Spacing Styles */\n{$spacing_css}\n";
        }

        // Container layout styles.
        $container_css = function_exists( 'Opulentia_dynamic_container_css' ) ? Opulentia_dynamic_container_css() : '';
        if ( ! empty( $container_css ) ) {
            $css .= "/* Container Layouts */\n{$container_css}\n";
        }

        // Header styles.
        $header_css = function_exists( 'Opulentia_dynamic_header_css' ) ? Opulentia_dynamic_header_css() : '';
        if ( ! empty( $header_css ) ) {
            $css .= "/* Header Styles */\n{$header_css}\n";
        }

        // Footer styles.
        $footer_css = function_exists( 'Opulentia_dynamic_footer_css' ) ? Opulentia_dynamic_footer_css() : '';
        if ( ! empty( $footer_css ) ) {
            $css .= "/* Footer Styles */\n{$footer_css}\n";
        }

        // Blog styles.
        $blog_css = function_exists( 'Opulentia_dynamic_blog_css' ) ? Opulentia_dynamic_blog_css() : '';
        if ( ! empty( $blog_css ) ) {
            $css .= "/* Blog Styles */\n{$blog_css}\n";
        }

        // Single post styles.
        $single_css = function_exists( 'Opulentia_dynamic_single_post_css' ) ? Opulentia_dynamic_single_post_css() : '';
        if ( ! empty( $single_css ) ) {
            $css .= "/* Single Post Styles */\n{$single_css}\n";
        }

        // Navigation styles.
        $nav_css = function_exists( 'Opulentia_dynamic_navigation_css' ) ? Opulentia_dynamic_navigation_css() : '';
        if ( ! empty( $nav_css ) ) {
            $css .= "/* Navigation Styles */\n{$nav_css}\n";
        }

        // Sidebar styles.
        $sidebar_css = function_exists( 'Opulentia_dynamic_sidebar_css' ) ? Opulentia_dynamic_sidebar_css() : '';
        if ( ! empty( $sidebar_css ) ) {
            $css .= "/* Sidebar Styles */\n{$sidebar_css}\n";
        }

        // Comments styles.
        $comments_css = function_exists( 'Opulentia_dynamic_comments_css' ) ? Opulentia_dynamic_comments_css() : '';
        if ( ! empty( $comments_css ) ) {
            $css .= "/* Comments Styles */\n{$comments_css}\n";
        }

        // Content background styles.
        $content_bg_css = function_exists( 'Opulentia_dynamic_content_background_css' ) ? Opulentia_dynamic_content_background_css() : '';
        if ( ! empty( $content_bg_css ) ) {
            $css .= "/* Content Background */\n{$content_bg_css}\n";
        }

        // Pagination styles.
        $pagination_css = function_exists( 'Opulentia_dynamic_pagination_css' ) ? Opulentia_dynamic_pagination_css() : '';
        if ( ! empty( $pagination_css ) ) {
            $css .= "/* Pagination Styles */\n{$pagination_css}\n";
        }

        // Typography styles.
        $typography_css = function_exists( 'Opulentia_dynamic_typography_css' ) ? Opulentia_dynamic_typography_css() : '';
        if ( ! empty( $typography_css ) ) {
            $css .= "/* Typography Styles */\n{$typography_css}\n";
        }

        // WooCommerce styles — only if WooCommerce is active.
        $wc_css = function_exists( 'Opulentia_dynamic_woocommerce_css' ) ? Opulentia_dynamic_woocommerce_css() : '';
        if ( ! empty( $wc_css ) ) {
            $css .= "/* WooCommerce Styles */\n{$wc_css}\n";
        }

        // Archive styles.
        $archive_css = function_exists( 'Opulentia_dynamic_archive_css' ) ? Opulentia_dynamic_archive_css() : '';
        if ( ! empty( $archive_css ) ) {
            $css .= "/* Archive Styles */\n{$archive_css}\n";
        }

        // Search results styles.
        $search_css = function_exists( 'Opulentia_dynamic_search_css' ) ? Opulentia_dynamic_search_css() : '';
        if ( ! empty( $search_css ) ) {
            $css .= "/* Search Styles */\n{$search_css}\n";
        }

        // Page styles.
        $page_css = function_exists( 'Opulentia_dynamic_page_css' ) ? Opulentia_dynamic_page_css() : '';
        if ( ! empty( $page_css ) ) {
            $css .= "/* Page Styles */\n{$page_css}\n";
        }

        // 404 styles.
        $css_404 = function_exists( 'Opulentia_dynamic_404_css' ) ? Opulentia_dynamic_404_css() : '';
        if ( ! empty( $css_404 ) ) {
            $css .= "/* 404 Styles */\n{$css_404}\n";
        }

        // Color scheme override — if a preset is selected, output its colors.
        $scheme_css = $this->get_color_scheme_css();
        if ( ! empty( $scheme_css ) ) {
            $css .= "/* Color Scheme Preset */\n{$scheme_css}\n";
        }

        return $css;
    }

    /**
     * Generate CSS for the 9-color global palette (--opulentia-global-color-0 through 8).
     *
     * For each color slot, reads the individual customizer override first.
     * Falls back to the active preset's default palette on a per-color basis.
     *
     * @return string CSS variables string (without wrapping in :root {}).
     */
    private function get_global_palette_css() {
        $css     = '';
        $scheme  = get_theme_mod( 'color_scheme_preset', 'dark-luxury' );
        $default = Opulentia_get_global_palette_by_preset( $scheme );

        for ( $i = 0; $i <= 8; $i++ ) {
            $color = get_theme_mod( 'global-color-' . $i );

            if ( empty( $color ) ) {
                $color = $default[ $i ] ?? '#1a1a1a';
            }

$css .= " --opulentia-global-color-{$i}: {$color};\n";
        }

        return $css;
    }

    /**
     * Get color scheme preset CSS.
     *
     * If a preset is active, output additional :root variables
     * that override the individual settings. The presets are
     * comprehensive theme_mod sets stored in the select option.
     *
     * Now also includes the 9-color global palette (--opulentia-global-color-0 through 8)
     * so switching presets updates both the legacy vars and the global palette.
     *
     * @return string CSS string for the selected preset.
     */
    private function get_color_scheme_css() {
        $scheme = get_theme_mod( 'color_scheme_preset', 'dark-luxury' );

        $presets = $this->get_color_presets();

        if ( ! isset( $presets[ $scheme ] ) ) {
            return '';
        }

        $colors = $presets[ $scheme ];

        $css = ":root {\n";

        // Legacy variables.
        if ( isset( $colors['--color-primary-dark'] ) ) {
            $css .= "    --color-primary-dark: {$colors['--color-primary-dark']};\n";
        }
        if ( isset( $colors['--color-secondary-dark'] ) ) {
            $css .= "    --color-secondary-dark: {$colors['--color-secondary-dark']};\n";
        }
        if ( isset( $colors['--color-accent'] ) ) {
            $css .= "    --color-accent: {$colors['--color-accent']};\n";
        }
        if ( isset( $colors['--color-gold'] ) ) {
            $css .= "    --color-gold: {$colors['--color-gold']};\n";
        }
        if ( isset( $colors['--color-light-gold'] ) ) {
            $css .= "    --color-light-gold: {$colors['--color-light-gold']};\n";
        }
        if ( isset( $colors['--color-text'] ) ) {
            $css .= "    --color-text: {$colors['--color-text']};\n";
        }
        if ( isset( $colors['--color-medium-gray'] ) ) {
            $css .= "    --color-medium-gray: {$colors['--color-medium-gray']};\n";
        }
        if ( isset( $colors['--color-border'] ) ) {
            $css .= "    --color-border: {$colors['--color-border']};\n";
        }

        $css .= "}\n";

        return $css;
    }

    /**
     * Get available color scheme presets.
     *
     * Delegates to the shared Opulentia_get_color_presets() function
     * defined in inc/dynamic-css/presets.php (single source of truth).
     *
     * @return array Associative array of preset_id => { --css-var: value }.
     */
    public function get_color_presets() {
        return Opulentia_get_color_presets();
    }

    /**
     * Get preset choices array for use in Customizer select control.
     *
     * Delegates to the shared Opulentia_get_preset_choices() function.
     *
     * @return array Select choices.
     */
    public function get_preset_choices() {
        return Opulentia_get_preset_choices();
    }

    /**
     * Generate spacing CSS from customizer settings.
     *
     * When the Spacing System module is active, spacing CSS is
     * generated by the module instead. This method falls back
     * to the legacy settings only when the module is not loaded.
     *
     * @return string CSS string for spacing overrides.
     */
    private function get_spacing_css() {
        // If the Spacing System module is loaded, delegate to it.
        // This ensures spacing CSS is included in the cached compilation.
        if ( class_exists( 'Opulentia_Spacing' ) ) {
            return Opulentia_Spacing::get_instance()->get_spacing_css();
        }

        // Legacy spacing CSS (only used if module is not active).
        $css   = '';
        $top   = (int) get_theme_mod( 'layout_section_padding_top', 80 );
        $bottom = (int) get_theme_mod( 'layout_section_padding_bottom', 80 );

        $section_selectors = '.collection-section, .about-section, .brand-story, .testimonials, .instagram-feed, .elite-collection';

        if ( 80 !== $top ) {
            $css .= $section_selectors . " {";
            $css .= "\n    padding-top: {$top}px;\n";
            $css .= "}\n\n";
        }

        if ( 80 !== $bottom ) {
            $css .= $section_selectors . " {";
            $css .= "\n    padding-bottom: {$bottom}px;\n";
            $css .= "}\n\n";
        }

        return $css;
    }

    /**
     * Invalidate the dynamic CSS cache.
     */
    public function invalidate_cache() {
        delete_transient( self::CACHE_KEY );
    }
}

<?php
/**
 * Opulentia Theme Options
 *
 * Singleton that manages all theme option defaults, retrieval,
 * and storage in a single wp_options row (`Opulentia_settings`).
 * Patterned after Astra_Theme_Options for maximum compatibility.
 *
 * @package Opulentia
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Opulentia_Theme_Options' ) ) {

    /**
     * Opulentia_Theme_Options class.
     */
    class Opulentia_Theme_Options {

        /**
         * Singleton instance.
         *
         * @var self|null
         */
        private static $instance;

        /**
         * Cached defaults array.
         *
         * @var array|null
         */
        private static $defaults;

        /**
         * Cached DB options (with defaults merged).
         *
         * @var array|null
         */
        private static $db_options;

        /**
         * Raw DB options (no defaults).
         *
         * @var array|null
         */
        private static $db_options_no_defaults;

        /**
         * Cached Opulentia_settings option value.
         *
         * @var array|null
         */
        public static $Opulentia_options = null;

        /**
         * Post ID cache for meta lookups.
         *
         * @var int|null
         */
        public static $post_id = null;

        /**
         * Returns the singleton instance.
         *
         * @return self
         */
        public static function get_instance() {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor — refreshes option cache after setup.
         */
        private function __construct() {
            add_action( 'after_setup_theme', array( __CLASS__, 'refresh' ) );
        }

        /**
         * Retrieve raw option from database.
         *
         * @return array
         */
        public static function get_Opulentia_options() {
            if ( is_null( self::$Opulentia_options ) || is_customize_preview() ) {
                self::$Opulentia_options = get_option( Opulentia_SETTINGS );
                if ( ! is_array( self::$Opulentia_options ) ) {
                    self::$Opulentia_options = array();
                }
            }
            return self::$Opulentia_options;
        }

        /**
         * Return the full defaults array.
         *
         * @return array
         */
        public static function defaults() {
            if ( ! is_null( self::$defaults ) ) {
                return self::$defaults;
            }

            self::$defaults = apply_filters( 'Opulentia_theme_defaults', array(

                // ── Colors ──────────────────────────────────────────
                'color-primary-dark'      => '#1a1a1a',
                'color-secondary-dark'    => '#111111',
                'color-accent'            => '#b8860b',
                'color-accent-hover'      => '#d4a843',
                'color-gold'              => '#c9a96e',
                'color-light-gold'        => '#e8d5a3',
                'color-text'              => '#f5f5f5',
                'color-text-muted'        => '#999999',
                'color-border'            => '#333333',
                'color-link'              => '#c9a96e',
                'color-link-hover'        => '#d4a843',
                'color-button-bg'         => '#c9a96e',
                'color-button-text'       => '#ffffff',
                'color-button-hover-bg'   => '#b8944f',
                'color-header-bg'         => '#1a1a1a',
                'color-footer-bg'         => '#1a1a1a',

                // ── Typography ──────────────────────────────────────
                'font-heading-family'     => 'Playfair Display',
                'font-heading-weight'     => '600',
                'font-heading-transform'  => '',
                'font-body-family'        => 'Inter',
                'font-body-weight'        => '400',
                'font-body-size'          => array(
                    'desktop'      => '16',
                    'tablet'       => '15',
                    'mobile'       => '14',
                    'desktop-unit' => 'px',
                    'tablet-unit'  => 'px',
                    'mobile-unit'  => 'px',
                ),
                'font-body-line-height'   => '1.6',
                'font-nav-transform'      => 'uppercase',
                'font-nav-weight'         => '500',
                'font-nav-spacing'        => '1px',
                'font-button-transform'   => 'uppercase',
                'font-button-weight'      => '500',
                'font-button-spacing'     => '1px',

                // ── Layout ─────────────────────────────────────────
                'container-max-width'     => 1200,
                'content-layout'          => 'boxed',
                'sidebar-position'        => 'right',
                'sticky-header'           => true,
                'header-layout'           => 'standard',
                'header-transparent'      => false,
                'header-show-top-bar'     => true,
                'header-show-search'      => true,
                'header-show-account'     => true,
                'header-show-cart'        => true,

                // ── Spacing ─────────────────────────────────────────
                'section-padding-top'     => 80,
                'section-padding-bottom'  => 80,
                'content-spacing'         => array(
                    'desktop'      => array(
                        'top'    => '0',
                        'right'  => '20',
                        'bottom' => '0',
                        'left'   => '20',
                    ),
                    'tablet'       => array(
                        'top'    => '0',
                        'right'  => '15',
                        'bottom' => '0',
                        'left'   => '15',
                    ),
                    'mobile'       => array(
                        'top'    => '0',
                        'right'  => '10',
                        'bottom' => '0',
                        'left'   => '10',
                    ),
                    'desktop-unit' => 'px',
                    'tablet-unit'  => 'px',
                    'mobile-unit'  => 'px',
                ),

                // ── Blog ────────────────────────────────────────────
                'blog-layout'             => 'grid',
                'blog-grid-columns'       => 2,
                'blog-posts-per-page'     => 6,
                'blog-excerpt-length'     => 20,
                'blog-read-more'          => 'Read More',
                'blog-image-radius'       => '8px',

                // ── WooCommerce ─────────────────────────────────────
                'wc-product-columns'      => 4,
                'wc-products-per-page'    => 12,
                'wc-sale-badge-color'     => '#b8860b',
                'wc-button-bg'            => '#c9a96e',
                'wc-button-text'          => '#ffffff',

                // ── Footer ──────────────────────────────────────────
                'footer-columns'          => 4,
                'footer-show-brand'       => true,
                'footer-show-social'      => true,
                'footer-show-payment'     => true,
                'footer-copyright'        => '&copy; [current_year] Opulentia. All Rights Reserved.',

                // ── Hero Section ────────────────────────────────────
                'hero-title'              => 'opulentia',
                'hero-subtitle'           => 'Premium Italian Footwear',
                'hero-button-1-text'      => 'Explore Collection',
                'hero-button-1-url'       => '/collection',
                'hero-button-2-text'      => 'View Styles',
                'hero-button-2-url'       => '/styles',

                // ── Social Media ────────────────────────────────────
                'social-facebook'         => '',
                'social-instagram'        => '',
                'social-twitter'          => '',
                'social-youtube'          => '',
                'social-pinterest'        => '',

                // ── Performance ─────────────────────────────────────
                'enable-css-minify'       => false,
                'enable-js-defer'         => true,
                'enable-font-optimize'    => true,
                'enable-lazy-load'        => true,

                // ── Accessibility ───────────────────────────────────
                'enable-accessibility'    => true,
                'accessibility-outline-style' => 'solid',
                'accessibility-outline-color' => '#c9a96e',
                'accessibility-input-style'   => 'solid',
                'accessibility-input-color'   => '#c9a96e',

                // ── Page Headers ────────────────────────────────────
                'page-header-enabled'         => true,
                'page-header-bg-color'        => '#111111',
                'page-header-overlay-color'   => 'rgba(0,0,0,0.5)',
                'page-header-alignment'       => 'center',
                'page-header-show-breadcrumbs'=> true,
                'page-header-padding-top'     => 100,
                'page-header-padding-bottom'  => 60,
                'page-header-home-title'      => '',
                'page-header-blog-title'      => 'Blog',

                // ── Mega Menu ───────────────────────────────────────
                'enable-mega-menu'            => false,
                'mega-menu-animation'         => 'fade',

                // ── Live Search ─────────────────────────────────────
                'enable-live-search'           => true,
                'search-style'                 => 'dropdown',
                'live-search-post-types'       => array( 'post', 'product' ),
                'live-search-count'            => 6,

                // ── Blog Pro ────────────────────────────────────────
                'blog-pro-read-time'           => true,
                'blog-pro-wpm'                 => 200,
                'blog-pro-infinite-scroll'     => 'pagination',
                'blog-related-filter'          => 'category',
                'blog-related-show-image'      => true,
                'blog-related-show-date'       => true,

                // ── Scroll to Top ───────────────────────────────────
                'enable-scroll-to-top'         => true,
                'scroll-to-top-position'       => 'right',
                'scroll-to-top-threshold'      => 300,
                'scroll-to-top-icon'           => 'chevron-up',

                // ── Transparent Header ──────────────────────────────
                'header-transparent'           => false,
                'transparent-header-conditions' => array( 'front_page' ),
                'transparent-header-menu-color' => '#ffffff',
                'transparent-header-menu-hover-color' => '#c9a96e',
                'transparent-header-title-color' => '#ffffff',
                'transparent-header-border-color' => 'rgba(255,255,255,0.15)',

                // ── Dark Mode ───────────────────────────────────────
                'dark-mode-mode'               => 'off',
                'dark-mode-bg-color'           => '#0a0a0a',
                'dark-mode-text-color'         => '#e0e0e0',
                'dark-mode-link-color'         => '#c9a96e',
                'dark-mode-heading-color'      => '#ffffff',
                'dark-mode-border-color'       => '#2a2a2a',
                'dark-mode-image-brightness'   => 85,

                // ── Miscellaneous ───────────────────────────────────
                'enable-breadcrumbs'      => true,
            ) );

            return self::$defaults;
        }

        /**
         * Return merged options (DB + defaults).
         *
         * @return array
         */
        public static function get_options() {
            return self::$db_options;
        }

        /**
         * Refresh the cached merged options array.
         */
        public static function refresh() {
            $switched = false;

            if ( is_admin() ) {
                $site_locale = get_option( 'WPLANG' ) ?: 'en_US';
                if ( determine_locale() !== $site_locale ) {
                    $switched       = switch_to_locale( $site_locale );
                    self::$defaults = null;
                }
            }

            self::$db_options = wp_parse_args(
                self::get_db_options(),
                self::defaults()
            );

            if ( $switched ) {
                restore_previous_locale();
                self::$defaults = null;
            }
        }

        /**
         * Get raw options from database (no defaults merged).
         *
         * @return array
         */
        public static function get_db_options() {
            self::$db_options_no_defaults = self::get_Opulentia_options();
            return self::$db_options_no_defaults;
        }
    }

    // Kick off the singleton.
    Opulentia_Theme_Options::get_instance();
}

// -----------------------------------------------------------------------------
// Global Helper Functions
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_get_options' ) ) {
    /**
     * Retrieve the full theme options array.
     *
     * @return array
     */
    function Opulentia_get_options() {
        if ( wp_installing() ) {
            return array();
        }

        if ( apply_filters( 'Opulentia_get_options_nocache', false ) ) {
            $options = get_option( Opulentia_SETTINGS, array() );
        } else {
            static $cached_options = null;
            if ( is_null( $cached_options ) || is_customize_preview() ) {
                $cached_options = Opulentia_Theme_Options::get_Opulentia_options();
            }
            $options = $cached_options;
        }

        return apply_filters( 'Opulentia_get_options', $options );
    }
}

if ( ! function_exists( 'Opulentia_get_option' ) ) {
    /**
     * Retrieve a single theme option value.
     *
     * @param  string $option  Option key.
     * @param  mixed  $default Optional default value.
     * @return mixed
     */
    function Opulentia_get_option( $option, $default = '' ) {
        $theme_options = Opulentia_Theme_Options::get_options();

        $theme_options = apply_filters( 'Opulentia_get_option_array', $theme_options, $option, $default );

        // Check the Theme Options API (Opulentia_settings) first.
        if ( isset( $theme_options[ $option ] ) && '' !== $theme_options[ $option ] ) {
            $value = $theme_options[ $option ];
        } else {
            // Fall back to theme_mod for Customizer-saved settings.
            $mod = get_theme_mod( $option, '__Opulentia_NOT_SET__' );
            if ( '__Opulentia_NOT_SET__' !== $mod ) {
                $value = $mod;
            } else {
                $value = $default;
            }
        }

        return apply_filters( "Opulentia_get_option_{$option}", $value, $option, $default );
    }
}

if ( ! function_exists( 'Opulentia_update_option' ) ) {
    /**
     * Update a single theme option value.
     *
     * @param  string $option Option key.
     * @param  mixed  $value  New value.
     * @return void
     */
    function Opulentia_update_option( $option, $value ) {
        do_action( "Opulentia_before_update_option_{$option}", $value, $option );

        $theme_options = get_option( Opulentia_SETTINGS );
        if ( ! is_array( $theme_options ) ) {
            $theme_options = array();
        }
        $theme_options[ $option ] = $value;

        update_option( Opulentia_SETTINGS, $theme_options );

        do_action( "Opulentia_after_update_option_{$option}", $value, $option );
    }
}

if ( ! function_exists( 'Opulentia_delete_option' ) ) {
    /**
     * Delete a single theme option.
     *
     * @param  string $option Option key.
     * @return void
     */
    function Opulentia_delete_option( $option ) {
        do_action( "Opulentia_before_delete_option_{$option}", $option );

        $theme_options = get_option( Opulentia_SETTINGS );
        if ( is_array( $theme_options ) ) {
            unset( $theme_options[ $option ] );
            update_option( Opulentia_SETTINGS, $theme_options );
        }

        do_action( "Opulentia_after_delete_option_{$option}", $option );
    }
}

if ( ! function_exists( 'Opulentia_get_i18n_option' ) ) {
    /**
     * Get a translated theme option value.
     *
     * Returns the translated string if a translation exists,
     * otherwise returns the raw option value.
     *
     * Usage:
     *     $value = Opulentia_get_i18n_option( 'footer-copyright', esc_html_x( '%Opulentia%', 'Footer copyright text', 'opulentia' ) );
     *
     * @param  string $option     Option key.
     * @param  string $translated Translated string (with %Opulentia% marker for TP compat).
     * @param  string $default    Default fallback.
     * @return string
     */
    function Opulentia_get_i18n_option( $option, $translated, $default = '' ) {
        $is_translated = '%Opulentia%' !== $translated && false === strpos( $translated, '#%Opulentia%#' );
        return $is_translated ? $translated : Opulentia_get_option( $option, $default );
    }
}

if ( ! function_exists( 'Opulentia_get_i18n_string' ) ) {
    /**
     * Return a translated string or default fallback.
     *
     * @param  string $default    Default string value.
     * @param  string $translated Translated string (with %Opulentia% marker).
     * @return string
     */
    function Opulentia_get_i18n_string( $default, $translated ) {
        $is_translated = '%Opulentia%' !== $translated && false === strpos( $translated, '#%Opulentia%#' );
        return $is_translated ? $translated : $default;
    }
}

if ( ! function_exists( 'Opulentia_get_option_meta' ) ) {
    /**
     * Get a theme option with per-post meta override support.
     *
     * Checks post meta first for a per-post override. If the meta value
     * is empty or 'default', falls back to the global theme option.
     *
     * @param  string $option_id Option ID.
     * @param  string $default   Default fallback.
     * @param  bool   $only_meta If true, return only meta value (no global fallback).
     * @param  int    $post_id   Optional post ID. Defaults to current post.
     * @return mixed
     */
    function Opulentia_get_option_meta( $option_id, $default = '', $only_meta = false, $post_id = 0 ) {
        $post_id = $post_id ? $post_id : Opulentia_get_post_id();
        $value   = Opulentia_get_option( $option_id, $default );

        if ( is_singular() || ( is_home() && ! is_front_page() ) ) {
            $meta_value = get_post_meta( $post_id, $option_id, true );

            if ( ! empty( $meta_value ) && 'default' !== $meta_value ) {
                return apply_filters( "Opulentia_get_option_meta_{$option_id}", $meta_value, $default, $default );
            }

            if ( true === $only_meta ) {
                return false;
            }
        }

        return apply_filters( "Opulentia_get_option_meta_{$option_id}", $value, $default, $default );
    }
}

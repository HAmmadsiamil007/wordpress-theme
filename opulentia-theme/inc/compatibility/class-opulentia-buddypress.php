<?php
/**
 * BuddyPress Compatibility — Singleton
 *
 * Integrates Opulentia with BuddyPress:
 * - Registers theme support for BuddyPress
 * - Adds body classes for BuddyPress components
 * - Provides theme-styled member/group profiles, activity streams
 * - Ensures proper template loading and full-width layouts
 * - Suppresses theme CSS when not needed in BuddyPress context
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_BuddyPress class.
 */
class Opulentia_BuddyPress {

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
     * Initialize BuddyPress compatibility.
     */
    public function init() {
        if ( ! $this->is_buddypress_active() ) {
            return;
        }

        // Register theme support.
        add_theme_support( 'buddypress' );

        // Use theme compatibility template system.
        add_action( 'bp_init', array( $this, 'set_template_stack' ), 5 );

        // Add body classes.
        add_filter( 'body_class', array( $this, 'body_classes' ) );

        // Set full-width layout for BuddyPress pages.
        add_action( 'wp', array( $this, 'support_full_width' ) );

        // Enqueue theme styles for BuddyPress components.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 20 );
    }

    /**
     * Check if BuddyPress is active.
     *
     * @return bool
     */
    private function is_buddypress_active() {
        return class_exists( 'BuddyPress' )
            || defined( 'BP_VERSION' );
    }

    /**
     * Set the BuddyPress template stack to use theme compatibility.
     *
     * Ensures BuddyPress looks for templates in the theme's directory
     * and falls back to plugin templates appropriately.
     */
    public function set_template_stack() {
        if ( ! function_exists( 'bp_register_template_stack' ) ) {
            return;
        }

        bp_register_template_stack( array( $this, 'get_template_directory' ), 14 );
    }

    /**
     * Return the theme's BuddyPress template directory path.
     *
     * @return string
     */
    public function get_template_directory() {
        return Opulentia_DIR . '/buddypress';
    }

    /**
     * Add full-width support for BuddyPress pages.
     *
     * Removes sidebar and content constraints so member/group
     * profiles can use the full page width.
     */
    public function support_full_width() {
        if ( ! function_exists( 'bp_is_buddypress' ) || ! bp_is_buddypress() ) {
            return;
        }

        add_filter( 'Opulentia_layout_content_layout', '__return_false' );
    }

    /**
     * Enqueue theme-compatible BuddyPress styles.
     *
     * Applies the Opulentia design system to BuddyPress components
     * (member profiles, groups, activity streams, notifications)
     * via inline styles to avoid an additional HTTP request.
     */
    public function enqueue_styles() {
        if ( ! function_exists( 'bp_is_buddypress' ) || ! bp_is_buddypress() ) {
            return;
        }

        wp_add_inline_style(
            'opulentia-style',
            '
                /* ── BuddyPress Theme Integration ── */

                /* Member & Group profile headers */
                #buddypress div#item-header {
                    background: var(--color-secondary-dark, #111);
                    border-bottom: 1px solid var(--color-border, #333);
                    padding: 24px;
                    border-radius: 8px;
                    margin-bottom: 24px;
                }

                #buddypress #item-header-avatar img.avatar {
                    border-radius: 50%;
                    border: 3px solid var(--color-gold, #c9a96e);
                }

                #buddypress #item-header-content .user-nicename {
                    color: var(--color-gold, #c9a96e);
                    font-family: var(--font-heading, "Playfair Display", serif);
                }

                #buddypress div#item-nav {
                    background: var(--color-secondary-dark, #111);
                    border-radius: 8px;
                    margin-bottom: 24px;
                }

                #buddypress div.item-list-tabs ul li a {
                    color: var(--color-text, #f5f5f5);
                    padding: 12px 16px;
                }

                #buddypress div.item-list-tabs ul li a:hover {
                    color: var(--color-gold, #c9a96e);
                }

                #buddypress div.item-list-tabs ul li.current a {
                    color: var(--color-gold, #c9a96e);
                    border-bottom: 2px solid var(--color-gold, #c9a96e);
                }

                /* Activity stream */
                #buddypress .activity-list .activity-item {
                    background: var(--color-secondary-dark, #111);
                    border: 1px solid var(--color-border, #333);
                    border-radius: 8px;
                    padding: 16px;
                    margin-bottom: 16px;
                }

                #buddypress .activity-list .activity-item .activity-header a {
                    color: var(--color-gold, #c9a96e);
                }

                #buddypress .activity-list .activity-item .activity-inner {
                    color: var(--color-text, #f5f5f5);
                }

                #buddypress .activity-list .activity-item .activity-time-since {
                    color: var(--color-text-muted, #999);
                }

                /* Member directories */
                #buddypress ul.item-list li {
                    background: var(--color-secondary-dark, #111);
                    border: 1px solid var(--color-border, #333);
                    border-radius: 8px;
                    padding: 16px;
                    margin-bottom: 12px;
                }

                #buddypress ul.item-list li .item-title a {
                    color: var(--color-gold, #c9a96e);
                }

                #buddypress ul.item-list li .item-meta {
                    color: var(--color-text-muted, #999);
                }

                /* Group directories */
                #buddypress .group-dir-list .group-item {
                    background: var(--color-secondary-dark, #111);
                    border: 1px solid var(--color-border, #333);
                    border-radius: 8px;
                    padding: 16px;
                    margin-bottom: 12px;
                }

                #buddypress .groups-dir-list .group-item .group-title a {
                    color: var(--color-gold, #c9a96e);
                }

                /* Buttons */
                #buddypress .generic-button a,
                #buddypress input[type="submit"] {
                    background: var(--color-accent, #b8860b) !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 4px;
                    padding: 10px 20px;
                    font-family: var(--font-body, Inter, sans-serif);
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    font-size: 0.8rem;
                    cursor: pointer;
                    transition: background var(--transition-fast, 0.2s ease);
                }

                #buddypress .generic-button a:hover,
                #buddypress input[type="submit"]:hover {
                    background: var(--color-gold-hover, #b8944f) !important;
                }

                /* Notifications */
                #buddypress .notification-list li {
                    background: var(--color-secondary-dark, #111);
                    border: 1px solid var(--color-border, #333);
                    border-radius: 8px;
                    padding: 12px 16px;
                    margin-bottom: 8px;
                }

                #buddypress .notification-list li .notification-description a {
                    color: var(--color-gold, #c9a96e);
                }

                /* Forms & inputs */
                #buddypress .standard-form input[type="text"],
                #buddypress .standard-form input[type="email"],
                #buddypress .standard-form input[type="password"],
                #buddypress .standard-form textarea,
                #buddypress .standard-form select {
                    background: var(--color-primary-dark, #1a1a1a);
                    border: 1px solid var(--color-border, #333);
                    border-radius: 4px;
                    color: var(--color-text, #f5f5f5);
                    padding: 10px 14px;
                    font-family: var(--font-body, Inter, sans-serif);
                    font-size: 0.9rem;
                }

                #buddypress .standard-form input:focus,
                #buddypress .standard-form textarea:focus,
                #buddypress .standard-form select:focus {
                    border-color: var(--color-gold, #c9a96e);
                    outline: none;
                }

                /* Registration page */
                #buddypress .register-page .layout-wrap {
                    background: var(--color-secondary-dark, #111);
                    border-radius: 8px;
                    padding: 24px;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    #buddypress div#item-header {
                        flex-direction: column;
                        text-align: center;
                    }

                    #buddypress div.item-list-tabs ul {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                    }

                    #buddypress div.item-list-tabs ul li {
                        float: none;
                    }
                }

                @media (max-width: 576px) {
                    #buddypress .activity-list .activity-item {
                        padding: 12px;
                    }

                    #buddypress ul.item-list li {
                        padding: 12px;
                    }
                }
            '
        );
    }

    /**
     * Add BuddyPress-specific body classes.
     *
     * @param array $classes Body classes.
     * @return array
     */
    public function body_classes( $classes ) {
        $classes[] = 'opulentia-buddypress-compat';

        if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
            $classes[] = 'bp-group-page';
        }

        if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
            $classes[] = 'bp-member-page';
        }

        if ( function_exists( 'bp_is_activity_component' ) && bp_is_activity_component() ) {
            $classes[] = 'bp-activity-page';
        }

        return $classes;
    }
}

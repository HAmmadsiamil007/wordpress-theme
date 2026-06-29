<?php
/**
 * SVG Icon Utility — Singleton
 *
 * Central source of truth for all inline SVGs used across the theme.
 * Eliminates ~52 duplicate inline SVGs scattered across 17 template files.
 *
 * Each icon is a Feather-style 24×24 SVG with `currentColor` stroke
 * unless otherwise noted (social icons use fill).
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SoleOrigine_Icons class.
 */
class SoleOrigine_Icons {

    /**
     * Singleton instance.
     *
     * @var SoleOrigine_Icons
     */
    private static $instance = null;

    /**
     * Registered icon store.
     *
     * @var array
     */
    private $icons = array();

    /**
     * Constructor — loads the icon library.
     */
    private function __construct() {
        $this->register_defaults();
    }

    /**
     * Get singleton instance.
     *
     * @return SoleOrigine_Icons
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register the default icon set.
     *
     * Uses 24×24 viewBox, stroke="currentColor", stroke-width="2",
     * fill="none" for Feather-style icons. Social/brand icons use fill.
     */
    private function register_defaults() {
        /* ─── Brand ─────────────────────────────────────────────── */

        $this->add( 'logo', '<svg class="soleorigine-icon soleorigine-icon--logo" viewBox="0 0 100 100" fill="none" aria-hidden="true"><circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="2"/><text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text></svg>' );

        $this->add( 'shoe', '<svg class="soleorigine-icon soleorigine-icon--shoe" viewBox="0 0 200 100" fill="none" aria-hidden="true"><path d="M20 80 Q100 20 180 80" stroke="#8B4513" stroke-width="3" fill="none"/><ellipse cx="100" cy="85" rx="80" ry="10" fill="#8B4513" opacity="0.3"/><path d="M30 75 Q100 30 170 75" fill="#A0522D"/><path d="M50 60 Q100 25 150 60" fill="#8B4513"/></svg>' );

        $this->add( 'hero-arc', '<svg class="soleorigine-icon soleorigine-icon--hero-arc" viewBox="0 0 80 70" fill="none" aria-hidden="true"><path d="M5 35 Q40 5 75 35" stroke="#C9A96E" stroke-width="2" fill="none"/></svg>' );

        $this->add( 'hero-bg', '<svg class="soleorigine-icon soleorigine-icon--hero-bg" viewBox="0 0 600 500" fill="none" aria-hidden="true"><path d="M100 400 Q200 100 400 150 Q550 200 500 400 Q450 550 250 500 Q50 450 100 400Z" fill="#1a1a1a" stroke="#c9a96e" stroke-width="1" opacity="0.3"/><path d="M200 350 Q300 150 450 200 Q500 250 450 350 Q400 450 300 400 Q150 350 200 350Z" fill="#111" stroke="#c9a96e" stroke-width="1" opacity="0.2"/></svg>' );

        /* ─── Navigation ────────────────────────────────────────── */

        $this->add( 'search', '<svg class="soleorigine-icon soleorigine-icon--search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>' );

        $this->add( 'user', '<svg class="soleorigine-icon soleorigine-icon--user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' );

        $this->add( 'cart', '<svg class="soleorigine-icon soleorigine-icon--cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>' );

        /* ─── Arrows ────────────────────────────────────────────── */

        $this->add( 'arrow-right', '<svg class="soleorigine-icon soleorigine-icon--arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>' );

        $this->add( 'chevron-left', '<svg class="soleorigine-icon soleorigine-icon--chevron-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>' );

        $this->add( 'chevron-right', '<svg class="soleorigine-icon soleorigine-icon--chevron-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>' );

        $this->add( 'chevron-up', '<svg class="soleorigine-icon soleorigine-icon--chevron-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 15l-6-6-6 6"/></svg>' );

        /* ─── Features / About ──────────────────────────────────── */

        $this->add( 'tag', '<svg class="soleorigine-icon soleorigine-icon--tag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>' );

        $this->add( 'wrench', '<svg class="soleorigine-icon soleorigine-icon--wrench" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>' );

        $this->add( 'clock', '<svg class="soleorigine-icon soleorigine-icon--clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' );

        $this->add( 'globe', '<svg class="soleorigine-icon soleorigine-icon--globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>' );

        $this->add( 'layers', '<svg class="soleorigine-icon soleorigine-icon--layers" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>' );

        $this->add( 'shield', '<svg class="soleorigine-icon soleorigine-icon--shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>' );

        $this->add( 'checkmark', '<svg class="soleorigine-icon soleorigine-icon--checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M9 12l2 2 4-4"/></svg>' );

        $this->add( 'diamond', '<svg class="soleorigine-icon soleorigine-icon--diamond" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83M16.62 12l-5.74 9.94"/></svg>' );

        $this->add( 'heart', '<svg class="soleorigine-icon soleorigine-icon--heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>' );

        $this->add( 'star', '<svg class="soleorigine-icon soleorigine-icon--star" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>' );

        /* ─── Trust Badges ──────────────────────────────────────── */

        $this->add( 'card', '<svg class="soleorigine-icon soleorigine-icon--card" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>' );

        $this->add( 'refresh', '<svg class="soleorigine-icon soleorigine-icon--refresh" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>' );

        $this->add( 'chat', '<svg class="soleorigine-icon soleorigine-icon--chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' );

        $this->add( 'ticket', '<svg class="soleorigine-icon soleorigine-icon--ticket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/><path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"/></svg>' );

        /* ─── Contact ───────────────────────────────────────────── */

        $this->add( 'map-pin', '<svg class="soleorigine-icon soleorigine-icon--map-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>' );

        $this->add( 'phone', '<svg class="soleorigine-icon soleorigine-icon--phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>' );

        $this->add( 'email', '<svg class="soleorigine-icon soleorigine-icon--email" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>' );

        /* ─── Header Utilities ──────────────────────────────────── */

        $this->add( 'package', '<svg class="soleorigine-icon soleorigine-icon--package" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>' );

        /* ─── Social (fill-based) ───────────────────────────────── */

        $this->add( 'facebook', '<svg class="soleorigine-icon soleorigine-icon--facebook" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>' );

        $this->add( 'twitter', '<svg class="soleorigine-icon soleorigine-icon--twitter" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' );

        $this->add( 'instagram', '<svg class="soleorigine-icon soleorigine-icon--instagram" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>' );

        $this->add( 'pinterest', '<svg class="soleorigine-icon soleorigine-icon--pinterest" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>' );
    }

    /**
     * Register or override an icon.
     *
     * @param string $name  Icon identifier.
     * @param string $markup Full <svg> markup.
     */
    public function add( $name, $markup ) {
        $this->icons[ $name ] = $markup;
    }

    /**
     * Check if an icon exists.
     *
     * @param string $name Icon identifier.
     * @return bool
     */
    public function has( $name ) {
        return isset( $this->icons[ $name ] );
    }

    /**
     * Get an icon's SVG markup.
     *
     * @param string $name  Icon identifier.
     * @param string $class Additional CSS classes (appended to defaults).
     * @return string SVG markup or empty string if not found.
     */
    public function get( $name, $class = '' ) {
        if ( ! isset( $this->icons[ $name ] ) ) {
            return '';
        }

        $svg = $this->icons[ $name ];

        if ( $class ) {
            $svg = str_replace(
                'class="soleorigine-icon soleorigine-icon--' . $name . '"',
                'class="soleorigine-icon soleorigine-icon--' . $name . ' ' . esc_attr( $class ) . '"',
                $svg
            );
        }

        return $svg;
    }

    /**
     * Echo an icon's SVG markup.
     *
     * @param string $name  Icon identifier.
     * @param string $class Additional CSS classes.
     */
    public function the( $name, $class = '' ) {
        echo $this->get( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Get the list of all registered icon names.
     *
     * @return array
     */
    public function get_names() {
        return array_keys( $this->icons );
    }
}

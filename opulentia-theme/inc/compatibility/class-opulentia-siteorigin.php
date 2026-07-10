<?php
/**
 * SiteOrigin Page Builder Compatibility — Singleton
 *
 * Integrates Opulentia with SiteOrigin Page Builder:
 * - Registers theme support for SiteOrigin Panels
 * - Enables responsive tablet/mobile layout
 * - Adds body classes for builder detection
 * - Suppresses theme CSS when builder is active
 * - Provides theme-styled row/widget layouts
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_SiteOrigin class.
 */
class Opulentia_SiteOrigin {

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
	 * Initialize SiteOrigin compatibility.
	 */
	public function init() {
		if ( ! $this->is_siteorigin_active() ) {
			return;
		}

		// Register theme support.
		add_theme_support(
			'siteorigin-panels',
			array(
				'home-page'     => true,
				'tablet-layout' => true,
			)
		);

		// Suppress theme dynamic CSS when SiteOrigin is active on the page.
		add_filter( 'opulentia_dynamic_css_enabled', array( $this, 'maybe_suppress_theme_css' ) );

		// Add SiteOrigin-specific body classes.
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		// Ensure full-width layouts work with SiteOrigin rows.
		add_action( 'wp', array( $this, 'support_full_width' ) );

		// Enqueue theme-compatible SiteOrigin styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 20 );
	}

	/**
	 * Check if SiteOrigin Page Builder is active.
	 *
	 * @return bool
	 */
	private function is_siteorigin_active() {
		return class_exists( 'SiteOrigin_Panels' )
			|| defined( 'SITEORIGIN_PANELS_VERSION' );
	}

	/**
	 * Suppress theme dynamic CSS when SiteOrigin is active on the page.
	 *
	 * @param bool $enabled Whether dynamic CSS is enabled.
	 * @return bool
	 */
	public function maybe_suppress_theme_css( $enabled ) {
		if ( is_singular() ) {
			$post = get_post();
			if ( $post && function_exists( 'siteorigin_panels_is_panel' ) && siteorigin_panels_is_panel( $post->ID ) ) {
				return false;
			}
		}

		return $enabled;
	}

	/**
	 * Add full-width support for SiteOrigin Page Builder.
	 *
	 * When a page uses SiteOrigin, removes theme content
	 * constraints so rows can span the full viewport width.
	 */
	public function support_full_width() {
		if ( ! is_singular() ) {
			return;
		}

		$post = get_post();
		if ( ! $post ) {
			return;
		}

		if ( function_exists( 'siteorigin_panels_is_panel' ) && siteorigin_panels_is_panel( $post->ID ) ) {
			add_filter( 'Opulentia_layout_content_layout', '__return_false' );
		}
	}

	/**
	 * Enqueue theme-compatible SiteOrigin styles.
	 */
	public function enqueue_styles() {
		wp_add_inline_style(
			'opulentia-style',
			'
                /* ── SiteOrigin Page Builder Theme Integration ── */

                /* SiteOrigin row with theme styling */
                .siteorigin-panels .panel-grid {
                    margin-bottom: 0 !important;
                }

                .siteorigin-panels .panel-grid-cell {
                    padding: 0 16px;
                }

                /* Theme-styled SiteOrigin widgets */
                .so-panel.widget {
                    margin-bottom: 24px;
                }

                .so-panel .widget-title {
                    color: var(--color-gold, #c9a96e);
                    font-family: var(--font-heading, "Playfair Display", serif);
                    font-size: 1.25rem;
                    margin-bottom: 16px;
                    padding-bottom: 8px;
                    border-bottom: 1px solid var(--color-border, #333);
                }

                /* Full-width row support */
                .panel-grid.panel-no-style,
                .panel-grid .panel-row-style {
                    margin-left: auto;
                    margin-right: auto;
                    max-width: var(--container-max, 1200px);
                }

                .siteorigin-panels .panel-grid .panel-row-style[class*="full-width"],
                .siteorigin-panels .panel-grid .panel-row-style[class*="fullwidth"] {
                    max-width: 100%;
                }

                /* SiteOrigin home page */
                .siteorigin-panels-home {
                    padding: 0;
                }

                /* Responsive panels */
                @media (max-width: 768px) {
                    .siteorigin-panels .panel-grid {
                        grid-template-columns: 1fr !important;
                    }

                    .siteorigin-panels .panel-grid-cell {
                        padding: 0 12px;
                    }
                }
            '
		);
	}

	/**
	 * Add SiteOrigin-specific body classes.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function body_classes( $classes ) {
		$classes[] = 'opulentia-siteorigin-compat';

		if ( is_singular() ) {
			$post = get_post();
			if ( $post && function_exists( 'siteorigin_panels_is_panel' ) && siteorigin_panels_is_panel( $post->ID ) ) {
				$classes[] = 'siteorigin-panels-active';
			}
		}

		return $classes;
	}
}

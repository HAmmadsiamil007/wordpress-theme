<?php
/**
 * SureCart Compatibility — Singleton
 *
 * Integrates Opulentia with SureCart:
 * - Theme support declaration
 * - Product cards, buttons, checkout dark theme styling
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_SureCart class.
 */
class Opulentia_SureCart {

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
	 * Initialize SureCart compatibility.
	 */
	public function init() {
		if ( ! function_exists( 'SureCart' ) ) {
			return;
		}

		add_theme_support( 'surecart' );

		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
	}

	/**
	 * Output SureCart-specific inline CSS.
	 */
	public function inline_css() {
		wp_add_inline_style( 'opulentia-style', $this->surecart_compat_styles() );
	}

	/**
	 * SureCart compat CSS string.
	 *
	 * @return string
	 */
	public function surecart_compat_styles() {
		return '
			/* ── Product Cards ── */
			sc-product-item {
				background: var(--opulentia-global-color-1, #111);
				border: 1px solid var(--opulentia-global-color-7, #333);
				border-radius: 0;
				overflow: hidden;
				transition: border-color 0.3s ease;
			}
			sc-product-item:hover {
				border-color: var(--opulentia-global-color-3, #c9a96e);
			}
			sc-product-item sc-product-item-title {
				font-family: var(--font-heading, "Playfair Display", serif);
				color: var(--opulentia-global-color-3, #c9a96e);
			}
			sc-product-item sc-product-item-price {
				color: var(--opulentia-global-color-5, #f5f5f5);
			}

			/* ── Buttons ── */
			sc-product-item sc-product-buy-button sc-button,
			sc-product-item sc-button,
			sc-checkout sc-button {
				--sc-button-primary-background: var(--opulentia-global-color-2, #b8860b);
				--sc-button-primary-color: var(--opulentia-global-color-8, #fff);
				--sc-button-primary-hover-background: var(--opulentia-global-color-3, #c9a96e);
				--sc-button-min-height: 44px;
				font-family: var(--font-body, Inter, sans-serif);
				font-size: 0.8125rem;
				font-weight: 500;
				text-transform: uppercase;
				letter-spacing: 1px;
				border-radius: 0;
			}
			sc-order-submit sc-button {
				--sc-button-primary-background: var(--opulentia-global-color-2, #b8860b);
				--sc-button-primary-color: var(--opulentia-global-color-8, #fff);
				--sc-button-primary-hover-background: var(--opulentia-global-color-3, #c9a96e);
			}

			/* ── Checkout ── */
			sc-checkout {
				--sc-color-primary: var(--opulentia-global-color-3, #c9a96e);
				--sc-color-primary-hover: var(--opulentia-global-color-5, #f5f5f5);
				--sc-color-bg: var(--opulentia-global-color-0, #1a1a1a);
				--sc-color-body: var(--opulentia-global-color-5, #f5f5f5);
				--sc-color-muted: var(--opulentia-global-color-6, #999);
				--sc-color-border: var(--opulentia-global-color-7, #333);
				--sc-input-bg: var(--opulentia-global-color-1, #111);
				--sc-input-color: var(--opulentia-global-color-5, #f5f5f5);
				--sc-input-border: var(--opulentia-global-color-7, #333);
				--sc-sc-affirm-bg: var(--opulentia-global-color-1, #111);
			}
			sc-checkout sc-choice {
				--sc-choice-background: var(--opulentia-global-color-1, #111);
				--sc-choice-border: var(--opulentia-global-color-7, #333);
			}
		';
	}
}
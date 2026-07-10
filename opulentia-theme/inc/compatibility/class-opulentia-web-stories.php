<?php
/**
 * Web Stories Compatibility — Singleton
 *
 * Integrates Opulentia with Google Web Stories:
 * - Theme support declaration
 * - AMP story player dark theme styling
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Web_Stories class.
 */
class Opulentia_Web_Stories {

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
	 * Initialize Web Stories compatibility.
	 */
	public function init() {
		if ( ! defined( 'WEBSTORIES_PLUGIN_FILE' ) ) {
			return;
		}

		add_theme_support( 'web-stories' );

		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
	}

	/**
	 * Output Web Stories-specific inline CSS.
	 */
	public function inline_css() {
		wp_add_inline_style( 'opulentia-style', $this->web_stories_compat_styles() );
	}

	/**
	 * Web Stories compat CSS string.
	 *
	 * @return string
	 */
	public function web_stories_compat_styles() {
		return '
			/* ── Stories Block / Embed ── */
			.web-stories-wrapper,
			.wp-block-web-stories-embed {
				margin: 32px 0;
			}
			.web-stories-list__story {
				border-radius: 0;
				overflow: hidden;
				border: 1px solid var(--opulentia-global-color-7, #333);
				background: var(--opulentia-global-color-1, #111);
				transition: border-color 0.3s ease;
			}
			.web-stories-list__story:hover {
				border-color: var(--opulentia-global-color-3, #c9a96e);
			}
			.web-stories-list__story-poster {
				border-radius: 0;
			}
			.web-stories-list__story-title {
				font-family: var(--font-heading, "Playfair Display", serif);
				color: var(--opulentia-global-color-3, #c9a96e);
				font-size: 0.875rem;
				padding: 8px 12px;
			}
			.web-stories-list__story-content {
				color: var(--opulentia-global-color-5, #f5f5f5);
				font-size: 0.8125rem;
				padding: 0 12px 12px;
			}

			/* ── AMP Story Player Overlay ── */
			amp-story-player {
				background: var(--opulentia-global-color-0, #1a1a1a);
				border-radius: 0;
			}
			amp-story-player .story-player-title {
				font-family: var(--font-heading, "Playfair Display", serif);
				color: var(--opulentia-global-color-3, #c9a96e);
			}
			amp-story-player .story-player-excerpt {
				color: var(--opulentia-global-color-5, #f5f5f5);
			}
			amp-story-player .story-player-button {
				background: var(--opulentia-global-color-2, #b8860b);
				color: var(--opulentia-global-color-8, #fff);
				font-family: var(--font-body, Inter, sans-serif);
				font-size: 0.8125rem;
				font-weight: 500;
				text-transform: uppercase;
				letter-spacing: 1px;
				border-radius: 0;
				padding: 10px 24px;
				transition: background 0.3s ease;
			}
			amp-story-player .story-player-button:hover {
				background: var(--opulentia-global-color-3, #c9a96e);
			}
		';
	}
}

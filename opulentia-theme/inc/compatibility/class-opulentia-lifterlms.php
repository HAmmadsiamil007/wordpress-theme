<?php
/**
 * LifterLMS Compatibility — Singleton
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_LifterLMS class.
 */
class Opulentia_LifterLMS {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
	}

	/**
	 * Output LifterLMS specific CSS.
	 */
	public function inline_css() {
		if ( ! function_exists( 'llms' ) ) {
			return;
		}

		$css = '
        .llms-loop-item-content {
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            overflow: hidden;
        }
        .llms-loop-item-content:hover {
            border-color: var(--color-gold);
        }
        .llms-loop-item-content .llms-loop-title {
            font-family: var(--font-heading);
        }
        .llms-loop-item-content .llms-meta,
        .llms-loop-item-content .llms-author {
            color: var(--color-text-muted);
            font-size: 0.875rem;
        }
        .llms-loop-item-content .llms-loop-link {
            color: var(--color-text);
        }
        .llms-loop-item-content .llms-loop-link:hover {
            color: var(--color-gold);
        }
        .llms-button-primary,
        .llms-button-action {
            background: var(--color-gold) !important;
            border-color: var(--color-gold) !important;
            color: #fff !important;
        }
        .llms-button-primary:hover,
        .llms-button-action:hover {
            background: var(--color-gold-hover) !important;
        }
        .llms-progress .progress-bar-completed {
            background: var(--color-gold);
        }
        .llms-access-plan-title {
            background: var(--color-secondary-dark);
            color: var(--color-gold);
        }
        .llms-access-plan-content {
            background: var(--color-primary-dark);
            border: 1px solid var(--color-border);
        }
        .llms-access-plan-footer {
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

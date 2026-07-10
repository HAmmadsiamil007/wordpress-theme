<?php
/**
 * Rank Math SEO Compatibility — Singleton
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Rank_Math class.
 */
class Opulentia_Rank_Math {

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
	 *
	 * Rank Math breadcrumb args styling is now handled by the Breadcrumbs
	 * module (class-opulentia-breadcrumbs.php → rank_math_args()).
	 * This compat class remains for other Rank Math integrations.
	 */
	private function __construct() {
		// Breadcrumb args are now managed by the Breadcrumbs module.
		// Filter is ONLY applied when 'rank_math' is selected as the source.
		// Keeping this for backward compat with direct plugin usage.
		add_action( 'init', array( $this, 'maybe_init' ) );
	}

	/**
	 * Initialize Rank Math breadcrumb overrides only if the Breadcrumbs
	 * module is not handling it.
	 */
	public function maybe_init() {
		if ( ! defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		// Only register our filter if the Breadcrumbs module is NOT loaded
		// (for backward compatibility with direct theme_breadcrumb use).
		if ( ! class_exists( 'Opulentia_Breadcrumbs' ) ) {
			add_filter( 'rank_math/frontend/breadcrumb/args', array( $this, 'breadcrumb_args' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
		}
	}

	/**
	 * Customize Rank Math breadcrumb args to match theme styling.
	 *
	 * @param array $args Breadcrumb args.
	 * @return array
	 */
	public function breadcrumb_args( $args ) {
		$args['wrap_before'] = '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'opulentia' ) . '"><div class="container"><ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';
		$args['wrap_after']  = '</ol></div></nav>';
		$args['before']      = '<li class="breadcrumbs__item">';
		$args['after']       = '</li>';
		$args['delimiter']   = '<li class="breadcrumbs__separator" aria-hidden="true">/</li>';
		return $args;
	}

	/**
	 * Output Rank Math breadcrumb CSS if needed.
	 */
	public function inline_css() {
		if ( ! defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		$css = '
        .breadcrumbs__item + .breadcrumbs__separator {
            margin: 0 8px;
        }
        .breadcrumbs__item a {
            color: var(--color-medium-gray);
            transition: color 0.2s ease;
        }
        .breadcrumbs__item a:hover {
            color: var(--color-gold);
        }
        .breadcrumbs__item:last-child {
            color: var(--color-text);
            font-weight: 500;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

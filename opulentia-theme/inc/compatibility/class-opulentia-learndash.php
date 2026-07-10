<?php
/**
 * LearnDash LMS Compatibility — Singleton
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_LearnDash class.
 */
class Opulentia_LearnDash {

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
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
	}

	/**
	 * Add LearnDash theme support.
	 */
	public function add_theme_support() {
		if ( ! defined( 'LEARNDASH_VERSION' ) ) {
			return;
		}
		add_theme_support( 'learndash-theme-support' );
	}

	/**
	 * Output LearnDash specific CSS.
	 */
	public function inline_css() {
		if ( ! defined( 'LEARNDASH_VERSION' ) ) {
			return;
		}

		$css = '
        .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
            background: var(--color-gold) !important;
            color: #fff !important;
        }
        .ld-course-list-items .ld_course_grid .thumbnail.course {
            border: 1px solid var(--color-border);
            background: var(--color-secondary-dark);
        }
        .ld-course-list-items .ld_course_grid .thumbnail.course .caption {
            padding: 20px;
        }
        .ld-course-list-items .ld_course_grid .thumbnail.course .caption h3 {
            font-family: var(--font-heading);
        }
        .ld_course_grid .btn-primary {
            background: var(--color-gold) !important;
            border-color: var(--color-gold) !important;
            color: #fff !important;
        }
        .ld_course_grid .btn-primary:hover {
            background: var(--color-gold-hover) !important;
            border-color: var(--color-gold-hover) !important;
        }
        .learndash-wrapper .ld-focus {
            background: var(--color-primary-dark);
        }
        .learndash-wrapper .ld-focus .ld-focus-sidebar {
            background: var(--color-secondary-dark);
            border-right: 1px solid var(--color-border);
        }
        .learndash-wrapper .ld-focus .ld-focus-header {
            background: var(--color-secondary-dark);
            border-bottom: 1px solid var(--color-border);
        }
        .learndash-wrapper .ld-tabs .ld-tabs-navigation .ld-tab {
            color: var(--color-text-muted);
        }
        .learndash-wrapper .ld-tabs .ld-tabs-navigation .ld-tab.ld-active {
            color: var(--color-gold);
            border-bottom-color: var(--color-gold);
        }
        .learndash-wrapper .ld-alert-success {
            background: rgba(201, 169, 110, 0.1);
            border-color: var(--color-gold);
        }
        .learndash-wrapper .ld-alert-success .ld-alert-icon {
            background: var(--color-gold);
        }
        .learndash-wrapper .ld-progress .ld-progress-bar .ld-progress-bar-percentage {
            background: var(--color-gold);
        }
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_button {
            background: var(--color-gold) !important;
            border-color: var(--color-gold) !important;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

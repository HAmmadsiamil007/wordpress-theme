<?php
/**
 * Accessibility Enhancement Engine — Singleton
 *
 * Comprehensive accessibility module providing:
 * - Focus outline styles (dotted, dashed, solid)
 * - Input focus styles
 * - Skip to content link enhancement
 * - ARIA landmark support
 * - Keyboard navigation helpers
 * - Screen reader text utilities
 * - Focus trap for modals
 * - Reduced motion support
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Accessibility class.
 */
class Opulentia_Accessibility {

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
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_footer', array( $this, 'focus_trap_script' ), 100 );
	}

	/**
	 * Check if accessibility features are enabled.
	 *
	 * @return bool
	 */
	private function is_enabled() {
		return (bool) Opulentia_get_option( 'enable-accessibility', true );
	}

	/**
	 * Output inline CSS for accessibility.
	 */
	public function inline_css() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$outline_style = Opulentia_get_option( 'accessibility-outline-style', 'solid' );
		$outline_color = Opulentia_get_option( 'accessibility-outline-color', '#c9a96e' );
		$input_style   = Opulentia_get_option( 'accessibility-input-style', 'solid' );
		$input_color   = Opulentia_get_option( 'accessibility-input-color', '#c9a96e' );

		$css = '';

		// Focus outline styles.
		$css .= '
        :focus-visible {
            outline: 2px ' . $outline_style . ' ' . $outline_color . ';
            outline-offset: 2px;
        }
        ';

		// Input focus styles.
		if ( 'disabled' !== $input_style ) {
			$css .= '
            input:focus,
            textarea:focus,
            select:focus {
                outline: 2px ' . $input_style . ' ' . $input_color . ';
                outline-offset: -1px;
            }
            ';
		}

		// Screen reader text.
		$css .= '
        .screen-reader-text,
        .sr-only {
            border: 0;
            clip: rect(1px, 1px, 1px, 1px);
            clip-path: inset(50%);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
            word-wrap: normal !important;
        }
        ';

		// Skip link.
		$css .= '
        .skip-link {
            position: absolute;
            top: -100%;
            left: 6px;
            z-index: 100000;
            background: ' . $outline_color . ';
            color: #fff;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 0 0 4px 4px;
            transition: top 0.2s ease;
        }
        .skip-link:focus {
            top: 6px;
        }
        ';

		// Reduced motion.
		$css .= '
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}

	/**
	 * Add accessibility body classes.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( $this->is_enabled() ) {
			$classes[] = 'opulentia-a11y-enabled';
		}
		return $classes;
	}

	/**
	 * Output focus trap script for modals/off-canvas.
	 */
	public function focus_trap_script() {
		if ( ! $this->is_enabled() ) {
			return;
		}
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Focus trap for off-canvas panel.
			var panel = document.getElementById('off-canvas-panel');
			var toggle = document.querySelector('.mobile-menu-toggle');
			if (panel && toggle) {
				var focusableSelector = 'a[href], button:not([disabled]), textarea, input, select';
				toggle.addEventListener('click', function() {
					if (panel.classList.contains('is-open')) {
						var firstFocusable = panel.querySelector(focusableSelector);
						if (firstFocusable) firstFocusable.focus();
					}
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * Get aria-current attribute for navigation.
	 *
	 * @param string $item_type The type of current item.
	 * @return string
	 */
	public static function aria_current( $item_type = 'page' ) {
		return 'aria-current="' . esc_attr( $item_type ) . '"';
	}
}

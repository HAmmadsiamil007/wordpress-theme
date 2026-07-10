<?php
/**
 * Dark Mode Module — Singleton
 *
 * Provides:
 * - System preference detection (prefers-color-scheme: dark)
 * - Manual dark mode toggle
 * - Dark mode CSS generation
 * - Image brightness adjustment in dark mode
 * - Color scheme switching
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Dark_Mode class.
 */
class Opulentia_Dark_Mode {

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
		add_action( 'wp_head', array( $this, 'output_dark_mode_script' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Get dark mode mode.
	 *
	 * @return string 'auto', 'manual', 'off'
	 */
	private function get_mode() {
		$mode = Opulentia_get_option( 'dark-mode-mode', 'off' );
		return in_array( $mode, array( 'auto', 'manual', 'off' ), true ) ? $mode : 'off';
	}

	/**
	 * Output dark mode detection script in head.
	 */
	public function output_dark_mode_script() {
		$mode = $this->get_mode();

		if ( 'off' === $mode ) {
			return;
		}
		?>
		<script>
		(function() {
			var mode = '<?php echo esc_js( $mode ); ?>';
			var isDark = false;

			if (mode === 'auto') {
				isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
			} else if (mode === 'manual') {
				isDark = localStorage.getItem('Opulentia_dark_mode') === 'true';
			}

			if (isDark) {
				document.documentElement.classList.add('opulentia-dark-mode');
			}

			if (mode === 'auto') {
				window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
					if (e.matches) {
						document.documentElement.classList.add('opulentia-dark-mode');
					} else {
						document.documentElement.classList.remove('opulentia-dark-mode');
					}
				});
			}
		})();
		</script>
		<?php
	}

	/**
	 * Output dark mode inline CSS.
	 */
	public function inline_css() {
		$mode = $this->get_mode();

		if ( 'off' === $mode ) {
			return;
		}

		$bg_color         = Opulentia_get_option( 'dark-mode-bg-color', '#0a0a0a' );
		$text_color       = Opulentia_get_option( 'dark-mode-text-color', '#e0e0e0' );
		$link_color       = Opulentia_get_option( 'dark-mode-link-color', '#c9a96e' );
		$heading_color    = Opulentia_get_option( 'dark-mode-heading-color', '#ffffff' );
		$border_color     = Opulentia_get_option( 'dark-mode-border-color', '#2a2a2a' );
		$image_brightness = (int) Opulentia_get_option( 'dark-mode-image-brightness', 85 );

		$css = '
        .opulentia-dark-mode {
            --color-primary-dark: ' . $bg_color . ';
            --color-secondary-dark: #0e0e0e;
            --color-text: ' . $text_color . ';
            --color-text-muted: #777777;
            --color-border: ' . $border_color . ';
            --color-link: ' . $link_color . ';
            --color-link-hover: ' . $link_color . ';
            --color-white: ' . $heading_color . ';
            --color-off-white: #141414;
            --color-light-gray: #1a1a1a;
        }

        .opulentia-dark-mode img:not(.skip-dark-mode) {
            filter: brightness(' . ( $image_brightness / 100 ) . ');
        }

        .opulentia-dark-mode .site-header {
            background-color: ' . $bg_color . ';
        }

        .opulentia-dark-mode .footer-newsletter {
            background: linear-gradient(135deg, #0e0e0e 0%, #1a1a1a 100%);
        }
        ';

		// Dark mode toggle button styles.
		$css .= '
        .dark-mode-toggle {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-text);
            padding: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }
        .dark-mode-toggle:hover {
            color: var(--color-gold);
        }
        .dark-mode-toggle__icon {
            width: 20px;
            height: 20px;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}

	/**
	 * Add dark mode body class.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		$mode = $this->get_mode();

		if ( 'manual' === $mode ) {
			$classes[] = 'dark-mode-manual';
		} elseif ( 'auto' === $mode ) {
			$classes[] = 'dark-mode-auto';
		}

		return $classes;
	}
}

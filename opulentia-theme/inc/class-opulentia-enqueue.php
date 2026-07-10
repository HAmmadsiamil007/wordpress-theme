<?php
/**
 * Script and Style Enqueue — Singleton
 *
 * Merges enqueue.php and vite.php into a single class.
 * Handles GSAP CDN, Google Fonts via Opulentia_Fonts,
 * Vite HMR (dev/prod), admin scripts, defer attributes,
 * and font preloading.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Enqueue class.
 */
class Opulentia_Enqueue {

	/**
	 * Vite dev server port.
	 */
	const VITE_PORT = '5173';

	/**
	 * Vite dev server host URL.
	 */
	const VITE_HOST = 'http://localhost:' . self::VITE_PORT;

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 3 );
	}

	/**
	 * Enqueue front-end styles and scripts.
	 */
	public function enqueue_scripts() {
		$this->enqueue_google_fonts();
		$this->enqueue_main_stylesheet();
		$this->enqueue_gsap();
		$this->enqueue_theme_assets();

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue Google Fonts via the Fonts manager class.
	 *
	 * Delegates to Opulentia_Fonts::get_google_fonts_url() as the
	 * single source of truth for font family collection and URL building.
	 */
	private function enqueue_google_fonts() {
		$fonts_url = Opulentia_Fonts::get_instance()->get_google_fonts_url();

		if ( empty( $fonts_url ) ) {
			return;
		}

		wp_enqueue_style(
			'opulentia-google-fonts',
			$fonts_url,
			array(),
			null
		);
	}

	/**
	 * Enqueue main theme stylesheet.
	 */
	private function enqueue_main_stylesheet() {
		wp_enqueue_style(
			'opulentia-style',
			get_stylesheet_uri(),
			array(),
			Opulentia_VERSION
		);
	}

	/**
	 * Enqueue GSAP Core + ScrollTrigger + ScrollToPlugin from CDN.
	 */
	private function enqueue_gsap() {
		$version = '3.12.7';
		$deps    = array( 'gsap-core' );

		wp_enqueue_script(
			'gsap-core',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/gsap.min.js',
			array(),
			$version,
			true
		);

		wp_enqueue_script(
			'gsap-scrolltrigger',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/ScrollTrigger.min.js',
			$deps,
			$version,
			true
		);

		wp_enqueue_script(
			'gsap-scrollto',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/ScrollToPlugin.min.js',
			$deps,
			$version,
			true
		);
	}

	/**
	 * Enqueue theme assets — Vite (dev/prod) or direct fallback.
	 */
	private function enqueue_theme_assets() {
		$is_vite       = $this->is_vite_running();
		$manifest_path = Opulentia_DIR . '/dist/.vite/manifest.json';

		if ( $is_vite || file_exists( $manifest_path ) ) {
			$this->enqueue_vite_assets( $is_vite );
		} else {
			$this->enqueue_direct();
		}
	}

	/**
	 * Fallback: enqueue assets directly (no Vite).
	 */
	private function enqueue_direct() {
		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style(
				'opulentia-woocommerce',
				Opulentia_URI . '/css/woocommerce.css',
				array( 'opulentia-style' ),
				Opulentia_VERSION
			);

			wp_enqueue_style(
				'opulentia-woocommerce-pro',
				Opulentia_URI . '/css/woocommerce-pro.css',
				array( 'opulentia-woocommerce' ),
				Opulentia_VERSION
			);
		}

		wp_enqueue_style(
			'opulentia-responsive',
			Opulentia_URI . '/css/responsive.css',
			array( 'opulentia-style' ),
			Opulentia_VERSION
		);

		wp_enqueue_script(
			'opulentia-navigation',
			Opulentia_URI . '/js/navigation.js',
			array(),
			Opulentia_VERSION,
			true
		);

		wp_enqueue_script(
			'opulentia-custom',
			Opulentia_URI . '/js/custom.js',
			array(),
			Opulentia_VERSION,
			true
		);

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_script(
				'opulentia-woocommerce-pro',
				Opulentia_URI . '/js/woocommerce-pro.js',
				array( 'jquery', 'wc-add-to-cart' ),
				Opulentia_VERSION,
				true
			);
		}
	}

	/**
	 * Enqueue admin styles and scripts.
	 */
	public function admin_scripts() {
		$manifest_path = Opulentia_DIR . '/dist/.vite/manifest.json';

		if ( file_exists( $manifest_path ) ) {
			$data    = json_decode( file_get_contents( $manifest_path ), true );
			$css_key = 'css/admin.css';

			if ( isset( $data[ $css_key ] ) ) {
				$uri = Opulentia_URI . '/dist/' . $data[ $css_key ]['file'];
				wp_enqueue_style(
					'opulentia-admin',
					$uri,
					array(),
					hash( 'crc32b', $data[ $css_key ]['file'] )
				);
				return;
			}
		}

		wp_enqueue_style(
			'opulentia-admin',
			Opulentia_URI . '/css/admin.css',
			array(),
			Opulentia_VERSION
		);
	}

	/**
	 * Add defer attributes to scripts.
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @param string $src    The script source.
	 * @return string
	 */
	public function script_loader_tag( $tag, $handle, $src ) {
		$defer_scripts = array(
			'opulentia-navigation',
			'opulentia-custom',
			'opulentia-woocommerce-pro',
			'gsap-core',
			'gsap-scrolltrigger',
			'gsap-scrollto',
		);

		if ( in_array( $handle, $defer_scripts, true ) ) {
			return str_replace( ' src', ' defer src', $tag );
		}

		return $tag;
	}



	// -------------------------------------------------------------------------
	// Vite HMR Helpers
	// -------------------------------------------------------------------------

	/**
	 * Check if the Vite dev server is running.
	 *
	 * @return bool
	 */
	private function is_vite_running() {
		$cache_key = 'Opulentia_vite_check';
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return 'yes' === $cached;
		}

		$handle = @fsockopen( 'localhost', (int) self::VITE_PORT, $errno, $errstr, 0.5 );

		if ( $handle ) {
			fclose( $handle );
			set_transient( $cache_key, 'yes', 15 );
			return true;
		}

		set_transient( $cache_key, 'no', 15 );
		return false;
	}

	/**
	 * Enqueue assets via Vite (dev) or built files (production).
	 *
	 * @param bool $is_vite Whether the Vite dev server is running.
	 */
	private function enqueue_vite_assets( $is_vite ) {
		if ( $is_vite ) {
			$this->enqueue_vite_dev();
		} else {
			$this->enqueue_vite_prod();
		}
	}

	/**
	 * Enqueue assets from Vite dev server (HMR enabled).
	 */
	private function enqueue_vite_dev() {
		add_action(
			'wp_head',
			function () {
				?>
			<script type="module" src="<?php echo esc_url( self::VITE_HOST ); ?>/@vite/client"></script>
				<?php
			},
			1
		);

		$entries = array(
			'custom'     => '/js/custom.js',
			'navigation' => '/js/navigation.js',
		);

		foreach ( $entries as $handle => $path ) {
			wp_enqueue_script(
				"opulentia-{$handle}",
				self::VITE_HOST . $path,
				array(),
				null,
				true
			);
		}

		$css_entries = array(
			'opulentia-woocommerce' => '/css/woocommerce.css',
			'opulentia-responsive'  => '/css/responsive.css',
			'opulentia-admin'       => '/css/admin.css',
		);

		foreach ( $css_entries as $handle => $path ) {
			wp_enqueue_script(
				"{$handle}-vite",
				self::VITE_HOST . $path,
				array(),
				null,
				true
			);
		}
	}

	/**
	 * Enqueue assets from built /dist/ directory (production).
	 */
	private function enqueue_vite_prod() {
		$manifest_path = Opulentia_DIR . '/dist/.vite/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			return;
		}

		$manifest = json_decode( file_get_contents( $manifest_path ), true );

		if ( ! is_array( $manifest ) ) {
			return;
		}

		$entries = array(
			'custom'     => 'js/custom.js',
			'navigation' => 'js/navigation.js',
		);

		foreach ( $entries as $handle => $key ) {
			if ( ! isset( $manifest[ $key ] ) ) {
				continue;
			}

			$entry   = $manifest[ $key ];
			$js_uri  = Opulentia_URI . '/dist/' . $entry['file'];
			$version = hash( 'crc32b', $entry['file'] );

			wp_enqueue_script(
				"opulentia-{$handle}",
				$js_uri,
				array(),
				$version,
				true
			);

			if ( ! empty( $entry['css'] ) ) {
				foreach ( $entry['css'] as $css_file ) {
					wp_enqueue_style(
						"opulentia-{$handle}-css",
						Opulentia_URI . '/dist/' . $css_file,
						array(),
						$version
					);
				}
			}
		}

		$css_keys = array(
			'woocommerce' => 'css/woocommerce.css',
			'responsive'  => 'css/responsive.css',
			'admin'       => 'css/admin.css',
		);

		foreach ( $css_keys as $handle => $key ) {
			if ( ! isset( $manifest[ $key ] ) ) {
				continue;
			}

			$entry   = $manifest[ $key ];
			$uri     = Opulentia_URI . '/dist/' . $entry['file'];
			$version = hash( 'crc32b', $entry['file'] );

			wp_enqueue_style(
				"opulentia-{$handle}",
				$uri,
				array( 'opulentia-style' ),
				$version
			);
		}
	}
}

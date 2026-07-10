<?php
/**
 * Transparent Header Module — Singleton
 *
 * Enables transparent header on selected page types with:
 * - Conditional display (entire site, homepage, archives, pages, posts, 404)
 * - Per-page override via meta box
 * - Transparent logo (separate image for light/dark backgrounds)
 * - Menu color scheme for transparent state
 * - Background overlay color
 * - Border bottom style
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Transparent_Header class.
 */
class Opulentia_Transparent_Header {

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
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
	}

	/**
	 * Check if transparent header should be applied.
	 *
	 * @return bool
	 */
	private function is_transparent() {
		// Master toggle.
		if ( ! Opulentia_get_option( 'header-transparent', false ) ) {
			return false;
		}

		// Per-page override.
		if ( is_singular() ) {
			$post_id    = get_the_ID();
			$meta_value = get_post_meta( $post_id, '_Opulentia_transparent_header', true );
			if ( 'disable' === $meta_value ) {
				return false;
			}
			if ( 'enable' === $meta_value ) {
				return true;
			}
		}

		// Check conditions.
		$conditions = Opulentia_get_option( 'transparent-header-conditions', array( 'front_page' ) );

		if ( in_array( 'entire_site', $conditions, true ) ) {
			return true;
		}

		if ( is_front_page() && in_array( 'front_page', $conditions, true ) ) {
			return true;
		}

		if ( ( is_home() || is_archive() ) && in_array( 'archives', $conditions, true ) ) {
			return true;
		}

		if ( is_singular( 'page' ) && in_array( 'pages', $conditions, true ) ) {
			return true;
		}

		if ( is_singular( 'post' ) && in_array( 'posts', $conditions, true ) ) {
			return true;
		}

		if ( is_404() && in_array( '404', $conditions, true ) ) {
			return true;
		}

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() && in_array( 'woocommerce', $conditions, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add transparent header body class.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( $this->is_transparent() ) {
			$classes[] = 'header-is-transparent';
		}
		return $classes;
	}

	/**
	 * Output transparent header inline CSS.
	 */
	public function inline_css() {
		if ( ! $this->is_transparent() ) {
			return;
		}

		$menu_color   = Opulentia_get_option( 'transparent-header-menu-color', '#ffffff' );
		$menu_hover   = Opulentia_get_option( 'transparent-header-menu-hover-color', '#c9a96e' );
		$title_color  = Opulentia_get_option( 'transparent-header-title-color', '#ffffff' );
		$border_color = Opulentia_get_option( 'transparent-header-border-color', 'rgba(255,255,255,0.15)' );

		$css = '
        .header-is-transparent .site-header {
            position: absolute;
            background-color: transparent !important;
            border-bottom-color: ' . $border_color . ';
        }
        .header-is-transparent .site-header .main-navigation a {
            color: ' . $menu_color . ';
        }
        .header-is-transparent .site-header .main-navigation a:hover {
            color: ' . $menu_hover . ';
        }
        .header-is-transparent .site-header .site-logo__text {
            color: ' . $title_color . ';
        }
        .header-is-transparent .site-header .header-actions__btn {
            color: ' . $menu_color . ';
        }
        .header-is-transparent .site-header .header-actions__btn:hover {
            color: ' . $menu_hover . ';
        }
        .header-is-transparent .site-header.scrolled {
            background-color: var(--color-primary-dark) !important;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

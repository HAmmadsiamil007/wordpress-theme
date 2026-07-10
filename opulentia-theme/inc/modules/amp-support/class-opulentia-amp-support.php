<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_AMP_Support {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp', array( $this, 'init' ) );
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function init() {
		if ( ! function_exists( 'is_amp_endpoint' ) || ! is_amp_endpoint() ) {
			return;
		}

		add_filter( 'Opulentia_is_svg_icons', '__return_false' );
		add_filter( 'Opulentia_enqueue_custom_js', '__return_false' );
		add_filter( 'Opulentia_enqueue_gsap', '__return_false' );
		remove_action( 'wp_head', array( 'Opulentia_Fonts', 'preload_fonts' ), 2 );
		remove_action( 'wp_enqueue_scripts', array( 'Opulentia_Sticky_Header', 'enqueue_assets' ), 100 );
		remove_action( 'wp_enqueue_scripts', array( 'Opulentia_Live_Search', 'enqueue_scripts' ) );
		remove_action( 'wp_footer', array( 'Opulentia_Scroll_To_Top', 'render' ) );

		add_filter( 'nav_menu_link_attributes', array( $this, 'amp_nav_attributes' ), 10, 3 );
		add_action( 'amp_post_template_css', array( $this, 'amp_inline_css' ) );
		add_filter( 'amp_content_sanitizers', array( $this, 'amp_sanitizers' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'amp_css' ), 120 );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_amp',
			array(
				'title'    => __( 'AMP Support', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 90,
			)
		);

		$wp_customize->add_setting(
			'amp-logo',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'amp-logo',
				array(
					'label'       => __( 'AMP Logo', 'opulentia' ),
					'description' => __( 'Separate logo for AMP pages (optimized for mobile).', 'opulentia' ),
					'section'     => 'opulentia_amp',
				)
			)
		);

		$wp_customize->add_setting(
			'amp-header-bg',
			array(
				'default'           => 'var(--color-primary-dark)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'amp-header-bg',
			array(
				'label'       => __( 'AMP Header Background', 'opulentia' ),
				'section'     => 'opulentia_amp',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-primary-dark)' ),
			)
		);

		$wp_customize->add_setting(
			'amp-enable-sidebar',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'amp-enable-sidebar',
			array(
				'label'   => __( 'Enable AMP Sidebar', 'opulentia' ),
				'section' => 'opulentia_amp',
				'type'    => 'checkbox',
			)
		);
	}

	public function body_class( $classes ) {
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			$classes[] = 'amp-active';
		}
		return $classes;
	}

	public function amp_nav_attributes( $atts, $item, $args ) {
		if ( ! function_exists( 'is_amp_endpoint' ) || ! is_amp_endpoint() ) {
			return $atts;
		}
		if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
			$atts['on']       = 'tap:AMP.setState({ submenu: { open: !submenu.open } })';
			$atts['role']     = 'button';
			$atts['tabindex'] = '0';
		}
		return $atts;
	}

	public function amp_sanitizers( $sanitizers ) {
		$sanitizers['Opulentia_AMP_Sanitizer'] = array();
		return $sanitizers;
	}

	public function amp_css() {
		if ( ! function_exists( 'is_amp_endpoint' ) || ! is_amp_endpoint() ) {
			return;
		}

		$header_bg = Opulentia_get_option( 'amp-header-bg', 'var(--color-primary-dark)' );

		$css = '
        amp-sidebar {
            background: ' . $header_bg . ';
            width: 300px;
        }
        amp-sidebar nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        amp-sidebar nav a {
            display: block;
            padding: 14px 20px;
            color: var(--color-text);
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        amp-sidebar nav a:hover {
            color: var(--color-gold);
        }
        .amp-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background: ' . $header_bg . ';
            border-bottom: 1px solid var(--color-border);
        }
        .amp-header__logo img {
            max-height: 32px;
            width: auto;
        }
        .amp-header__logo a {
            color: var(--color-text);
            font-family: var(--font-heading);
            font-size: 1.1rem;
            text-decoration: none;
        }
        .amp-header__toggle {
            background: none;
            border: none;
            color: var(--color-text);
            cursor: pointer;
            padding: 8px;
        }
        .amp-footer {
            padding: 20px;
            background: var(--color-primary-dark);
            text-align: center;
            color: var(--color-text-muted);
            font-size: 0.85rem;
            border-top: 1px solid var(--color-border);
        }
        amp-img.amp-responsive-img img {
            object-fit: cover;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}

	public function amp_inline_css() {
		echo '.amp-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; }';
	}
}

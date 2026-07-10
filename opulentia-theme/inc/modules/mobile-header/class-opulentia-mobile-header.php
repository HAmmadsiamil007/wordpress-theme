<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Mobile_Header {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'Opulentia_header_after', array( $this, 'render_mobile_header' ) );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_mobile_header',
			array(
				'title'    => __( 'Mobile Header', 'opulentia' ),
				'panel'    => 'Opulentia_header_settings',
				'priority' => 35,
			)
		);

		$wp_customize->add_setting(
			'mobile-header-breakpoint',
			array(
				'default'           => 992,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'mobile-header-breakpoint',
			array(
				'label'       => __( 'Mobile Breakpoint (px)', 'opulentia' ),
				'description' => __( 'Screen width at which mobile header replaces desktop header.', 'opulentia' ),
				'section'     => 'opulentia_mobile_header',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 320,
					'max'  => 1200,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'mobile-header-logo',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'mobile-header-logo',
				array(
					'label'       => __( 'Mobile Logo', 'opulentia' ),
					'description' => __( 'Separate logo for mobile header.', 'opulentia' ),
					'section'     => 'opulentia_mobile_header',
				)
			)
		);

		$wp_customize->add_setting(
			'mobile-header-menu-style',
			array(
				'default'           => 'hamburger',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'mobile-header-menu-style',
			array(
				'label'   => __( 'Menu Toggle Style', 'opulentia' ),
				'section' => 'opulentia_mobile_header',
				'type'    => 'select',
				'choices' => array(
					'hamburger' => __( 'Hamburger Icon', 'opulentia' ),
					'text'      => __( 'Menu Text', 'opulentia' ),
					'icon-text' => __( 'Icon + Text', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'mobile-header-menu-animation',
			array(
				'default'           => 'slide',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'mobile-header-menu-animation',
			array(
				'label'   => __( 'Menu Animation', 'opulentia' ),
				'section' => 'opulentia_mobile_header',
				'type'    => 'select',
				'choices' => array(
					'slide'      => __( 'Slide In', 'opulentia' ),
					'fade'       => __( 'Fade In', 'opulentia' ),
					'slide-down' => __( 'Slide Down', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'mobile-header-bg-color',
			array(
				'default'           => 'var(--color-primary-dark)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'mobile-header-bg-color',
			array(
				'label'       => __( 'Background Color', 'opulentia' ),
				'section'     => 'opulentia_mobile_header',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-primary-dark)' ),
			)
		);

		$wp_customize->add_setting(
			'mobile-header-text-color',
			array(
				'default'           => 'var(--color-text)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'mobile-header-text-color',
			array(
				'label'       => __( 'Text / Link Color', 'opulentia' ),
				'section'     => 'opulentia_mobile_header',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-text)' ),
			)
		);

		$wp_customize->add_setting(
			'mobile-header-active-color',
			array(
				'default'           => 'var(--color-gold)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'mobile-header-active-color',
			array(
				'label'       => __( 'Active / Hover Color', 'opulentia' ),
				'section'     => 'opulentia_mobile_header',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
			)
		);

		$wp_customize->add_setting(
			'mobile-header-sticky',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'mobile-header-sticky',
			array(
				'label'   => __( 'Sticky Mobile Header', 'opulentia' ),
				'section' => 'opulentia_mobile_header',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'mobile-header-search',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'mobile-header-search',
			array(
				'label'   => __( 'Show Search Icon', 'opulentia' ),
				'section' => 'opulentia_mobile_header',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'mobile-header-cart',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'mobile-header-cart',
			array(
				'label'   => __( 'Show Cart Icon', 'opulentia' ),
				'section' => 'opulentia_mobile_header',
				'type'    => 'checkbox',
			)
		);
	}

	public function render_mobile_header() {
		$breakpoint = Opulentia_get_option( 'mobile-header-breakpoint', 992 );
		?>
		<div class="opulentia-mobile-header" id="opulentia-mobile-header" style="display:none;">
			<div class="opulentia-mobile-header__bar">
				<div class="opulentia-mobile-header__left">
					<?php $this->render_mobile_logo(); ?>
				</div>
				<div class="opulentia-mobile-header__right">
					<?php $this->render_mobile_actions(); ?>
					<?php $this->render_toggle_button(); ?>
				</div>
			</div>
			<div class="opulentia-mobile-header__menu" id="opulentia-mobile-menu" aria-hidden="true">
				<nav class="opulentia-mobile-header__nav">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'mobile-menu',
							'container'      => false,
							'fallback_cb'    => false,
							'depth'          => 3,
							'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						)
					);
					?>
				</nav>
			</div>
			<div class="opulentia-mobile-header__overlay" id="opulentia-mobile-overlay"></div>
		</div>
		<?php
	}

	private function render_mobile_logo() {
		$mobile_logo = Opulentia_get_option( 'mobile-header-logo', '' );
		if ( ! empty( $mobile_logo ) ) {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home" class="opulentia-mobile-header__logo-link">';
			echo '<img src="' . esc_url( $mobile_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="opulentia-mobile-header__logo">';
			echo '</a>';
		} elseif ( has_custom_logo() ) {
			the_custom_logo();
		} else {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home" class="opulentia-mobile-header__site-title">';
			echo esc_html( get_bloginfo( 'name' ) );
			echo '</a>';
		}
	}

	private function render_mobile_actions() {
		$show_search = Opulentia_get_option( 'mobile-header-search', true );
		$show_cart   = Opulentia_get_option( 'mobile-header-cart', true );
		if ( $show_search ) {
			echo '<button class="opulentia-mobile-header__action opulentia-mobile-header__action--search js-search-toggle" aria-label="' . esc_attr__( 'Search', 'opulentia' ) . '">';
			echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>';
			echo '</button>';
		}
		if ( $show_cart && class_exists( 'WooCommerce' ) ) {
			$cart_count = WC()->cart->get_cart_contents_count();
			echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="opulentia-mobile-header__action opulentia-mobile-header__action--cart" aria-label="' . esc_attr__( 'Cart', 'opulentia' ) . '">';
			echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>';
			if ( $cart_count > 0 ) {
				echo '<span class="opulentia-mobile-header__cart-count">' . esc_html( $cart_count ) . '</span>';
			}
			echo '</a>';
		}
	}

	private function render_toggle_button() {
		$style = Opulentia_get_option( 'mobile-header-menu-style', 'hamburger' );
		echo '<button class="opulentia-mobile-header__toggle" id="opulentia-mobile-toggle" aria-label="' . esc_attr__( 'Toggle mobile menu', 'opulentia' ) . '" aria-expanded="false">';
		if ( 'text' === $style ) {
			echo '<span class="opulentia-mobile-header__toggle-text">' . esc_html__( 'Menu', 'opulentia' ) . '</span>';
		} elseif ( 'icon-text' === $style ) {
			echo '<span class="opulentia-mobile-header__toggle-icon">';
			echo '<span class="opulentia-mobile-header__toggle-line"></span>';
			echo '<span class="opulentia-mobile-header__toggle-line"></span>';
			echo '<span class="opulentia-mobile-header__toggle-line"></span>';
			echo '</span>';
			echo '<span class="opulentia-mobile-header__toggle-text">' . esc_html__( 'Menu', 'opulentia' ) . '</span>';
		} else {
			echo '<span class="opulentia-mobile-header__toggle-line"></span>';
			echo '<span class="opulentia-mobile-header__toggle-line"></span>';
			echo '<span class="opulentia-mobile-header__toggle-line"></span>';
		}
		echo '</button>';
	}

	public function body_class( $classes ) {
		$classes[] = 'mobile-header-enabled';
		if ( Opulentia_get_option( 'mobile-header-sticky', true ) ) {
			$classes[] = 'mobile-header-sticky';
		}
		$animation = Opulentia_get_option( 'mobile-header-menu-animation', 'slide' );
		$classes[] = 'mobile-menu-' . $animation;
		return $classes;
	}

	public function inline_css() {
		$breakpoint = Opulentia_get_option( 'mobile-header-breakpoint', 992 );
		$bg_color   = Opulentia_get_option( 'mobile-header-bg-color', 'var(--color-primary-dark)' );
		$text_color = Opulentia_get_option( 'mobile-header-text-color', 'var(--color-text)' );
		$active_clr = Opulentia_get_option( 'mobile-header-active-color', 'var(--color-gold)' );

		$css = '
        @media (max-width: ' . $breakpoint . 'px) {
            .site-header {
                display: none !important;
            }
            .opulentia-mobile-header {
                display: block !important;
            }
        }
        .opulentia-mobile-header {
            display: none;
            position: relative;
            z-index: 9990;
        }
        .mobile-header-sticky .opulentia-mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
        }
        .admin-bar.mobile-header-sticky .opulentia-mobile-header {
            top: 46px;
        }
        @media (min-width: 783px) {
            .admin-bar.mobile-header-sticky .opulentia-mobile-header {
                top: 32px;
            }
        }
        .opulentia-mobile-header__bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background-color: ' . $bg_color . ';
            border-bottom: 1px solid var(--color-border);
        }
        .opulentia-mobile-header__left {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .opulentia-mobile-header__right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .opulentia-mobile-header__logo {
            max-height: 36px;
            width: auto;
        }
        .opulentia-mobile-header__site-title {
            color: ' . $text_color . ';
            font-family: var(--font-heading);
            font-size: 1.1rem;
            text-decoration: none;
        }
        .opulentia-mobile-header__action {
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: ' . $text_color . ';
            cursor: pointer;
            padding: 4px;
            position: relative;
        }
        .opulentia-mobile-header__action:hover {
            color: ' . $active_clr . ';
        }
        .opulentia-mobile-header__cart-count {
            position: absolute;
            top: -4px;
            right: -6px;
            background: ' . $active_clr . ';
            color: #000;
            font-size: 10px;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            padding: 0 4px;
        }
        .opulentia-mobile-header__toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: ' . $text_color . ';
        }
        .opulentia-mobile-header__toggle:hover {
            color: ' . $active_clr . ';
        }
        .opulentia-mobile-header__toggle-line {
            display: block;
            width: 22px;
            height: 2px;
            background: currentColor;
            margin: 4px 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
            border-radius: 1px;
        }
        .opulentia-mobile-header__toggle[aria-expanded="true"] .opulentia-mobile-header__toggle-line:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }
        .opulentia-mobile-header__toggle[aria-expanded="true"] .opulentia-mobile-header__toggle-line:nth-child(2) {
            opacity: 0;
        }
        .opulentia-mobile-header__toggle[aria-expanded="true"] .opulentia-mobile-header__toggle-line:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }
        .opulentia-mobile-header__toggle-icon {
            display: flex;
            flex-direction: column;
        }
        .opulentia-mobile-header__toggle-text {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .opulentia-mobile-header__menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: ' . $bg_color . ';
            border-bottom: 1px solid var(--color-border);
            max-height: 70vh;
            overflow-y: auto;
            z-index: 9991;
        }
        .opulentia-mobile-header__menu.open {
            display: block;
        }
        .mobile-menu-slide .opulentia-mobile-header__menu {
            display: block;
            position: fixed;
            top: 0;
            left: -100%;
            width: 80%;
            max-width: 320px;
            height: 100vh;
            max-height: 100vh;
            transition: left 0.3s ease;
            z-index: 9992;
        }
        .mobile-menu-slide .opulentia-mobile-header__menu.open {
            left: 0;
        }
        .mobile-menu-fade .opulentia-mobile-header__menu {
            display: block;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .mobile-menu-fade .opulentia-mobile-header__menu.open {
            opacity: 1;
            visibility: visible;
        }
        .mobile-menu-slide-down .opulentia-mobile-header__menu {
            display: block;
            transform: translateY(-10px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .mobile-menu-slide-down .opulentia-mobile-header__menu.open {
            transform: translateY(0);
            opacity: 1;
        }
        .opulentia-mobile-header__overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9991;
        }
        .opulentia-mobile-header__overlay.open {
            display: block;
        }
        .opulentia-mobile-header__nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .opulentia-mobile-header__nav a {
            display: block;
            padding: 14px 20px;
            color: ' . $text_color . ';
            text-decoration: none;
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .opulentia-mobile-header__nav a:hover,
        .opulentia-mobile-header__nav .current-menu-item > a {
            color: ' . $active_clr . ';
            background: rgba(255,255,255,0.03);
        }
        .opulentia-mobile-header__nav .menu-item-has-children > a::after {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg);
            margin-left: 8px;
            transition: transform 0.2s ease;
            float: right;
            margin-top: 5px;
        }
        .opulentia-mobile-header__nav .sub-menu {
            display: none;
            padding-left: 16px;
        }
        .opulentia-mobile-header__nav .sub-menu.open {
            display: block;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Responsive_Controls {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
	}

	private function get_breakpoints() {
		$custom = get_theme_mod( 'op_responsive_breakpoints', '' );
		if ( $custom ) {
			$parts = array_map( 'trim', explode( ',', $custom ) );
			if ( count( $parts ) === 2 ) {
				return array(
					'tablet' => absint( $parts[0] ) ?: 992,
					'mobile' => absint( $parts[1] ) ?: 576,
				);
			}
		}
		return array(
			'tablet' => 992,
			'mobile' => 576,
		);
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_panel(
			'opulentia_responsive',
			array(
				'title'       => __( 'Responsive Controls', 'opulentia' ),
				'description' => __( 'Per-device visibility, typography, spacing, and custom breakpoints.', 'opulentia' ),
				'priority'    => 150,
			)
		);

		// ── Breakpoints ──
		$wp_customize->add_section(
			'op_responsive_breakpoints_section',
			array(
				'title' => __( 'Breakpoints', 'opulentia' ),
				'panel' => 'opulentia_responsive',
			)
		);

		$wp_customize->add_setting(
			'op_responsive_breakpoints',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_responsive_breakpoints',
			array(
				'label'       => __( 'Custom Breakpoints', 'opulentia' ),
				'description' => __( 'Format: tablet,mobile (e.g., 992,576). Leave empty for defaults.', 'opulentia' ),
				'section'     => 'op_responsive_breakpoints_section',
				'type'        => 'text',
			)
		);

		// ── Visibility ──
		$wp_customize->add_section(
			'op_responsive_visibility',
			array(
				'title' => __( 'Visibility', 'opulentia' ),
				'panel' => 'opulentia_responsive',
			)
		);

		$wp_customize->add_setting(
			'op_responsive_hide_desktop',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_responsive_hide_desktop',
			array(
				'label'       => __( 'Hide on Desktop', 'opulentia' ),
				'description' => __( 'Comma-separated: header,footer,sidebar,breadcrumbs,scroll-top', 'opulentia' ),
				'section'     => 'op_responsive_visibility',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_responsive_hide_tablet',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_responsive_hide_tablet',
			array(
				'label'       => __( 'Hide on Tablet', 'opulentia' ),
				'description' => __( 'Comma-separated: header,footer,sidebar,breadcrumbs,scroll-top', 'opulentia' ),
				'section'     => 'op_responsive_visibility',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_responsive_hide_mobile',
			array(
				'default'           => 'sidebar',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_responsive_hide_mobile',
			array(
				'label'       => __( 'Hide on Mobile', 'opulentia' ),
				'description' => __( 'Comma-separated: header,footer,sidebar,breadcrumbs,scroll-top', 'opulentia' ),
				'section'     => 'op_responsive_visibility',
				'type'        => 'text',
			)
		);

		// ── Per-Device Typography ──
		$wp_customize->add_section(
			'op_responsive_typography',
			array(
				'title' => __( 'Typography', 'opulentia' ),
				'panel' => 'opulentia_responsive',
			)
		);

		$devices = array(
			'tablet' => __( 'Tablet', 'opulentia' ),
			'mobile' => __( 'Mobile', 'opulentia' ),
		);

		foreach ( $devices as $device => $device_label ) {
			$wp_customize->add_setting(
				"op_responsive_{$device}_body_size",
				array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				"op_responsive_{$device}_body_size",
				array(
					'label'       => sprintf( __( '%s — Body Font Size', 'opulentia' ), $device_label ),
					'description' => __( 'CSS value (e.g., 15px, 0.9375rem). Leave empty to inherit.', 'opulentia' ),
					'section'     => 'op_responsive_typography',
					'type'        => 'text',
					'input_attrs' => array( 'placeholder' => '15px' ),
				)
			);

			$wp_customize->add_setting(
				"op_responsive_{$device}_heading_scale",
				array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				"op_responsive_{$device}_heading_scale",
				array(
					'label'       => sprintf( __( '%s — Heading Scale Factor', 'opulentia' ), $device_label ),
					'description' => __( 'Multiplier (0.75 = 75%). Leave empty for default.', 'opulentia' ),
					'section'     => 'op_responsive_typography',
					'type'        => 'number',
					'input_attrs' => array(
						'step' => '0.05',
						'min'  => '0.5',
						'max'  => '1.5',
					),
				)
			);

			$wp_customize->add_setting(
				"op_responsive_{$device}_h1_size",
				array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				"op_responsive_{$device}_h1_size",
				array(
					'label'       => sprintf( __( '%s — H1 Font Size', 'opulentia' ), $device_label ),
					'section'     => 'op_responsive_typography',
					'type'        => 'text',
					'input_attrs' => array( 'placeholder' => '2rem' ),
				)
			);

			$wp_customize->add_setting(
				"op_responsive_{$device}_h2_size",
				array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				"op_responsive_{$device}_h2_size",
				array(
					'label'       => sprintf( __( '%s — H2 Font Size', 'opulentia' ), $device_label ),
					'section'     => 'op_responsive_typography',
					'type'        => 'text',
					'input_attrs' => array( 'placeholder' => '1.75rem' ),
				)
			);

			$wp_customize->add_setting(
				"op_responsive_{$device}_h3_size",
				array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				"op_responsive_{$device}_h3_size",
				array(
					'label'       => sprintf( __( '%s — H3 Font Size', 'opulentia' ), $device_label ),
					'section'     => 'op_responsive_typography',
					'type'        => 'text',
					'input_attrs' => array( 'placeholder' => '1.5rem' ),
				)
			);

			$wp_customize->add_setting(
				"op_responsive_{$device}_content_width",
				array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				"op_responsive_{$device}_content_width",
				array(
					'label'       => sprintf( __( '%s — Content Width', 'opulentia' ), $device_label ),
					'description' => __( 'CSS max-width value (e.g., 100%, 100vw).', 'opulentia' ),
					'section'     => 'op_responsive_typography',
					'type'        => 'text',
					'input_attrs' => array( 'placeholder' => '100%' ),
				)
			);
		}
	}

	public function enqueue_scripts() {
		$css = '
.op-hide-desktop{display:none!important}
';
		wp_add_inline_style( 'opulentia-style', $css );

		if ( is_customize_preview() ) {
			wp_enqueue_script(
				'opulentia-responsive-controls',
				Opulentia_URI . '/inc/modules/responsive-controls/js/responsive-preview.js',
				array( 'jquery', 'customize-preview' ),
				Opulentia_VERSION,
				true
			);
		}
	}

	public function body_classes( $classes ) {
		$bp        = $this->get_breakpoints();
		$classes[] = 'op-bp-tablet-' . $bp['tablet'];
		$classes[] = 'op-bp-mobile-' . $bp['mobile'];
		return $classes;
	}

	public function dynamic_css( $css ) {
		$bp         = $this->get_breakpoints();
		$tablet_max = $bp['tablet'] - 1;
		$mobile_max = $bp['mobile'] - 1;

		// Visibility rules
		$hide_desktop = get_theme_mod( 'op_responsive_hide_desktop', '' );
		$hide_tablet  = get_theme_mod( 'op_responsive_hide_tablet', '' );
		$hide_mobile  = get_theme_mod( 'op_responsive_hide_mobile', 'sidebar' );

		$elements = array( 'header', 'footer', 'sidebar', 'breadcrumbs', 'scroll-top' );

		$css .= "
@media(min-width:{$bp['tablet']}px){
" . $this->visibility_css( $hide_desktop, $elements ) . "}

@media(min-width:{$bp['mobile']}px) and (max-width:{$tablet_max}px){
" . $this->visibility_css( $hide_tablet, $elements ) . "}

@media(max-width:{$mobile_max}px){
" . $this->visibility_css( $hide_mobile, $elements ) . '}
';

		// Responsive typography
		$devices = array(
			'tablet' => $bp['tablet'],
			'mobile' => $bp['mobile'],
		);
		foreach ( $devices as $device => $breakpoint ) {
			$max_bp = $device === 'mobile' ? $breakpoint : ( $breakpoint - 1 );
			$min_bp = $device === 'mobile' ? '' : $breakpoint;
			$media  = $device === 'mobile'
				? "@media(max-width:{$max_bp}px)"
				: "@media(min-width:{$min_bp}px) and (max-width:{$max_bp}px)";

			$body_size     = get_theme_mod( "op_responsive_{$device}_body_size", '' );
			$h1_size       = get_theme_mod( "op_responsive_{$device}_h1_size", '' );
			$h2_size       = get_theme_mod( "op_responsive_{$device}_h2_size", '' );
			$h3_size       = get_theme_mod( "op_responsive_{$device}_h3_size", '' );
			$content_width = get_theme_mod( "op_responsive_{$device}_content_width", '' );
			$heading_scale = get_theme_mod( "op_responsive_{$device}_heading_scale", '' );

			$rules = '';

			if ( $body_size ) {
				$rules .= "body,body p,.op-body-text{font-size:{$body_size}!important}";
			}
			if ( $h1_size ) {
				$rules .= "h1,.h1,.entry-title{font-size:{$h1_size}!important}";
			}
			if ( $h2_size ) {
				$rules .= "h2,.h2{font-size:{$h2_size}!important}";
			}
			if ( $h3_size ) {
				$rules .= "h3,.h3{font-size:{$h3_size}!important}";
			}
			if ( $heading_scale ) {
				$rules .= "h1,h2,h3,h4,h5,h6{transform:scale({$heading_scale});transform-origin:left center}";
			}
			if ( $content_width ) {
				$rules .= ".site-content,.container,.op-container{max-width:{$content_width}!important}";
			}

			if ( $rules ) {
				$css .= "{$media}{{$rules}}";
			}
		}

		return $css;
	}

	private function visibility_css( $setting, $all_elements ) {
		if ( ! $setting ) {
			return '';
		}
		$hidden = array_map( 'trim', explode( ',', $setting ) );
		$rules  = '';
		foreach ( $hidden as $el ) {
			if ( in_array( $el, $all_elements, true ) ) {
				$rules .= ".op-{$el}-wrap,.op-{$el}-area,#op-{$el}{display:none!important}";
			}
		}
		return $rules;
	}
}

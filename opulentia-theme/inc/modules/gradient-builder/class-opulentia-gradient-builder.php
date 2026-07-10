<?php
/**
 * Gradient Builder Module
 *
 * @package Opulentia
 * @subpackage Modules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Gradient_Builder {

	private static $instance = null;

	/**
	 * Get singleton instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer_controls' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'inject_dynamic_css' ) );
		add_shortcode( 'op_gradient', array( $this, 'render_gradient_shortcode' ) );
		add_shortcode( 'op_gradient_btn', array( $this, 'render_gradient_btn_shortcode' ) );
	}

	/**
	 * Register Customizer controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager.
	 */
	public function register_customizer_controls( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_gradient_builder',
			array(
				'title'    => esc_html__( 'Gradient Builder', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 30,
			)
		);

		$controls = array(
			'gradient-primary-start' => array(
				'label'   => esc_html__( 'Primary Gradient — Start Color', 'opulentia' ),
				'type'    => 'color',
				'default' => '#b8860b',
			),
			'gradient-primary-end'   => array(
				'label'   => esc_html__( 'Primary Gradient — End Color', 'opulentia' ),
				'type'    => 'color',
				'default' => '#c9a96e',
			),
			'gradient-primary-angle' => array(
				'label'       => esc_html__( 'Primary Gradient — Angle', 'opulentia' ),
				'type'        => 'range',
				'default'     => 135,
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 360,
					'step' => 1,
				),
			),
			'gradient-dark-start'    => array(
				'label'   => esc_html__( 'Dark Gradient — Start Color', 'opulentia' ),
				'type'    => 'color',
				'default' => '#1a1a1a',
			),
			'gradient-dark-end'      => array(
				'label'   => esc_html__( 'Dark Gradient — End Color', 'opulentia' ),
				'type'    => 'color',
				'default' => '#111111',
			),
			'gradient-dark-angle'    => array(
				'label'       => esc_html__( 'Dark Gradient — Angle', 'opulentia' ),
				'type'        => 'range',
				'default'     => 135,
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 360,
					'step' => 1,
				),
			),
			'gradient-accent-start'  => array(
				'label'   => esc_html__( 'Accent Gradient — Start Color', 'opulentia' ),
				'type'    => 'color',
				'default' => '#c9a96e',
			),
			'gradient-accent-end'    => array(
				'label'   => esc_html__( 'Accent Gradient — End Color', 'opulentia' ),
				'type'    => 'color',
				'default' => '#d4a843',
			),
		);

		foreach ( $controls as $id => $args ) {
			$setting_id = 'op_' . $id;

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $args['default'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
					'theme_supports'    => false,
				)
			);

			if ( 'range' === $args['type'] ) {
				$wp_customize->add_control(
					$setting_id,
					array(
						'label'       => $args['label'],
						'section'     => 'opulentia_gradient_builder',
						'type'        => 'range',
						'input_attrs' => $args['input_attrs'],
					)
				);
			} else {
				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						$setting_id,
						array(
							'label'   => $args['label'],
							'section' => 'opulentia_gradient_builder',
						)
					)
				);
			}
		}
	}

	/**
	 * Inject dynamic gradient CSS into the frontend.
	 */
	public function inject_dynamic_css() {
		if ( is_admin() ) {
			return;
		}

		$primary_start = get_theme_mod( 'op_gradient-primary-start', '#b8860b' );
		$primary_end   = get_theme_mod( 'op_gradient-primary-end', '#c9a96e' );
		$primary_angle = get_theme_mod( 'op_gradient-primary-angle', 135 );

		$dark_start = get_theme_mod( 'op_gradient-dark-start', '#1a1a1a' );
		$dark_end   = get_theme_mod( 'op_gradient-dark-end', '#111111' );
		$dark_angle = get_theme_mod( 'op_gradient-dark-angle', 135 );

		$accent_start = get_theme_mod( 'op_gradient-accent-start', '#c9a96e' );
		$accent_end   = get_theme_mod( 'op_gradient-accent-end', '#d4a843' );

		$css = '
:root {
    --gradient-primary: linear-gradient(' . intval( $primary_angle ) . 'deg, ' . esc_attr( $primary_start ) . ', ' . esc_attr( $primary_end ) . ');
    --gradient-dark: linear-gradient(' . intval( $dark_angle ) . 'deg, ' . esc_attr( $dark_start ) . ', ' . esc_attr( $dark_end ) . ');
    --gradient-accent: linear-gradient(' . intval( $accent_angle ?? 135 ) . 'deg, ' . esc_attr( $accent_start ) . ', ' . esc_attr( $accent_end ) . ');
}
.op-gradient-primary { background: var(--gradient-primary); }
.op-gradient-dark { background: var(--gradient-dark); }
.op-gradient-accent { background: var(--gradient-accent); }
.op-gradient-text-primary {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.op-gradient-text-dark {
    background: var(--gradient-dark);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.op-gradient-text-accent {
    background: var(--gradient-accent);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.op-gradient-wrap {
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}
.op-gradient-text-primary,
.op-gradient-text-dark,
.op-gradient-text-accent {
    display: inline-block;
}
.op-gradient-btn {
    display: inline-block;
    padding: 12px 32px;
    border-radius: 6px;
    font-family: var(--font-body, Inter);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: opacity 0.3s ease;
}
.op-gradient-btn:hover { opacity: 0.9; }
';

		wp_add_inline_style( 'opulentia-main', $css );
	}

	/**
	 * Render the [op_gradient] shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Enclosed content.
	 * @return string
	 */
	public function render_gradient_shortcode( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'type'       => 'primary',
				'angle'      => '135',
				'start'      => '#b8860b',
				'end'        => '#c9a96e',
				'padding'    => '40px',
				'text_color' => '#ffffff',
			),
			$atts,
			'op_gradient'
		);

		if ( 'custom' === $atts['type'] ) {
			$angle  = intval( $atts['angle'] );
			$start  = sanitize_hex_color( $atts['start'] );
			$end    = sanitize_hex_color( $atts['end'] );
			$style  = 'background:linear-gradient(' . $angle . 'deg,' . $start . ',' . $end . ');';
			$style .= 'padding:' . esc_attr( $atts['padding'] ) . ';';
			$style .= 'color:' . sanitize_hex_color( $atts['text_color'] ) . ';';

			return '<div class="op-gradient-wrap" style="' . $style . '">' . do_shortcode( wp_kses_post( $content ) ) . '</div>';
		}

		$valid_types = array( 'primary', 'dark', 'accent' );
		$type        = in_array( $atts['type'], $valid_types, true ) ? $atts['type'] : 'primary';

		return '<div class="op-gradient-' . esc_attr( $type ) . ' op-gradient-wrap" style="padding:' . esc_attr( $atts['padding'] ) . ';color:' . sanitize_hex_color( $atts['text_color'] ) . '">' . do_shortcode( wp_kses_post( $content ) ) . '</div>';
	}

	/**
	 * Render the [op_gradient_btn] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_gradient_btn_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'url'    => '#',
				'text'   => esc_html__( 'Click Me', 'opulentia' ),
				'type'   => 'primary',
				'target' => '',
			),
			$atts,
			'op_gradient_btn'
		);

		$valid_types = array( 'primary', 'dark', 'accent' );
		$type        = in_array( $atts['type'], $valid_types, true ) ? $atts['type'] : 'primary';
		$target      = '_blank' === $atts['target'] ? ' target="_blank" rel="noopener noreferrer"' : '';

		return '<a href="' . esc_url( $atts['url'] ) . '" class="op-gradient-' . esc_attr( $type ) . ' op-gradient-btn"' . $target . '>' . esc_html( $atts['text'] ) . '</a>';
	}
}

Opulentia_Gradient_Builder::get_instance();

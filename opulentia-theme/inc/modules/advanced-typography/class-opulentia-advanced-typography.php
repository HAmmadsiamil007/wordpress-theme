<?php
/**
 * Advanced Typography Module — Singleton
 *
 * Per-device typography controls for 7 element groups with
 * live preview in the customizer. Generates responsive inline
 * CSS for desktop, tablet, and mobile breakpoints.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Advanced_Typography class.
 */
class Opulentia_Advanced_Typography {

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
		add_action( 'customize_register', array( $this, 'register_customizer' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );
	}

	/**
	 * Element groups with their CSS selectors.
	 *
	 * @var array
	 */
	private $elements = array(
		'body'       => 'Body',
		'h1'         => 'Heading 1',
		'h2'         => 'Heading 2',
		'h3'         => 'Heading 3',
		'h4'         => 'Heading 4-6',
		'site_title' => 'Site Title',
		'navigation' => 'Navigation',
	);

	/**
	 * Device keys with labels.
	 *
	 * @var array
	 */
	private $devices = array( 'desktop', 'tablet', 'mobile' );

	/**
	 * Device display labels.
	 *
	 * @var array
	 */
	private $device_labels = array(
		'desktop' => 'Desktop',
		'tablet'  => 'Tablet',
		'mobile'  => 'Mobile',
	);

	/**
	 * CSS property mapping.
	 *
	 * @var array
	 */
	private $css_props = array(
		'font_family'    => 'font-family',
		'font_weight'    => 'font-weight',
		'font_size'      => 'font-size',
		'line_height'    => 'line-height',
		'letter_spacing' => 'letter-spacing',
		'text_transform' => 'text-transform',
	);

	/**
	 * Element selector map for CSS output.
	 *
	 * @var array
	 */
	private $element_selectors = array(
		'body'       => 'body, p, .entry-content',
		'h1'         => 'h1, .h1, .entry-title',
		'h2'         => 'h2, .h2',
		'h3'         => 'h3, .h3',
		'h4'         => 'h4, h5, h6, .h4, .h5, .h6',
		'site_title' => '.site-title, .site-branding .site-title a',
		'navigation' => '.main-navigation a, .nav-menu a, .menu-primary-menu a, .primary-menu a',
	);

	/**
	 * Register customizer section and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager.
	 */
	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_advanced_typography',
			array(
				'title'    => __( 'Advanced Typography', 'opulentia' ),
				'priority' => 5,
				'panel'    => 'Opulentia_global_settings',
			)
		);

		foreach ( $this->elements as $el => $el_label ) {
			$wp_customize->add_setting(
				'typo_' . $el . '_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				'typo_' . $el . '_heading',
				array(
					'label'   => $el_label,
					'section' => 'opulentia_advanced_typography',
					'type'    => 'hidden',
				)
			);

			foreach ( $this->devices as $device ) {
				$label_prefix = $this->device_labels[ $device ];

				$wp_customize->add_setting(
					'typo_' . $el . '_' . $device . '_font_family',
					array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					'typo_' . $el . '_' . $device . '_font_family',
					array(
						'label'   => $label_prefix . ' — ' . __( 'Font Family', 'opulentia' ),
						'section' => 'opulentia_advanced_typography',
						'type'    => 'select',
						'choices' => $this->get_font_choices(),
					)
				);

				$wp_customize->add_setting(
					'typo_' . $el . '_' . $device . '_font_weight',
					array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					'typo_' . $el . '_' . $device . '_font_weight',
					array(
						'label'   => $label_prefix . ' — ' . __( 'Weight', 'opulentia' ),
						'section' => 'opulentia_advanced_typography',
						'type'    => 'select',
						'choices' => array(
							''    => __( 'Inherit', 'opulentia' ),
							'100' => '100',
							'200' => '200',
							'300' => '300',
							'400' => '400',
							'500' => '500',
							'600' => '600',
							'700' => '700',
							'800' => '800',
							'900' => '900',
						),
					)
				);

				$wp_customize->add_setting(
					'typo_' . $el . '_' . $device . '_font_size',
					array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					'typo_' . $el . '_' . $device . '_font_size',
					array(
						'label'       => $label_prefix . ' — ' . __( 'Size', 'opulentia' ),
						'section'     => 'opulentia_advanced_typography',
						'type'        => 'number',
						'input_attrs' => array(
							'min'  => 8,
							'max'  => 100,
							'step' => 1,
						),
					)
				);

				$wp_customize->add_setting(
					'typo_' . $el . '_' . $device . '_line_height',
					array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					'typo_' . $el . '_' . $device . '_line_height',
					array(
						'label'       => $label_prefix . ' — ' . __( 'Line Height', 'opulentia' ),
						'section'     => 'opulentia_advanced_typography',
						'type'        => 'number',
						'input_attrs' => array(
							'min'  => 1,
							'max'  => 3,
							'step' => 0.1,
						),
					)
				);

				$wp_customize->add_setting(
					'typo_' . $el . '_' . $device . '_letter_spacing',
					array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					'typo_' . $el . '_' . $device . '_letter_spacing',
					array(
						'label'       => $label_prefix . ' — ' . __( 'Letter Spacing', 'opulentia' ),
						'section'     => 'opulentia_advanced_typography',
						'type'        => 'number',
						'input_attrs' => array(
							'min'  => -2,
							'max'  => 10,
							'step' => 0.5,
						),
					)
				);

				$wp_customize->add_setting(
					'typo_' . $el . '_' . $device . '_text_transform',
					array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					'typo_' . $el . '_' . $device . '_text_transform',
					array(
						'label'   => $label_prefix . ' — ' . __( 'Transform', 'opulentia' ),
						'section' => 'opulentia_advanced_typography',
						'type'    => 'select',
						'choices' => array(
							''           => __( 'Inherit', 'opulentia' ),
							'none'       => __( 'None', 'opulentia' ),
							'uppercase'  => __( 'Uppercase', 'opulentia' ),
							'lowercase'  => __( 'Lowercase', 'opulentia' ),
							'capitalize' => __( 'Capitalize', 'opulentia' ),
						),
					)
				);
			}
		}
	}

	/**
	 * Output inline CSS for advanced typography settings.
	 */
	public function inline_css() {
		if ( is_admin() ) {
			return;
		}

		$css = '';

		$desktop_css = $this->build_device_css( 'desktop' );
		if ( ! empty( $desktop_css ) ) {
			$css .= $desktop_css;
		}

		$tablet_css = $this->build_device_css( 'tablet' );
		if ( ! empty( $tablet_css ) ) {
			$css .= '@media (max-width: 768px) { ' . $tablet_css . ' } ';
		}

		$mobile_css = $this->build_device_css( 'mobile' );
		if ( ! empty( $mobile_css ) ) {
			$css .= '@media (max-width: 480px) { ' . $mobile_css . ' } ';
		}

		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'opulentia-style', $css );
		}
	}

	/**
	 * Build CSS rules for a given device.
	 *
	 * @param string $device Device key (desktop, tablet, mobile).
	 * @return string Compiled CSS.
	 */
	private function build_device_css( $device ) {
		$css   = '';
		$props = array( 'font_family', 'font_weight', 'font_size', 'line_height', 'letter_spacing', 'text_transform' );

		foreach ( $this->element_selectors as $el => $selector ) {
			$rules = array();

			foreach ( $props as $prop ) {
				$value = Opulentia_get_option( 'typo_' . $el . '_' . $device . '_' . $prop, '' );

				if ( '' === $value ) {
					continue;
				}

				$css_prop = $this->css_props[ $prop ];

				switch ( $prop ) {
					case 'font_family':
						$rules[] = $css_prop . ': ' . esc_attr( $value ) . ';';
						break;
					case 'font_size':
						$rules[] = $css_prop . ': ' . floatval( $value ) . 'px;';
						break;
					case 'letter_spacing':
						$rules[] = $css_prop . ': ' . floatval( $value ) . 'px;';
						break;
					case 'line_height':
						$rules[] = $css_prop . ': ' . floatval( $value ) . ';';
						break;
					default:
						$rules[] = $css_prop . ': ' . esc_attr( $value ) . ';';
						break;
				}
			}

			if ( ! empty( $rules ) ) {
				$css .= $selector . ' { ' . implode( ' ', $rules ) . ' } ';
			}
		}

		return $css;
	}

	/**
	 * Get font choices for the select dropdown.
	 *
	 * @return array Font name => Font name.
	 */
	private function get_font_choices() {
		$fonts = array(
			''                   => __( 'Inherit', 'opulentia' ),
			'Inter'              => 'Inter',
			'Playfair Display'   => 'Playfair Display',
			'Roboto'             => 'Roboto',
			'Open Sans'          => 'Open Sans',
			'Lato'               => 'Lato',
			'Montserrat'         => 'Montserrat',
			'Poppins'            => 'Poppins',
			'Merriweather'       => 'Merriweather',
			'Source Sans Pro'    => 'Source Sans Pro',
			'Raleway'            => 'Raleway',
			'Oswald'             => 'Oswald',
			'PT Sans'            => 'PT Sans',
			'Nunito'             => 'Nunito',
			'DM Sans'            => 'DM Sans',
			'Work Sans'          => 'Work Sans',
			'Cormorant Garamond' => 'Cormorant Garamond',
			'Libre Baskerville'  => 'Libre Baskerville',
			'Josefin Sans'       => 'Josefin Sans',
			'Fira Sans'          => 'Fira Sans',
		);

		return $fonts;
	}
}

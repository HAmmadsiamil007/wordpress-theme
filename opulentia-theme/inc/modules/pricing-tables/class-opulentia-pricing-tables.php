<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Pricing_Tables {

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
		add_shortcode( 'op_pricing', array( $this, 'render_shortcode' ) );
		add_shortcode( 'op_pricing_plan', array( $this, 'render_plan_shortcode' ) );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_pricing',
			array(
				'title'    => __( 'Pricing Tables', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 210,
			)
		);

		$wp_customize->add_setting(
			'pricing-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'pricing-enable',
			array(
				'label'   => __( 'Enable Pricing Tables', 'opulentia' ),
				'section' => 'opulentia_pricing',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'pricing-default-columns',
			array(
				'default'           => '3',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'pricing-default-columns',
			array(
				'label'   => __( 'Default Columns', 'opulentia' ),
				'section' => 'opulentia_pricing',
				'type'    => 'select',
				'choices' => array(
					'2' => __( '2 Columns', 'opulentia' ),
					'3' => __( '3 Columns', 'opulentia' ),
					'4' => __( '4 Columns', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'pricing-default-style',
			array(
				'default'           => 'cards',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'pricing-default-style',
			array(
				'label'   => __( 'Default Style', 'opulentia' ),
				'section' => 'opulentia_pricing',
				'type'    => 'select',
				'choices' => array(
					'cards'   => __( 'Cards', 'opulentia' ),
					'minimal' => __( 'Minimal', 'opulentia' ),
				),
			)
		);
	}

	public function render_shortcode( $atts, $content = null ) {
		if ( ! Opulentia_get_option( 'pricing-enable', true ) ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'columns'   => Opulentia_get_option( 'pricing-default-columns', '3' ),
				'style'     => Opulentia_get_option( 'pricing-default-style', 'cards' ),
				'highlight' => '0',
			),
			$atts,
			'op_pricing'
		);

		$columns   = in_array( $atts['columns'], array( '2', '3', '4' ) ) ? $atts['columns'] : '3';
		$style     = in_array( $atts['style'], array( 'cards', 'minimal' ) ) ? $atts['style'] : 'cards';
		$highlight = intval( $atts['highlight'] );

		$output  = '<div class="op-pricing-grid op-pricing-grid--' . esc_attr( $style ) . '" style="--op-columns: ' . esc_attr( $columns ) . ';" data-highlight="' . esc_attr( $highlight ) . '">';
		$output .= do_shortcode( $content );
		$output .= '</div>';

		return $output;
	}

	public function render_plan_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'name'        => '',
				'price'       => '',
				'period'      => '',
				'description' => '',
				'btn_text'    => '',
				'btn_url'     => '',
				'features'    => '',
				'featured'    => 'false',
			),
			$atts,
			'op_pricing_plan'
		);

		if ( empty( $atts['name'] ) ) {
			return '';
		}

		$featured   = 'true' === $atts['featured'];
		$features   = ! empty( $atts['features'] ) ? array_map( 'trim', explode( ',', $atts['features'] ) ) : array();
		$card_class = 'op-pricing-card';
		if ( $featured ) {
			$card_class .= ' op-pricing-card--featured';
		}

		$output  = '<div class="' . esc_attr( $card_class ) . '">';
		$output .= '<div class="op-pricing-header">';
		$output .= '<h3 class="op-pricing-name">' . esc_html( $atts['name'] ) . '</h3>';
		if ( ! empty( $atts['price'] ) ) {
			$output .= '<div class="op-pricing-price-wrap">';
			$output .= '<span class="op-pricing-price">' . esc_html( $atts['price'] ) . '</span>';
			if ( ! empty( $atts['period'] ) ) {
				$output .= '<span class="op-pricing-period">' . esc_html( $atts['period'] ) . '</span>';
			}
			$output .= '</div>';
		}
		if ( ! empty( $atts['description'] ) ) {
			$output .= '<div class="op-pricing-desc">' . wp_kses_post( $atts['description'] ) . '</div>';
		}
		$output .= '</div>';

		if ( ! empty( $features ) ) {
			$output .= '<ul class="op-pricing-features">';
			foreach ( $features as $feature ) {
				$output .= '<li>' . esc_html( $feature ) . '</li>';
			}
			$output .= '</ul>';
		}

		if ( ! empty( $atts['btn_text'] ) && ! empty( $atts['btn_url'] ) ) {
			$output .= '<a href="' . esc_url( $atts['btn_url'] ) . '" class="op-pricing-btn">' . esc_html( $atts['btn_text'] ) . '</a>';
		}

		$output .= '</div>';

		return $output;
	}

	public function inline_css() {
		if ( ! Opulentia_get_option( 'pricing-enable', true ) ) {
			return;
		}

		$css = '
        .op-pricing-grid {
            display: grid;
            grid-template-columns: repeat(var(--op-columns, 3), 1fr);
            gap: 24px;
            margin: 30px 0;
        }
        .op-pricing-card {
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-radius: 12px;
            padding: 32px 24px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .op-pricing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .op-pricing-card--featured {
            border-color: var(--color-gold, #c9a96e);
            transform: scale(1.03);
            box-shadow: 0 0 20px rgba(201, 169, 110, 0.15);
        }
        .op-pricing-card--featured:hover {
            transform: scale(1.03) translateY(-4px);
        }
        .op-pricing-name {
            font-family: var(--font-heading, "Playfair Display");
            font-size: 1.4rem;
            color: var(--color-text, #f5f5f5);
            margin-bottom: 8px;
        }
        .op-pricing-price-wrap {
            margin: 16px 0;
        }
        .op-pricing-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-gold, #c9a96e);
        }
        .op-pricing-period {
            font-size: 0.9rem;
            color: var(--color-text-muted, #999);
        }
        .op-pricing-desc {
            color: var(--color-text-muted, #999);
            font-size: 0.85rem;
            margin-bottom: 16px;
        }
        .op-pricing-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .op-pricing-features li {
            padding: 8px 0;
            border-bottom: 1px solid var(--color-border, #333);
            color: var(--color-text, #f5f5f5);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .op-pricing-features li:before {
            content: "";
            display: inline-block;
            width: 14px;
            height: 14px;
            background: currentColor;
            -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\'%3E%3Cpolyline points=\'20 6 9 17 4 12\'%3E%3C/polyline%3E%3C/svg%3E") center/contain no-repeat;
            mask: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\'%3E%3Cpolyline points=\'20 6 9 17 4 12\'%3E%3C/polyline%3E%3C/svg%3E") center/contain no-repeat;
            color: var(--color-gold, #c9a96e);
            flex-shrink: 0;
        }
        .op-pricing-btn {
            display: inline-block;
            padding: 12px 32px;
            background: var(--color-accent, #b8860b);
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-family: var(--font-body, Inter);
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }
        .op-pricing-btn:hover {
            background: var(--color-accent-hover, #d4a843);
        }
        .op-pricing-card--featured .op-pricing-btn {
            background: var(--color-gold, #c9a96e);
        }
        .op-pricing-grid--minimal .op-pricing-card {
            background: transparent;
            border: none;
            border-radius: 0;
        }
        @media (max-width: 768px) {
            .op-pricing-grid { grid-template-columns: 1fr; }
            .op-pricing-card--featured { transform: none; }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

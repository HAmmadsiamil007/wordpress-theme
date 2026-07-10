<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Image_Hotspots {

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
		add_shortcode( 'op_hotspots', array( $this, 'render_hotspots' ) );
		add_shortcode( 'op_hotspot', array( $this, 'render_hotspot' ) );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_hotspots',
			array(
				'title'    => __( 'Image Hotspots', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 210,
			)
		);

		$wp_customize->add_setting(
			'hotspots_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'hotspots_enable',
			array(
				'label'   => __( 'Enable Image Hotspots', 'opulentia' ),
				'section' => 'opulentia_hotspots',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'hotspots_pin_size',
			array(
				'default'           => 'medium',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'hotspots_pin_size',
			array(
				'label'   => __( 'Default Pin Size', 'opulentia' ),
				'section' => 'opulentia_hotspots',
				'type'    => 'select',
				'choices' => array(
					'small'  => __( 'Small', 'opulentia' ),
					'medium' => __( 'Medium', 'opulentia' ),
					'large'  => __( 'Large', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'hotspots_pin_color',
			array(
				'default'           => '#c9a96e',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'hotspots_pin_color',
				array(
					'label'   => __( 'Pin Color', 'opulentia' ),
					'section' => 'opulentia_hotspots',
				)
			)
		);
	}

	private function is_enabled() {
		return (bool) get_theme_mod( 'hotspots_enable', true );
	}

	private function get_pin_size() {
		return get_theme_mod( 'hotspots_pin_size', 'medium' );
	}

	private function get_pin_color() {
		return get_theme_mod( 'hotspots_pin_color', '#c9a96e' );
	}

	public function render_hotspots( $atts, $content = '' ) {
		if ( ! $this->is_enabled() ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'image'    => '',
				'width'    => '',
				'height'   => '',
				'pin_size' => $this->get_pin_size(),
			),
			$atts,
			'op_hotspots'
		);

		if ( empty( $atts['image'] ) ) {
			return '';
		}

		$pin_size   = in_array( $atts['pin_size'], array( 'small', 'medium', 'large' ), true ) ? $atts['pin_size'] : $this->get_pin_size();
		$size_class = 'medium' !== $pin_size ? ' op-hotspot--' . $pin_size : '';

		$id = 'op-hotspots-' . uniqid();

		$output = '<div id="' . esc_attr( $id ) . '" class="op-hotspots">';

		$img_attrs = 'src="' . esc_url( $atts['image'] ) . '" alt=""';
		if ( ! empty( $atts['width'] ) ) {
			$img_attrs .= ' width="' . intval( $atts['width'] ) . '"';
		}
		if ( ! empty( $atts['height'] ) ) {
			$img_attrs .= ' height="' . intval( $atts['height'] ) . '"';
		}
		$img_attrs .= ' loading="lazy"';

		$output .= '<img ' . $img_attrs . '>';

		$output .= '<div class="op-hotspots__pins">';

		if ( ! empty( $content ) ) {
			$content = do_shortcode( $content );
			$content = str_replace( 'class="op-hotspot"', 'class="op-hotspot' . esc_attr( $size_class ) . '"', $content );
			$output .= $content;
		}

		$output .= '</div></div>';

		$output .= $this->inline_js( $id );

		return $output;
	}

	public function render_hotspot( $atts ) {
		$atts = shortcode_atts(
			array(
				'x'           => '50',
				'y'           => '50',
				'title'       => '',
				'description' => '',
				'link'        => '',
			),
			$atts,
			'op_hotspot'
		);

		$x = floatval( $atts['x'] );
		$y = floatval( $atts['y'] );
		$x = max( 0, min( 100, $x ) );
		$y = max( 0, min( 100, $y ) );

		$pin = '<span class="op-hotspot-pin">+</span>';

		$popup = '';
		if ( ! empty( $atts['title'] ) || ! empty( $atts['description'] ) ) {
			$popup .= '<div class="op-hotspot-popup">';
			if ( ! empty( $atts['title'] ) ) {
				$popup .= '<div class="op-hotspot-popup-title">' . esc_html( $atts['title'] ) . '</div>';
			}
			if ( ! empty( $atts['description'] ) ) {
				$popup .= '<div class="op-hotspot-popup-desc">' . esc_html( $atts['description'] ) . '</div>';
			}
			$popup .= '</div>';
		}

		$has_link  = ! empty( $atts['link'] );
		$tag       = $has_link ? 'a' : 'span';
		$link_attr = $has_link ? ' href="' . esc_url( $atts['link'] ) . '" target="_blank" rel="noopener noreferrer"' : '';
		$style     = 'left:' . $x . '%;top:' . $y . '%;';

		$output  = '<' . $tag . ' class="op-hotspot" style="' . $style . '"' . $link_attr . '>';
		$output .= $pin;
		$output .= $popup;
		$output .= '</' . $tag . '>';

		return $output;
	}

	private function inline_js( $id ) {
		return '<script>
        (function() {
            var container = document.getElementById("' . $id . '");
            if (!container) return;
            var hotspots = container.querySelectorAll(".op-hotspot");
            hotspots.forEach(function(hotspot) {
                var pin = hotspot.querySelector(".op-hotspot-pin");
                var popup = hotspot.querySelector(".op-hotspot-popup");
                if (!popup) return;
                pin.addEventListener("click", function(e) {
                    e.stopPropagation();
                    container.querySelectorAll(".op-hotspot-popup.open").forEach(function(p) {
                        if (p !== popup) p.classList.remove("open");
                    });
                    popup.classList.toggle("open");
                });
            });
            document.addEventListener("click", function(e) {
                if (!container.contains(e.target)) {
                    container.querySelectorAll(".op-hotspot-popup.open").forEach(function(p) {
                        p.classList.remove("open");
                    });
                }
            });
        })();
        </script>';
	}

	public function inline_css() {
		$pin_color = $this->get_pin_color();

		$pin_color_rgb = $this->hex_to_rgb( $pin_color );
		$shadow_rgba   = $pin_color_rgb ? 'rgba(' . $pin_color_rgb . ',0.4)' : 'rgba(201, 169, 110, 0.4)';

		$css = '
        .op-hotspots {
            position: relative;
            display: inline-block;
            max-width: 100%;
            overflow: visible;
        }
        .op-hotspots img {
            display: block;
            width: 100%;
            height: auto;
        }
        .op-hotspots__pins {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .op-hotspot {
            position: absolute;
            transform: translate(-50%, -50%);
            z-index: 10;
            pointer-events: auto;
            text-decoration: none;
            display: inline-block;
        }
        .op-hotspot-pin {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: ' . $pin_color . ';
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            line-height: 1;
        }
        .op-hotspot-pin:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 16px ' . $shadow_rgba . ';
        }
        .op-hotspot-popup {
            position: absolute;
            left: 50%;
            bottom: calc(100% + 12px);
            transform: translateX(-50%);
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-radius: 8px;
            padding: 12px 16px;
            min-width: 180px;
            max-width: 280px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
            pointer-events: none;
            z-index: 20;
            text-align: left;
        }
        .op-hotspot-popup.open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        .op-hotspot-popup-title {
            font-family: var(--font-heading, \'Playfair Display\');
            font-size: 0.95rem;
            color: var(--color-gold, #c9a96e);
            margin-bottom: 4px;
        }
        .op-hotspot-popup-desc {
            font-family: var(--font-body, Inter);
            font-size: 0.8rem;
            color: var(--color-text, #f5f5f5);
            line-height: 1.5;
        }
        .op-hotspot-popup::after {
            content: \'\';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 8px solid transparent;
            border-top-color: var(--color-border, #333);
        }
        .op-hotspot--small .op-hotspot-pin {
            width: 24px;
            height: 24px;
            font-size: 14px;
        }
        .op-hotspot--small .op-hotspot-popup {
            min-width: 140px;
            max-width: 220px;
            padding: 8px 12px;
        }
        .op-hotspot--large .op-hotspot-pin {
            width: 40px;
            height: 40px;
            font-size: 22px;
        }
        .op-hotspot--large .op-hotspot-popup {
            min-width: 220px;
            max-width: 340px;
            padding: 16px 20px;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}

	private function hex_to_rgb( $hex ) {
		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( strlen( $hex ) !== 6 ) {
			return '';
		}
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		return $r . ',' . $g . ',' . $b;
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Countdown_Timer {

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
        add_shortcode( 'op_countdown', array( $this, 'render_shortcode' ) );
        add_action( 'woocommerce_single_product_summary', array( $this, 'render_wc_countdown' ), 35 );
        add_action( 'init', array( $this, 'register_block' ) );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_countdown', array(
            'title'    => __( 'Countdown Timer', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 201,
        ) );

        $wp_customize->add_setting( 'countdown-default-style', array(
            'default'           => 'dark',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'countdown-default-style', array(
            'label'   => __( 'Default Style', 'opulentia' ),
            'section' => 'opulentia_countdown',
            'type'    => 'select',
            'choices' => array(
                'dark'   => __( 'Dark', 'opulentia' ),
                'light'  => __( 'Light', 'opulentia' ),
                'inline' => __( 'Inline', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'countdown-default-size', array(
            'default'           => 'medium',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'countdown-default-size', array(
            'label'   => __( 'Default Size', 'opulentia' ),
            'section' => 'opulentia_countdown',
            'type'    => 'select',
            'choices' => array(
                'small'  => __( 'Small', 'opulentia' ),
                'medium' => __( 'Medium', 'opulentia' ),
                'large'  => __( 'Large', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'countdown-wc-sale', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'countdown-wc-sale', array(
            'label'   => __( 'Show Countdown on Sale Products', 'opulentia' ),
            'section' => 'opulentia_countdown',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'countdown-expiry-text', array(
            'default'           => 'Sale ended',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'countdown-expiry-text', array(
            'label'   => __( 'Expiry Text', 'opulentia' ),
            'section' => 'opulentia_countdown',
            'type'    => 'text',
        ) );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'date'        => '',
            'label'       => '',
            'expiry_text' => Opulentia_get_option( 'countdown-expiry-text', 'Expired' ),
            'style'       => Opulentia_get_option( 'countdown-default-style', 'dark' ),
            'size'        => Opulentia_get_option( 'countdown-default-size', 'medium' ),
        ), $atts, 'op_countdown' );

        if ( empty( $atts['date'] ) ) {
            return '';
        }

        $timestamp = strtotime( $atts['date'] );
        if ( ! $timestamp ) {
            return '';
        }

        $js_date = date( 'Y/m/d H:i:s', $timestamp );
        $id = 'op-countdown-' . uniqid();

        $label = ! empty( $atts['label'] ) ? '<div class="op-countdown__label">' . esc_html( $atts['label'] ) . '</div>' : '';

        $output = '<div id="' . esc_attr( $id ) . '" class="op-countdown op-countdown--' . esc_attr( $atts['style'] ) . ' op-countdown--' . esc_attr( $atts['size'] ) . '" data-date="' . esc_attr( $js_date ) . '" data-expiry="' . esc_attr( $atts['expiry_text'] ) . '">';
        $output .= $label;
        $output .= '<div class="op-countdown__inner">';
        $output .= '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="days">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Days', 'opulentia' ) . '</span></div>';
        $output .= '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="hours">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Hours', 'opulentia' ) . '</span></div>';
        $output .= '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="minutes">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Minutes', 'opulentia' ) . '</span></div>';
        $output .= '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="seconds">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Seconds', 'opulentia' ) . '</span></div>';
        $output .= '</div></div>';

        $output .= '<script>
        (function() {
            var el = document.getElementById("' . $id . '");
            if (!el) return;
            var target = new Date(el.getAttribute("data-date")).getTime();
            var expiryText = el.getAttribute("data-expiry") || "Expired";
            function update() {
                var now = new Date().getTime();
                var diff = target - now;
                if (diff <= 0) {
                    el.innerHTML = "<div class=\"op-countdown__expired\">" + expiryText + "</div>";
                    return;
                }
                var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((diff % (1000 * 60)) / 1000);
                var nums = el.querySelectorAll(".op-countdown__num");
                if (nums.length >= 4) {
                    nums[0].textContent = String(days).padStart(2, "0");
                    nums[1].textContent = String(hours).padStart(2, "0");
                    nums[2].textContent = String(minutes).padStart(2, "0");
                    nums[3].textContent = String(seconds).padStart(2, "0");
                }
            }
            update();
            setInterval(update, 1000);
        })();
        </script>';

        return $output;
    }

    public function render_wc_countdown() {
        if ( ! Opulentia_get_option( 'countdown-wc-sale', true ) ) {
            return;
        }

        global $product;
        if ( ! $product || ! $product->is_on_sale() ) {
            return;
        }

        if ( method_exists( $product, 'get_date_on_sale_to' ) ) {
            $sale_end = $product->get_date_on_sale_to();
            if ( ! $sale_end ) {
                return;
            }
            $sale_end_ts = $sale_end->getTimestamp();
            if ( $sale_end_ts <= time() ) {
                return;
            }
            $date_str = date( 'Y/m/d H:i:s', $sale_end_ts );
            $id = 'op-countdown-wc-' . $product->get_id();
            $expiry_text = Opulentia_get_option( 'countdown-expiry-text', 'Sale ended' );

            echo '<div id="' . esc_attr( $id ) . '" class="op-countdown op-countdown--wc op-countdown--dark op-countdown--small" data-date="' . esc_attr( $date_str ) . '" data-expiry="' . esc_attr( $expiry_text ) . '">';
            echo '<div class="op-countdown__label">' . esc_html__( 'Sale ends in:', 'opulentia' ) . '</div>';
            echo '<div class="op-countdown__inner">';
            echo '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="days">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Days', 'opulentia' ) . '</span></div>';
            echo '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="hours">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Hours', 'opulentia' ) . '</span></div>';
            echo '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="minutes">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Minutes', 'opulentia' ) . '</span></div>';
            echo '<div class="op-countdown__unit"><span class="op-countdown__num" data-unit="seconds">00</span><span class="op-countdown__unit-label">' . esc_html__( 'Seconds', 'opulentia' ) . '</span></div>';
            echo '</div></div>';

            echo '<script>
            (function() {
                var el = document.getElementById("' . $id . '");
                if (!el) return;
                var target = new Date(el.getAttribute("data-date")).getTime();
                var expiryText = el.getAttribute("data-expiry") || "Sale ended";
                function update() {
                    var now = new Date().getTime();
                    var diff = target - now;
                    if (diff <= 0) {
                        el.innerHTML = "<div class=\"op-countdown__expired\">" + expiryText + "</div>";
                        return;
                    }
                    var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    var nums = el.querySelectorAll(".op-countdown__num");
                    if (nums.length >= 4) {
                        nums[0].textContent = String(days).padStart(2, "0");
                        nums[1].textContent = String(hours).padStart(2, "0");
                        nums[2].textContent = String(minutes).padStart(2, "0");
                        nums[3].textContent = String(seconds).padStart(2, "0");
                    }
                }
                update();
                setInterval(update, 1000);
            })();
            </script>';
        }
    }

    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        wp_register_script(
            'opulentia-countdown-block',
            '',
            array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-editor' ),
            '1.0.0',
            true
        );

        register_block_type( 'opulentia/countdown', array(
            'render_callback' => array( $this, 'render_shortcode' ),
            'attributes'      => array(
                'date'        => array( 'type' => 'string', 'default' => '' ),
                'label'       => array( 'type' => 'string', 'default' => '' ),
                'expiry_text' => array( 'type' => 'string', 'default' => 'Expired' ),
                'style'       => array( 'type' => 'string', 'default' => 'dark' ),
                'size'        => array( 'type' => 'string', 'default' => 'medium' ),
            ),
        ) );
    }

    public function inline_css() {
        $css = '
        .op-countdown {
            margin: 20px 0;
            text-align: center;
        }
        .op-countdown__label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .op-countdown__inner {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .op-countdown__unit {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 60px;
        }
        .op-countdown__num {
            font-weight: 700;
            line-height: 1.2;
            color: #c9a96e;
        }
        .op-countdown__unit-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-muted, #999);
            margin-top: 4px;
        }
        .op-countdown__expired {
            font-weight: 600;
            color: var(--color-accent, #b8860b);
            padding: 10px 0;
        }
        .op-countdown--small .op-countdown__num {
            font-size: 14px;
        }
        .op-countdown--medium .op-countdown__num {
            font-size: 24px;
        }
        .op-countdown--large .op-countdown__num {
            font-size: 36px;
        }
        .op-countdown--dark .op-countdown__unit {
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-radius: 8px;
            padding: 12px 16px;
            min-width: 70px;
        }
        .op-countdown--light .op-countdown__unit {
            background: var(--color-light-gray, #f5f5f5);
            border: 1px solid var(--color-border, #ddd);
            border-radius: 8px;
            padding: 12px 16px;
            min-width: 70px;
        }
        .op-countdown--light .op-countdown__num {
            color: var(--color-accent, #b8860b);
        }
        .op-countdown--inline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .op-countdown--inline .op-countdown__inner {
            gap: 8px;
        }
        .op-countdown--inline .op-countdown__unit {
            flex-direction: row;
            background: none;
            border: none;
            padding: 0;
            min-width: auto;
            gap: 4px;
        }
        .op-countdown--inline .op-countdown__unit-label {
            margin-top: 0;
        }
        .op-countdown--wc {
            margin: 10px 0 20px;
            padding: 15px;
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-radius: 8px;
        }
        .op-countdown--wc .op-countdown__label {
            color: #c9a96e;
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

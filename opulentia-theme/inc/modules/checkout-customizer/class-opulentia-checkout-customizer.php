<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Checkout_Customizer {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        add_action( 'customize_register', array( $this, 'register_customizer' ), 55 );
        add_filter( 'woocommerce_checkout_fields', array( $this, 'modify_checkout_fields' ) );
        add_action( 'wp', array( $this, 'distraction_free_checkout' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'checkout_styles' ), 120 );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_panel( 'opulentia_checkout', array(
            'title'       => __( 'Checkout', 'opulentia' ),
            'priority'    => 55,
            'capability'  => 'edit_theme_options',
        ) );

        $wp_customize->add_section( 'opulentia_checkout_layout', array(
            'title'       => __( 'Layout', 'opulentia' ),
            'panel'       => 'opulentia_checkout',
        ) );

        $wp_customize->add_section( 'opulentia_checkout_fields', array(
            'title'       => __( 'Field Visibility', 'opulentia' ),
            'panel'       => 'opulentia_checkout',
        ) );

        $this->add_layout_controls( $wp_customize );
        $this->add_field_controls( $wp_customize );
    }

    private function add_layout_controls( $wp_customize ) {
        $wp_customize->add_setting( 'checkout_layout', array(
            'default'           => 'two-column',
            'sanitize_callback' => array( $this, 'sanitize_checkout_layout' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_layout', array(
            'label'       => __( 'Checkout Layout', 'opulentia' ),
            'section'     => 'opulentia_checkout_layout',
            'type'        => 'select',
            'choices'     => array(
                'two-column' => __( 'Two Column', 'opulentia' ),
                'one-column' => __( 'One Column', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'checkout_distraction_free', array(
            'default'           => false,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_distraction_free', array(
            'label'       => __( 'Distraction-Free Checkout', 'opulentia' ),
            'description' => __( 'Hide header and footer on the checkout page.', 'opulentia' ),
            'section'     => 'opulentia_checkout_layout',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'checkout_show_order_review_first', array(
            'default'           => false,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_show_order_review_first', array(
            'label'       => __( 'Show Order Review First', 'opulentia' ),
            'description' => __( 'Display the order review panel on the left side.', 'opulentia' ),
            'section'     => 'opulentia_checkout_layout',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'checkout_label_style', array(
            'default'           => 'floating',
            'sanitize_callback' => array( $this, 'sanitize_label_style' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_label_style', array(
            'label'       => __( 'Label Style', 'opulentia' ),
            'section'     => 'opulentia_checkout_layout',
            'type'        => 'select',
            'choices'     => array(
                'default'  => __( 'Default', 'opulentia' ),
                'floating' => __( 'Floating', 'opulentia' ),
            ),
        ) );
    }

    private function add_field_controls( $wp_customize ) {
        $wp_customize->add_setting( 'checkout_hide_company', array(
            'default'           => false,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_hide_company', array(
            'label'       => __( 'Hide Company Field', 'opulentia' ),
            'description' => __( 'Remove the company field from billing and shipping.', 'opulentia' ),
            'section'     => 'opulentia_checkout_fields',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'checkout_hide_address_2', array(
            'default'           => false,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_hide_address_2', array(
            'label'       => __( 'Hide Address Line 2', 'opulentia' ),
            'description' => __( 'Remove the apartment/unit field from billing and shipping.', 'opulentia' ),
            'section'     => 'opulentia_checkout_fields',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'checkout_hide_order_notes', array(
            'default'           => false,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_hide_order_notes', array(
            'label'       => __( 'Hide Order Notes', 'opulentia' ),
            'description' => __( 'Remove the order notes textarea.', 'opulentia' ),
            'section'     => 'opulentia_checkout_fields',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'checkout_make_phone_optional', array(
            'default'           => false,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_make_phone_optional', array(
            'label'       => __( 'Make Phone Optional', 'opulentia' ),
            'section'     => 'opulentia_checkout_fields',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'checkout_make_company_optional', array(
            'default'           => true,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'checkout_make_company_optional', array(
            'label'       => __( 'Make Company Optional', 'opulentia' ),
            'section'     => 'opulentia_checkout_fields',
            'type'        => 'checkbox',
        ) );
    }

    public function sanitize_checkout_layout( $value ) {
        $valid = array( 'two-column', 'one-column' );
        if ( ! in_array( $value, $valid, true ) ) {
            return 'two-column';
        }
        return $value;
    }

    public function sanitize_label_style( $value ) {
        $valid = array( 'default', 'floating' );
        if ( ! in_array( $value, $valid, true ) ) {
            return 'floating';
        }
        return $value;
    }

    public function sanitize_checkbox( $value ) {
        return wp_validate_boolean( $value );
    }

    public function modify_checkout_fields( $fields ) {
        if ( Opulentia_get_option( 'checkout_hide_company', false ) ) {
            unset( $fields['billing']['billing_company'] );
            unset( $fields['shipping']['shipping_company'] );
        }

        if ( Opulentia_get_option( 'checkout_hide_address_2', false ) ) {
            unset( $fields['billing']['billing_address_2'] );
            unset( $fields['shipping']['shipping_address_2'] );
        }

        if ( Opulentia_get_option( 'checkout_hide_order_notes', false ) ) {
            unset( $fields['order']['order_comments'] );
        }

        if ( Opulentia_get_option( 'checkout_make_phone_optional', false ) ) {
            $fields['billing']['billing_phone']['required'] = false;
        }

        if ( Opulentia_get_option( 'checkout_make_company_optional', true ) ) {
            if ( isset( $fields['billing']['billing_company'] ) ) {
                $fields['billing']['billing_company']['required'] = false;
            }
            if ( isset( $fields['shipping']['shipping_company'] ) ) {
                $fields['shipping']['shipping_company']['required'] = false;
            }
        }

        return $fields;
    }

    public function distraction_free_checkout() {
        if ( ! is_checkout() || ! Opulentia_get_option( 'checkout_distraction_free', false ) ) {
            return;
        }

        remove_action( 'opulentia_header', 'opulentia_render_header' );
        remove_action( 'opulentia_footer', 'opulentia_render_footer' );

        add_filter( 'body_class', array( $this, 'distraction_free_body_class' ) );
    }

    public function distraction_free_body_class( $classes ) {
        $classes[] = 'op-distraction-free';
        return $classes;
    }

    public function checkout_styles() {
        if ( ! is_checkout() ) {
            return;
        }

        $layout     = Opulentia_get_option( 'checkout_layout', 'two-column' );
        $label_style = Opulentia_get_option( 'checkout_label_style', 'floating' );

        $css = '';

        if ( 'one-column' === $layout ) {
            $css .= '
            .woocommerce-checkout .col2-set {
                width: 100%;
                float: none;
            }
            .woocommerce-checkout .col2-set .col-1,
            .woocommerce-checkout .col2-set .col-2 {
                float: none;
                width: 100%;
            }
            .woocommerce-checkout .woocommerce-checkout-review-order {
                width: 100%;
                float: none;
                margin-top: 30px;
            }
            ';
        }

        if ( 'floating' === $label_style ) {
            $css .= '
            .op-checkout--floating .woocommerce-input-wrapper {
                position: relative;
            }
            .op-checkout--floating .form-row label {
                position: absolute;
                top: 12px;
                left: 14px;
                font-size: 0.85rem;
                color: var(--color-text-muted, #999);
                transition: all 0.2s ease;
                pointer-events: none;
                z-index: 1;
            }
            .op-checkout--floating .form-row .input-text:focus + label,
            .op-checkout--floating .form-row .input-text:not(:placeholder-shown) + label {
                top: -8px;
                left: 10px;
                font-size: 0.7rem;
                background: var(--color-secondary-dark, #111);
                padding: 0 4px;
            }
            ';
        }

        $css .= '
        .op-distraction-free #masthead,
        .op-distraction-free .site-footer {
            display: none;
        }
        .woocommerce-checkout .col2-set .col-1,
        .woocommerce-checkout .col2-set .col-2 {
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-radius: 8px;
            padding: 24px;
        }
        .woocommerce-checkout .woocommerce-checkout-review-order {
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-radius: 8px;
            padding: 24px;
        }
        #add_payment_method #payment,
        .woocommerce-cart #payment,
        .woocommerce-checkout #payment {
            background: transparent;
        }
        .woocommerce-checkout #payment div.payment_box {
            background: var(--color-primary-dark, #1a1a1a);
        }
        ';

        wp_add_inline_style( 'opulentia-woocommerce-style', $css );
    }
}

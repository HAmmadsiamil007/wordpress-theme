<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_WooCommerce_Catalog {

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
        add_action( 'wp', array( $this, 'init' ) );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_wc_catalog', array(
            'title'    => __( 'Catalog Mode', 'opulentia' ),
            'panel'    => 'Opulentia_woocommerce',
            'priority' => 25,
        ) );

        $wp_customize->add_setting( 'wc-catalog-enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-catalog-enable', array(
            'label'   => __( 'Enable Catalog Mode', 'opulentia' ),
            'section' => 'opulentia_wc_catalog',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'wc-catalog-hide-price', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-catalog-hide-price', array(
            'label'   => __( 'Hide Prices', 'opulentia' ),
            'section' => 'opulentia_wc_catalog',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'wc-catalog-hide-cart', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-catalog-hide-cart', array(
            'label'   => __( 'Hide Add to Cart Buttons', 'opulentia' ),
            'section' => 'opulentia_wc_catalog',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'wc-catalog-inquiry-btn', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-catalog-inquiry-btn', array(
            'label'   => __( 'Show Inquiry / Contact Button', 'opulentia' ),
            'section' => 'opulentia_wc_catalog',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'wc-catalog-inquiry-text', array(
            'default'           => __( 'Request a Quote', 'opulentia' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-catalog-inquiry-text', array(
            'label'   => __( 'Inquiry Button Text', 'opulentia' ),
            'section' => 'opulentia_wc_catalog',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'wc-catalog-inquiry-url', array(
            'default'           => home_url( '/contact/' ),
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-catalog-inquiry-url', array(
            'label'   => __( 'Inquiry Button URL', 'opulentia' ),
            'section' => 'opulentia_wc_catalog',
            'type'    => 'url',
        ) );

        $wp_customize->add_setting( 'wc-catalog-redirect', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-catalog-redirect', array(
            'label'       => __( 'Add to Cart Redirect URL', 'opulentia' ),
            'description' => __( 'Redirect cart/checkout pages to this URL. Leave empty to disable.', 'opulentia' ),
            'section'     => 'opulentia_wc_catalog',
            'type'        => 'url',
        ) );
    }

    public function init() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        if ( ! Opulentia_get_option( 'wc-catalog-enable', false ) ) {
            return;
        }

        if ( Opulentia_get_option( 'wc-catalog-hide-price', true ) ) {
            remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
            add_filter( 'woocommerce_get_price_html', array( $this, 'hide_price' ), 999, 2 );
            add_filter( 'woocommerce_variable_price_html', array( $this, 'hide_price' ), 999, 2 );
        }

        if ( Opulentia_get_option( 'wc-catalog-hide-cart', true ) ) {
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
            remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
            remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
            remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
            add_filter( 'woocommerce_is_purchasable', '__return_false' );
        }

        if ( Opulentia_get_option( 'wc-catalog-inquiry-btn', true ) && Opulentia_get_option( 'wc-catalog-hide-cart', true ) ) {
            add_action( 'woocommerce_after_shop_loop_item', array( $this, 'inquiry_button' ), 15 );
            add_action( 'woocommerce_single_product_summary', array( $this, 'inquiry_button' ), 35 );
        }

        $redirect = Opulentia_get_option( 'wc-catalog-redirect', '' );
        if ( ! empty( $redirect ) ) {
            add_action( 'template_redirect', array( $this, 'redirect_checkout' ) );
        }
    }

    public function hide_price( $price, $product ) {
        return '';
    }

    public function inquiry_button() {
        $text = Opulentia_get_option( 'wc-catalog-inquiry-text', __( 'Request a Quote', 'opulentia' ) );
        $url  = Opulentia_get_option( 'wc-catalog-inquiry-url', home_url( '/contact/' ) );
        echo '<a href="' . esc_url( $url ) . '" class="op-catalog-inquiry-btn btn btn--primary">' . esc_html( $text ) . '</a>';
    }

    public function redirect_checkout() {
        $redirect = Opulentia_get_option( 'wc-catalog-redirect', '' );
        if ( empty( $redirect ) ) {
            return;
        }
        if ( is_cart() || is_checkout() ) {
            wp_redirect( esc_url( $redirect ) );
            exit;
        }
    }

    public function inline_css() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        if ( ! Opulentia_get_option( 'wc-catalog-enable', false ) ) {
            return;
        }

        $css = '
        .op-catalog-inquiry-btn {
            display: inline-block;
            margin-top: 12px;
        }
        .woocommerce ul.products li.product .op-catalog-inquiry-btn {
            margin-top: 8px;
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_RTL {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_rtl_css' ), 120 );
        add_filter( 'body_class', array( $this, 'body_class' ) );
        add_filter( 'language_attributes', array( $this, 'rtl_dir_attribute' ) );
        add_filter( 'stylesheet_uri', array( $this, 'rtl_stylesheet' ), 10, 2 );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_rtl', array(
            'title'    => __( 'RTL Language Support', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 95,
        ) );

        $wp_customize->add_setting( 'rtl-force', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'rtl-force', array(
            'label'       => __( 'Force RTL Mode', 'opulentia' ),
            'description' => __( 'Enable RTL even when site language is LTR. Useful for testing.', 'opulentia' ),
            'section'     => 'opulentia_rtl',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'rtl-font-family', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'rtl-font-family', array(
            'label'       => __( 'RTL Font Family', 'opulentia' ),
            'description' => __( 'Font family for RTL text (e.g., "Noto Naskh Arabic", "Vazirmatn"). Leave empty to use default.', 'opulentia' ),
            'section'     => 'opulentia_rtl',
            'type'        => 'text',
        ) );

        $wp_customize->add_setting( 'rtl-font-size-base', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'rtl-font-size-base', array(
            'label'       => __( 'RTL Base Font Size', 'opulentia' ),
            'description' => __( 'Base font size for RTL text (e.g., "16px", "0.95rem").', 'opulentia' ),
            'section'     => 'opulentia_rtl',
            'type'        => 'text',
        ) );

        $wp_customize->add_setting( 'rtl-line-height', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'rtl-line-height', array(
            'label'       => __( 'RTL Line Height', 'opulentia' ),
            'description' => __( 'Line height for RTL text (e.g., "1.8").', 'opulentia' ),
            'section'     => 'opulentia_rtl',
            'type'        => 'text',
        ) );
    }

    public function body_class( $classes ) {
        if ( $this->is_rtl() ) {
            $classes[] = 'opulentia-rtl';
        }
        return $classes;
    }

    public function rtl_dir_attribute( $output ) {
        if ( $this->is_rtl() ) {
            $output = str_replace( 'dir="ltr"', 'dir="rtl"', $output );
        }
        return $output;
    }

    public function rtl_stylesheet( $stylesheet_uri, $stylesheet_dir_uri ) {
        return $stylesheet_uri;
    }

    private function is_rtl() {
        if ( Opulentia_get_option( 'rtl-force', false ) ) {
            return true;
        }
        if ( is_rtl() ) {
            return true;
        }
        return false;
    }

    public function inline_rtl_css() {
        if ( ! $this->is_rtl() ) {
            return;
        }

        $font_family = Opulentia_get_option( 'rtl-font-family', '' );
        $font_size   = Opulentia_get_option( 'rtl-font-size-base', '' );
        $line_height = Opulentia_get_option( 'rtl-line-height', '' );

        $css = '
        body.opulentia-rtl {
            direction: rtl;
            unicode-bidi: embed;
        }
        body.opulentia-rtl .container,
        body.opulentia-rtl .header-row__inner,
        body.opulentia-rtl .footer-widget-grid {
            direction: rtl;
        }
        body.opulentia-rtl .main-navigation ul {
            text-align: right;
        }
        body.opulentia-rtl .main-navigation ul ul {
            left: auto;
            right: 0;
        }
        body.opulentia-rtl .main-navigation ul ul ul {
            right: 100%;
            left: auto;
        }
        body.opulentia-rtl .header-col--left {
            order: 1;
        }
        body.opulentia-rtl .header-col--center {
            order: 0;
        }
        body.opulentia-rtl .header-col--right {
            order: -1;
        }
        body.opulentia-rtl .header-actions__btn {
            margin-left: 0;
            margin-right: 12px;
        }
        body.opulentia-rtl .header-cart-wrapper .mini-cart-dropdown {
            left: auto;
            right: 0;
        }
        body.opulentia-rtl .site-logo__tagline {
            text-align: right;
        }
        body.opulentia-rtl .text-align-left {
            text-align: right;
        }
        body.opulentia-rtl .text-align-right {
            text-align: left;
        }
        body.opulentia-rtl .post-meta span {
            margin-right: 0;
            margin-left: 16px;
        }
        body.opulentia-rtl .sidebar .widget ul {
            padding-right: 0;
            padding-left: 0;
        }
        body.opulentia-rtl .sidebar .widget li {
            padding-left: 0;
            padding-right: 4px;
        }
        body.opulentia-rtl .pagination .page-numbers {
            margin: 0 0 0 4px;
        }
        body.opulentia-rtl .btn svg,
        body.opulentia-rtl .header-actions__btn svg {
            margin-right: 0;
            margin-left: 6px;
        }
        body.opulentia-rtl .search-form .search-submit {
            right: auto;
            left: 0;
        }
        body.opulentia-rtl .search-form input[type="search"] {
            padding-right: 16px;
            padding-left: 50px;
        }
        body.opulentia-rtl .comment-list .children {
            margin-left: 0;
            margin-right: 40px;
        }
        body.opulentia-rtl .wp-caption-text {
            text-align: right;
        }
        body.opulentia-rtl blockquote {
            border-left: none;
            border-right: 3px solid var(--color-gold);
            padding-left: 0;
            padding-right: 20px;
        }
        body.opulentia-rtl ul, body.opulentia-rtl ol {
            padding-left: 0;
            padding-right: 20px;
        }
        body.opulentia-rtl .menu-item-has-children > a::after {
            margin-left: 0;
            margin-right: 8px;
            float: left;
        }
        body.opulentia-rtl .header-standard-grid {
            direction: rtl;
        }
        body.opulentia-rtl .header-minimal-grid {
            direction: rtl;
        }
        body.opulentia-rtl .footer-bottom {
            flex-direction: row-reverse;
        }
        body.opulentia-rtl .social-icons a {
            margin-right: 0;
            margin-left: 10px;
        }
        body.opulentia-rtl .breadcrumb span + span::before {
            content: "\\\\";
            margin: 0 8px;
        }
        body.opulentia-rtl input,
        body.opulentia-rtl textarea,
        body.opulentia-rtl select {
            direction: rtl;
        }
        body.opulentia-rtl table th,
        body.opulentia-rtl table td {
            text-align: right;
        }
        body.opulentia-rtl .woocommerce .col2-set .col-1,
        body.opulentia-rtl .woocommerce .col2-set .col-2 {
            float: right;
        }
        body.opulentia-rtl .woocommerce ul.products li.product {
            margin: 0 0 2.992em 2.992em;
        }
        body.opulentia-rtl .woocommerce ul.products li.last {
            margin-left: 0;
        }
        body.opulentia-rtl .woocommerce .quantity .qty {
            margin-right: 0;
            margin-left: 8px;
        }
        body.opulentia-rtl .woocommerce-cart .wc-proceed-to-checkout {
            text-align: left;
        }
        body.opulentia-rtl .woocommerce-info,
        body.opulentia-rtl .woocommerce-message {
            padding: 12px 50px 12px 20px;
        }
        body.opulentia-rtl .woocommerce-info::before,
        body.opulentia-rtl .woocommerce-message::before {
            left: auto;
            right: 15px;
        }
        ';

        if ( ! empty( $font_family ) ) {
            $css .= '
            body.opulentia-rtl,
            body.opulentia-rtl p,
            body.opulentia-rtl .entry-content,
            body.opulentia-rtl .widget {
                font-family: ' . $font_family . ', sans-serif;
            }
            ';
        }

        if ( ! empty( $font_size ) ) {
            $css .= '
            body.opulentia-rtl {
                font-size: ' . $font_size . ';
            }
            ';
        }

        if ( ! empty( $line_height ) ) {
            $css .= '
            body.opulentia-rtl p,
            body.opulentia-rtl .entry-content {
                line-height: ' . $line_height . ';
            }
            ';
        }

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

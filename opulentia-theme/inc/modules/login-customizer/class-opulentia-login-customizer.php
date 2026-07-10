<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Login_Customizer {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'login_head', array( $this, 'login_styles' ) );
        add_filter( 'login_headerurl', array( $this, 'login_logo_url' ) );
        add_filter( 'login_headertext', array( $this, 'login_logo_title' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'preview_styles' ), 120 );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_login', array(
            'title'    => __( 'Login Page', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 120,
        ) );

        $wp_customize->add_setting( 'login-logo', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'login-logo', array(
            'label'       => __( 'Login Logo', 'opulentia' ),
            'description' => __( 'Appears above the login form.', 'opulentia' ),
            'section'     => 'opulentia_login',
        ) ) );

        $wp_customize->add_setting( 'login-logo-width', array(
            'default'           => 180,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-logo-width', array(
            'label'       => __( 'Logo Width (px)', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 50, 'max' => 400, 'step' => 10 ),
        ) );

        $wp_customize->add_setting( 'login-bg-color', array(
            'default'           => 'var(--color-primary-dark)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-bg-color', array(
            'label'       => __( 'Background Color', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-primary-dark)' ),
        ) );

        $wp_customize->add_setting( 'login-bg-image', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'login-bg-image', array(
            'label'   => __( 'Background Image', 'opulentia' ),
            'section' => 'opulentia_login',
        ) ) );

        $wp_customize->add_setting( 'login-form-bg', array(
            'default'           => 'var(--color-secondary-dark)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-form-bg', array(
            'label'       => __( 'Form Background Color', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-secondary-dark)' ),
        ) );

        $wp_customize->add_setting( 'login-form-text', array(
            'default'           => 'var(--color-text)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-form-text', array(
            'label'       => __( 'Form Text Color', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-text)' ),
        ) );

        $wp_customize->add_setting( 'login-btn-bg', array(
            'default'           => 'var(--color-gold)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-btn-bg', array(
            'label'       => __( 'Button Background Color', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
        ) );

        $wp_customize->add_setting( 'login-btn-text', array(
            'default'           => '#000000',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-btn-text', array(
            'label'       => __( 'Button Text Color', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => '#000000' ),
        ) );

        $wp_customize->add_setting( 'login-btn-hover', array(
            'default'           => 'var(--color-gold-hover)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-btn-hover', array(
            'label'       => __( 'Button Hover Color', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-gold-hover)' ),
        ) );

        $wp_customize->add_setting( 'login-custom-css', array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'login-custom-css', array(
            'label'       => __( 'Custom CSS', 'opulentia' ),
            'description' => __( 'Additional CSS for the login page.', 'opulentia' ),
            'section'     => 'opulentia_login',
            'type'        => 'textarea',
        ) );
    }

    public function login_styles() {
        $bg_color    = Opulentia_get_option( 'login-bg-color', 'var(--color-primary-dark)' );
        $bg_image    = Opulentia_get_option( 'login-bg-image', '' );
        $form_bg     = Opulentia_get_option( 'login-form-bg', 'var(--color-secondary-dark)' );
        $form_text   = Opulentia_get_option( 'login-form-text', 'var(--color-text)' );
        $btn_bg      = Opulentia_get_option( 'login-btn-bg', 'var(--color-gold)' );
        $btn_text    = Opulentia_get_option( 'login-btn-text', '#000000' );
        $btn_hover   = Opulentia_get_option( 'login-btn-hover', '#d4a843' );
        $logo        = Opulentia_get_option( 'login-logo', '' );
        $logo_width  = Opulentia_get_option( 'login-logo-width', 180 );
        $custom_css  = Opulentia_get_option( 'login-custom-css', '' );

        $logo_css = '';
        if ( ! empty( $logo ) ) {
            $logo_css = '
            #login h1 a, .login h1 a {
                background-image: url(' . $logo . ');
                background-size: contain;
                background-position: center;
                background-repeat: no-repeat;
                width: ' . $logo_width . 'px;
                height: ' . round( $logo_width * 0.4 ) . 'px;
            }
            ';
        }

        $bg_css = 'background-color: ' . $bg_color . ';';
        if ( ! empty( $bg_image ) ) {
            $bg_css .= ' background-image: url(' . $bg_image . '); background-size: cover; background-position: center;';
        }

        echo '<style type="text/css">
        body.login {' . $bg_css . '}
        body.login #loginform {
            background: ' . $form_bg . ';
            border: 1px solid var(--color-border);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        body.login #loginform label {
            color: ' . $form_text . ';
        }
        body.login #loginform input[type="text"],
        body.login #loginform input[type="password"] {
            background: rgba(0,0,0,0.2);
            border: 1px solid var(--color-border);
            color: ' . $form_text . ';
            border-radius: 6px;
        }
        body.login #loginform input:focus {
            border-color: ' . $btn_bg . ';
            box-shadow: 0 0 0 1px ' . $btn_bg . ';
        }
        body.login #loginform .button-primary,
        body.login #loginform .wp-submit {
            background: ' . $btn_bg . ' !important;
            border-color: ' . $btn_bg . ' !important;
            color: ' . $btn_text . ' !important;
            text-shadow: none !important;
            box-shadow: none !important;
            border-radius: 6px;
            font-weight: 600;
            transition: opacity 0.2s ease;
        }
        body.login #loginform .button-primary:hover,
        body.login #loginform .wp-submit:hover {
            background: ' . $btn_hover . ' !important;
            border-color: ' . $btn_hover . ' !important;
            opacity: 0.9;
        }
        body.login #loginform .button-primary:active,
        body.login #loginform .wp-submit:active {
            background: ' . $btn_hover . ' !important;
        }
        body.login .message,
        body.login #login_error {
            border-left-color: ' . $btn_bg . ';
        }
        body.login a {
            color: ' . $form_text . ';
        }
        body.login a:hover {
            color: ' . $btn_bg . ';
        }
        body.login .privacy-policy-page-link a {
            color: ' . $form_text . ';
        }
        ' . $logo_css . $custom_css . '
        </style>';
    }

    public function login_logo_url() {
        return home_url();
    }

    public function login_logo_title() {
        return get_bloginfo( 'name' );
    }

    public function preview_styles() {
        if ( ! is_customize_preview() ) {
            return;
        }
        $btn_bg   = Opulentia_get_option( 'login-btn-bg', 'var(--color-gold)' );
        $btn_text = Opulentia_get_option( 'login-btn-text', '#000000' );
        $form_bg  = Opulentia_get_option( 'login-form-bg', 'var(--color-secondary-dark)' );

        $css = '
        .login-preview-notice {
            background: ' . $form_bg . ';
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin: 40px auto;
            max-width: 400px;
        }
        .login-preview-notice .button-primary {
            background: ' . $btn_bg . ' !important;
            color: ' . $btn_text . ' !important;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        ';
        wp_add_inline_style( 'opulentia-style', $css );
    }
}

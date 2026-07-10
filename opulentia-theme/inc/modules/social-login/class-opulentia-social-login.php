<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Social_Login {

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
        add_action( 'login_form', array( $this, 'render_login_buttons' ) );
        add_action( 'register_form', array( $this, 'render_register_buttons' ) );
        add_action( 'comment_form_after_fields', array( $this, 'render_comment_buttons' ) );
        add_action( 'init', array( $this, 'handle_oauth_callback' ) );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_social_login', array(
            'title'    => __( 'Social Login', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 202,
        ) );

        $wp_customize->add_setting( 'social-login-enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-enable', array(
            'label'   => __( 'Enable Social Login', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'social-login-google-enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-google-enable', array(
            'label'   => __( 'Enable Google Login', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'social-login-google-client-id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-google-client-id', array(
            'label'   => __( 'Google Client ID', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'social-login-google-client-secret', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-google-client-secret', array(
            'label'   => __( 'Google Client Secret', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'social-login-facebook-enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-facebook-enable', array(
            'label'   => __( 'Enable Facebook Login', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'social-login-facebook-app-id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-facebook-app-id', array(
            'label'   => __( 'Facebook App ID', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'social-login-button-style', array(
            'default'           => 'default',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-button-style', array(
            'label'   => __( 'Button Style', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'select',
            'choices' => array(
                'default' => __( 'Default', 'opulentia' ),
                'rounded' => __( 'Rounded', 'opulentia' ),
                'outline' => __( 'Outline', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'social-login-show-on-login', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-show-on-login', array(
            'label'   => __( 'Show on Login Page', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'social-login-show-on-register', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-show-on-register', array(
            'label'   => __( 'Show on Registration Page', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'social-login-show-on-comments', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'social-login-show-on-comments', array(
            'label'   => __( 'Show on Comments', 'opulentia' ),
            'section' => 'opulentia_social_login',
            'type'    => 'checkbox',
        ) );
    }

    public function is_enabled() {
        return (bool) Opulentia_get_option( 'social-login-enable', false );
    }

    public function render_login_buttons() {
        if ( ! $this->is_enabled() || ! Opulentia_get_option( 'social-login-show-on-login', true ) ) {
            return;
        }
        echo $this->build_buttons();
    }

    public function render_register_buttons() {
        if ( ! $this->is_enabled() || ! Opulentia_get_option( 'social-login-show-on-register', true ) ) {
            return;
        }
        echo $this->build_buttons();
    }

    public function render_comment_buttons() {
        if ( ! $this->is_enabled() || ! Opulentia_get_option( 'social-login-show-on-comments', false ) ) {
            return;
        }
        if ( is_user_logged_in() ) {
            return;
        }
        echo '<p class="op-social-login-comment-note">' . esc_html__( 'Login with:', 'opulentia' ) . '</p>';
        echo $this->build_buttons();
    }

    private function build_buttons() {
        $style = Opulentia_get_option( 'social-login-button-style', 'default' );
        $html = '<div class="op-social-login op-social-login--' . esc_attr( $style ) . '">';
        $html .= '<div class="op-social-login__buttons">';

        if ( Opulentia_get_option( 'social-login-google-enable', false ) ) {
            $client_id = Opulentia_get_option( 'social-login-google-client-id', '' );
            if ( ! empty( $client_id ) ) {
                $redirect_uri = $this->get_redirect_uri();
                $state = wp_create_nonce( 'op_social_login_google' );
                $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=' . urlencode( $client_id ) . '&redirect_uri=' . urlencode( $redirect_uri ) . '&scope=' . urlencode( 'email profile' ) . '&state=' . urlencode( $state ) . '&access_type=offline';

                $html .= '<a href="' . esc_url( $auth_url ) . '" class="op-social-login__btn op-social-login__btn--google">';
                $html .= $this->get_svg( 'google' );
                $html .= '<span>' . esc_html__( 'Login with Google', 'opulentia' ) . '</span>';
                $html .= '</a>';
            }
        }

        if ( Opulentia_get_option( 'social-login-facebook-enable', false ) ) {
            $app_id = Opulentia_get_option( 'social-login-facebook-app-id', '' );
            if ( ! empty( $app_id ) ) {
                $redirect_uri = $this->get_redirect_uri();
                $state = wp_create_nonce( 'op_social_login_facebook' );
                $auth_url = 'https://www.facebook.com/v19.0/dialog/oauth?client_id=' . urlencode( $app_id ) . '&redirect_uri=' . urlencode( $redirect_uri ) . '&scope=' . urlencode( 'email public_profile' ) . '&state=' . urlencode( $state );

                $html .= '<a href="' . esc_url( $auth_url ) . '" class="op-social-login__btn op-social-login__btn--facebook">';
                $html .= $this->get_svg( 'facebook' );
                $html .= '<span>' . esc_html__( 'Login with Facebook', 'opulentia' ) . '</span>';
                $html .= '</a>';
            }
        }

        $html .= '</div></div>';
        return $html;
    }

    private function get_redirect_uri() {
        return home_url( '/?op_social_login=callback' );
    }

    public function handle_oauth_callback() {
        if ( ! isset( $_GET['op_social_login'] ) ) {
            return;
        }

        $action = sanitize_text_field( wp_unslash( $_GET['op_social_login'] ) );

        if ( 'callback' === $action ) {
            $this->handle_google_callback();
        }
    }

    private function handle_google_callback() {
        if ( ! isset( $_GET['code'] ) || ! isset( $_GET['state'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['state'] ) ), 'op_social_login_google' ) ) {
            wp_die( esc_html__( 'Invalid state parameter.', 'opulentia' ) );
        }

        $client_id = Opulentia_get_option( 'social-login-google-client-id', '' );
        $client_secret = Opulentia_get_option( 'social-login-google-client-secret', '' );

        if ( empty( $client_id ) || empty( $client_secret ) ) {
            return;
        }

        $code = sanitize_text_field( wp_unslash( $_GET['code'] ) );
        $redirect_uri = $this->get_redirect_uri();

        $token_response = wp_remote_post( 'https://oauth2.googleapis.com/token', array(
            'body' => array(
                'code'          => $code,
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
            ),
        ) );

        if ( is_wp_error( $token_response ) ) {
            return;
        }

        $token_body = json_decode( wp_remote_retrieve_body( $token_response ), true );

        if ( ! isset( $token_body['access_token'] ) ) {
            return;
        }

        $user_response = wp_remote_get( 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode( $token_body['access_token'] ) );

        if ( is_wp_error( $user_response ) ) {
            return;
        }

        $user_data = json_decode( wp_remote_retrieve_body( $user_response ), true );

        if ( ! isset( $user_data['email'] ) ) {
            return;
        }

        $email = sanitize_email( $user_data['email'] );
        $name  = isset( $user_data['name'] ) ? sanitize_text_field( $user_data['name'] ) : $email;

        $user = get_user_by( 'email', $email );

        if ( $user ) {
            wp_set_auth_cookie( $user->ID );
            wp_safe_redirect( home_url() );
            exit;
        }

        $username = $this->generate_username( $email, $name );
        $user_id = wp_insert_user( array(
            'user_login' => $username,
            'user_email' => $email,
            'display_name' => $name,
            'user_pass'  => wp_generate_password(),
        ) );

        if ( is_wp_error( $user_id ) ) {
            return;
        }

        wp_set_auth_cookie( $user_id );
        wp_safe_redirect( home_url() );
        exit;
    }

    private function generate_username( $email, $name ) {
        $base = sanitize_user( str_replace( ' ', '_', strtolower( $name ) ), true );
        if ( empty( $base ) ) {
            $parts = explode( '@', $email );
            $base = sanitize_user( $parts[0], true );
        }
        if ( username_exists( $base ) ) {
            $base .= '_' . wp_rand( 1000, 9999 );
        }
        return $base;
    }

    private function get_svg( $provider ) {
        $svgs = array(
            'google' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="#fff" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#fff" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#fff" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#fff" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>',
            'facebook' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="#fff" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        );
        return isset( $svgs[ $provider ] ) ? $svgs[ $provider ] : '';
    }

    public function inline_css() {
        $button_style = Opulentia_get_option( 'social-login-button-style', 'default' );

        $border_radius = 'default' === $button_style ? '6px' : ( 'rounded' === $button_style ? '50px' : '6px' );
        $outline = 'outline' === $button_style;

        $css = '
        .op-social-login {
            margin: 15px 0;
        }
        .op-social-login__buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .op-social-login__btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border-radius: ' . $border_radius . ';
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            min-height: 44px;
            border: none;
            color: #fff;
        }
        .op-social-login__btn:hover {
            opacity: 0.85;
            color: #fff;
        }
        .op-social-login__btn--google {
            background: #4285F4;
        }
        .op-social-login__btn--facebook {
            background: #1877F2;
        }
        ';

        if ( $outline ) {
            $css .= '
            .op-social-login__btn--google {
                background: transparent;
                border: 2px solid #4285F4;
                color: #4285F4;
            }
            .op-social-login__btn--google:hover {
                background: #4285F4;
                color: #fff;
            }
            .op-social-login__btn--facebook {
                background: transparent;
                border: 2px solid #1877F2;
                color: #1877F2;
            }
            .op-social-login__btn--facebook:hover {
                background: #1877F2;
                color: #fff;
            }
            ';
        }

        $css .= '
        .op-social-login__btn svg {
            flex-shrink: 0;
        }
        .op-social-login-comment-note {
            margin: 10px 0 5px;
            font-size: 14px;
            font-weight: 600;
        }
        @media (max-width: 576px) {
            .op-social-login__buttons {
                flex-direction: column;
            }
            .op-social-login__btn {
                justify-content: center;
            }
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

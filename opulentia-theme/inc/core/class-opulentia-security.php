<?php
/**
 * Security Hardening Engine — Singleton
 *
 * Centralized security module for Opulentia theme.
 * Handles:
 * 1. Security headers (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy)
 * 2. CSRF token generation/verification for theme forms
 * 3. Login hardening (XML-RPC, version hiding, error obfuscation)
 * 4. Admin hardening (file editing, user enumeration)
 * 5. Input/output sanitization helpers
 * 6. Capability checking helpers
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Security class.
 */
class Opulentia_Security {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Nonce action prefix for theme forms.
     */
    const NONCE_ACTION = 'Opulentia_form_action';

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
        // Security headers — send early with high priority.
        add_action( 'send_headers', array( $this, 'send_security_headers' ), 1 );

        // Login hardening.
        add_filter( 'login_errors', array( $this, 'obfuscate_login_errors' ) );
        add_filter( 'wp_headers', array( $this, 'remove_x_pingback' ) );
        add_action( 'init', array( $this, 'disable_xmlrpc' ) );

        // Admin hardening.
        add_action( 'admin_init', array( $this, 'maybe_disable_file_edit' ) );

        // Remove WordPress version from public-facing output.
        add_filter( 'the_generator', '__return_empty_string' );

        // Disable user enumeration via REST API.
        add_filter( 'rest_prepare_user', array( $this, 'restrict_user_data' ), 10, 3 );

        // Add nonce to search form.
        add_filter( 'get_search_form', array( $this, 'add_nonce_to_search_form' ) );
    }

    // -------------------------------------------------------------------------
    // 1. Security Headers
    // -------------------------------------------------------------------------

    /**
     * Send security headers to protect against common web vulnerabilities.
     *
     * Hooked to 'send_headers' at priority 1 for early dispatch.
     */
    public function send_security_headers() {
        if ( is_admin() || is_customize_preview() ) {
            return;
        }

        // Prevent MIME-type sniffing.
        header( 'X-Content-Type-Options: nosniff' );

        // Prevent clickjacking.
        header( 'X-Frame-Options: SAMEORIGIN' );

        // Enable XSS filter in older browsers.
        header( 'X-XSS-Protection: 1; mode=block' );

        // Referrer policy — never send full URL in Referer header.
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );

        // Permissions policy — disable access to features that we don't use.
        header( "Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()" );

        // Content-Security-Policy — mitigate XSS and data injection attacks.
        // Policy is crafted to allow the theme's known external resources:
        //   - Google Fonts (fonts.googleapis.com, fonts.gstatic.com)
        //   - GSAP (cdnjs.cloudflare.com)
        //   - Unsplash demo images (images.unsplash.com)
        //   - YouTube/Vimeo embeds (www.youtube.com, player.vimeo.com)
        //   - Inline styles (dynamic CSS) and inline scripts (template scripts)
        header( "Content-Security-Policy: " . $this->build_csp() );
    }

    /**
     * Build the Content-Security-Policy header value.
     *
     * Consolidates all resource directives into a single policy string
     * that reflects the known external dependencies of the theme.
     *
     * @return string CSP policy string.
     */
    private function build_csp() {
        $directives = array(
            // Default: only same-origin resources.
            "default-src 'self'",

            // Scripts: same-origin + GSAP CDN. 'unsafe-inline' for template scripts.
            // 'unsafe-eval' for GSAP ScrollTrigger which uses dynamic code evaluation.
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com",

            // Styles: same-origin + Google Fonts CSS. 'unsafe-inline' for dynamic CSS engine.
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",

            // Fonts: same-origin + Google Fonts files.
            "font-src 'self' https://fonts.gstatic.com data:",

            // Images: same-origin + Unsplash demo products + data URIs.
            "img-src 'self' data: https://images.unsplash.com https://*.wp.com https://*.wordpress.org",

            // AJAX / API connections: only same-origin.
            "connect-src 'self'",

            // iframe embeds: same-origin + YouTube + Vimeo.
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com",

            // Forms: only submit to same-origin.
            "form-action 'self'",

            // Object/embed: block all.
            "object-src 'none'",

            // Base URI: restrict to same-origin.
            "base-uri 'self'",

            // Workers / frames: same-origin only.
            "worker-src 'self'",

            // Manifest: same-origin only.
            "manifest-src 'self'",
        );

        return implode( '; ', $directives );
    }

    // -------------------------------------------------------------------------
    // 2. CSRF Token Helpers
    // -------------------------------------------------------------------------

    /**
     * Generate a nonce field for a theme form.
     *
     * @param string $action Optional. A custom action name. Default 'Opulentia_form_action'.
     * @param string $name   Optional. Nonce field name. Default 'Opulentia_nonce'.
     * @param bool   $echo   Optional. Whether to echo the output. Default true.
     * @return string|void Nonce field HTML if $echo is false.
     */
    public static function nonce_field( $action = '', $name = 'Opulentia_nonce', $echo = true ) {
        if ( empty( $action ) ) {
            $action = self::NONCE_ACTION;
        }

        return wp_nonce_field( $action, $name, true, $echo );
    }

    /**
     * Verify a nonce from a theme form submission.
     *
     * @param string $action Optional. The action name used when generating the nonce.
     * @param string $name   Optional. The nonce field name. Default 'Opulentia_nonce'.
     * @return bool Whether the nonce is valid.
     */
    public static function verify_nonce( $action = '', $name = 'Opulentia_nonce' ) {
        if ( empty( $action ) ) {
            $action = self::NONCE_ACTION;
        }

        if ( ! isset( $_POST[ $name ] ) ) {
            return false;
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST[ $name ] ) );

        return (bool) wp_verify_nonce( $nonce, $action );
    }

    // -------------------------------------------------------------------------
    // 3. Login Hardening
    // -------------------------------------------------------------------------

    /**
     * Obfuscate login error messages to prevent user enumeration.
     *
     * @param string $errors The error message.
     * @return string Generic error message.
     */
    public function obfuscate_login_errors( $errors ) {
        // Return a generic message regardless of whether the username or password was wrong.
        return esc_html__( 'Invalid username, email address, or password.', 'opulentia' );
    }

    /**
     * Remove X-Pingback header to reduce attack surface.
     *
     * @param array $headers The HTTP headers.
     * @return array Modified headers.
     */
    public function remove_x_pingback( $headers ) {
        if ( isset( $headers['X-Pingback'] ) ) {
            unset( $headers['X-Pingback'] );
        }
        return $headers;
    }

    /**
     * Disable XML-RPC to prevent brute-force and DoS attacks.
     */
    public function disable_xmlrpc() {
        // Disable XML-RPC entirely.
        add_filter( 'xmlrpc_enabled', '__return_false' );

        // Remove the RSD link from the head.
        remove_action( 'wp_head', 'rsd_link' );
    }

    // -------------------------------------------------------------------------
    // 4. Admin Hardening
    // -------------------------------------------------------------------------

    /**
     * Disable file editing in the WordPress admin (theme/plugin editor).
     *
     * Respects the DISALLOW_FILE_EDIT constant if already defined by the user.
     */
    public function maybe_disable_file_edit() {
        if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
            define( 'DISALLOW_FILE_EDIT', true );
        }
    }

    /**
     * Restrict user data in REST API to prevent user enumeration.
     *
     * @param WP_REST_Response $response The response object.
     * @param WP_User          $user     User object.
     * @param WP_REST_Request  $request  Request object.
     * @return WP_REST_Response Modified response.
     */
    public function restrict_user_data( $response, $user, $request ) {
        // If the current user cannot edit users, only return minimal data.
        if ( ! current_user_can( 'edit_users' ) ) {
            $data = $response->get_data();
            // Only keep the ID if the request is not for the current user.
            if ( get_current_user_id() !== $user->ID ) {
                $response->set_data( array(
                    'id' => $user->ID,
                ) );
            }
        }

        return $response;
    }

    // -------------------------------------------------------------------------
    // 5. Sanitization Helpers
    // -------------------------------------------------------------------------

    /**
     * Sanitize a text input from POST/GET data.
     *
     * @param string $key     The key in the $_POST or $_GET array.
     * @param string $default Optional default value if key is not set.
     * @param string $source  Optional. 'post' or 'get'. Default 'post'.
     * @return string Sanitized value.
     */
    public static function sanitize_input( $key, $default = '', $source = 'post' ) {
        $data = ( 'get' === strtolower( $source ) ) ? $_GET : $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( ! isset( $data[ $key ] ) || ! is_scalar( $data[ $key ] ) ) {
            return $default;
        }

        return sanitize_text_field( wp_unslash( $data[ $key ] ) );
    }

    /**
     * Sanitize an email input from POST/GET data.
     *
     * @param string $key     The key in the $_POST or $_GET array.
     * @param string $default Optional default value.
     * @param string $source  Optional. 'post' or 'get'. Default 'post'.
     * @return string Sanitized email or empty string.
     */
    public static function sanitize_email_input( $key, $default = '', $source = 'post' ) {
        $value = self::sanitize_input( $key, $default, $source );

        if ( empty( $value ) ) {
            return $default;
        }

        return sanitize_email( $value );
    }

    /**
     * Sanitize a URL input from POST/GET data.
     *
     * @param string $key     The key in the $_POST or $_GET array.
     * @param string $default Optional default value.
     * @param string $source  Optional. 'post' or 'get'. Default 'post'.
     * @return string Sanitized URL or empty string.
     */
    public static function sanitize_url_input( $key, $default = '', $source = 'post' ) {
        $value = self::sanitize_input( $key, $default, $source );

        if ( empty( $value ) ) {
            return $default;
        }

        return esc_url_raw( $value );
    }

    /**
     * Sanitize a textarea input from POST/GET data.
     *
     * @param string $key     The key in the $_POST or $_GET array.
     * @param string $default Optional default value.
     * @param string $source  Optional. 'post' or 'get'. Default 'post'.
     * @return string Sanitized textarea content.
     */
    public static function sanitize_textarea_input( $key, $default = '', $source = 'post' ) {
        $data = ( 'get' === strtolower( $source ) ) ? $_GET : $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( ! isset( $data[ $key ] ) || ! is_scalar( $data[ $key ] ) ) {
            return $default;
        }

        return sanitize_textarea_field( wp_unslash( $data[ $key ] ) );
    }

    // -------------------------------------------------------------------------
    // 6. Capability Checking Helpers
    // -------------------------------------------------------------------------

    /**
     * Check if the current user has a specific capability.
     *
     * @param string $capability The capability to check.
     * @param int    $post_id    Optional post ID for context.
     * @return bool Whether the user has the capability.
     */
    public static function user_can( $capability, $post_id = null ) {
        return current_user_can( $capability, $post_id );
    }

    /**
     * Check if AJAX request has a valid nonce.
     *
     * Wrapper around check_ajax_referer with consistent error handling.
     *
     * @param string $action The nonce action name.
     * @param string $query_arg Optional. The nonce field name. Default 'nonce'.
     * @param bool   $die Optional. Whether to die on failure. Default false.
     * @return bool Whether the nonce is valid.
     */
    public static function verify_ajax_nonce( $action, $query_arg = 'nonce', $die = false ) {
        $result = check_ajax_referer( $action, $query_arg, $die );

        if ( false === $result ) {
            return false;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // 7. Search Form Nonce
    // -------------------------------------------------------------------------

    /**
     * Add a hidden nonce field to the search form.
     *
     * @param string $form The search form HTML.
     * @return string Modified search form HTML.
     */
    public function add_nonce_to_search_form( $form ) {
        if ( empty( $form ) ) {
            return $form;
        }

        // Add a hidden nonce field before the closing </form> tag.
        $nonce_field = wp_nonce_field( 'Opulentia_search_nonce', 'Opulentia_search_nonce', true, false );

        $form = str_replace( '</form>', $nonce_field . '</form>', $form );

        return $form;
    }

    // -------------------------------------------------------------------------
    // 8. Current User Sanitization
    // -------------------------------------------------------------------------

    /**
     * Get the current user ID safely.
     *
     * @return int User ID or 0 if not logged in.
     */
    public static function get_current_user_id() {
        return get_current_user_id();
    }

    /**
     * Check if a user is logged in.
     *
     * @return bool Whether a user is logged in.
     */
    public static function is_user_logged_in() {
        return is_user_logged_in();
    }
}

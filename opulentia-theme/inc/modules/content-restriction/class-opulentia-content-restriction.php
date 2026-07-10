<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Content_Restriction {

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
        add_shortcode( 'op_restrict', array( $this, 'shortcode_restrict' ) );
        add_shortcode( 'op_restrict_login', array( $this, 'shortcode_restrict_login' ) );
        add_shortcode( 'op_restrict_level', array( $this, 'shortcode_restrict_level' ) );
        add_filter( 'the_content', array( $this, 'filter_content' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
        add_action( 'template_redirect', array( $this, 'maybe_redirect' ) );
    }

    public function shortcode_restrict( $atts, $content = '' ) {
        $atts = shortcode_atts( array(
            'roles'   => '',
            'message' => '',
        ), $atts );

        if ( is_user_logged_in() && ! empty( $atts['roles'] ) ) {
            $user = wp_get_current_user();
            $allowed_roles = array_map( 'trim', explode( ',', $atts['roles'] ) );
            foreach ( $allowed_roles as $role ) {
                if ( in_array( $role, (array) $user->roles, true ) ) {
                    return do_shortcode( $content );
                }
            }
        }

        return $this->get_restriction_notice( $atts['message'] );
    }

    public function shortcode_restrict_login( $atts, $content = '' ) {
        if ( is_user_logged_in() ) {
            return do_shortcode( $content );
        }
        return $this->get_restriction_notice( '', true );
    }

    public function shortcode_restrict_level( $atts, $content = '' ) {
        $atts = shortcode_atts( array(
            'level'   => 1,
            'message' => '',
        ), $atts );
        return $this->get_restriction_notice( __( 'This content requires a membership level.', 'opulentia' ) );
    }

    public function filter_content( $content ) {
        if ( ! is_singular() ) {
            return $content;
        }
        $post_id = get_the_ID();
        $restricted = get_post_meta( $post_id, 'restrict_access', true );
        if ( ! $restricted ) {
            return $content;
        }
        $restrict_roles = get_post_meta( $post_id, 'restrict_roles', true );
        if ( ! is_array( $restrict_roles ) ) {
            $restrict_roles = array();
        }
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            foreach ( $restrict_roles as $role ) {
                if ( in_array( $role, (array) $user->roles, true ) ) {
                    return $content;
                }
            }
        }
        $message = Opulentia_get_option( 'restriction-global-message', 'This content is restricted to authorized users only.' );
        $show_excerpt = Opulentia_get_option( 'restriction-excerpt', true );
        $output = '';
        if ( $show_excerpt && has_excerpt( $post_id ) ) {
            $output .= '<div class="op-restrict-excerpt">' . wp_kses_post( get_the_excerpt( $post_id ) ) . '</div>';
        }
        $output .= $this->get_restriction_notice( $message, ! is_user_logged_in() );
        return $output;
    }

    private function get_restriction_notice( $custom_message = '', $show_login = false ) {
        $message = ! empty( $custom_message ) ? $custom_message : Opulentia_get_option( 'restriction-global-message', 'This content is restricted to authorized users only.' );
        $login_url = wp_login_url( get_permalink() );
        $html = '<div class="op-restrict-notice">';
        $html .= '<div class="op-restrict-notice__icon">';
        $html .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="36" height="36"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>';
        $html .= '</div>';
        $html .= '<p class="op-restrict-notice__message">' . esc_html( $message ) . '</p>';
        if ( $show_login && ! is_user_logged_in() ) {
            $html .= '<a href="' . esc_url( $login_url ) . '" class="op-restrict-notice__btn">' . esc_html__( 'Login to Access', 'opulentia' ) . '</a>';
        }
        $html .= '</div>';
        return $html;
    }

    public function add_meta_box() {
        $post_types = array( 'post', 'page' );
        foreach ( $post_types as $pt ) {
            add_meta_box(
                'opulentia_restrict_meta',
                __( 'Content Restriction', 'opulentia' ),
                array( $this, 'render_meta_box' ),
                $pt,
                'side',
                'default'
            );
        }
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'op_restrict_meta', 'op_restrict_meta_nonce' );
        $restricted = get_post_meta( $post->ID, 'restrict_access', true );
        $restrict_roles = get_post_meta( $post->ID, 'restrict_roles', true );
        if ( ! is_array( $restrict_roles ) ) {
            $restrict_roles = array();
        }
        $editable_roles = array_reverse( wp_roles()->get_names() );
        ?>
        <p>
            <label>
                <input type="checkbox" name="restrict_access" value="1" <?php checked( $restricted, true ); ?>>
                <?php esc_html_e( 'Restrict this content', 'opulentia' ); ?>
            </label>
        </p>
        <p><strong><?php esc_html_e( 'Allowed Roles:', 'opulentia' ); ?></strong></p>
        <div style="max-height:150px;overflow-y:auto;">
            <?php foreach ( $editable_roles as $role_key => $role_name ) : ?>
                <label style="display:block;margin-bottom:4px;font-size:0.85rem;">
                    <input type="checkbox" name="restrict_roles[]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $restrict_roles, true ) ); ?>>
                    <?php echo esc_html( $role_name ); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public function save_meta_box( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! isset( $_POST['op_restrict_meta_nonce'] ) || ! wp_verify_nonce( $_POST['op_restrict_meta_nonce'], 'op_restrict_meta' ) ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        $restricted = isset( $_POST['restrict_access'] ) ? true : false;
        update_post_meta( $post_id, 'restrict_access', $restricted );
        if ( $restricted && isset( $_POST['restrict_roles'] ) && is_array( $_POST['restrict_roles'] ) ) {
            $roles = array_map( 'sanitize_text_field', $_POST['restrict_roles'] );
            update_post_meta( $post_id, 'restrict_roles', $roles );
        } else {
            delete_post_meta( $post_id, 'restrict_roles' );
        }
    }

    public function maybe_redirect() {
        if ( ! is_singular() ) {
            return;
        }
        if ( is_user_logged_in() ) {
            return;
        }
        $post_id = get_the_ID();
        $restricted = get_post_meta( $post_id, 'restrict_access', true );
        if ( ! $restricted ) {
            return;
        }
        $redirect_url = Opulentia_get_option( 'restriction-redirect-url', '' );
        $login_redirect = Opulentia_get_option( 'restriction-login-redirect', true );
        if ( ! empty( $redirect_url ) ) {
            wp_redirect( esc_url( $redirect_url ) );
            exit;
        }
        if ( $login_redirect && ! is_user_logged_in() ) {
            wp_redirect( wp_login_url( get_permalink() ) );
            exit;
        }
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_content_restriction', array(
            'title'    => __( 'Content Restriction', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 110,
        ) );

        $wp_customize->add_setting( 'restriction-global-message', array(
            'default'           => 'This content is restricted to authorized users only.',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'restriction-global-message', array(
            'label'   => __( 'Global Restriction Message', 'opulentia' ),
            'section' => 'opulentia_content_restriction',
            'type'    => 'textarea',
        ) );

        $wp_customize->add_setting( 'restriction-redirect-url', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'restriction-redirect-url', array(
            'label'   => __( 'Redirect URL (empty = show message)', 'opulentia' ),
            'section' => 'opulentia_content_restriction',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'restriction-login-redirect', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'restriction-login-redirect', array(
            'label'   => __( 'Redirect to Login', 'opulentia' ),
            'section' => 'opulentia_content_restriction',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'restriction-excerpt', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'restriction-excerpt', array(
            'label'   => __( 'Show Excerpt Before Restriction', 'opulentia' ),
            'section' => 'opulentia_content_restriction',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'restriction-custom-css', array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'restriction-custom-css', array(
            'label'   => __( 'Custom CSS for Restriction Notice', 'opulentia' ),
            'section' => 'opulentia_content_restriction',
            'type'    => 'textarea',
            'input_attrs' => array( 'placeholder' => '.op-restrict-notice { ... }' ),
        ) );
    }

    public function inline_css() {
        $base_css = '
        .op-restrict-notice {
            max-width: 480px;
            margin: 40px auto;
            padding: 40px 32px;
            text-align: center;
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 8px;
        }
        .op-restrict-notice__icon {
            margin-bottom: 16px;
            color: var(--color-gold);
        }
        .op-restrict-notice__icon svg {
            display: inline-block;
        }
        .op-restrict-notice__message {
            font-size: 1rem;
            color: var(--color-text-muted);
            margin: 0 0 20px;
            line-height: 1.6;
        }
        .op-restrict-notice__btn {
            display: inline-block;
            padding: 12px 28px;
            background: var(--color-accent);
            color: #000;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.2s ease;
        }
        .op-restrict-notice__btn:hover {
            background: var(--color-accent-hover);
            color: #000;
        }
        .op-restrict-excerpt {
            margin-bottom: 24px;
            padding: 16px;
            border-left: 3px solid var(--color-gold);
            color: var(--color-text-muted);
            font-style: italic;
        }
        ';

        $custom_css = Opulentia_get_option( 'restriction-custom-css', '' );

        wp_add_inline_style( 'opulentia-style', $base_css . $custom_css );
    }
}

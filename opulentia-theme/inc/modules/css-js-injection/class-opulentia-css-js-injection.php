<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_CSS_JS_Injection {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
        add_action( 'wp_head', array( $this, 'output_head_css_js' ), 999 );
        add_action( 'wp_footer', array( $this, 'output_footer_js' ), 999 );
        add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
    }

    public function customize_register( $wp_customize ) {
        $wp_customize->add_panel( 'opulentia_injection', array(
            'title'       => __( 'CSS / JS Injection', 'opulentia' ),
            'description' => __( 'Add custom CSS and JavaScript globally or per-page.', 'opulentia' ),
            'priority'    => 145,
        ) );

        // ── Global CSS ──
        $wp_customize->add_section( 'op_injection_css', array(
            'title' => __( 'Global CSS', 'opulentia' ),
            'panel' => 'opulentia_injection',
        ) );

        $wp_customize->add_setting( 'op_injection_css', array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_injection_css', array(
            'label'       => __( 'Custom CSS', 'opulentia' ),
            'description' => __( 'Injected in <head> via <style>. No <style> tags needed.', 'opulentia' ),
            'section'     => 'op_injection_css',
            'type'        => 'textarea',
            'input_attrs' => array( 'rows' => 12 ),
        ) );

        // ── Global JS Head ──
        $wp_customize->add_section( 'op_injection_js_head', array(
            'title' => __( 'JavaScript — Head', 'opulentia' ),
            'panel' => 'opulentia_injection',
        ) );

        $wp_customize->add_setting( 'op_injection_js_head', array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_injection_js_head', array(
            'label'       => __( 'Custom JS (<head>)', 'opulentia' ),
            'description' => __( 'Injected just before </head>. No <script> tags needed.', 'opulentia' ),
            'section'     => 'op_injection_js_head',
            'type'        => 'textarea',
            'input_attrs' => array( 'rows' => 12 ),
        ) );

        // ── Global JS Footer ──
        $wp_customize->add_section( 'op_injection_js_footer', array(
            'title' => __( 'JavaScript — Footer', 'opulentia' ),
            'panel' => 'opulentia_injection',
        ) );

        $wp_customize->add_setting( 'op_injection_js_footer', array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_injection_js_footer', array(
            'label'       => __( 'Custom JS (Footer)', 'opulentia' ),
            'description' => __( 'Injected just before </body>. No <script> tags needed.', 'opulentia' ),
            'section'     => 'op_injection_js_footer',
            'type'        => 'textarea',
            'input_attrs' => array( 'rows' => 12 ),
        ) );

        // ─── Media Queries ──
        $wp_customize->add_section( 'op_injection_media', array(
            'title' => __( 'Media Queries', 'opulentia' ),
            'panel' => 'opulentia_injection',
        ) );

        $wp_customize->add_setting( 'op_injection_css_tablet', array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_injection_css_tablet', array(
            'label'       => __( 'Tablet CSS (768px–1024px)', 'opulentia' ),
            'section'     => 'op_injection_media',
            'type'        => 'textarea',
            'input_attrs' => array( 'rows' => 8 ),
        ) );

        $wp_customize->add_setting( 'op_injection_css_mobile', array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_injection_css_mobile', array(
            'label'       => __( 'Mobile CSS (<768px)', 'opulentia' ),
            'section'     => 'op_injection_media',
            'type'        => 'textarea',
            'input_attrs' => array( 'rows' => 8 ),
        ) );
    }

    // ── Per-Page Meta Box ──

    public function add_meta_box() {
        $post_types = get_post_types( array( 'public' => true ), 'names' );
        add_meta_box(
            'opulentia_css_js',
            __( 'Opulentia — Custom CSS / JS', 'opulentia' ),
            array( $this, 'render_meta_box' ),
            $post_types,
            'normal',
            'low'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'opulentia_css_js_save', 'opulentia_css_js_nonce' );
        $css = get_post_meta( $post->ID, '_op_custom_css', true );
        $js  = get_post_meta( $post->ID, '_op_custom_js', true );
        ?>
        <p>
            <label for="op-custom-css"><?php esc_html_e( 'Custom CSS', 'opulentia' ); ?></label>
            <textarea id="op-custom-css" name="op_custom_css" rows="6" style="width:100%;font-family:monospace"><?php echo esc_textarea( $css ); ?></textarea>
        </p>
        <p>
            <label for="op-custom-js"><?php esc_html_e( 'Custom JS (Footer)', 'opulentia' ); ?></label>
            <textarea id="op-custom-js" name="op_custom_js" rows="6" style="width:100%;font-family:monospace"><?php echo esc_textarea( $js ); ?></textarea>
        </p>
        <?php
    }

    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['opulentia_css_js_nonce'] ) || ! wp_verify_nonce( $_POST['opulentia_css_js_nonce'], 'opulentia_css_js_save' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['op_custom_css'] ) ) {
            update_post_meta( $post_id, '_op_custom_css', wp_strip_all_tags( $_POST['op_custom_css'] ) );
        }
        if ( isset( $_POST['op_custom_js'] ) ) {
            update_post_meta( $post_id, '_op_custom_js', wp_strip_all_tags( $_POST['op_custom_js'] ) );
        }
    }

    // ── Output ──

    public function output_head_css_js() {
        $css = get_theme_mod( 'op_injection_css', '' );
        $js  = get_theme_mod( 'op_injection_js_head', '' );
        $tablet = get_theme_mod( 'op_injection_css_tablet', '' );
        $mobile = get_theme_mod( 'op_injection_css_mobile', '' );

        if ( $css || $tablet || $mobile ) {
            echo "\n<!-- Opulentia Custom CSS -->\n<style>\n";
            if ( $css ) {
                echo $css . "\n";
            }
            if ( $tablet ) {
                echo "@media (min-width:768px) and (max-width:1024px){" . $tablet . "}\n";
            }
            if ( $mobile ) {
                echo "@media (max-width:767px){" . $mobile . "}\n";
            }
            echo "</style>\n<!-- /Opulentia Custom CSS -->\n";
        }

        if ( $js ) {
            echo "\n<!-- Opulentia Custom JS (Head) -->\n<script>\n" . $js . "\n</script>\n<!-- /Opulentia Custom JS (Head) -->\n";
        }

        // Per-page CSS
        if ( is_singular() ) {
            $post_id = get_queried_object_id();
            $per_css = get_post_meta( $post_id, '_op_custom_css', true );
            if ( $per_css ) {
                echo "\n<!-- Opulentia Per-Page CSS -->\n<style>\n" . $per_css . "\n</style>\n<!-- /Opulentia Per-Page CSS -->\n";
            }
        }
    }

    public function output_footer_js() {
        $global = get_theme_mod( 'op_injection_js_footer', '' );
        if ( $global ) {
            echo "\n<!-- Opulentia Custom JS (Footer) -->\n<script>\n" . $global . "\n</script>\n<!-- /Opulentia Custom JS (Footer) -->\n";
        }

        if ( is_singular() ) {
            $post_id = get_queried_object_id();
            $per_js = get_post_meta( $post_id, '_op_custom_js', true );
            if ( $per_js ) {
                echo "\n<!-- Opulentia Per-Page JS -->\n<script>\n" . $per_js . "\n</script>\n<!-- /Opulentia Per-Page JS -->\n";
            }
        }
    }

    public function dynamic_css( $css ) {
        return $css;
    }
}

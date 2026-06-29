<?php
/**
 * Config-Driven Customizer — Singleton
 *
 * Replaces the flat customizer.php with a class that registers
 * sections, settings, and controls from a configuration array.
 * Exposes a static get() helper so templates can retrieve
 * theme_mod values with their registered defaults.
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SoleOrigine_Customizer_Config class.
 */
class SoleOrigine_Customizer_Config {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Registered configuration array.
     *
     * @var array
     */
    private $config = array();

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
        $this->config = $this->get_config();
        add_action( 'customize_register', array( $this, 'register' ) );
        add_action( 'customize_preview_init', array( $this, 'enqueue_preview_js' ) );
    }

    // -------------------------------------------------------------------------
    // Config
    // -------------------------------------------------------------------------

    /**
     * Return the full configuration array.
     *
     * @return array {
     *     'sections' => array,
     *     'settings' => array,
     * }
     */
    public function get_config() {
        return array(
            'sections' => $this->get_sections(),
            'settings' => $this->get_settings(),
        );
    }

    /**
     * Section definitions.
     *
     * @return array
     */
    private function get_sections() {
        return array(
            'soleorigine_hero' => array(
                'title'    => __( 'Hero Section', 'soleorigine' ),
                'priority' => 30,
            ),
            'soleorigine_about' => array(
                'title'    => __( 'About Section', 'soleorigine' ),
                'priority' => 35,
            ),
            'soleorigine_collection' => array(
                'title'    => __( 'Collection Section', 'soleorigine' ),
                'priority' => 40,
            ),
            'soleorigine_footer' => array(
                'title'    => __( 'Footer Settings', 'soleorigine' ),
                'priority' => 45,
            ),
            'soleorigine_blog' => array(
                'title'    => __( 'Blog Settings', 'soleorigine' ),
                'priority' => 50,
            ),
        );
    }

    /**
     * Setting + control definitions.
     *
     * Each entry maps to one setting and one control.
     * Special keys:
     *   'control'    — WP_Customize_Control class name (e.g. 'WP_Customize_Image_Control').
     *   'type'       — Standard control type (text, textarea, url, number, etc.)
     *   'input_attrs' — Array of HTML attributes for the input.
     *   'description' — Optional description text.
     *
     * @return array
     */
    private function get_settings() {
        return array(

            // -----------------------------------------------------------------
            // Hero Section
            // -----------------------------------------------------------------
            'hero_title' => array(
                'default'           => __( 'SoleOrigine', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_hero',
                'label'             => __( 'Hero Title', 'soleorigine' ),
                'type'              => 'text',
            ),
            'hero_subtitle' => array(
                'default'           => __( 'Premium Italian Footwear', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_hero',
                'label'             => __( 'Hero Subtitle', 'soleorigine' ),
                'type'              => 'text',
            ),
            'hero_button_1_text' => array(
                'default'           => __( 'Explore Collection', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_hero',
                'label'             => __( 'Button 1 Text', 'soleorigine' ),
                'type'              => 'text',
            ),
            'hero_button_1_url' => array(
                'default'           => '/collection',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_hero',
                'label'             => __( 'Button 1 URL', 'soleorigine' ),
                'type'              => 'url',
            ),
            'hero_button_2_text' => array(
                'default'           => __( 'View Styles', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_hero',
                'label'             => __( 'Button 2 Text', 'soleorigine' ),
                'type'              => 'text',
            ),
            'hero_button_2_url' => array(
                'default'           => '/styles',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_hero',
                'label'             => __( 'Button 2 URL', 'soleorigine' ),
                'type'              => 'url',
            ),
            'hero_background' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_hero',
                'label'             => __( 'Hero Background Image', 'soleorigine' ),
                'control'           => 'WP_Customize_Image_Control',
            ),

            // -----------------------------------------------------------------
            // About Section
            // -----------------------------------------------------------------
            'about_title' => array(
                'default'           => __( 'Our Heritage', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_about',
                'label'             => __( 'About Title', 'soleorigine' ),
                'type'              => 'text',
            ),
            'about_subtitle' => array(
                'default'           => __( 'A Legacy of Excellence', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_about',
                'label'             => __( 'About Subtitle', 'soleorigine' ),
                'type'              => 'text',
            ),
            'about_text' => array(
                'default'           => __( 'Born from a passion for exceptional footwear, SoleOrigine represents the pinnacle of Italian craftsmanship. Each pair is meticulously handcrafted using time-honored techniques passed down through generations.', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_textarea_field',
                'section'           => 'soleorigine_about',
                'label'             => __( 'About Text', 'soleorigine' ),
                'type'              => 'textarea',
            ),
            'about_image' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_about',
                'label'             => __( 'About Image', 'soleorigine' ),
                'control'           => 'WP_Customize_Image_Control',
            ),

            // -----------------------------------------------------------------
            // Collection Section
            // -----------------------------------------------------------------
            'collection_title' => array(
                'default'           => __( 'Featured Collection', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_collection',
                'label'             => __( 'Collection Title', 'soleorigine' ),
                'type'              => 'text',
            ),
            'collection_subtitle' => array(
                'default'           => __( 'Discover Our Finest Creations', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_collection',
                'label'             => __( 'Collection Subtitle', 'soleorigine' ),
                'type'              => 'text',
            ),
            'collection_products_count' => array(
                'default'           => 8,
                'sanitize_callback' => 'absint',
                'section'           => 'soleorigine_collection',
                'label'             => __( 'Number of Products', 'soleorigine' ),
                'description'       => __( 'Number of products to display in the collection section.', 'soleorigine' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 1,
                    'max'  => 12,
                    'step' => 1,
                ),
            ),

            // -----------------------------------------------------------------
            // Footer Section
            // -----------------------------------------------------------------
            'footer_copyright' => array(
                'default'           => __( '&copy; 2026 SoleOrigine. All Rights Reserved.', 'soleorigine' ),
                'sanitize_callback' => 'wp_kses_post',
                'section'           => 'soleorigine_footer',
                'label'             => __( 'Footer Copyright Text', 'soleorigine' ),
                'description'       => __( 'HTML is allowed.', 'soleorigine' ),
                'type'              => 'textarea',
            ),
            'social_facebook' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_footer',
                'label'             => __( 'Facebook URL', 'soleorigine' ),
                'type'              => 'url',
            ),
            'social_instagram' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_footer',
                'label'             => __( 'Instagram URL', 'soleorigine' ),
                'type'              => 'url',
            ),
            'social_twitter' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_footer',
                'label'             => __( 'Twitter URL', 'soleorigine' ),
                'type'              => 'url',
            ),
            'social_youtube' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_footer',
                'label'             => __( 'YouTube URL', 'soleorigine' ),
                'type'              => 'url',
            ),
            'social_pinterest' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'soleorigine_footer',
                'label'             => __( 'Pinterest URL', 'soleorigine' ),
                'type'              => 'url',
            ),

            // -----------------------------------------------------------------
            // Blog Section
            // -----------------------------------------------------------------
            'blog_title' => array(
                'default'           => __( 'The Journal', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_blog',
                'label'             => __( 'Blog Section Title', 'soleorigine' ),
                'type'              => 'text',
            ),
            'blog_subtitle' => array(
                'default'           => __( 'Stories from the World of SoleOrigine', 'soleorigine' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'soleorigine_blog',
                'label'             => __( 'Blog Section Subtitle', 'soleorigine' ),
                'type'              => 'text',
            ),
            'blog_posts_per_page' => array(
                'default'           => 6,
                'sanitize_callback' => 'absint',
                'section'           => 'soleorigine_blog',
                'label'             => __( 'Posts Per Page', 'soleorigine' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 1,
                    'max'  => 12,
                    'step' => 1,
                ),
            ),
        );
    }

    // -------------------------------------------------------------------------
    // Registration
    // -------------------------------------------------------------------------

    /**
     * Register all sections, settings, and controls.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    public function register( $wp_customize ) {
        $this->register_sections( $wp_customize );
        $this->register_settings( $wp_customize );
    }

    /**
     * Register sections from config.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function register_sections( $wp_customize ) {
        foreach ( $this->config['sections'] as $id => $args ) {
            $wp_customize->add_section( $id, $args );
        }
    }

    /**
     * Register settings and their controls from config.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    private function register_settings( $wp_customize ) {
        foreach ( $this->config['settings'] as $id => $args ) {
            $this->register_setting( $wp_customize, $id, $args );
        }
    }

    /**
     * Register a single setting + control pair.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     * @param string               $id           Setting / control ID.
     * @param array                $args         Configuration arguments.
     */
    private function register_setting( $wp_customize, $id, $args ) {
        // Extract only the keys that belong to the setting.
        $setting_args = array_intersect_key( $args, array_flip( array(
            'default',
            'sanitize_callback',
            'transport',
        ) ) );

        $wp_customize->add_setting( $id, $setting_args );

        // Build control args from the remaining keys.
        $control_args = array_intersect_key( $args, array_flip( array(
            'label',
            'description',
            'section',
            'type',
            'input_attrs',
            'priority',
            'choices',
            'active_callback',
        ) ) );

        $control_args['settings'] = $id;
        $control_args['section']  = $args['section'];

        // Instantiate a custom control class if specified.
        if ( ! empty( $args['control'] ) && class_exists( $args['control'] ) ) {
            $wp_customize->add_control( new $args['control']( $wp_customize, $id, $control_args ) );
        } else {
            $wp_customize->add_control( $id, $control_args );
        }
    }

    // -------------------------------------------------------------------------
    // Preview JS
    // -------------------------------------------------------------------------

    /**
     * Enqueue customizer live-preview JavaScript.
     */
    public function enqueue_preview_js() {
        wp_enqueue_script(
            'soleorigine-customizer',
            get_template_directory_uri() . '/js/customizer.js',
            array( 'customize-preview' ),
            SOLEORIGINE_VERSION,
            true
        );
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Retrieve a theme_mod value with its registered default.
     *
     * Convenience wrapper so templates can do:
     *   SoleOrigine_Customizer_Config::get( 'hero_title' )
     * instead of remembering defaults in get_theme_mod() calls.
     *
     * @param string $id Setting ID.
     * @return mixed
     */
    public static function get( $id ) {
        $instance = self::get_instance();
        $default  = '';

        if ( isset( $instance->config['settings'][ $id ]['default'] ) ) {
            $default = $instance->config['settings'][ $id ]['default'];
        }

        return get_theme_mod( $id, $default );
    }
}

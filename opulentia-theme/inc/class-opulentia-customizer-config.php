<?php
/**
 * Config-Driven Customizer — Singleton
 *
 * Replaces the flat customizer.php with a class that registers
 * sections, settings, and controls from a configuration array.
 * Supports both theme_mod (default) and option-type settings
 * saving to the Opulentia_SETTINGS option for Theme Options API compatibility.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Customizer_Config class.
 */
class Opulentia_Customizer_Config {

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
        }

    // -------------------------------------------------------------------------
    // Registration
    // -------------------------------------------------------------------------

    /**
     * Register all customizer sections, settings, and controls from the config.
     *
     * @param WP_Customize_Manager $wp_customize Customizer manager instance.
     */
    public function register( $wp_customize ) {
        $this->register_typography_panel( $wp_customize );
$this->register_panels( $wp_customize );

        // Register sections.
        $sections = $this->config['sections'] ?? array();
        foreach ( $sections as $id => $args ) {
            $wp_customize->add_section( $id, array(
                'title'       => $args['title'] ?? '',
                'description' => $args['description'] ?? '',
                'panel'       => $args['panel'] ?? '',
                'priority'    => $args['priority'] ?? 10,
            ) );
        }

        // Register settings and controls.
        $settings = $this->config['settings'] ?? array();
        foreach ( $settings as $id => $args ) {
            $default          = $args['default'] ?? '';
            $sanitize_callback = $args['sanitize_callback'] ?? 'sanitize_text_field';
            $setting_type     = 'theme_mod'; // Dynamic CSS reads theme_mods

            $wp_customize->add_setting( $id, array(
                'default'           => $default,
                'sanitize_callback' => $sanitize_callback,
                'type'              => $setting_type,
                'capability'        => 'edit_theme_options',
                'transport'         => $args['transport'] ?? 'postMessage',
            ) );

            if ( ! empty( $args['control'] ) && class_exists( $args['control'] ) ) {
                $wp_customize->add_control( new $args['control'](
                    $wp_customize,
                    $id . '_ctrl',
                    array(
                        'label'       => $args['label'] ?? '',
                        'description' => $args['description'] ?? '',
                        'section'     => $args['section'] ?? '',
                        'settings'    => $id,
                        'priority'    => $args['priority'] ?? 10,
                    )
                ) );
            } else {
                $wp_customize->add_control( $id . '_ctrl', array(
                    'label'       => $args['label'] ?? '',
                    'description' => $args['description'] ?? '',
                    'section'     => $args['section'] ?? '',
                    'settings'    => $id,
                    'type'        => $args['type'] ?? 'text',
                    'priority'    => $args['priority'] ?? 10,
                    'input_attrs' => $args['input_attrs'] ?? array(),
                    'choices'     => $args['choices'] ?? array(),
                ) );
            }
        }
    }

    /**
     * Sanitize a checkbox value.
     *
     * @param mixed $value Checkbox input value.
     * @return bool
     */
    public function sanitize_checkbox( $value ) {
        return (bool) $value;
    }

    /**
     * Enqueue the customizer preview JavaScript for live preview.
     */
    public function enqueue_preview_js() {
        wp_enqueue_script(
            'opulentia-customizer-preview',
            Opulentia_URI . '/js/customizer.js',
            array( 'jquery', 'customize-preview' ),
            Opulentia_VERSION,
            true
        );
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
            'colors_global' => array(
                'title'    => __( 'Colors (Global)', 'opulentia' ),
                'panel'    => 'Opulentia_global_settings',
                'priority' => 10,
            ),
            'Opulentia_global_palette' => array(
                'title'    => __( 'Global Color Palette', 'opulentia' ),
                'description' => __( 'Set the 9-color global palette. These map to --opulentia-global-color-0 through 8.', 'opulentia' ),
                'panel'    => 'Opulentia_global_settings',
                'priority' => 20,
            ),
            'Opulentia_hero' => array(
                'title'    => __( 'Hero Section', 'opulentia' ),
                'panel'    => 'Opulentia_front_page',
                'priority' => 10,
            ),
            'Opulentia_about' => array(
                'title'    => __( 'About Section', 'opulentia' ),
                'panel'    => 'Opulentia_front_page',
                'priority' => 20,
            ),
            'Opulentia_collection' => array(
                'title'    => __( 'Collection Section', 'opulentia' ),
                'panel'    => 'Opulentia_front_page',
                'priority' => 30,
            ),
            'Opulentia_footer' => array(
                'title'    => __( 'Footer Settings', 'opulentia' ),
                'panel'    => 'Opulentia_footer_panel',
                'priority' => 10,
            ),
            'Opulentia_blog' => array(
                'title'    => __( 'Blog Settings', 'opulentia' ),
                'panel'    => 'Opulentia_blog_posts',
                'priority' => 10,
            ),
            'Opulentia_header' => array(
                'title'    => __( 'Header', 'opulentia' ),
                'panel'    => 'Opulentia_header_nav',
                'priority' => 10,
            ),

            'Opulentia_typography_headings' => array(
                'title'    => __( 'Headings (General)', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 10,
            ),
            'Opulentia_typography_h1' => array(
                'title'    => __( 'Heading 1 (H1)', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 20,
            ),
            'Opulentia_typography_h2' => array(
                'title'    => __( 'Heading 2 (H2)', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 30,
            ),
            'Opulentia_typography_h3' => array(
                'title'    => __( 'Heading 3 (H3)', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 40,
            ),
            'Opulentia_typography_h4' => array(
                'title'    => __( 'Heading 4 (H4)', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 50,
            ),
            'Opulentia_typography_h5' => array(
                'title'    => __( 'Heading 5 (H5)', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 60,
            ),
            'Opulentia_typography_h6' => array(
                'title'    => __( 'Heading 6 (H6)', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 70,
            ),
            'Opulentia_typography_body' => array(
                'title'    => __( 'Body', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 80,
            ),
            'Opulentia_typography_site_title' => array(
                'title'    => __( 'Site Title & Tagline', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 90,
            ),
            'Opulentia_typography_nav' => array(
                'title'    => __( 'Navigation', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 100,
            ),
            'Opulentia_typography_buttons' => array(
                'title'    => __( 'Buttons', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 110,
            ),
            'Opulentia_typography_blog' => array(
                'title'    => __( 'Blog Posts', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 120,
            ),
            'Opulentia_typography_widgets' => array(
                'title'    => __( 'Widget Titles', 'opulentia' ),
                'panel'    => 'Opulentia_typography',
                'priority' => 130,
            ),
            'Opulentia_layout' => array(
                'title'    => __( 'Layout', 'opulentia' ),
                'panel'    => 'Opulentia_global_settings',
                'priority' => 30,
            ),
            'Opulentia_wc' => array(
                'title'    => __( 'WooCommerce', 'opulentia' ),
                'panel'    => 'Opulentia_woocommerce_panel',
                'priority' => 10,
            ),

            // ── Page Header ─────────────────────────────────────────────
            'Opulentia_page_header' => array(
                'title'    => __( 'Page Header / Banner', 'opulentia' ),
                'panel'    => 'Opulentia_header_nav',
                'priority' => 20,
            ),

            // ── Breadcrumbs ─────────────────────────────────────────────
            'Opulentia_breadcrumbs' => array(
                'title'    => __( 'Breadcrumbs', 'opulentia' ),
                'panel'    => 'Opulentia_header_nav',
                'priority' => 30,
            ),



            // -----------------------------------------------------------------
            // Breadcrumbs
            // -----------------------------------------------------------------
            'breadcrumb_separator' => array(
                'default'           => '\/',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_breadcrumbs',
                'label'             => __( 'Breadcrumb Separator', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '\/' ),
                'priority'          => 10,
            ),
            'breadcrumb_home_text' => array(
                'default'           => __( 'Home', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_breadcrumbs',
                'label'             => __( 'Breadcrumb Home Text', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 20,
            ),
            'breadcrumb_show_current' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_breadcrumbs',
                'label'             => __( 'Show Current Page in Breadcrumbs', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 30,
            ),
            'breadcrumb_font_size' => array(
                'default'           => '14',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_breadcrumbs',
                'label'             => __( 'Breadcrumb Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array( '' => __( 'Default', 'opulentia' ), '12' => '12px', '13' => '13px', '14' => '14px', '15' => '15px', '16' => '16px' ),
                'priority'          => 40,
            ),
            'breadcrumb_font_color' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_breadcrumbs',
                'label'             => __( 'Breadcrumb Text Color', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from theme defaults.', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 50,
            ),
            // ── Mega Menu ────────────────────────────────────────────────
            'Opulentia_mega_menu' => array(
                'title'    => __( 'Mega Menu', 'opulentia' ),
                'panel'    => 'Opulentia_header_nav',
                'priority' => 40,
            ),

            // ── Live Search ──────────────────────────────────────────────
            'Opulentia_live_search' => array(
                'title'    => __( 'Live Search', 'opulentia' ),
                'panel'    => 'Opulentia_header_nav',
                'priority' => 50,
            ),

            // ── Blog Pro ─────────────────────────────────────────────────
            'Opulentia_blog_pro' => array(
                'title'    => __( 'Blog Pro', 'opulentia' ),
                'panel'    => 'Opulentia_blog_posts',
                'priority' => 20,
            ),

            // ── Scroll to Top ────────────────────────────────────────────
            'Opulentia_scroll_to_top' => array(
                'title'    => __( 'Scroll to Top', 'opulentia' ),
                'panel'    => 'Opulentia_footer_panel',
                'priority' => 20,
            ),

            // ── Dark Mode ────────────────────────────────────────────────
            'Opulentia_dark_mode' => array(
                'title'    => __( 'Dark Mode', 'opulentia' ),
                'panel'    => 'Opulentia_advanced',
                'priority' => 10,
            ),

            // ── Accessibility ────────────────────────────────────────────
            'Opulentia_accessibility' => array(
                'title'    => __( 'Accessibility', 'opulentia' ),
                'panel'    => 'Opulentia_global_settings',
                'priority' => 50,
            ),
        );
    }

    /**
     * Add the Typography panel to the customizer.
     *
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_typography_panel( $wp_customize ) {
        $wp_customize->add_panel( 'Opulentia_typography', array(
            'title'       => __( 'Typography', 'opulentia' ),
            'description' => __( 'Font settings for all theme elements.', 'opulentia' ),
            'priority'    => 55,
        ) );
    }

    /**
     * Register all theme panels.
     *
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_panels( $wp_customize ) {
        $panels = array(
            'Opulentia_global_settings' => array(
                'title'       => __( 'Global Settings', 'opulentia' ),
                'description' => __( 'Colors, layout, spacing, and accessibility.', 'opulentia' ),
                'priority'    => 20,
            ),
            'Opulentia_header_nav' => array(
                'title'       => __( 'Header & Navigation', 'opulentia' ),
                'description' => __( 'Header layout, page header, breadcrumbs, search, and mega menu.', 'opulentia' ),
                'priority'    => 30,
            ),
            'Opulentia_front_page' => array(
                'title'       => __( 'Front Page', 'opulentia' ),
                'description' => __( 'Hero, about, and collection sections for the homepage layout.', 'opulentia' ),
                'priority'    => 35,
            ),
            'Opulentia_footer_panel' => array(
                'title'       => __( 'Footer', 'opulentia' ),
                'description' => __( 'Footer widget areas, bottom bar, social links, and scroll-to-top.', 'opulentia' ),
                'priority'    => 40,
            ),
            'Opulentia_blog_posts' => array(
                'title'       => __( 'Blog & Posts', 'opulentia' ),
                'description' => __( 'Blog archive layout, single post settings, and blog pro features.', 'opulentia' ),
                'priority'    => 45,
            ),
            'Opulentia_woocommerce_panel' => array(
                'title'       => __( 'WooCommerce', 'opulentia' ),
                'description' => __( 'Shop, product, cart, and checkout settings.', 'opulentia' ),
                'priority'    => 60,
            ),
            'Opulentia_advanced' => array(
                'title'       => __( 'Performance & Advanced', 'opulentia' ),
                'description' => __( 'Dark mode, advanced hooks, and performance optimizations.', 'opulentia' ),
                'priority'    => 70,
            ),
        );

        foreach ( $panels as $id => $args ) {
            $wp_customize->add_panel( $id, $args );
        }
    }

    /**
     * Shared responsive font size choices for per-heading controls.
     *
     * @return array
     */
    private function get_font_size_choices() {
        $choices = array( '' => __( 'Inherit', 'opulentia' ) );
        for ( $i = 10; $i <= 100; $i++ ) {
            $choices[ (string) $i ] = $i . 'px';
        }
        return $choices;
    }

    /**
     * Shared weight choices.
     *
     * @return array
     */
    private function get_weight_choices() {
        return array(
            ''    => __( 'Inherit', 'opulentia' ),
            '100' => __( 'Thin (100)', 'opulentia' ),
            '200' => __( 'Extra Light (200)', 'opulentia' ),
            '300' => __( 'Light (300)', 'opulentia' ),
            '400' => __( 'Regular (400)', 'opulentia' ),
            '500' => __( 'Medium (500)', 'opulentia' ),
            '600' => __( 'Semi-Bold (600)', 'opulentia' ),
            '700' => __( 'Bold (700)', 'opulentia' ),
            '800' => __( 'Extra Bold (800)', 'opulentia' ),
            '900' => __( 'Black (900)', 'opulentia' ),
        );
    }

    /**
     * Shared transform choices.
     *
     * @return array
     */
    private function get_transform_choices() {
        return array(
            ''          => __( 'Inherit', 'opulentia' ),
            'none'      => __( 'Normal', 'opulentia' ),
            'uppercase' => __( 'UPPERCASE', 'opulentia' ),
            'capitalize' => __( 'Capitalize', 'opulentia' ),
            'lowercase' => __( 'lowercase', 'opulentia' ),
        );
    }


    /**
     * Shared color scheme preset choices for the Customizer select control.
     *
     * @return array
     */
    private function get_preset_choices() {
        if ( function_exists( 'Opulentia_get_preset_choices' ) ) {
            return Opulentia_get_preset_choices();
        }

        return array(
            'dark-luxury'     => __( 'Dark Luxury (Default)', 'opulentia' ),
            'midnight-gold'   => __( 'Midnight Gold', 'opulentia' ),
            'obsidian-silver' => __( 'Obsidian Silver', 'opulentia' ),
            'espresso-brown'  => __( 'Espresso Brown', 'opulentia' ),
            'royal-navy'      => __( 'Royal Navy', 'opulentia' ),
            'deep-burgundy'   => __( 'Deep Burgundy', 'opulentia' ),
            'emerald-night'   => __( 'Emerald Night', 'opulentia' ),
            'platinum-frost'  => __( 'Platinum Frost', 'opulentia' ),
        );
    }

    /**
     * Build per-heading setting config for a given heading tag.
     *
     * @param string $tag    h1, h2, h3, h4, h5, h6
     * @param string $label  Label prefix (e.g. 'Heading 1')
     * @param int    $start_priority
     * @return array
     */
    private function get_heading_settings( $tag, $label, $start_priority = 10 ) {
        $prefix = 'typo-' . $tag;
        $section = 'Opulentia_typography_' . $tag;
        $inherit_desc = __( 'Leave empty to inherit from Headings (General).', 'opulentia' );

        return array(
            $prefix . '-family' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Font Family', 'opulentia' ), $label ),
                'description'       => $inherit_desc,
                'type'              => 'text',
                'priority'          => $start_priority,
            ),
            $prefix . '-weight' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Font Weight', 'opulentia' ), $label ),
                'description'       => $inherit_desc,
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => $start_priority + 10,
            ),
            $prefix . '-size' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Font Size', 'opulentia' ), $label ),
                'description'       => sprintf( __( 'Desktop font size in px. %s', 'opulentia' ), $inherit_desc ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => $start_priority + 20,
            ),
            $prefix . '-size-tablet' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Font Size (Tablet)', 'opulentia' ), $label ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => $start_priority + 30,
            ),
            $prefix . '-size-mobile' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Font Size (Mobile)', 'opulentia' ), $label ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => $start_priority + 40,
            ),
            $prefix . '-line-height' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Line Height', 'opulentia' ), $label ),
                'description'       => $inherit_desc . ' ' . __( 'e.g. 1.2, 1.5, 2.0', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '1.2' ),
                'priority'          => $start_priority + 50,
            ),
            $prefix . '-transform' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Text Transform', 'opulentia' ), $label ),
                'description'       => $inherit_desc,
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => $start_priority + 60,
            ),
            $prefix . '-spacing' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => $section,
                'optionize'         => true,
                'label'             => sprintf( __( '%s: Letter Spacing', 'opulentia' ), $label ),
                'description'       => __( 'e.g. 1px, 0.5px, 2px', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '0.5px' ),
                'priority'          => $start_priority + 70,
            ),
        );
    }

    /**
     * Setting + control definitions.
     *
     * Each entry maps to one setting and one control.
     * Special keys:
     *   'control'     — WP_Customize_Control class name (e.g. 'WP_Customize_Image_Control').
     *   'type'        — Standard control type (text, textarea, url, number, etc.)
     *   'optionize'   — If true, saves to Opulentia_SETTINGS option array for Theme Options API compat.
     *   'input_attrs' — Array of HTML attributes for the input.
     *   'description' — Optional description text.
     *
     * @return array
     */
    private function get_settings() {
        $settings = array(

            // -----------------------------------------------------------------
            // Colors (Global)
            // -----------------------------------------------------------------
            'color_scheme_preset' => array(
                'default'           => 'dark-luxury',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'colors_global',
                'label'             => __( 'Color Scheme Preset', 'opulentia' ),
                'description'       => __( 'Select a preset color scheme for the entire theme. The preset sets both legacy vars and the 9-color global palette (--opulentia-global-color-0 through 8).', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_preset_choices(),
            ),
            'color_primary_dark' => array(
                'default'           => '#1a1a1a',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Primary Dark (Page Background)', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_secondary_dark' => array(
                'default'           => '#111111',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Secondary Dark (Cards, Dropdowns)', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_accent' => array(
                'default'           => '#b8860b',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Accent Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_accent_hover' => array(
                'default'           => '#d4a843',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Accent Hover Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_gold_hover' => array(
                'default'           => '#b8944f',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Gold Hover Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_gold' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Gold / Headings Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_text' => array(
                'default'           => '#f5f5f5',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Body Text Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_border' => array(
                'default'           => '#333333',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Border Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_header_bg' => array(
                'default'           => '#1a1a1a',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Header Background', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_header_top_bar_bg' => array(
                'default'           => '#111111',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Header Top Bar Background', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_footer_bg' => array(
                'default'           => '#1a1a1a',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Footer Background', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_link' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Link Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_link_hover' => array(
                'default'           => '#d4a843',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Link Hover Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_button_bg' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Button Background', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_button_text' => array(
                'default'           => '#ffffff',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Button Text Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_button_hover_bg' => array(
                'default'           => '#b8944f',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'colors_global',
                'label'             => __( 'Button Hover Background', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_header_scroll_bg' => array(
                'default'           => 'rgba(17, 17, 17, 0.95)',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'colors_global',
                'label'             => __( 'Header Scroll Background', 'opulentia' ),
                'description'       => __( 'Background when header scrolls. Accepts hex, rgb(), or rgba() values.', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array(
                    'placeholder' => 'rgba(17, 17, 17, 0.95)',
                ),
            ),

            // -----------------------------------------------------------------
            // Global Color Palette — 9 Colors (--opulentia-global-color-0 through 8)
            // -----------------------------------------------------------------
        );

        // Add 9 global palette color pickers.
        $palette_labels = array(
            0 => __( 'Global Color 0 — Page Background', 'opulentia' ),
            1 => __( 'Global Color 1 — Card / Section Background', 'opulentia' ),
            2 => __( 'Global Color 2 — Accent', 'opulentia' ),
            3 => __( 'Global Color 3 — Gold / Heading', 'opulentia' ),
            4 => __( 'Global Color 4 — Light Gold / Subtle Accent', 'opulentia' ),
            5 => __( 'Global Color 5 — Body Text', 'opulentia' ),
            6 => __( 'Global Color 6 — Muted Text', 'opulentia' ),
            7 => __( 'Global Color 7 — Border', 'opulentia' ),
            8 => __( 'Global Color 8 — White / Brightest', 'opulentia' ),
        );

        // Default palette values (Dark Luxury).
        $palette_defaults = array(
            0 => '#1a1a1a',
            1 => '#111111',
            2 => '#b8860b',
            3 => '#c9a96e',
            4 => '#e8d5a3',
            5 => '#f5f5f5',
            6 => '#999999',
            7 => '#333333',
            8 => '#ffffff',
        );

        foreach ( $palette_labels as $i => $label ) {
            $settings[ 'global-color-' . $i ] = array(
                'default'           => $palette_defaults[ $i ],
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_global_palette',
                'label'             => $label,
                'description'       => sprintf( __( 'Sets --opulentia-global-color-%d.', 'opulentia' ), $i ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 10 + ( $i * 10 ),
            );
        }

        $settings = array_merge( $settings, array(
            // -----------------------------------------------------------------
            // Typography — Headings (General)
            // -----------------------------------------------------------------
            'typo-headings-family' => array(
                'default'           => 'Playfair Display',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_headings',
                'optionize'         => true,
                'label'             => __( 'Headings Font Family', 'opulentia' ),
                'description'       => __( 'Google Font name (e.g., Playfair Display, Cormorant Garamond).', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 10,
            ),
            'typo-headings-weight' => array(
                'default'           => '600',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_headings',
                'optionize'         => true,
                'label'             => __( 'Headings Font Weight', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => 20,
            ),
            'typo-headings-line-height' => array(
                'default'           => '1.2',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_headings',
                'optionize'         => true,
                'label'             => __( 'Headings Line Height', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '1.2' ),
                'priority'          => 30,
            ),
            'typo-headings-transform' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_headings',
                'optionize'         => true,
                'label'             => __( 'Headings Text Transform', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => 40,
            ),
            'typo-headings-spacing' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_headings',
                'optionize'         => true,
                'label'             => __( 'Headings Letter Spacing', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '0.5px' ),
                'priority'          => 50,
            ),

            // -----------------------------------------------------------------
            // Typography — H1 through H6
            // -----------------------------------------------------------------
        ));

        // Add per-heading settings (H1-H6)
        $heading_tags = array(
            'h1' => __( 'Heading 1 (H1)', 'opulentia' ),
            'h2' => __( 'Heading 2 (H2)', 'opulentia' ),
            'h3' => __( 'Heading 3 (H3)', 'opulentia' ),
            'h4' => __( 'Heading 4 (H4)', 'opulentia' ),
            'h5' => __( 'Heading 5 (H5)', 'opulentia' ),
            'h6' => __( 'Heading 6 (H6)', 'opulentia' ),
        );

        $priority = 10;
        foreach ( $heading_tags as $tag => $label ) {
            $heading_settings = $this->get_heading_settings( $tag, $label, $priority );
            foreach ( $heading_settings as $key => $config ) {
                $settings[ $key ] = $config;
            }
            $priority += 100;
        }

        // -----------------------------------------------------------------
        // Typography — Body
        // -----------------------------------------------------------------
        $body_settings = array(
            'typo-body-family' => array(
                'default'           => 'Inter',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Font Family', 'opulentia' ),
                'description'       => __( 'Google Font name (e.g., Inter, Lato, Open Sans).', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 10,
            ),
            'typo-body-weight' => array(
                'default'           => '400',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Font Weight', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => 20,
            ),
            'typo-body-size' => array(
                'default'           => '16',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 30,
            ),
            'typo-body-size-tablet' => array(
                'default'           => '15',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Font Size (Tablet, px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 40,
            ),
            'typo-body-size-mobile' => array(
                'default'           => '14',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Font Size (Mobile, px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 50,
            ),
            'typo-body-line-height' => array(
                'default'           => '1.6',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Line Height', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '1.6' ),
                'priority'          => 60,
            ),
            'typo-body-transform' => array(
                'default'           => 'none',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Text Transform', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => 70,
            ),
            'typo-body-spacing' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_body',
                'optionize'         => true,
                'label'             => __( 'Body Letter Spacing', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '0.5px' ),
                'priority'          => 80,
            ),
        );

        // -----------------------------------------------------------------
        // Typography — Site Title & Tagline
        // -----------------------------------------------------------------
        $site_title_settings = array(
            'typo-site-title-family' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Site Title: Font Family', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from body.', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 10,
            ),
            'typo-site-title-weight' => array(
                'default'           => '600',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Site Title: Font Weight', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => 20,
            ),
            'typo-site-title-size' => array(
                'default'           => '24',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Site Title: Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 30,
            ),
            'typo-site-title-size-tablet' => array(
                'default'           => '20',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Site Title: Font Size (Tablet, px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 40,
            ),
            'typo-site-title-size-mobile' => array(
                'default'           => '18',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Site Title: Font Size (Mobile, px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 50,
            ),
            'typo-site-title-transform' => array(
                'default'           => 'none',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Site Title: Text Transform', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => 60,
            ),
            'typo-site-title-spacing' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Site Title: Letter Spacing', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '0.5px' ),
                'priority'          => 70,
            ),
            'typo-tagline-family' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Tagline: Font Family', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from body.', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 80,
            ),
            'typo-tagline-weight' => array(
                'default'           => '400',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Tagline: Font Weight', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => 90,
            ),
            'typo-tagline-size' => array(
                'default'           => '14',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Tagline: Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 100,
            ),
            'typo-tagline-transform' => array(
                'default'           => 'uppercase',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Tagline: Text Transform', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => 110,
            ),
            'typo-tagline-spacing' => array(
                'default'           => '2',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_site_title',
                'optionize'         => true,
                'label'             => __( 'Tagline: Letter Spacing (px)', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '2' ),
                'priority'          => 120,
            ),
        );

        // -----------------------------------------------------------------
        // Typography — Navigation
        // -----------------------------------------------------------------
        $nav_settings = array(
            'typo-nav-family' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_nav',
                'optionize'         => true,
                'label'             => __( 'Navigation: Font Family', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from body.', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 10,
            ),
            'typo-nav-weight' => array(
                'default'           => '500',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_nav',
                'optionize'         => true,
                'label'             => __( 'Navigation: Font Weight', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => 20,
            ),
            'typo-nav-size' => array(
                'default'           => '14',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_nav',
                'optionize'         => true,
                'label'             => __( 'Navigation: Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 30,
            ),
            'typo-nav-transform' => array(
                'default'           => 'uppercase',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_nav',
                'optionize'         => true,
                'label'             => __( 'Navigation: Text Transform', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => 40,
            ),
            'typo-nav-spacing' => array(
                'default'           => '1',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_nav',
                'optionize'         => true,
                'label'             => __( 'Navigation: Letter Spacing (px)', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '1' ),
                'priority'          => 50,
            ),
            'typo-nav-line-height' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_nav',
                'optionize'         => true,
                'label'             => __( 'Navigation: Line Height', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '1.5' ),
                'priority'          => 60,
            ),
        );

        // -----------------------------------------------------------------
        // Typography — Buttons
        // -----------------------------------------------------------------
        $button_settings = array(
            'typo-btn-family' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_buttons',
                'optionize'         => true,
                'label'             => __( 'Buttons: Font Family', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from body.', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 10,
            ),
            'typo-btn-weight' => array(
                'default'           => '500',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_buttons',
                'optionize'         => true,
                'label'             => __( 'Buttons: Font Weight', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => 20,
            ),
            'typo-btn-size' => array(
                'default'           => '14',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_buttons',
                'optionize'         => true,
                'label'             => __( 'Buttons: Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 30,
            ),
            'typo-btn-transform' => array(
                'default'           => 'uppercase',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_buttons',
                'optionize'         => true,
                'label'             => __( 'Buttons: Text Transform', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => 40,
            ),
            'typo-btn-spacing' => array(
                'default'           => '1',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_buttons',
                'optionize'         => true,
                'label'             => __( 'Buttons: Letter Spacing (px)', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '1' ),
                'priority'          => 50,
            ),
            'typo-btn-line-height' => array(
                'default'           => '1.5',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_buttons',
                'optionize'         => true,
                'label'             => __( 'Buttons: Line Height', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '1.5' ),
                'priority'          => 60,
            ),
        );

        // -----------------------------------------------------------------
        // Typography — Blog Posts
        // -----------------------------------------------------------------
        $blog_settings = array(
            'typo-post-title-size' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_blog',
                'optionize'         => true,
                'label'             => __( 'Post Title: Font Size (px)', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from H2.', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 10,
            ),
            'typo-post-title-size-tablet' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_blog',
                'optionize'         => true,
                'label'             => __( 'Post Title: Font Size (Tablet, px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 20,
            ),
            'typo-post-title-size-mobile' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_blog',
                'optionize'         => true,
                'label'             => __( 'Post Title: Font Size (Mobile, px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 30,
            ),
            'typo-post-meta-size' => array(
                'default'           => '13',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_blog',
                'optionize'         => true,
                'label'             => __( 'Post Meta: Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 40,
            ),
            'typo-post-taxonomy-size' => array(
                'default'           => '12',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_blog',
                'optionize'         => true,
                'label'             => __( 'Post Taxonomy: Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 50,
            ),
        );

        // -----------------------------------------------------------------
        // Typography — Widget Titles
        // -----------------------------------------------------------------
        $widget_settings = array(
            'typo-widget-family' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_widgets',
                'optionize'         => true,
                'label'             => __( 'Widget Title: Font Family', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from headings.', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 10,
            ),
            'typo-widget-weight' => array(
                'default'           => '600',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_widgets',
                'optionize'         => true,
                'label'             => __( 'Widget Title: Font Weight', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_weight_choices(),
                'priority'          => 20,
            ),
            'typo-widget-size' => array(
                'default'           => '18',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_widgets',
                'optionize'         => true,
                'label'             => __( 'Widget Title: Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 30,
            ),
            'typo-widget-transform' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_widgets',
                'optionize'         => true,
                'label'             => __( 'Widget Title: Text Transform', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_transform_choices(),
                'priority'          => 40,
            ),
            'typo-widget-spacing' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_typography_widgets',
                'optionize'         => true,
                'label'             => __( 'Widget Title: Letter Spacing', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '0.5px' ),
                'priority'          => 50,
            ),
        );

        // Merge all typography settings
        $settings = array_merge(
            $settings,
            $body_settings,
            $site_title_settings,
            $nav_settings,
            $button_settings,
            $blog_settings,
            $widget_settings
        );

        // -----------------------------------------------------------------
        // Layout
        // -----------------------------------------------------------------
        $layout_settings = array(
            'layout_container_max' => array(
                'default'           => '1200px',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_layout',
                'label'             => __( 'Container Max Width', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '960px'  => '960px',
                    '1100px' => '1100px',
                    '1200px' => '1200px',
                    '1300px' => '1300px',
                    '1400px' => '1400px',
                ),
            ),
            'header_sticky' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_layout',
                'label'             => __( 'Sticky Header', 'opulentia' ),
                'description'       => __( 'Enable sticky header that stays at top when scrolling.', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'layout_content_layout' => array(
                'default'           => 'boxed',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_layout',
                'label'             => __( 'Content Layout', 'opulentia' ),
                'type'              => 'select',
                'transport'         => 'refresh',
                'choices'           => array(
                    'boxed'    => __( 'Boxed', 'opulentia' ),
                    'full-width' => __( 'Full Width', 'opulentia' ),
                ),
            ),
            'layout_sidebar_position' => array(
                'default'           => 'right',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_layout',
                'label'             => __( 'Sidebar Position', 'opulentia' ),
                'type'              => 'select',
                'transport'         => 'refresh',
                'choices'           => array(
                    'right' => __( 'Right Sidebar', 'opulentia' ),
                    'left'  => __( 'Left Sidebar', 'opulentia' ),
                    'none'  => __( 'No Sidebar', 'opulentia' ),
                ),
            ),

            // -----------------------------------------------------------------
            // Hero Section
            // -----------------------------------------------------------------
            'hero_title' => array(
                'default'           => __( 'opulentia', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_hero',
                'label'             => __( 'Hero Title', 'opulentia' ),
                'type'              => 'text',
            ),
            'hero_subtitle' => array(
                'default'           => __( 'Premium Italian Footwear', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_hero',
                'label'             => __( 'Hero Subtitle', 'opulentia' ),
                'type'              => 'text',
            ),
            'hero_button_1_text' => array(
                'default'           => __( 'Explore Collection', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_hero',
                'label'             => __( 'Button 1 Text', 'opulentia' ),
                'type'              => 'text',
            ),
            'hero_button_1_url' => array(
                'default'           => '/collection',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_hero',
                'label'             => __( 'Button 1 URL', 'opulentia' ),
                'type'              => 'url',
            ),
            'hero_button_2_text' => array(
                'default'           => __( 'View Styles', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_hero',
                'label'             => __( 'Button 2 Text', 'opulentia' ),
                'type'              => 'text',
            ),
            'hero_button_2_url' => array(
                'default'           => '/styles',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_hero',
                'label'             => __( 'Button 2 URL', 'opulentia' ),
                'type'              => 'url',
            ),
            'hero_background' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_hero',
                'label'             => __( 'Hero Background Image', 'opulentia' ),
                'control'           => 'WP_Customize_Image_Control',
            ),

            // -----------------------------------------------------------------
            // About Section
            // -----------------------------------------------------------------
            'about_title' => array(
                'default'           => __( 'Our Heritage', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_about',
                'label'             => __( 'About Title', 'opulentia' ),
                'type'              => 'text',
            ),
            'about_subtitle' => array(
                'default'           => __( 'A Legacy of Excellence', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_about',
                'label'             => __( 'About Subtitle', 'opulentia' ),
                'type'              => 'text',
            ),
            'about_text' => array(
                'default'           => __( 'Born from a passion for exceptional footwear, Opulentia represents the pinnacle of Italian craftsmanship. Each pair is meticulously handcrafted using time-honored techniques passed down through generations.', 'opulentia' ),
                'sanitize_callback' => 'sanitize_textarea_field',
                'section'           => 'Opulentia_about',
                'label'             => __( 'About Text', 'opulentia' ),
                'type'              => 'textarea',
            ),
            'about_image' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_about',
                'label'             => __( 'About Image', 'opulentia' ),
                'control'           => 'WP_Customize_Image_Control',
            ),

            // -----------------------------------------------------------------
            // Collection Section
            // -----------------------------------------------------------------
            'collection_title' => array(
                'default'           => __( 'Featured Collection', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_collection',
                'label'             => __( 'Collection Title', 'opulentia' ),
                'type'              => 'text',
            ),
            'collection_subtitle' => array(
                'default'           => __( 'Discover Our Finest Creations', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_collection',
                'label'             => __( 'Collection Subtitle', 'opulentia' ),
                'type'              => 'text',
            ),
            'collection_products_count' => array(
                'default'           => 8,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_collection',
                'label'             => __( 'Number of Products', 'opulentia' ),
                'description'       => __( 'Number of products to display in the collection section.', 'opulentia' ),
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
            'footer-layout' => array(
                'default'           => 'boxed',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_footer',
                'optionize'         => true,
                'label'             => __( 'Footer Layout', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'boxed'      => __( 'Boxed (Container)', 'opulentia' ),
                    'full-width' => __( 'Full Width', 'opulentia' ),
                ),
                'priority'          => 5,
            ),
            'footer_columns' => array(
                'default'           => 4,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Footer Column Layout', 'opulentia' ),
                'type'              => 'select',
                'transport'         => 'refresh',
                'choices'           => array(
                    '2' => __( '2 Columns', 'opulentia' ),
                    '3' => __( '3 Columns', 'opulentia' ),
                    '4' => __( '4 Columns', 'opulentia' ),
                    '5' => __( '5 Columns', 'opulentia' ),
                ),
                'priority'          => 10,
            ),
            'footer_show_brand' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Show Brand Column', 'opulentia' ),
                'description'       => __( 'First column with logo, description, and social links.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 15,
            ),
            'footer_show_newsletter' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Show Newsletter Section', 'opulentia' ),
                'description'       => __( 'Displays above the footer widget grid.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 16,
            ),
            'footer_show_trust_badges' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Show Trust Badges', 'opulentia' ),
                'description'       => __( 'Security, returns, support, and offers icons above the widget grid.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 17,
            ),
            'footer_show_social' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Show Social Icons in Brand Column', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 18,
            ),
            'footer_show_html_block' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Show HTML Block in Brand Column', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 19,
            ),
            'footer-html-block' => array(
                'default'           => '',
                'sanitize_callback' => 'wp_kses_post',
                'section'           => 'Opulentia_footer',
                'optionize'         => true,
                'label'             => __( 'HTML Block', 'opulentia' ),
                'description'       => __( 'Custom HTML shown in the brand column below social icons.', 'opulentia' ),
                'type'              => 'textarea',
                'priority'          => 20,
            ),
            'footer_show_payment_icons' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Show Payment Icons', 'opulentia' ),
                'description'       => __( 'VISA, MC, AMEX, COD in the footer bottom bar.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 25,
            ),
            'footer_copyright' => array(
                'default'           => __( '&copy; 2026 Opulentia. All Rights Reserved.', 'opulentia' ),
                'sanitize_callback' => 'wp_kses_post',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Footer Copyright Text', 'opulentia' ),
                'description'       => __( 'HTML is allowed. Shown in the bottom bar.', 'opulentia' ),
                'type'              => 'textarea',
                'priority'          => 30,
            ),
            'social_facebook' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Facebook URL', 'opulentia' ),
                'type'              => 'url',
                'priority'          => 35,
            ),
            'social_instagram' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Instagram URL', 'opulentia' ),
                'type'              => 'url',
                'priority'          => 40,
            ),
            'social_twitter' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Twitter URL', 'opulentia' ),
                'type'              => 'url',
                'priority'          => 45,
            ),
            'social_youtube' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'YouTube URL', 'opulentia' ),
                'type'              => 'url',
                'priority'          => 50,
            ),
            'social_pinterest' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Pinterest URL', 'opulentia' ),
                'type'              => 'url',
                'priority'          => 55,
            ),
            'color_footer_bottom_bg' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Footer Bottom Bar Background', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from footer background.', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 60,
            ),
            'color_newsletter_bg' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Newsletter Section Background', 'opulentia' ),
                'description'       => __( 'Accepts hex, gradient, or URL. Leave empty for default gradient.', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => 'linear-gradient(...)', ),
                'priority'          => 65,
            ),
            'color_trust_badges_bg' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Trust Badges Background', 'opulentia' ),
                'description'       => __( 'Accepts hex or gradient. Leave empty for default dark.', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '#1e1e1e' ),
                'priority'          => 70,
            ),
            'color_footer_above_bg' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_footer',
                'label'             => __( 'Footer Above Row Background', 'opulentia' ),
                'description'       => __( 'Background for the top section of the footer.', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 75,
            ),

            // -----------------------------------------------------------------
            // Blog Section
            // -----------------------------------------------------------------
            'blog_title' => array(
                'default'           => __( 'The Journal', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Blog Section Title', 'opulentia' ),
                'type'              => 'text',
            ),
            'blog_subtitle' => array(
                'default'           => __( 'Stories from the World of Opulentia', 'opulentia' ),
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Blog Section Subtitle', 'opulentia' ),
                'type'              => 'text',
            ),
            'blog_posts_per_page' => array(
                'default'           => 6,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Posts Per Page', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 1,
                    'max'  => 12,
                    'step' => 1,
                ),
            ),
            'blog_layout' => array(
                'default'           => 'grid',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Blog Layout', 'opulentia' ),
                'type'              => 'select',
                'transport'         => 'refresh',
                'choices'           => array(
                    'classic' => __( 'Classic (Full Width)', 'opulentia' ),
                    'grid'    => __( 'Grid', 'opulentia' ),
                    'list'    => __( 'List (Thumbnail)', 'opulentia' ),
                ),
            ),
            'blog_grid_columns' => array(
                'default'           => 2,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Blog Grid Columns', 'opulentia' ),
                'transport'         => 'refresh',
                'description'       => __( 'Only applies when Grid layout is selected.', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ),
            ),
            'blog_excerpt_length' => array(
                'default'           => 20,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Excerpt Length (Words)', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 10,
                    'max'  => 100,
                    'step' => 5,
                ),
            ),
            'blog_read_more_text' => array(
                'default'           => 'Read More',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Read More Button Text', 'opulentia' ),
                'type'              => 'text',
            ),
            'blog_image_radius' => array(
                'default'           => '8px',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Featured Image Border Radius', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '0px'   => '0px (Sharp)',
                    '4px'   => '4px (Rounded)',
                    '8px'   => '8px (Default)',
                    '12px'  => '12px (More Rounded)',
                    '50%'   => '50% (Circle)',
                ),
            ),
            'blog_image_aspect_ratio' => array(
                'default'           => '16/10',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Blog Image Aspect Ratio', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '16/10' => '16:10 (Default)',
                    '16/9'  => '16:9 (Widescreen)',
                    '4/3'   => '4:3 (Classic)',
                    '3/2'   => '3:2 (Photo)',
                    '1/1'   => '1:1 (Square)',
                    'original' => __( 'Original (No Crop)', 'opulentia' ),
                ),
            ),
            'blog_card_bg' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Post Card Background Color', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from theme default.', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'blog_card_border' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Post Card Border Color', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit from theme default.', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'blog_card_radius' => array(
                'default'           => '8',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Post Card Border Radius (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '0'  => '0px (Sharp)',
                    '4'  => '4px',
                    '8'  => '8px (Default)',
                    '12' => '12px',
                    '16' => '16px',
                ),
            ),
            'blog_card_shadow' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Post Card Hover Shadow', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'blog_single_show_related' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Single Post: Show Related Posts', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'blog_related_count' => array(
                'default'           => 3,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Related Posts Count', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 1,
                    'max'  => 6,
                    'step' => 1,
                ),
            ),
            'blog_single_show_author' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Single Post: Show Author Box', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'blog_single_show_navigation' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Single Post: Show Post Navigation', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'blog_post_nav_style' => array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog',
                'label'             => __( 'Post Navigation Style', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'default'   => __( 'Default (Inline)', 'opulentia' ),
                    'thumbnail' => __( 'Card Style', 'opulentia' ),
                ),
            ),

            // -----------------------------------------------------------------
            // Header — Layout & Components
            // -----------------------------------------------------------------
            'header-layout' => array(
                'default'           => 'standard',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Header Layout', 'opulentia' ),
                'description'       => __( 'Choose the header layout preset.', 'opulentia' ),
                'type'              => 'select',
                'transport'         => 'refresh',
                'choices'           => array(
                    'standard'   => __( 'Standard — Logo / Nav / Actions', 'opulentia' ),
                    'centered'   => __( 'Centered — Centered logo, nav below', 'opulentia' ),
                    'minimal'    => __( 'Minimal — Logo left, nav + actions right', 'opulentia' ),
                    'stacked'    => __( 'Stacked — Logo top, nav middle, actions bottom', 'opulentia' ),
                    'off-canvas' => __( 'Off-Canvas — Hamburger, fullscreen overlay nav', 'opulentia' ),
                ),
                'priority'          => 10,
            ),
            'header-transparent' => array(
                'default'           => false,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Transparent Header', 'opulentia' ),
                'description'       => __( 'Make header transparent on homepage/hero pages.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 20,
            ),
            'header-show-tagline' => array(
                'default'           => false,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Show Tagline Below Logo', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 25,
            ),
            'header-sticky' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Sticky Header', 'opulentia' ),
                'description'       => __( 'Keep header fixed at top when scrolling.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 30,
            ),
            'header-show-top-bar' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Show Above Header Row (Top Bar)', 'opulentia' ),
                'description'       => __( 'Display the top bar with tagline and shipping info.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 40,
            ),
            'header-show-top-bar-tagline' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Top Bar: Show Tagline', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 50,
            ),
            'header-show-top-bar-shipping' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Top Bar: Show Shipping Info', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 60,
            ),
            'header-show-search' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Show Search Icon', 'opulentia' ),
                'description'       => __( 'Toggle for all layouts except off-canvas which always shows it.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 70,
            ),
            'header-show-account' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Show Account Icon', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 80,
            ),
            'header-show-cart' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Show Cart Icon', 'opulentia' ),
                'description'       => __( 'Shows WooCommerce cart icon with count badge and mini cart dropdown.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 90,
            ),
            'header-show-wishlist' => array(
                'default'           => false,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Show Wishlist Icon', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 100,
            ),
            'header-custom-button-text' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Custom Button: Text', 'opulentia' ),
                'description'       => __( 'Leave empty to hide. Example: Get 20% Off', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 110,
            ),
            'header-custom-button-url' => array(
                'default'           => '#',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Custom Button: URL', 'opulentia' ),
                'type'              => 'url',
                'priority'          => 120,
            ),
            'header-custom-button-style' => array(
                'default'           => 'outline',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'Custom Button: Style', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'primary' => __( 'Primary (Gold Fill)', 'opulentia' ),
                    'outline' => __( 'Outline (Bordered)', 'opulentia' ),
                    'minimal' => __( 'Minimal (Text Only)', 'opulentia' ),
                ),
                'priority'          => 130,
            ),
            'header-html-block' => array(
                'default'           => '',
                'sanitize_callback' => 'wp_kses_post',
                'section'           => 'Opulentia_header',
                'optionize'         => true,
                'label'             => __( 'HTML Block', 'opulentia' ),
                'description'       => __( 'Custom HTML to display in the header (shown in stacked layout).', 'opulentia' ),
                'type'              => 'textarea',
                'priority'          => 140,
            ),

            // -----------------------------------------------------------------
            // Spacing
            // -----------------------------------------------------------------
            'layout_section_padding_top' => array(
                'default'           => 80,
                'sanitize_callback' => 'absint',
                'section'           => 'opulentia_spacing',
                'label'             => __( 'Section Padding Top (px)', 'opulentia' ),
                'description'       => __( 'Top padding for content sections.', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 20,
                    'max'  => 160,
                    'step' => 10,
                ),
            ),
            'layout_section_padding_bottom' => array(
                'default'           => 80,
                'sanitize_callback' => 'absint',
                'section'           => 'opulentia_spacing',
                'label'             => __( 'Section Padding Bottom (px)', 'opulentia' ),
                'description'       => __( 'Bottom padding for content sections.', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 20,
                    'max'  => 160,
                    'step' => 10,
                ),
            ),

            // -----------------------------------------------------------------
            // WooCommerce — Product Catalog
            // -----------------------------------------------------------------
            'wc_product_columns' => array(
                'default'           => 4,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Product Grid Columns', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                    '5' => '5 Columns',
                    '6' => '6 Columns',
                ),
            ),
            'wc_products_per_page' => array(
                'default'           => 12,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Products Per Page', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 4,
                    'max'  => 48,
                    'step' => 4,
                ),
            ),
            'wc_product_card_shadow' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Product Card Shadow', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'wc_product_card_radius' => array(
                'default'           => '0',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Product Card Border Radius', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '0'   => '0px (Sharp)',
                    '4'   => '4px (Rounded)',
                    '8'   => '8px (Default)',
                    '12'  => '12px (More Rounded)',
                ),
            ),
            'wc_product_hover_effect' => array(
                'default'           => 'lift',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Product Card Hover Effect', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'lift'  => __( 'Lift Up', 'opulentia' ),
                    'zoom'  => __( 'Zoom Image', 'opulentia' ),
                    'none'  => __( 'None', 'opulentia' ),
                ),
            ),

            // -----------------------------------------------------------------
            // WooCommerce — Single Product
            // -----------------------------------------------------------------
            'wc_gallery_layout' => array(
                'default'           => 'stacked',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Gallery Layout', 'opulentia' ),
                'description'       => __( 'Stacked: vertical thumbnails below. Grid: side-by-side.', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'stacked' => __( 'Stacked', 'opulentia' ),
                    'grid'    => __( 'Grid', 'opulentia' ),
                ),
            ),
            'wc_gallery_zoom' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Gallery Image Zoom', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'wc_related_count' => array(
                'default'           => 4,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Related Products Count', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 1,
                    'max'  => 8,
                    'step' => 1,
                ),
            ),
            'wc_related_columns' => array(
                'default'           => 4,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Related Products Columns', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                    '5' => '5 Columns',
                    '6' => '6 Columns',
                ),
            ),

            // -----------------------------------------------------------------
            // WooCommerce — Cart & Checkout
            // -----------------------------------------------------------------
            'wc_cross_sells_columns' => array(
                'default'           => 2,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Cross-Sells Columns', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ),
            ),
            'wc_cross_sells_total' => array(
                'default'           => 4,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Cross-Sells Count', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 1,
                    'max'  => 8,
                    'step' => 1,
                ),
            ),

            // -----------------------------------------------------------------
            // WooCommerce — Quick View
            // -----------------------------------------------------------------
            'wc_enable_quick_view' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Enable Quick View', 'opulentia' ),
                'description'       => __( 'Quick view button on product cards.', 'opulentia' ),
                'type'              => 'checkbox',
            ),

            // -----------------------------------------------------------------
            // WooCommerce — Variation Swatches
            // -----------------------------------------------------------------
            'wc_enable_swatches' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Enable Variation Swatches', 'opulentia' ),
                'description'       => __( 'Replace dropdowns with color/image/label swatches on variable products.', 'opulentia' ),
                'type'              => 'checkbox',
            ),
            'wc_swatch_size' => array(
                'default'           => 30,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Swatch Size (px)', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array(
                    'min'  => 20,
                    'max'  => 50,
                    'step' => 2,
                ),
            ),
            'wc_swatch_style' => array(
                'default'           => 'rounded',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Swatch Style', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'rounded' => __( 'Rounded', 'opulentia' ),
                    'square'  => __( 'Square', 'opulentia' ),
                    'pill'    => __( 'Pill', 'opulentia' ),
                ),
            ),

            // -----------------------------------------------------------------
            // WooCommerce — Colors
            // -----------------------------------------------------------------
            'wc_sticky_add_to_cart' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Enable Sticky Add to Cart', 'opulentia' ),
                'description'       => __( 'Show a sticky add-to-cart bar at the bottom on single product pages.', 'opulentia' ),
                'type'              => 'checkbox',
            ),

            'color_wc_sale_badge' => array(
                'default'           => '#b8860b',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Sale Badge Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_wc_button' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Add to Cart Button Background', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_wc_button_text' => array(
                'default'           => '#ffffff',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Add to Cart Button Text', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_wc_rating_stars' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Rating Stars Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),
            'color_wc_price' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_wc',
                'label'             => __( 'Price Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
            ),

            // -----------------------------------------------------------------
            // Page Header / Banner
            // -----------------------------------------------------------------
            'page-header-enabled' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Enable Page Header', 'opulentia' ),
                'description'       => __( 'Show the page header banner on archive and singular pages.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 5,
            ),
            'page-header-bg-color' => array(
                'default'           => '#111111',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Background Color', 'opulentia' ),
                'description'       => __( 'Background color for the page header banner.', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 10,
            ),
            'page-header-overlay-color' => array(
                'default'           => 'rgba(0,0,0,0.5)',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Overlay Color', 'opulentia' ),
                'description'       => __( 'Accepts hex, rgb, or rgba.', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => 'rgba(0,0,0,0.5)' ),
                'priority'          => 15,
            ),
            'page-header-alignment' => array(
                'default'           => 'center',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Content Alignment', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'left'   => __( 'Left', 'opulentia' ),
                    'center' => __( 'Center', 'opulentia' ),
                    'right'  => __( 'Right', 'opulentia' ),
                ),
                'priority'          => 20,
            ),
            'page-header-show-breadcrumbs' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Show Breadcrumbs in Header', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 25,
            ),
            'page-header-padding-top' => array(
                'default'           => 100,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Padding Top (px)', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array( 'min' => 0, 'max' => 300, 'step' => 10 ),
                'priority'          => 30,
            ),
            'page-header-padding-bottom' => array(
                'default'           => 60,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Padding Bottom (px)', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array( 'min' => 0, 'max' => 300, 'step' => 10 ),
                'priority'          => 35,
            ),
            'page-header-bg-image' => array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Default Background Image', 'opulentia' ),
                'control'           => 'WP_Customize_Image_Control',
                'priority'          => 40,
            ),
            'page-header-home-title' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Homepage Title Override', 'opulentia' ),
                'description'       => __( 'Leave empty to use site title.', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 50,
            ),
            'page-header-blog-title' => array(
                'default'           => 'Blog',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Blog Page Title', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 55,
            ),
            'page-header-subtitle' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( 'Default Subtitle', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 60,
            ),
            'page-header-404-title' => array(
                'default'           => 'Page Not Found',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_page_header',
                'optionize'         => true,
                'label'             => __( '404 Page Title', 'opulentia' ),
                'type'              => 'text',
                'priority'          => 65,
            ),

            // -----------------------------------------------------------------
            // Breadcrumbs
            // -----------------------------------------------------------------
            'enable-breadcrumbs' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_breadcrumbs',
                'optionize'         => true,
                'label'             => __( 'Enable Breadcrumbs', 'opulentia' ),
                'description'       => __( 'Show breadcrumb navigation across the site.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 5,
            ),
            'breadcrumbs-position' => array(
                'default'           => 'page-header',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_breadcrumbs',
                'optionize'         => true,
                'label'             => __( 'Breadcrumb Position', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'page-header'   => __( 'Inside Page Header', 'opulentia' ),
                    'above-content' => __( 'Above Content', 'opulentia' ),
                    'both'          => __( 'Both', 'opulentia' ),
                ),
                'priority'          => 10,
            ),
            'breadcrumbs-separator' => array(
                'default'           => '/',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_breadcrumbs',
                'optionize'         => true,
                'label'             => __( 'Separator', 'opulentia' ),
                'type'              => 'text',
                'input_attrs'       => array( 'placeholder' => '/' ),
                'priority'          => 15,
            ),
            'breadcrumbs-color' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_breadcrumbs',
                'optionize'         => true,
                'label'             => __( 'Text Color', 'opulentia' ),
                'description'       => __( 'Leave empty to inherit.', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 20,
            ),
            'breadcrumbs-color-hover' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_breadcrumbs',
                'optionize'         => true,
                'label'             => __( 'Link Hover Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 25,
            ),
            'breadcrumbs-font-size' => array(
                'default'           => '13',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_breadcrumbs',
                'optionize'         => true,
                'label'             => __( 'Font Size (px)', 'opulentia' ),
                'type'              => 'select',
                'choices'           => $this->get_font_size_choices(),
                'priority'          => 30,
            ),

            // -----------------------------------------------------------------
            // Mega Menu
            // -----------------------------------------------------------------
            'enable-mega-menu' => array(
                'default'           => false,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_mega_menu',
                'optionize'         => true,
                'label'             => __( 'Enable Mega Menu', 'opulentia' ),
                'description'       => __( 'Transform the primary navigation.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 5,
            ),
            'mega-menu-animation' => array(
                'default'           => 'fade',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_mega_menu',
                'optionize'         => true,
                'label'             => __( 'Dropdown Animation', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'fade'  => __( 'Fade In', 'opulentia' ),
                    'slide' => __( 'Slide Down', 'opulentia' ),
                    'grow'  => __( 'Grow / Scale', 'opulentia' ),
                ),
                'priority'          => 10,
            ),

            // -----------------------------------------------------------------
            // Live Search
            // -----------------------------------------------------------------
            'enable-live-search' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_live_search',
                'optionize'         => true,
                'label'             => __( 'Enable Live Search', 'opulentia' ),
                'description'       => __( 'AJAX search with live results.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 5,
            ),
            'search-style' => array(
                'default'           => 'dropdown',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_live_search',
                'optionize'         => true,
                'label'             => __( 'Search Panel Style', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'dropdown'    => __( 'Dropdown', 'opulentia' ),
                    'slide'       => __( 'Slide In', 'opulentia' ),
                    'full-screen' => __( 'Full Screen Overlay', 'opulentia' ),
                ),
                'priority'          => 10,
            ),
            'live-search-count' => array(
                'default'           => 6,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_live_search',
                'optionize'         => true,
                'label'             => __( 'Max Results', 'opulentia' ),
                'description'       => __( 'Max search results.', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array( 'min' => 2, 'max' => 20, 'step' => 1 ),
                'priority'          => 15,
            ),

            // -----------------------------------------------------------------
            // Blog Pro
            // -----------------------------------------------------------------
            'blog-pro-read-time' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_blog_pro',
                'optionize'         => true,
                'label'             => __( 'Show Read Time', 'opulentia' ),
                'description'       => __( 'Display X min read on single posts.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 5,
            ),
            'blog-pro-wpm' => array(
                'default'           => 200,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_blog_pro',
                'optionize'         => true,
                'label'             => __( 'Words Per Minute', 'opulentia' ),
                'description'       => __( 'For read time.', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array( 'min' => 100, 'max' => 400, 'step' => 10 ),
                'priority'          => 10,
            ),
            'blog-pro-infinite-scroll' => array(
                'default'           => 'pagination',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog_pro',
                'optionize'         => true,
                'label'             => __( 'Pagination Style', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'pagination' => __( 'Standard Pagination', 'opulentia' ),
                    'button'     => __( 'Load More Button', 'opulentia' ),
                    'scroll'     => __( 'Infinite Scroll', 'opulentia' ),
                ),
                'priority'          => 15,
            ),
            'blog-related-filter' => array(
                'default'           => 'category',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_blog_pro',
                'optionize'         => true,
                'label'             => __( 'Related Posts Filter', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'category' => __( 'Category', 'opulentia' ),
                    'tag'      => __( 'Tag', 'opulentia' ),
                    'both'     => __( 'Category and Tag', 'opulentia' ),
                ),
                'priority'          => 20,
            ),
            'blog-related-show-image' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_blog_pro',
                'optionize'         => true,
                'label'             => __( 'Related: Show Image', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 25,
            ),
            'blog-related-show-date' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_blog_pro',
                'optionize'         => true,
                'label'             => __( 'Related: Show Date', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 30,
            ),

            // -----------------------------------------------------------------
            // Scroll to Top
            // -----------------------------------------------------------------
            'enable-scroll-to-top' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_scroll_to_top',
                'optionize'         => true,
                'label'             => __( 'Enable Scroll to Top', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 5,
            ),
            'scroll-to-top-position' => array(
                'default'           => 'right',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_scroll_to_top',
                'optionize'         => true,
                'label'             => __( 'Button Position', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'left'  => __( 'Left', 'opulentia' ),
                    'right' => __( 'Right', 'opulentia' ),
                ),
                'priority'          => 10,
            ),
            'scroll-to-top-threshold' => array(
                'default'           => 300,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_scroll_to_top',
                'optionize'         => true,
                'label'             => __( 'Scroll Threshold (px)', 'opulentia' ),
                'description'       => __( 'Pixels before button appears.', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array( 'min' => 50, 'max' => 2000, 'step' => 50 ),
                'priority'          => 15,
            ),
            'scroll-to-top-icon' => array(
                'default'           => 'chevron-up',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_scroll_to_top',
                'optionize'         => true,
                'label'             => __( 'Icon Type', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'chevron-up' => __( 'Chevron Up', 'opulentia' ),
                    'arrow-up'   => __( 'Arrow Up', 'opulentia' ),
                ),
                'priority'          => 20,
            ),

            // -----------------------------------------------------------------
            // Dark Mode
            // -----------------------------------------------------------------
            'dark-mode-mode' => array(
                'default'           => 'off',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_dark_mode',
                'optionize'         => true,
                'label'             => __( 'Dark Mode Mode', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'off'    => __( 'Disabled', 'opulentia' ),
                    'auto'   => __( 'Auto (OS Preference)', 'opulentia' ),
                    'manual' => __( 'Manual Toggle', 'opulentia' ),
                ),
                'priority'          => 5,
            ),
            'dark-mode-bg-color' => array(
                'default'           => '#0a0a0a',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_dark_mode',
                'optionize'         => true,
                'label'             => __( 'Background Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 10,
            ),
            'dark-mode-text-color' => array(
                'default'           => '#e0e0e0',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_dark_mode',
                'optionize'         => true,
                'label'             => __( 'Text Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 15,
            ),
            'dark-mode-link-color' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_dark_mode',
                'optionize'         => true,
                'label'             => __( 'Link Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 20,
            ),
            'dark-mode-heading-color' => array(
                'default'           => '#ffffff',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_dark_mode',
                'optionize'         => true,
                'label'             => __( 'Heading Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 25,
            ),
            'dark-mode-border-color' => array(
                'default'           => '#2a2a2a',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_dark_mode',
                'optionize'         => true,
                'label'             => __( 'Border Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 30,
            ),
            'dark-mode-image-brightness' => array(
                'default'           => 85,
                'sanitize_callback' => 'absint',
                'section'           => 'Opulentia_dark_mode',
                'optionize'         => true,
                'label'             => __( 'Image Brightness (%)', 'opulentia' ),
                'description'       => __( 'Lower = dimmer. 100 = no change.', 'opulentia' ),
                'type'              => 'number',
                'input_attrs'       => array( 'min' => 30, 'max' => 100, 'step' => 5 ),
                'priority'          => 35,
            ),

            // -----------------------------------------------------------------
            // Accessibility
            // -----------------------------------------------------------------
            'enable-accessibility' => array(
                'default'           => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'section'           => 'Opulentia_accessibility',
                'optionize'         => true,
                'label'             => __( 'Enable Accessibility', 'opulentia' ),
                'description'       => __( 'Focus outlines, screen reader text, ARIA, skip link.', 'opulentia' ),
                'type'              => 'checkbox',
                'priority'          => 5,
            ),
            'accessibility-outline-style' => array(
                'default'           => 'solid',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_accessibility',
                'optionize'         => true,
                'label'             => __( 'Focus Outline Style', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'solid'  => __( 'Solid', 'opulentia' ),
                    'dashed' => __( 'Dashed', 'opulentia' ),
                    'dotted' => __( 'Dotted', 'opulentia' ),
                ),
                'priority'          => 10,
            ),
            'accessibility-outline-color' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_accessibility',
                'optionize'         => true,
                'label'             => __( 'Focus Outline Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 15,
            ),
            'accessibility-input-style' => array(
                'default'           => 'solid',
                'sanitize_callback' => 'sanitize_text_field',
                'section'           => 'Opulentia_accessibility',
                'optionize'         => true,
                'label'             => __( 'Input Focus Style', 'opulentia' ),
                'type'              => 'select',
                'choices'           => array(
                    'solid'    => __( 'Solid', 'opulentia' ),
                    'dashed'   => __( 'Dashed', 'opulentia' ),
                    'dotted'   => __( 'Dotted', 'opulentia' ),
                    'disabled' => __( 'Disabled', 'opulentia' ),
                ),
                'priority'          => 20,
            ),
            'accessibility-input-color' => array(
                'default'           => '#c9a96e',
                'sanitize_callback' => 'sanitize_hex_color',
                'section'           => 'Opulentia_accessibility',
                'optionize'         => true,
                'label'             => __( 'Input Focus Color', 'opulentia' ),
                'control'           => 'WP_Customize_Color_Control',
                'priority'          => 25,
            ),

        );
        return $settings;
    }
}

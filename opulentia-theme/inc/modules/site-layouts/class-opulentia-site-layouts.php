<?php
/**
 * Site Layouts Module — Singleton
 *
 * Provides full-width, boxed (contained), and padded layout options
 * for the site container, with per-page meta box overrides.
 *
 * Integrates with the Dynamic CSS engine and Theme Customizer.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Site_Layouts class.
 */
class Opulentia_Site_Layouts {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

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
     *
     * CSS flows through the dynamic CSS engine via container-layouts.php delegation.
     */
    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer_section' ), 20 );
        add_filter( 'body_class', array( $this, 'body_classes' ) );

        // Per-page meta box override.
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
    }

    // -------------------------------------------------------------------------
    // Customizer Section — Self-Registered
    // -------------------------------------------------------------------------

    /**
     * Register customizer section + settings for site layouts.
     *
     * Also cleans up legacy layout settings that are now managed here.
     *
     * @param WP_Customize_Manager $wp_customize Customizer manager instance.
     */
    public function register_customizer_section( $wp_customize ) {
        // Clean up legacy settings now managed by this module.
        $legacy_settings = array(
            'layout_content_layout',
            'layout_container_max',
        );
        foreach ( $legacy_settings as $legacy_id ) {
            $wp_customize->remove_setting( $legacy_id );
        }

        // Section.
        $wp_customize->add_section( 'Opulentia_site_layouts', array(
            'title'    => __( 'Site Layout', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 35,
        ) );

        // Site-Wide Layout.
        $wp_customize->add_setting( 'site_layout_type', array(
            'default'           => 'boxed',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'site_layout_type', array(
            'label'       => __( 'Site Layout', 'opulentia' ),
            'description' => __( 'Choose how the main site container is laid out.', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'select',
            'choices'     => array(
                'boxed'      => __( 'Boxed (Contained)', 'opulentia' ),
                'full-width' => __( 'Full Width (Edge to Edge)', 'opulentia' ),
                'padded'     => __( 'Padded (Max-width + Side Padding)', 'opulentia' ),
            ),
            'priority'    => 10,
        ) );

        // Container Max Width.
        $wp_customize->add_setting( 'site_container_max_width', array(
            'default'           => '1200px',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'site_container_max_width', array(
            'label'       => __( 'Container Max Width', 'opulentia' ),
            'description' => __( 'Maximum width of the main content container.', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'select',
            'choices'     => array(
                '960px'  => '960px',
                '1100px' => '1100px',
                '1200px' => '1200px (Default)',
                '1280px' => '1280px',
                '1400px' => '1400px',
            ),
            'priority'    => 20,
        ) );

        // Container Wide Max Width.
        $wp_customize->add_setting( 'site_container_wide_max_width', array(
            'default'           => '1400px',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'site_container_wide_max_width', array(
            'label'       => __( 'Container Wide Max Width', 'opulentia' ),
            'description' => __( 'Maximum width of the .container--wide variant for full-width sections.', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'select',
            'choices'     => array(
                '960px'   => '960px',
                '1100px'  => '1100px',
                '1200px'  => '1200px',
                '1280px'  => '1280px',
                '1400px'  => '1400px (Default)',
                '1500px'  => '1500px',
                '1600px'  => '1600px',
                '1700px'  => '1700px',
                '1800px'  => '1800px',
            ),
            'priority'    => 25,
        ) );

        // Container Side Padding.
        $wp_customize->add_setting( 'site_container_padding', array(
            'default'           => 24,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'site_container_padding', array(
            'label'       => __( 'Container Side Padding (px)', 'opulentia' ),
            'description' => __( 'Left/right padding for the container on desktop.', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 80,
                'step' => 4,
            ),
            'priority'    => 30,
        ) );

        // Boxed Content Background.
        $wp_customize->add_setting( 'site_boxed_content_bg', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize,
            'site_boxed_content_bg',
            array(
                'label'       => __( 'Boxed: Content Background', 'opulentia' ),
                'description' => __( 'Background color for the content area in boxed mode. Leave empty to inherit.', 'opulentia' ),
                'section'     => 'Opulentia_site_layouts',
                'priority'    => 40,
            )
        ) );

        // Boxed Shadow.
        $wp_customize->add_setting( 'site_boxed_shadow', array(
            'default'           => true,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'site_boxed_shadow', array(
            'label'       => __( 'Boxed: Show Content Shadow', 'opulentia' ),
            'description' => __( 'Add a shadow around the content area in boxed layout.', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'checkbox',
            'priority'    => 45,
        ) );

        // Content Top Padding.
        $wp_customize->add_setting( 'site_content_padding_top', array(
            'default'           => 80,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'site_content_padding_top', array(
            'label'       => __( 'Content Area: Top Padding (px)', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 200,
                'step' => 10,
            ),
            'priority'    => 50,
        ) );

        // Content Bottom Padding.
        $wp_customize->add_setting( 'site_content_padding_bottom', array(
            'default'           => 80,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'site_content_padding_bottom', array(
            'label'       => __( 'Content Area: Bottom Padding (px)', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 200,
                'step' => 10,
            ),
            'priority'    => 60,
        ) );

        // Enable Per-Page Overrides.
        $wp_customize->add_setting( 'site_enable_page_overrides', array(
            'default'           => true,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
        ) );
        $wp_customize->add_control( 'site_enable_page_overrides', array(
            'label'       => __( 'Enable Per-Page Layout Overrides', 'opulentia' ),
            'description' => __( 'Add a meta box on pages/posts to override layout for individual content.', 'opulentia' ),
            'section'     => 'Opulentia_site_layouts',
            'type'        => 'checkbox',
            'priority'    => 70,
        ) );
    }

    /**
     * Sanitize checkbox values.
     *
     * @param  mixed $value Input value.
     * @return bool
     */
    public function sanitize_checkbox( $value ) {
        return (bool) $value;
    }

    // -------------------------------------------------------------------------
    // Page Meta Box — Per-Page Layout Override
    // -------------------------------------------------------------------------

    /**
     * Add layout override meta box to pages and posts.
     */
    public function add_meta_box() {
        if ( ! get_theme_mod( 'site_enable_page_overrides', true ) ) {
            return;
        }

        $post_types = apply_filters( 'Opulentia_site_layout_post_types', array( 'page', 'post' ) );

        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'Opulentia_site_layout',
                __( 'Site Layout Override', 'opulentia' ),
                array( $this, 'render_meta_box' ),
                $post_type,
                'side',
                'default'
            );
        }
    }

    /**
     * Render the meta box fields.
     *
     * @param WP_Post $post Current post object.
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'Opulentia_site_layout_meta', 'Opulentia_site_layout_nonce' );

        $current_layout = get_post_meta( $post->ID, '_Opulentia_site_layout', true );
        if ( empty( $current_layout ) ) {
            $current_layout = 'default';
        }
        ?>
        <p>
            <label for="opulentia-site-layout-override">
                <?php esc_html_e( 'Override Site Layout:', 'opulentia' ); ?>
            </label>
        </p>
        <select id="opulentia-site-layout-override" name="_Opulentia_site_layout" style="width: 100%;">
            <option value="default" <?php selected( $current_layout, 'default' ); ?>>
                <?php esc_html_e( 'Default (Inherit from Customizer)', 'opulentia' ); ?>
            </option>
            <option value="boxed" <?php selected( $current_layout, 'boxed' ); ?>>
                <?php esc_html_e( 'Boxed (Contained)', 'opulentia' ); ?>
            </option>
            <option value="full-width" <?php selected( $current_layout, 'full-width' ); ?>>
                <?php esc_html_e( 'Full Width', 'opulentia' ); ?>
            </option>
            <option value="padded" <?php selected( $current_layout, 'padded' ); ?>>
                <?php esc_html_e( 'Padded', 'opulentia' ); ?>
            </option>
        </select>
        <p class="description" style="margin-top: 8px;">
            <?php esc_html_e( 'Override the global site layout for this content only.', 'opulentia' ); ?>
        </p>
        <?php
    }

    /**
     * Save the meta box value.
     *
     * @param int $post_id Post ID.
     */
    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['Opulentia_site_layout_nonce'] ) ||
             ! wp_verify_nonce( $_POST['Opulentia_site_layout_nonce'], 'Opulentia_site_layout_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['_Opulentia_site_layout'] ) &&
             in_array( $_POST['_Opulentia_site_layout'], array( 'default', 'boxed', 'full-width', 'padded' ), true ) ) {
            $layout = sanitize_text_field( $_POST['_Opulentia_site_layout'] );
            if ( 'default' === $layout ) {
                delete_post_meta( $post_id, '_Opulentia_site_layout' );
            } else {
                update_post_meta( $post_id, '_Opulentia_site_layout', $layout );
            }
        }
    }

    /**
     * Get the effective layout for the current page/query.
     *
     * Checks per-page override first, then falls back to global customizer setting.
     *
     * @return string 'boxed', 'full-width', or 'padded'.
     */
    public function get_effective_layout() {
        $global = get_theme_mod( 'site_layout_type', 'boxed' );

        if ( get_theme_mod( 'site_enable_page_overrides', true ) && is_singular() ) {
            $override = get_post_meta( get_the_ID(), '_Opulentia_site_layout', true );
            if ( ! empty( $override ) ) {
                return $override;
            }
        }

        return $global;
    }

    // -------------------------------------------------------------------------
    // Body Classes
    // -------------------------------------------------------------------------

    /**
     * Add layout body classes.
     *
     * @param  array $classes Existing body classes.
     * @return array Modified body classes.
     */
    public function body_classes( $classes ) {
        $layout = $this->get_effective_layout();

        $classes[] = 'layout-' . sanitize_html_class( $layout );

        if ( 'boxed' === $layout ) {
            $has_shadow = get_theme_mod( 'site_boxed_shadow', true );
            if ( $has_shadow ) {
                $classes[] = 'layout-boxed-shadow';
            }
        }

        return $classes;
    }

    // -------------------------------------------------------------------------
    // Dynamic CSS
    // -------------------------------------------------------------------------

    /**
     * Generate site layout CSS from customizer settings.
     *
     * Called by the dynamic CSS engine via container-layouts.php delegation.
     * Follows the same single-path pattern as the Spacing module.
     *
     * @return string CSS string.
     */
    public function get_site_layout_css() {
        $css         = '';
        $layout      = $this->get_effective_layout();
        $max_width   = get_theme_mod( 'site_container_max_width', '1200px' );
        $padding     = (int) get_theme_mod( 'site_container_padding', 24 );
        $content_top = (int) get_theme_mod( 'site_content_padding_top', 80 );
        $content_bot = (int) get_theme_mod( 'site_content_padding_bottom', 80 );
        $boxed_bg    = get_theme_mod( 'site_boxed_content_bg', '' );
        $has_shadow  = get_theme_mod( 'site_boxed_shadow', true );

        // Container :root variable override.
        $wide_max_width = get_theme_mod( 'site_container_wide_max_width', '1400px' );

        $css .= ".layout-boxed,\n";
        $css .= ".layout-full-width,\n";
        $css .= ".layout-padded {\n";
        $css .= "    --container-max: {$max_width};\n";
        $css .= "    --container-wide: {$wide_max_width};\n";
        $css .= "}\n\n";

        // Container padding override.
        if ( 24 !== $padding ) {
            $css .= ".container {\n";
            $css .= "    padding-left: {$padding}px;\n";
            $css .= "    padding-right: {$padding}px;\n";
            $css .= "}\n\n";
        }

        // Content area padding.
        $content_selectors = '.site-main, .content-sidebar-layout, .page-header + .site-main';

        if ( 80 !== $content_top ) {
            $css .= "{$content_selectors} {\n";
            $css .= "    padding-top: {$content_top}px;\n";
            $css .= "}\n\n";
        }
        if ( 80 !== $content_bot ) {
            $css .= "{$content_selectors} {\n";
            $css .= "    padding-bottom: {$content_bot}px;\n";
            $css .= "}\n\n";
        }

        // Full-width layout.
        if ( 'full-width' === $layout ) {
            $css .= ".layout-full-width .container {\n";
            $css .= "    max-width: 100%;\n";
            $css .= "    padding-left: {$padding}px;\n";
            $css .= "    padding-right: {$padding}px;\n";
            $css .= "}\n\n";

            $css .= ".layout-full-width .site-content > .container--full,\n";
            $css .= ".layout-full-width .site-content > [class*=\"__grid\"] {\n";
            $css .= "    max-width: 100%;\n";
            $css .= "}\n\n";
        }

        // Padded layout.
        if ( 'padded' === $layout ) {
            $padded_padding = max( $padding, 40 );
            $css .= ".layout-padded .container {\n";
            $css .= "    max-width: {$max_width};\n";
            $css .= "    padding-left: {$padded_padding}px;\n";
            $css .= "    padding-right: {$padded_padding}px;\n";
            $css .= "}\n\n";
        }

        // Boxed layout content background.
        if ( 'boxed' === $layout && ! empty( $boxed_bg ) ) {
            $css .= ".layout-boxed .site-content {\n";
            $css .= "    background-color: {$boxed_bg};\n";
            $css .= "}\n\n";
        }

        // Boxed shadow.
        if ( 'boxed' === $layout && $has_shadow ) {
            $css .= ".layout-boxed .site-content {\n";
            $css .= "    box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);\n";
            $css .= "}\n\n";
        }

        // Responsive: reduce side padding on mobile.
        $mobile_padding = max( 16, (int) $padding / 2 );
        $css .= "@media (max-width: 576px) {\n";
        $css .= "    .container {\n";
        $css .= "        padding-left: {$mobile_padding}px;\n";
        $css .= "        padding-right: {$mobile_padding}px;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        return $css;
    }
}

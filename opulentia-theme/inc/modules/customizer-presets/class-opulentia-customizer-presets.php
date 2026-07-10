<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Customizer_Presets {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'customize_register' ), 100 );
        add_action( 'wp_ajax_opulentia_apply_preset', array( $this, 'ajax_apply_preset' ) );
        add_action( 'wp_ajax_opulentia_save_preset', array( $this, 'ajax_save_preset' ) );
        add_action( 'wp_ajax_opulentia_export_presets', array( $this, 'ajax_export_presets' ) );
        add_action( 'wp_ajax_opulentia_import_presets', array( $this, 'ajax_import_presets' ) );
        add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'controls_enqueue' ) );
    }

    public function controls_enqueue() {
        wp_enqueue_script(
            'opulentia-preset-preview',
            Opulentia_URI . '/js/customizer-presets.js',
            array( 'jquery', 'customize-controls' ),
            Opulentia_VERSION,
            true
        );

        wp_localize_script( 'opulentia-preset-preview', 'OpulentiaPresets', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'opulentia_presets_nonce' ),
            'presets' => $this->get_presets(),
        ) );
    }

    public function customize_preview_js() {
        wp_enqueue_script(
            'opulentia-preset-live',
            Opulentia_URI . '/js/customizer-presets-live.js',
            array( 'jquery', 'customize-preview' ),
            Opulentia_VERSION,
            true
        );
    }

    public function customize_register( $wp_customize ) {
        $wp_customize->add_panel( 'opulentia_presets', array(
            'title'       => __( 'Presets', 'opulentia' ),
            'description' => __( 'Save, apply, import, and export full customizer presets.', 'opulentia' ),
            'priority'    => 5,
        ) );

        // ── Apply Presets ──
        $wp_customize->add_section( 'op_presets_apply', array(
            'title'       => __( 'Apply Preset', 'opulentia' ),
            'panel'       => 'opulentia_presets',
            'priority'    => 1,
        ) );

        $wp_customize->add_setting( 'op_preset_apply', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( 'op_preset_apply', array(
            'label'       => __( 'Choose Preset', 'opulentia' ),
            'description' => __( 'Select a preset and click Apply to instantly change your site appearance.', 'opulentia' ),
            'section'     => 'op_presets_apply',
            'type'        => 'select',
            'choices'     => $this->get_preset_choices(),
            'input_attrs' => array(
                'data-preset-apply' => '1',
            ),
        ) );

        // ── Save Preset ──
        $wp_customize->add_section( 'op_presets_save', array(
            'title'       => __( 'Save Preset', 'opulentia' ),
            'panel'       => 'opulentia_presets',
            'priority'    => 5,
        ) );

        $wp_customize->add_setting( 'op_preset_save_name', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( 'op_preset_save_name', array(
            'label'       => __( 'Preset Name', 'opulentia' ),
            'section'     => 'op_presets_save',
            'type'        => 'text',
        ) );
    }

    private function get_preset_choices() {
        $presets = $this->get_presets();
        $choices = array( '' => __( '— Select —', 'opulentia' ) );

        foreach ( $presets as $slug => $preset ) {
            $choices[ $slug ] = $preset['name'];
        }

        return $choices;
    }

    public function get_presets() {
        $saved = get_option( 'opulentia_custom_presets', array() );
        $built_in = $this->get_built_in_presets();

        return array_merge( $built_in, $saved );
    }

    private function get_built_in_presets() {
        return array(
            'dark-luxury' => array(
                'name'        => __( 'Dark Luxury', 'opulentia' ),
                'description' => __( 'Rich dark tones with gold accents — the signature Opulentia look.', 'opulentia' ),
                'preview'     => '',
                'settings'    => $this->preset_settings( array(
                    '--color-primary-dark'   => '#1a1a1a',
                    '--color-secondary-dark' => '#111',
                    '--color-accent'         => '#b8860b',
                    '--color-accent-hover'   => '#d4a843',
                    '--color-gold'           => '#c9a96e',
                    '--color-light-gold'     => '#e8d5a3',
                    '--color-text'           => '#f5f5f5',
                    '--color-text-muted'     => '#999',
                    '--color-border'         => '#333',
                ) ),
            ),
            'light-elegance' => array(
                'name'        => __( 'Light Elegance', 'opulentia' ),
                'description' => __( 'Clean light background with warm gold accents.', 'opulentia' ),
                'preview'     => '',
                'settings'    => $this->preset_settings( array(
                    '--color-primary-dark'   => '#ffffff',
                    '--color-secondary-dark' => '#f8f8f8',
                    '--color-accent'         => '#b8860b',
                    '--color-accent-hover'   => '#d4a843',
                    '--color-gold'           => '#b8860b',
                    '--color-light-gold'     => '#d4a843',
                    '--color-text'           => '#1a1a1a',
                    '--color-text-muted'     => '#666',
                    '--color-border'         => '#e0e0e0',
                ) ),
            ),
            'midnight-blue' => array(
                'name'        => __( 'Midnight Blue', 'opulentia' ),
                'description' => __( 'Deep navy blue palette with silver accents.', 'opulentia' ),
                'preview'     => '',
                'settings'    => $this->preset_settings( array(
                    '--color-primary-dark'   => '#0a1628',
                    '--color-secondary-dark' => '#0d1f3c',
                    '--color-accent'         => '#4a7cbd',
                    '--color-accent-hover'   => '#5a8cd0',
                    '--color-gold'           => '#8ab4f8',
                    '--color-light-gold'     => '#a8c8fa',
                    '--color-text'           => '#e8f0fe',
                    '--color-text-muted'     => '#7a9bcb',
                    '--color-border'         => '#1a3555',
                ) ),
            ),
            'forest-green' => array(
                'name'        => __( 'Forest Green', 'opulentia' ),
                'description' => __( 'Earthy green tones with warm amber accents.', 'opulentia' ),
                'preview'     => '',
                'settings'    => $this->preset_settings( array(
                    '--color-primary-dark'   => '#0f1f12',
                    '--color-secondary-dark' => '#162a1a',
                    '--color-accent'         => '#7a9e3a',
                    '--color-accent-hover'   => '#8db84a',
                    '--color-gold'           => '#c4a35a',
                    '--color-light-gold'     => '#d4c07a',
                    '--color-text'           => '#eaf5e5',
                    '--color-text-muted'     => '#8aaa7a',
                    '--color-border'         => '#2a4a2e',
                ) ),
            ),
            'rose-gold' => array(
                'name'        => __( 'Rose Gold', 'opulentia' ),
                'description' => __( 'Warm rose and copper tones for a luxurious feel.', 'opulentia' ),
                'preview'     => '',
                'settings'    => $this->preset_settings( array(
                    '--color-primary-dark'   => '#1a1215',
                    '--color-secondary-dark' => '#2a1a1e',
                    '--color-accent'         => '#d4a0a0',
                    '--color-accent-hover'   => '#e0b5b5',
                    '--color-gold'           => '#c9a0a0',
                    '--color-light-gold'     => '#e0c0c0',
                    '--color-text'           => '#f5e8ea',
                    '--color-text-muted'     => '#b09095',
                    '--color-border'         => '#3a2528',
                ) ),
            ),
            'ocean-deep' => array(
                'name'        => __( 'Ocean Deep', 'opulentia' ),
                'description' => __( 'Deep teal and ocean blues with coral accents.', 'opulentia' ),
                'preview'     => '',
                'settings'    => $this->preset_settings( array(
                    '--color-primary-dark'   => '#0a1a1e',
                    '--color-secondary-dark' => '#0e242a',
                    '--color-accent'         => '#2a9d8f',
                    '--color-accent-hover'   => '#3ab5a5',
                    '--color-gold'           => '#e9c46a',
                    '--color-light-gold'     => '#f0d48a',
                    '--color-text'           => '#e0f0f0',
                    '--color-text-muted'     => '#7aa8a8',
                    '--color-border'         => '#1a3a40',
                ) ),
            ),
        );
    }

    private function preset_settings( $colors ) {
        return array(
            // ── Base Colors ──
            'op_site_bg_color'          => $colors['--color-primary-dark'],
            'op_container_bg_color'     => $colors['--color-secondary-dark'],
            'op_accent_color'           => $colors['--color-accent'],
            'op_accent_hover_color'     => $colors['--color-accent-hover'],
            'op_text_color'             => $colors['--color-text'],
            'op_text_muted_color'       => $colors['--color-text-muted'],
            'op_border_color'           => $colors['--color-border'],
            'op_headings_color'         => $colors['--color-gold'],
            'op_headings_hover_color'   => $colors['--color-light-gold'],
            'op_header_bg_color'        => $colors['--color-primary-dark'],
            'op_footer_bg_color'        => $colors['--color-secondary-dark'],
            // ── Layout ──
            'container_width'           => 1200,
            'site_layout'               => 'full-width',
            // ── Animations ──
            'op_animations_enable'      => true,
            'op_anim_reveal_effect'     => 'fade',
            'op_anim_reveal_direction'  => 'up',
            // ── Misc ──
            'blog_layout'               => 'grid',
            'wc_product_columns'        => 4,
        );
    }

    public function ajax_apply_preset() {
        check_ajax_referer( 'opulentia_presets_nonce', 'nonce' );

        $slug = sanitize_text_field( wp_unslash( $_POST['preset'] ) );
        $presets = $this->get_presets();

        if ( ! isset( $presets[ $slug ] ) ) {
            wp_send_json_error( array( 'message' => __( 'Preset not found.', 'opulentia' ) ) );
        }

        $settings = $presets[ $slug ]['settings'];

        foreach ( $settings as $key => $value ) {
            set_theme_mod( $key, $value );
        }

        wp_send_json_success( array(
            'message' => sprintf(
                __( 'Preset "%s" applied successfully.', 'opulentia' ),
                $presets[ $slug ]['name']
            ),
        ) );
    }

    public function ajax_save_preset() {
        check_ajax_referer( 'opulentia_presets_nonce', 'nonce' );

        $name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
        if ( empty( $name ) ) {
            wp_send_json_error( array( 'message' => __( 'Preset name is required.', 'opulentia' ) ) );
        }

        $slug = sanitize_title( $name );
        $settings = $this->capture_current_settings();

        $saved = get_option( 'opulentia_custom_presets', array() );

        $saved[ $slug ] = array(
            'name'        => $name,
            'description' => __( 'Custom saved preset.', 'opulentia' ),
            'preview'     => '',
            'settings'    => $settings,
        );

        update_option( 'opulentia_custom_presets', $saved );

        wp_send_json_success( array(
            'message' => sprintf( __( 'Preset "%s" saved.', 'opulentia' ), $name ),
            'slug'    => $slug,
        ) );
    }

    public function ajax_export_presets() {
        check_ajax_referer( 'opulentia_presets_nonce', 'nonce' );

        $presets = $this->get_presets();
        $export  = array();

        foreach ( $presets as $slug => $preset ) {
            $export[ $slug ] = array(
                'name'        => $preset['name'],
                'description' => $preset['description'],
                'settings'    => $preset['settings'],
            );
        }

        $filename = 'opulentia-presets-' . date( 'Y-m-d' ) . '.json';

        wp_send_json_success( array(
            'filename' => $filename,
            'data'     => wp_json_encode( $export, JSON_PRETTY_PRINT ),
        ) );
    }

    public function ajax_import_presets() {
        check_ajax_referer( 'opulentia_presets_nonce', 'nonce' );

        if ( ! isset( $_FILES['file'] ) ) {
            wp_send_json_error( array( 'message' => __( 'No file uploaded.', 'opulentia' ) ) );
        }

        $file = $_FILES['file'];
        if ( 'application/json' !== $file['type'] && 'text/plain' !== $file['type'] ) {
            $wp_filetype = wp_check_filetype( $file['name'] );
            if ( 'json' !== $wp_filetype['ext'] ) {
                wp_send_json_error( array( 'message' => __( 'Invalid file type. Please upload a JSON file.', 'opulentia' ) ) );
            }
        }

        $content = file_get_contents( $file['tmp_name'] );
        $data    = json_decode( $content, true );

        if ( ! is_array( $data ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid JSON format.', 'opulentia' ) ) );
        }

        $saved = get_option( 'opulentia_custom_presets', array() );

        foreach ( $data as $slug => $preset ) {
            if ( isset( $preset['name'], $preset['settings'] ) && is_array( $preset['settings'] ) ) {
                $saved[ sanitize_title( $slug ) ] = array(
                    'name'        => sanitize_text_field( $preset['name'] ),
                    'description' => isset( $preset['description'] ) ? sanitize_text_field( $preset['description'] ) : '',
                    'preview'     => '',
                    'settings'    => $preset['settings'],
                );
            }
        }

        update_option( 'opulentia_custom_presets', $saved );

        wp_send_json_success( array(
            'message' => __( 'Presets imported successfully.', 'opulentia' ),
        ) );
    }

    private function capture_current_settings() {
        $theme_mods = get_theme_mods();
        $keys = array_keys( $this->preset_settings( array(
            '--color-primary-dark'   => '#1a1a1a',
            '--color-secondary-dark' => '#111',
            '--color-accent'         => '#b8860b',
            '--color-accent-hover'   => '#d4a843',
            '--color-gold'           => '#c9a96e',
            '--color-light-gold'     => '#e8d5a3',
            '--color-text'           => '#f5f5f5',
            '--color-text-muted'     => '#999',
            '--color-border'         => '#333',
        ) ) );

        $settings = array();
        foreach ( $keys as $key ) {
            if ( isset( $theme_mods[ $key ] ) ) {
                $settings[ $key ] = $theme_mods[ $key ];
            }
        }

        return $settings;
    }
}

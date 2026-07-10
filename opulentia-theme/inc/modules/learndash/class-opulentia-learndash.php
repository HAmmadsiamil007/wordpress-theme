<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_LearnDash {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( ! defined( 'LEARNDASH_VERSION' ) ) {
            return;
        }
        add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
        add_filter( 'body_class', array( $this, 'body_class' ) );
        add_filter( 'learndash_course_grid_columns', array( $this, 'grid_columns' ) );
    }

    public function add_theme_support() {
        add_theme_support( 'learndash-theme-support' );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_learndash', array(
            'title'    => __( 'LearnDash', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 86,
        ) );

        $wp_customize->add_setting( 'learndash-course-columns', array(
            'default'           => 3,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'learndash-course-columns', array(
            'label'       => __( 'Course Grid Columns', 'opulentia' ),
            'section'     => 'opulentia_learndash',
            'type'        => 'select',
            'choices'     => array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ),
        ) );

        $wp_customize->add_setting( 'learndash-header-color', array(
            'default'           => 'var(--color-gold)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'learndash-header-color', array(
            'label'       => __( 'Course / Lesson Title Color', 'opulentia' ),
            'section'     => 'opulentia_learndash',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
        ) );

        $wp_customize->add_setting( 'learndash-accent-color', array(
            'default'           => 'var(--color-gold)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'learndash-accent-color', array(
            'label'       => __( 'Accent Color (Buttons, Progress)', 'opulentia' ),
            'section'     => 'opulentia_learndash',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
        ) );

        $wp_customize->add_setting( 'learndash-card-bg', array(
            'default'           => 'var(--color-secondary-dark)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'learndash-card-bg', array(
            'label'       => __( 'Course Card Background', 'opulentia' ),
            'section'     => 'opulentia_learndash',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-secondary-dark)' ),
        ) );

        $wp_customize->add_setting( 'learndash-focus-mode', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'learndash-focus-mode', array(
            'label'       => __( 'Enable Focus Mode', 'opulentia' ),
            'section'     => 'opulentia_learndash',
            'type'        => 'checkbox',
        ) );

        $wp_customize->add_setting( 'learndash-hide-breadcrumbs', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'learndash-hide-breadcrumbs', array(
            'label'       => __( 'Hide Breadcrumbs on Course Pages', 'opulentia' ),
            'section'     => 'opulentia_learndash',
            'type'        => 'checkbox',
        ) );
    }

    public function grid_columns( $columns ) {
        $cols = Opulentia_get_option( 'learndash-course-columns', 3 );
        return absint( $cols );
    }

    public function body_class( $classes ) {
        if ( defined( 'LEARNDASH_VERSION' ) ) {
            $classes[] = 'learndash-themed';
            if ( Opulentia_get_option( 'learndash-focus-mode', true ) ) {
                $classes[] = 'learndash-focus-enabled';
            }
        }
        return $classes;
    }

    public function inline_css() {
        if ( ! defined( 'LEARNDASH_VERSION' ) ) {
            return;
        }

        $header_color = Opulentia_get_option( 'learndash-header-color', 'var(--color-gold)' );
        $accent_color = Opulentia_get_option( 'learndash-accent-color', 'var(--color-gold)' );
        $card_bg      = Opulentia_get_option( 'learndash-card-bg', 'var(--color-secondary-dark)' );

        $css = '
        .learndash-themed .ld-course-list-items .ld_course_grid .thumbnail.course {
            border: 1px solid var(--color-border);
            background: ' . $card_bg . ';
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.3s ease, transform 0.3s ease;
        }
        .learndash-themed .ld-course-list-items .ld_course_grid .thumbnail.course:hover {
            border-color: ' . $accent_color . ';
            transform: translateY(-2px);
        }
        .learndash-themed .ld-course-list-items .ld_course_grid .thumbnail.course .caption {
            padding: 20px;
        }
        .learndash-themed .ld-course-list-items .ld_course_grid .thumbnail.course .caption h3 {
            font-family: var(--font-heading);
            color: ' . $header_color . ';
        }
        .learndash-themed .ld_course_grid .btn-primary {
            background: ' . $accent_color . ' !important;
            border-color: ' . $accent_color . ' !important;
            color: #fff !important;
        }
        .learndash-themed .ld_course_grid .btn-primary:hover {
            opacity: 0.85;
        }
        .learndash-themed .learndash-wrapper .ld-focus {
            background: var(--color-primary-dark);
        }
        .learndash-themed .learndash-wrapper .ld-focus .ld-focus-sidebar {
            background: ' . $card_bg . ';
            border-right: 1px solid var(--color-border);
        }
        .learndash-themed .learndash-wrapper .ld-focus .ld-focus-header {
            background: ' . $card_bg . ';
            border-bottom: 1px solid var(--color-border);
        }
        .learndash-themed .learndash-wrapper .ld-focus .ld-focus-header .ld-focus-menu-actions {
            border-left: 1px solid var(--color-border);
        }
        .learndash-themed .learndash-wrapper .ld-tabs .ld-tabs-navigation .ld-tab {
            color: var(--color-text-muted);
        }
        .learndash-themed .learndash-wrapper .ld-tabs .ld-tabs-navigation .ld-tab.ld-active {
            color: ' . $accent_color . ';
            border-bottom-color: ' . $accent_color . ';
        }
        .learndash-themed .learndash-wrapper .ld-alert-success {
            background: rgba(201, 169, 110, 0.1);
            border-color: ' . $accent_color . ';
        }
        .learndash-themed .learndash-wrapper .ld-alert-success .ld-alert-icon {
            background: ' . $accent_color . ';
        }
        .learndash-themed .learndash-wrapper .ld-progress .ld-progress-bar .ld-progress-bar-percentage {
            background: ' . $accent_color . ';
        }
        .learndash-themed .learndash-wrapper .ld-progress .ld-progress-steps {
            color: var(--color-text-muted);
        }
        .learndash-themed .learndash-wrapper .wpProQuiz_content .wpProQuiz_button {
            background: ' . $accent_color . ' !important;
            border-color: ' . $accent_color . ' !important;
        }
        .learndash-themed .learndash-wrapper .wpProQuiz_content .wpProQuiz_button:hover {
            opacity: 0.85;
        }
        .learndash-themed .learndash-wrapper .ld-item-list .ld-item-list-item {
            background: ' . $card_bg . ';
            border: 1px solid var(--color-border);
            border-radius: 6px;
            margin-bottom: 8px;
            transition: border-color 0.3s ease;
        }
        .learndash-themed .learndash-wrapper .ld-item-list .ld-item-list-item:hover {
            border-color: ' . $accent_color . ';
        }
        .learndash-themed .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-list-item-preview {
            padding: 14px 16px;
        }
        .learndash-themed .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-name {
            color: var(--color-text);
        }
        .learndash-themed .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-title {
            color: ' . $header_color . ';
        }
        .learndash-themed .learndash-wrapper .ld-table-list .ld-table-list-item {
            border-bottom: 1px solid var(--color-border);
        }
        .learndash-themed .learndash-wrapper .ld-table-list .ld-table-list-header {
            background: ' . $card_bg . ';
            border-bottom: 1px solid var(--color-border);
        }
        .learndash-themed .learndash-wrapper .ld-status-icon {
            border-color: var(--color-border);
        }
        .learndash-themed .learndash-wrapper .ld-status-icon.ld-status-complete {
            background: ' . $accent_color . ';
            border-color: ' . $accent_color . ';
        }
        .learndash-themed .learndash-wrapper .ld-course-status .ld-course-status-content {
            border-top: 1px solid var(--color-border);
        }
        .learndash-themed .learndash-wrapper .ld-focus-comments {
            background: var(--color-primary-dark);
        }
        .learndash-themed .learndash-wrapper .ld-focus-comments .ld-comment-avatar img {
            border-color: var(--color-border);
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

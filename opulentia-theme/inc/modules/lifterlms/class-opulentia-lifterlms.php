<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_LifterLMS {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		if ( ! function_exists( 'llms' ) ) {
			return;
		}
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'llms_get_loop_defaults', array( $this, 'loop_columns' ) );
	}

	public function add_theme_support() {
		add_theme_support( 'lifterlms-sidebar-support' );
		add_theme_support( 'lifterlms' );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_lifterlms',
			array(
				'title'    => __( 'LifterLMS', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 85,
			)
		);

		$wp_customize->add_setting(
			'lifterlms-course-columns',
			array(
				'default'           => 3,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'lifterlms-course-columns',
			array(
				'label'   => __( 'Course Grid Columns', 'opulentia' ),
				'section' => 'opulentia_lifterlms',
				'type'    => 'select',
				'choices' => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
				),
			)
		);

		$wp_customize->add_setting(
			'lifterlms-header-color',
			array(
				'default'           => 'var(--color-gold)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'lifterlms-header-color',
			array(
				'label'       => __( 'Course / Lesson Title Color', 'opulentia' ),
				'section'     => 'opulentia_lifterlms',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
			)
		);

		$wp_customize->add_setting(
			'lifterlms-accent-color',
			array(
				'default'           => 'var(--color-gold)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'lifterlms-accent-color',
			array(
				'label'       => __( 'Accent Color (Buttons, Progress)', 'opulentia' ),
				'section'     => 'opulentia_lifterlms',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
			)
		);

		$wp_customize->add_setting(
			'lifterlms-card-bg',
			array(
				'default'           => 'var(--color-secondary-dark)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'lifterlms-card-bg',
			array(
				'label'       => __( 'Course Card Background', 'opulentia' ),
				'section'     => 'opulentia_lifterlms',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-secondary-dark)' ),
			)
		);

		$wp_customize->add_setting(
			'lifterlms-hide-syllabus',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'lifterlms-hide-syllabus',
			array(
				'label'   => __( 'Hide Syllabus on Course Page', 'opulentia' ),
				'section' => 'opulentia_lifterlms',
				'type'    => 'checkbox',
			)
		);
	}

	public function loop_columns( $defaults ) {
		$cols             = Opulentia_get_option( 'lifterlms-course-columns', 3 );
		$defaults['cols'] = absint( $cols );
		return $defaults;
	}

	public function body_class( $classes ) {
		if ( function_exists( 'llms' ) ) {
			$classes[] = 'lifterlms-themed';
		}
		return $classes;
	}

	public function inline_css() {
		if ( ! function_exists( 'llms' ) ) {
			return;
		}

		$header_color = Opulentia_get_option( 'lifterlms-header-color', 'var(--color-gold)' );
		$accent_color = Opulentia_get_option( 'lifterlms-accent-color', 'var(--color-gold)' );
		$card_bg      = Opulentia_get_option( 'lifterlms-card-bg', 'var(--color-secondary-dark)' );

		$css = '
        .lifterlms-themed .llms-loop-item-content {
            background: ' . $card_bg . ';
            border: 1px solid var(--color-border);
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.3s ease, transform 0.3s ease;
        }
        .lifterlms-themed .llms-loop-item-content:hover {
            border-color: ' . $accent_color . ';
            transform: translateY(-2px);
        }
        .lifterlms-themed .llms-loop-item-content .llms-loop-title {
            font-family: var(--font-heading);
            color: ' . $header_color . ';
        }
        .lifterlms-themed .llms-loop-item-content .llms-meta,
        .lifterlms-themed .llms-loop-item-content .llms-author {
            color: var(--color-text-muted);
            font-size: 0.875rem;
        }
        .lifterlms-themed .llms-loop-item-content .llms-loop-link {
            color: var(--color-text);
        }
        .lifterlms-themed .llms-loop-item-content .llms-loop-link:hover {
            color: ' . $accent_color . ';
        }
        .lifterlms-themed .llms-button-primary,
        .lifterlms-themed .llms-button-action {
            background: ' . $accent_color . ' !important;
            border-color: ' . $accent_color . ' !important;
            color: #fff !important;
        }
        .lifterlms-themed .llms-button-primary:hover,
        .lifterlms-themed .llms-button-action:hover {
            opacity: 0.85;
        }
        .lifterlms-themed .llms-progress .progress-bar-completed {
            background: ' . $accent_color . ';
        }
        .lifterlms-themed .llms-access-plan-title {
            background: ' . $card_bg . ';
            color: ' . $header_color . ';
        }
        .lifterlms-themed .llms-access-plan-content {
            background: var(--color-primary-dark);
            border: 1px solid var(--color-border);
        }
        .lifterlms-themed .llms-access-plan-footer {
            background: ' . $card_bg . ';
            border: 1px solid var(--color-border);
        }
        .lifterlms-themed .llms-lesson-preview {
            background: ' . $card_bg . ';
            border: 1px solid var(--color-border);
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 8px;
            transition: border-color 0.3s ease;
        }
        .lifterlms-themed .llms-lesson-preview:hover {
            border-color: ' . $accent_color . ';
        }
        .lifterlms-themed .llms-lesson-preview .llms-lesson-title {
            color: var(--color-text);
            font-size: 0.95rem;
        }
        .lifterlms-themed .llms-lesson-preview.is-complete .llms-lesson-title {
            color: ' . $accent_color . ';
        }
        .lifterlms-themed .llms-lesson-preview .llms-lesson-counter {
            color: var(--color-text-muted);
            font-size: 0.8rem;
        }
        .lifterlms-themed .llms-checkout-wrapper {
            border: 1px solid var(--color-border);
            background: ' . $card_bg . ';
            border-radius: 8px;
            padding: 24px;
        }
        .lifterlms-themed .llms-checkout-wrapper .llms-title {
            color: ' . $header_color . ';
        }
        .lifterlms-themed .llms-student-dashboard .llms-sd-title {
            color: ' . $header_color . ';
            font-family: var(--font-heading);
        }
        .lifterlms-themed .llms-student-dashboard .llms-sd-nav {
            border-bottom: 1px solid var(--color-border);
        }
        .lifterlms-themed .llms-student-dashboard .llms-sd-nav a {
            color: var(--color-text-muted);
        }
        .lifterlms-themed .llms-student-dashboard .llms-sd-nav a.llms-active {
            color: ' . $accent_color . ';
            border-bottom-color: ' . $accent_color . ';
        }
        .lifterlms-themed .llms-notice {
            border-radius: 6px;
        }
        .lifterlms-themed .llms-notice.llms-notice-info {
            background: rgba(201, 169, 110, 0.1);
            border-color: ' . $accent_color . ';
        }
        .lifterlms-themed .llms-form-field input,
        .lifterlms-themed .llms-form-field select,
        .lifterlms-themed .llms-form-field textarea {
            background: var(--color-primary-dark);
            border: 1px solid var(--color-border);
            color: var(--color-text);
            border-radius: 6px;
            padding: 10px 14px;
        }
        .lifterlms-themed .llms-form-field input:focus,
        .lifterlms-themed .llms-form-field select:focus,
        .lifterlms-themed .llms-form-field textarea:focus {
            border-color: ' . $accent_color . ';
            outline: none;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

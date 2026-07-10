<?php
/**
 * Gravity Forms Compatibility — Singleton
 *
 * Integrates Opulentia styling with Gravity Forms:
 * - Theme-styled form fields (inputs, textareas, selects, radio, checkbox)
 * - Validation/error state styling matching theme design tokens
 * - Submit button matching theme gold button styles
 * - Custom CSS class on form wrapper for scoped styling
 * - Responsive form layout
 * - Success message styling
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Gravity_Forms class.
 */
class Opulentia_Gravity_Forms {

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
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );

		// Add custom CSS class to all Gravity Forms.
		add_filter( 'gform_form_tag', array( $this, 'form_class' ), 10, 2 );

		// Customize submit button wrapper.
		add_filter( 'gform_submit_button', array( $this, 'submit_button' ), 10, 2 );

		// Disable default Gravity Forms CSS.
		add_filter( 'gform_disable_form_theme_css', '__return_true' );
	}

	/**
	 * Check if Gravity Forms is active.
	 *
	 * @return bool
	 */
	private function has_gf() {
		return class_exists( 'GFForms' ) || defined( 'GFORMS_VERSION' );
	}

	/**
	 * Add custom CSS class to form wrapper.
	 *
	 * @param string $form_tag Form HTML tag.
	 * @param array  $form     Form object.
	 * @return string
	 */
	public function form_class( $form_tag, $form ) {
		if ( ! $this->has_gf() ) {
			return $form_tag;
		}

		$class    = 'opulentia-gf-form';
		$form_tag = preg_replace(
			'/class=["\']([^"\']*)["\']/',
			'class="$1 ' . $class . '"',
			$form_tag
		);

		return $form_tag;
	}

	/**
	 * Customize the submit button markup.
	 *
	 * @param string $button Submit button HTML.
	 * @param array  $form   Form object.
	 * @return string
	 */
	public function submit_button( $button, $form ) {
		if ( ! $this->has_gf() ) {
			return $button;
		}

		// Wrap in a div for spacing and styling control.
		$button = '<div class="opulentia-gf-actions">' . $button . '</div>';

		return $button;
	}

	/**
	 * Output Gravity Forms inline CSS matching theme design tokens.
	 */
	public function inline_css() {
		if ( ! $this->has_gf() ) {
			return;
		}

		$css = '
            /* ── Form Container ── */
            .opulentia-gf-form .gform_wrapper {
                max-width: 720px;
                margin: 0 auto;
            }
            .opulentia-gf-form .gform_heading {
                margin-bottom: 32px;
            }
            .opulentia-gf-form .gform_title {
                font-family: var(--font-heading, "Playfair Display", serif);
                font-size: 1.5rem;
                color: var(--color-gold, #c9a96e);
                margin-bottom: 8px;
            }
            .opulentia-gf-form .gform_description {
                color: var(--color-text-muted, #999);
                font-size: 0.9375rem;
            }
            .opulentia-gf-form .gsection {
                border-bottom: 1px solid var(--color-border, #333);
                margin: 32px 0;
            }
            .opulentia-gf-form .gsection_title {
                font-family: var(--font-heading, "Playfair Display", serif);
                color: var(--color-gold, #c9a96e);
                font-size: 1.125rem;
            }

            /* ── Form Fields ── */
            .opulentia-gf-form .gfield {
                margin-bottom: 24px;
            }
            .opulentia-gf-form .gfield_label {
                display: block;
                margin-bottom: 6px;
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--color-text-muted, #999);
            }
            .opulentia-gf-form .gfield_required {
                color: var(--color-gold, #c9a96e);
                font-weight: 600;
            }
            .opulentia-gf-form .gfield_description {
                font-size: 0.8125rem;
                color: var(--color-text-muted, #999);
                margin-top: 4px;
            }

            /* ── Inputs, Textareas, Selects ── */
            .opulentia-gf-form input[type="text"],
            .opulentia-gf-form input[type="email"],
            .opulentia-gf-form input[type="tel"],
            .opulentia-gf-form input[type="url"],
            .opulentia-gf-form input[type="number"],
            .opulentia-gf-form input[type="password"],
            .opulentia-gf-form textarea,
            .opulentia-gf-form select {
                width: 100%;
                padding: 12px 16px;
                background: var(--color-secondary-dark, #111);
                border: 1px solid var(--color-border, #333);
                border-radius: 4px;
                color: var(--color-text, #f5f5f5);
                font-family: var(--font-body, Inter, sans-serif);
                font-size: 1rem;
                line-height: 1.5;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
                box-sizing: border-box;
            }
            .opulentia-gf-form input[type="text"]:focus,
            .opulentia-gf-form input[type="email"]:focus,
            .opulentia-gf-form input[type="tel"]:focus,
            .opulentia-gf-form input[type="url"]:focus,
            .opulentia-gf-form input[type="number"]:focus,
            .opulentia-gf-form input[type="password"]:focus,
            .opulentia-gf-form textarea:focus,
            .opulentia-gf-form select:focus {
                outline: none;
                border-color: var(--color-gold, #c9a96e);
                box-shadow: 0 0 0 2px rgba(201, 169, 110, 0.15);
            }
            .opulentia-gf-form textarea {
                min-height: 160px;
                resize: vertical;
            }
            .opulentia-gf-form select {
                appearance: none;
                -webkit-appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23999\' stroke-width=\'2\'%3E%3Cpath d=\'M6 9l6 6 6-6\'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                background-size: 16px;
                padding-right: 40px;
            }
            .opulentia-gf-form select option {
                background: var(--color-primary-dark, #1a1a1a);
                color: var(--color-text, #f5f5f5);
            }

            /* ── Placeholder ── */
            .opulentia-gf-form ::placeholder {
                color: var(--color-text-muted, #999);
                opacity: 0.6;
            }

            /* ── Radio / Checkbox ── */
            .opulentia-gf-form .gfield_radio label,
            .opulentia-gf-form .gfield_checkbox label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-right: 20px;
                text-transform: none;
                font-weight: 400;
                font-size: 0.9375rem;
                cursor: pointer;
                color: var(--color-text, #f5f5f5);
            }
            .opulentia-gf-form .gfield_radio input[type="radio"],
            .opulentia-gf-form .gfield_checkbox input[type="checkbox"] {
                accent-color: var(--color-gold, #c9a96e);
            }
            .opulentia-gf-form .gchoice {
                margin-bottom: 8px;
            }

            /* ── Submit Button ── */
            .opulentia-gf-actions {
                margin-top: 16px;
            }
            .opulentia-gf-form input[type="submit"],
            .opulentia-gf-form .gform_button {
                display: inline-block;
                padding: 14px 40px;
                background: var(--color-gold, #c9a96e);
                color: var(--color-white, #ffffff);
                font-family: var(--font-body, Inter, sans-serif);
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 1px;
                border: none;
                border-radius: 0;
                cursor: pointer;
                transition: background 0.2s ease, transform 0.2s ease;
            }
            .opulentia-gf-form input[type="submit"]:hover,
            .opulentia-gf-form .gform_button:hover {
                background: var(--color-gold-hover, #b8944f);
                transform: translateY(-2px);
            }
            .opulentia-gf-form input[type="submit"]:active,
            .opulentia-gf-form .gform_button:active {
                transform: translateY(0);
            }

            /* ── Validation / Error States ── */
            .opulentia-gf-form .gfield_error input,
            .opulentia-gf-form .gfield_error textarea,
            .opulentia-gf-form .gfield_error select {
                border-color: #e74c3c !important;
            }
            .opulentia-gf-form .gfield_error input:focus,
            .opulentia-gf-form .gfield_error textarea:focus {
                box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.15) !important;
            }
            .opulentia-gf-form .gfield_error .gfield_label {
                color: #e74c3c;
            }
            .opulentia-gf-form .validation_message {
                color: #e74c3c;
                font-size: 0.8125rem;
                margin-top: 4px;
            }
            .opulentia-gf-form .gfield_error {
                background: rgba(231, 76, 60, 0.05);
                border: 1px solid rgba(231, 76, 60, 0.2);
                border-radius: 4px;
                padding: 16px;
                margin-bottom: 24px;
            }
            .opulentia-gf-form .validation_error {
                background: rgba(231, 76, 60, 0.1);
                border: 1px solid #e74c3c;
                border-radius: 4px;
                color: var(--color-text, #f5f5f5);
                font-size: 0.875rem;
                padding: 12px 16px;
                margin-bottom: 24px;
            }

            /* ── Success Message ── */
            .opulentia-gf-form .gform_confirmation_message {
                background: rgba(46, 204, 113, 0.1);
                border: 1px solid #2ecc71;
                border-radius: 4px;
                color: var(--color-text, #f5f5f5);
                font-size: 0.9375rem;
                padding: 16px 20px;
                margin: 24px 0;
            }

            /* ── Inline / Multi-Column ── */
            .opulentia-gf-form .gform_fields {
                grid-row-gap: 0 !important;
            }
            .opulentia-gf-form .gfield--width-half {
                padding-right: 12px;
            }
            .opulentia-gf-form .gfield--width-half + .gfield--width-half {
                padding-left: 12px;
                padding-right: 0;
            }

            /* ── Responsive ── */
            @media (max-width: 576px) {
                .opulentia-gf-form input[type="submit"],
                .opulentia-gf-form .gform_button {
                    width: 100%;
                    text-align: center;
                }
                .opulentia-gf-form .gfield--width-half {
                    padding-right: 0;
                }
                .opulentia-gf-form .gfield--width-half + .gfield--width-half {
                    padding-left: 0;
                }
            }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

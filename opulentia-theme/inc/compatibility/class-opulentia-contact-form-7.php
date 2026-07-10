<?php
/**
 * Contact Form 7 Compatibility — Singleton
 *
 * Integrates Opulentia styling with Contact Form 7:
 * - Theme-styled form fields (inputs, textareas, selects)
 * - Validation/error state styling
 * - Submit button matching theme button styles
 * - Ajax spinner styling
 * - Responsive form layout
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Contact_Form_7 class.
 */
class Opulentia_Contact_Form_7 {

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

        // Add custom form wrapper.
        add_filter( 'wpcf7_form_class_attr', array( $this, 'form_class_attr' ) );

        // Customize submit button.
        add_filter( 'wpcf7_form_elements', array( $this, 'submit_button_wrapper' ) );
    }

    /**
     * Check if Contact Form 7 is active.
     *
     * @return bool
     */
    private function has_cf7() {
        return defined( 'WPCF7_VERSION' ) || class_exists( 'WPCF7' );
    }

    /**
     * Add custom CSS class to CF7 form.
     *
     * @param string $class Default class attribute.
     * @return string
     */
    public function form_class_attr( $class ) {
        $class .= ' opulentia-cf7-form';
        return $class;
    }

    /**
     * Wrap the submit button in a form-actions div for consistent spacing.
     *
     * @param string $html Form HTML.
     * @return string
     */
    public function submit_button_wrapper( $html ) {
        // Wrap submit inputs in a form-actions div for spacing.
        // CF7 uses <input type="submit"> by default.
        $html = preg_replace(
            '/<input\s+type=["\']submit["\']([^>]*)>/i',
            '<div class="opulentia-cf7-actions"><input type="submit"$1></div>',
            $html
        );

        return $html;
    }

    /**
     * Output CF7-specific inline CSS matching theme design tokens.
     */
    public function inline_css() {
        if ( ! $this->has_cf7() ) {
            return;
        }

        $css = '
            /* ── Form Container ── */
            .opulentia-cf7-form {
                max-width: 720px;
                margin: 0 auto;
            }
            .opulentia-cf7-form p {
                margin-bottom: 20px;
            }
            .opulentia-cf7-form label {
                display: block;
                margin-bottom: 6px;
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--color-text-muted, #999);
            }

            /* ── Text Inputs, Textareas, Selects ── */
            .opulentia-cf7-form input[type="text"],
            .opulentia-cf7-form input[type="email"],
            .opulentia-cf7-form input[type="tel"],
            .opulentia-cf7-form input[type="url"],
            .opulentia-cf7-form input[type="number"],
            .opulentia-cf7-form textarea,
            .opulentia-cf7-form select {
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
            .opulentia-cf7-form input[type="text"]:focus,
            .opulentia-cf7-form input[type="email"]:focus,
            .opulentia-cf7-form input[type="tel"]:focus,
            .opulentia-cf7-form input[type="url"]:focus,
            .opulentia-cf7-form textarea:focus,
            .opulentia-cf7-form select:focus {
                outline: none;
                border-color: var(--color-gold, #c9a96e);
                box-shadow: 0 0 0 2px rgba(201, 169, 110, 0.15);
            }
            .opulentia-cf7-form textarea {
                min-height: 160px;
                resize: vertical;
            }
            .opulentia-cf7-form select {
                appearance: none;
                -webkit-appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23999\' stroke-width=\'2\'%3E%3Cpath d=\'M6 9l6 6 6-6\'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                background-size: 16px;
                padding-right: 40px;
            }
            .opulentia-cf7-form select option {
                background: var(--color-primary-dark, #1a1a1a);
                color: var(--color-text, #f5f5f5);
            }

            /* ── Placeholder ── */
            .opulentia-cf7-form ::placeholder {
                color: var(--color-text-muted, #999);
                opacity: 0.6;
            }

            /* ── Submit Button ── */
            .opulentia-cf7-actions {
                margin-top: 8px;
            }
            .opulentia-cf7-form input[type="submit"],
            .opulentia-cf7-form .wpcf7-submit {
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
            .opulentia-cf7-form input[type="submit"]:hover,
            .opulentia-cf7-form .wpcf7-submit:hover {
                background: var(--color-gold-hover, #b8944f);
                transform: translateY(-2px);
            }
            .opulentia-cf7-form input[type="submit"]:active,
            .opulentia-cf7-form .wpcf7-submit:active {
                transform: translateY(0);
            }

            /* ── Validation / Error States ── */
            .opulentia-cf7-form .wpcf7-not-valid-tip {
                color: #e74c3c;
                font-size: 0.8125rem;
                margin-top: 4px;
            }
            .opulentia-cf7-form .wpcf7-not-valid {
                border-color: #e74c3c !important;
            }
            .opulentia-cf7-form .wpcf7-not-valid:focus {
                box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.15) !important;
            }
            .opulentia-cf7-form .wpcf7-validation-errors,
            .opulentia-cf7-form .wpcf7-acceptance-missing {
                background: rgba(231, 76, 60, 0.1);
                border: 1px solid #e74c3c;
                border-radius: 4px;
                color: var(--color-text, #f5f5f5);
                font-size: 0.875rem;
                padding: 12px 16px;
                margin: 24px 0;
            }

            /* ── Success Message ── */
            .opulentia-cf7-form .wpcf7-mail-sent-ok {
                background: rgba(46, 204, 113, 0.1);
                border: 1px solid #2ecc71;
                border-radius: 4px;
                color: var(--color-text, #f5f5f5);
                font-size: 0.875rem;
                padding: 12px 16px;
                margin: 24px 0;
            }
            .opulentia-cf7-form .wpcf7-mail-sent-ok:before {
                color: #2ecc71;
                margin-right: 8px;
            }

            /* ── Ajax Spinner ── */
            .opulentia-cf7-form .wpcf7-spinner {
                background: var(--color-gold, #c9a96e);
                opacity: 0.3;
            }
            .opulentia-cf7-form .wpcf7-spinner::before {
                background-color: var(--color-gold, #c9a96e);
            }

            /* ── Required Asterisk ── */
            .opulentia-cf7-form .wpcf7-required,
            .opulentia-cf7-form .wpcf7-form-control-wrap .wpcf7-required {
                color: var(--color-gold, #c9a96e);
            }

            /* ── Checkbox / Radio ── */
            .opulentia-cf7-form .wpcf7-checkbox label,
            .opulentia-cf7-form .wpcf7-radio label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-right: 20px;
                text-transform: none;
                font-weight: 400;
                font-size: 0.9375rem;
                cursor: pointer;
            }
            .opulentia-cf7-form .wpcf7-checkbox input[type="checkbox"],
            .opulentia-cf7-form .wpcf7-radio input[type="radio"] {
                accent-color: var(--color-gold, #c9a96e);
            }

            /* ── Acceptance ── */
            .opulentia-cf7-form .wpcf7-acceptance label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-transform: none;
                font-weight: 400;
                font-size: 0.9375rem;
                cursor: pointer;
            }
            .opulentia-cf7-form .wpcf7-acceptance input[type="checkbox"] {
                accent-color: var(--color-gold, #c9a96e);
            }

            /* ── Responsive ── */
            @media (max-width: 576px) {
                .opulentia-cf7-form {
                    max-width: 100%;
                }
                .opulentia-cf7-form input[type="submit"],
                .opulentia-cf7-form .wpcf7-submit {
                    width: 100%;
                    text-align: center;
                }
            }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

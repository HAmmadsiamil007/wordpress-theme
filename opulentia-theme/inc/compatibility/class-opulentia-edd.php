<?php
/**
 * Easy Digital Downloads Compatibility — Singleton
 *
 * Integrates Opulentia with Easy Digital Downloads:
 * - Theme-styled download archive grid
 * - Download purchase buttons matching theme button styles
 * - Single download page styling
 * - Checkout page integration
 * - Shopping cart styling
 * - Download history / user account pages
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_EDD class.
 */
class Opulentia_EDD {

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

        // Customize purchase button markup.
        add_filter( 'edd_purchase_link_defaults', array( $this, 'purchase_button_defaults' ) );

        // Wrap download image in a styled container.
        add_filter( 'edd_downloads_list_wrapper_class', array( $this, 'downloads_list_class' ) );

        // Add theme body class.
        add_filter( 'body_class', array( $this, 'body_classes' ) );
    }

    /**
     * Check if EDD is active.
     *
     * @return bool
     */
    private function has_edd() {
        return class_exists( 'Easy_Digital_Downloads' ) || defined( 'EDD_VERSION' );
    }

    /**
     * Customize purchase button default classes to match theme buttons.
     *
     * @param array $args Purchase link default args.
     * @return array
     */
    public function purchase_button_defaults( $args ) {
        if ( ! $this->has_edd() ) {
            return $args;
        }

        $args['color']   = 'opulentia-edd-btn';
        $args['style']   = 'opulentia-edd-btn';
        $args['class']   = 'opulentia-edd-btn edd-submit';

        return $args;
    }

    /**
     * Add custom class to downloads list wrapper.
     *
     * @param string $class Wrapper class string.
     * @return string
     */
    public function downloads_list_class( $class ) {
        if ( ! $this->has_edd() ) {
            return $class;
        }

        $class .= ' opulentia-edd-grid';
        return $class;
    }

    /**
     * Add EDD-specific body classes.
     *
     * @param array $classes Body classes.
     * @return array
     */
    public function body_classes( $classes ) {
        if ( ! $this->has_edd() ) {
            return $classes;
        }

        if ( function_exists( 'edd_is_checkout' ) && edd_is_checkout() ) {
            $classes[] = 'opulentia-edd-checkout';
        }

        return $classes;
    }

    /**
     * Output EDD-specific inline CSS.
     */
    public function inline_css() {
        if ( ! $this->has_edd() ) {
            return;
        }

        $css = '
            /* ── Download Archive Grid ── */
            .opulentia-edd-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 32px;
                margin: 32px 0;
            }
            @media (max-width: 992px) {
                .opulentia-edd-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 576px) {
                .opulentia-edd-grid {
                    grid-template-columns: 1fr;
                }
            }

            /* ── Download Card ── */
            .edd_download {
                background: var(--color-secondary-dark, #111);
                border: 1px solid var(--color-border, #333);
                border-radius: 8px;
                overflow: hidden;
                transition: border-color 0.3s ease, transform 0.3s ease;
            }
            .edd_download:hover {
                border-color: var(--color-gold, #c9a96e);
                transform: translateY(-4px);
            }
            .edd_download .edd_download_image {
                margin: 0;
                overflow: hidden;
            }
            .edd_download .edd_download_image img {
                width: 100%;
                height: 240px;
                object-fit: cover;
                display: block;
                transition: transform 0.4s ease;
            }
            .edd_download:hover .edd_download_image img {
                transform: scale(1.05);
            }
            .edd_download .edd_download_inner {
                padding: 24px;
            }
            .edd_download .edd_download_title {
                font-family: var(--font-heading, "Playfair Display", serif);
                font-size: 1.125rem;
                color: var(--color-gold, #c9a96e);
                margin-bottom: 8px;
                line-height: 1.3;
            }
            .edd_download .edd_download_title a {
                color: inherit;
                text-decoration: none;
                transition: opacity 0.2s ease;
            }
            .edd_download .edd_download_title a:hover {
                opacity: 0.8;
            }
            .edd_download .edd_download_excerpt {
                font-size: 0.875rem;
                color: var(--color-text-muted, #999);
                margin-bottom: 16px;
                line-height: 1.6;
            }
            .edd_download .edd_download_price {
                font-size: 1rem;
                font-weight: 600;
                color: var(--color-text, #f5f5f5);
                margin-bottom: 16px;
            }

            /* ── Purchase Button ── */
            .opulentia-edd-btn,
            .edd_download .edd-submit,
            .edd-submit.button {
                display: inline-block;
                padding: 12px 28px;
                background: var(--color-gold, #c9a96e);
                color: var(--color-white, #ffffff) !important;
                font-family: var(--font-body, Inter, sans-serif);
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 1px;
                border: none;
                border-radius: 0;
                cursor: pointer;
                transition: background 0.2s ease, transform 0.2s ease;
                text-decoration: none;
                line-height: 1.4;
            }
            .opulentia-edd-btn:hover,
            .edd_download .edd-submit:hover,
            .edd-submit.button:hover {
                background: var(--color-gold-hover, #b8944f);
                transform: translateY(-2px);
                color: var(--color-white, #ffffff) !important;
            }
            .edd-submit.button-ghost {
                background: transparent;
                border: 1px solid var(--color-gold, #c9a96e);
                color: var(--color-gold, #c9a96e) !important;
            }
            .edd-submit.button-ghost:hover {
                background: var(--color-gold, #c9a96e);
                color: var(--color-white, #ffffff) !important;
            }

            /* ── Single Download ── */
            .single-download .edd_download_image {
                margin-bottom: 32px;
            }
            .single-download .edd_download_image img {
                width: 100%;
                border-radius: 8px;
            }
            .single-download .edd_price {
                font-size: 1.5rem;
                font-weight: 600;
                color: var(--color-gold, #c9a96e);
                margin-bottom: 24px;
            }
            .single-download .edd_purchase_submit_wrapper {
                margin-top: 24px;
            }

            /* ── Checkout ── */
            .opulentia-edd-checkout #edd_checkout_wrap {
                max-width: 800px;
                margin: 0 auto;
            }
            .opulentia-edd-checkout #edd_checkout_cart {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 32px;
            }
            .opulentia-edd-checkout #edd_checkout_cart th {
                background: var(--color-secondary-dark, #111);
                padding: 12px 16px;
                text-align: left;
                font-size: 0.8125rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--color-text-muted, #999);
                border-bottom: 1px solid var(--color-border, #333);
            }
            .opulentia-edd-checkout #edd_checkout_cart td {
                padding: 16px;
                border-bottom: 1px solid var(--color-border, #333);
                color: var(--color-text, #f5f5f5);
                font-size: 0.9375rem;
            }
            .opulentia-edd-checkout #edd_checkout_cart .edd_cart_header_row th {
                border-bottom: 2px solid var(--color-gold, #c9a96e);
            }
            .opulentia-edd-checkout #edd_checkout_cart .edd_cart_footer_row td {
                border-top: 2px solid var(--color-gold, #c9a96e);
                font-weight: 600;
            }
            .opulentia-edd-checkout #edd_checkout_cart .edd_cart_item_name a {
                color: var(--color-gold, #c9a96e);
                text-decoration: none;
            }
            .opulentia-edd-checkout #edd_checkout_cart .edd_cart_actions input[type="submit"],
            .opulentia-edd-checkout #edd_purchase_form input[type="submit"] {
                display: inline-block;
                padding: 14px 40px;
                background: var(--color-gold, #c9a96e);
                color: var(--color-white, #ffffff) !important;
                font-family: var(--font-body, Inter, sans-serif);
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 1px;
                border: none;
                border-radius: 0;
                cursor: pointer;
                transition: background 0.2s ease;
            }
            .opulentia-edd-checkout #edd_checkout_cart .edd_cart_actions input[type="submit"]:hover,
            .opulentia-edd-checkout #edd_purchase_form input[type="submit"]:hover {
                background: var(--color-gold-hover, #b8944f);
            }

            /* ── Checkout Form ── */
            .opulentia-edd-checkout #edd_purchase_form label {
                display: block;
                margin-bottom: 6px;
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--color-text-muted, #999);
            }
            .opulentia-edd-checkout #edd_purchase_form input[type="text"],
            .opulentia-edd-checkout #edd_purchase_form input[type="email"],
            .opulentia-edd-checkout #edd_purchase_form input[type="password"],
            .opulentia-edd-checkout #edd_purchase_form select {
                width: 100%;
                padding: 12px 16px;
                background: var(--color-secondary-dark, #111);
                border: 1px solid var(--color-border, #333);
                border-radius: 4px;
                color: var(--color-text, #f5f5f5);
                font-family: var(--font-body, Inter, sans-serif);
                font-size: 1rem;
                margin-bottom: 20px;
                box-sizing: border-box;
            }
            .opulentia-edd-checkout #edd_purchase_form input:focus,
            .opulentia-edd-checkout #edd_purchase_form select:focus {
                outline: none;
                border-color: var(--color-gold, #c9a96e);
                box-shadow: 0 0 0 2px rgba(201, 169, 110, 0.15);
            }
            .opulentia-edd-checkout #edd_purchase_form .edd-description {
                font-size: 0.8125rem;
                color: var(--color-text-muted, #999);
                margin-top: -16px;
                margin-bottom: 20px;
            }
            .opulentia-edd-checkout #edd_purchase_form .edd-error {
                background: rgba(231, 76, 60, 0.1);
                border: 1px solid #e74c3c;
                border-radius: 4px;
                padding: 12px 16px;
                margin-bottom: 20px;
                color: var(--color-text, #f5f5f5);
                font-size: 0.875rem;
            }

            /* ── Cart Widget ── */
            .widget_edd_cart_widget .edd-cart {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .widget_edd_cart_widget .edd-cart-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid var(--color-border, #333);
                font-size: 0.875rem;
            }
            .widget_edd_cart_widget .edd-cart-item a {
                color: var(--color-text, #f5f5f5);
                text-decoration: none;
            }
            .widget_edd_cart_widget .edd-cart-item a:hover {
                color: var(--color-gold, #c9a96e);
            }
            .widget_edd_cart_widget .edd-cart-item .edd-remove-from-cart {
                color: #e74c3c;
                font-size: 1.125rem;
                margin-left: 8px;
            }
            .widget_edd_cart_widget .edd-cart-number-of-items {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--color-text-muted, #999);
                margin-bottom: 12px;
            }
            .widget_edd_cart_widget .cart_item.empty {
                color: var(--color-text-muted, #999);
                font-size: 0.875rem;
                padding: 8px 0;
            }
            .widget_edd_cart_widget .edd-cart-meta {
                padding: 8px 0;
                font-weight: 600;
                color: var(--color-text, #f5f5f5);
            }
            .widget_edd_cart_widget .edd_checkout a {
                display: inline-block;
                padding: 10px 24px;
                background: var(--color-gold, #c9a96e);
                color: var(--color-white, #ffffff) !important;
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                text-decoration: none;
                transition: background 0.2s ease;
                margin-top: 8px;
            }
            .widget_edd_cart_widget .edd_checkout a:hover {
                background: var(--color-gold-hover, #b8944f);
            }

            /* ── User Account / Download History ── */
            .opulentia-edd-checkout #edd_user_history {
                width: 100%;
                border-collapse: collapse;
            }
            .opulentia-edd-checkout #edd_user_history th {
                background: var(--color-secondary-dark, #111);
                padding: 12px 16px;
                text-align: left;
                font-size: 0.8125rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--color-text-muted, #999);
                border-bottom: 1px solid var(--color-border, #333);
            }
            .opulentia-edd-checkout #edd_user_history td {
                padding: 12px 16px;
                border-bottom: 1px solid var(--color-border, #333);
                color: var(--color-text, #f5f5f5);
                font-size: 0.875rem;
            }
            .opulentia-edd-checkout #edd_user_history td a {
                color: var(--color-gold, #c9a96e);
                text-decoration: none;
            }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

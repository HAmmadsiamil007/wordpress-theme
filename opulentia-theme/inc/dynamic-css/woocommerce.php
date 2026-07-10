<?php
/**
 * WooCommerce Dynamic CSS
 *
 * Generates inline CSS for WooCommerce elements based on
 * customizer settings via the Theme Options API.
 * Only loads when WooCommerce is active.
 *
 * @package Opulentia
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate WooCommerce dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_woocommerce_css() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$css = '';

	// Product grid columns.
	$product_columns = (int) Opulentia_get_option( 'wc-product-columns', 4 );
	$product_columns = max( 1, min( 6, $product_columns ) );

	if ( 4 !== $product_columns ) {
		$css .= Opulentia_parse_css(
			array(
				'ul.products' => array(
					'grid-template-columns' => 'repeat(' . $product_columns . ', 1fr)',
				),
				'body.wc-columns-' . $product_columns . ' ul.products' => array(
					'grid-template-columns' => 'repeat(' . $product_columns . ', 1fr)',
				),
			)
		);
	}

	// Product card border radius.
	$card_radius = Opulentia_get_option( 'wc-product-card-radius', '0' );
	if ( '0' !== $card_radius ) {
		$css .= Opulentia_parse_css(
			array(
				'.woocommerce .product-card'             => array(
					'border-radius' => $card_radius . 'px',
				),
				'.woocommerce .product-card__image-wrap' => array(
					'border-radius' => $card_radius . 'px ' . $card_radius . 'px 0 0',
				),
			)
		);
	}

	// Product card shadow.
	if ( ! Opulentia_get_option( 'wc-product-card-shadow', true ) ) {
		$css .= Opulentia_parse_css(
			array(
				'.woocommerce .product-card' => array(
					'box-shadow' => 'none',
				),
			)
		);
	}

	// Sale badge colors.
	$sale_bg = Opulentia_get_option( 'color-wc-sale-badge', '#b8860b' );
	if ( '#b8860b' !== $sale_bg ) {
		$css .= Opulentia_parse_css(
			array(
				'.onsale, .product-card__badge--sale, span.onsale' => array(
					'background-color' => $sale_bg,
				),
			)
		);
	}

	// Add to Cart button colors.
	$wc_button_bg   = Opulentia_get_option( 'color-wc-button', '#c9a96e' );
	$wc_button_text = Opulentia_get_option( 'color-wc-button-text', '#ffffff' );

	if ( '#c9a96e' !== $wc_button_bg || '#ffffff' !== $wc_button_text ) {
		$css .= Opulentia_parse_css(
			array(
				'.woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce a.add_to_cart_button, .woocommerce .opulentia-ajax-add-to-cart' => array(
					'background-color' => $wc_button_bg . ' !important',
					'color'            => $wc_button_text . ' !important',
				),
			)
		);
	}

	// Rating stars color.
	$rating_color = Opulentia_get_option( 'color-wc-rating-stars', '#c9a96e' );
	if ( '#c9a96e' !== $rating_color ) {
		$css .= Opulentia_parse_css(
			array(
				'.woocommerce .star-rating' => array(
					'color' => $rating_color,
				),
			)
		);
	}

	// Price color.
	$price_color = Opulentia_get_option( 'color-wc-price', '#c9a96e' );
	if ( '#c9a96e' !== $price_color ) {
		$css .= Opulentia_parse_css(
			array(
				'.woocommerce .price, .woocommerce .price ins, .woocommerce .product-card__price' => array(
					'color' => $price_color,
				),
			)
		);
	}

	// Responsive product columns using theme breakpoint utilities.
	$tablet_bp = Opulentia_get_tablet_breakpoint( '', 1 );
	$mobile_bp = Opulentia_get_mobile_breakpoint();

	$css .= Opulentia_parse_css(
		array(
			'ul.products' => array(
				'grid-template-columns' => 'repeat(2, 1fr)',
			),
		),
		'',
		$tablet_bp
	);

	$css .= Opulentia_parse_css(
		array(
			'ul.products' => array(
				'grid-template-columns' => '1fr',
			),
		),
		'',
		$mobile_bp
	);

	// ── Checkout-specific colors ──
	$checkout_btn_bg   = Opulentia_get_option( 'color-wc-checkout-button', '#c9a96e' );
	$checkout_btn_text = Opulentia_get_option( 'color-wc-checkout-button-text', '#ffffff' );
	$checkout_accent   = Opulentia_get_option( 'color-wc-checkout-accent', '#c9a96e' );

	if ( '#c9a96e' !== $checkout_btn_bg || '#ffffff' !== $checkout_btn_text ) {
		$css .= Opulentia_parse_css(
			array(
				'.so-checkout-payment__submit, #place_order' => array(
					'background-color' => $checkout_btn_bg . ' !important',
					'color'            => $checkout_btn_text . ' !important',
				),
			)
		);
	}

	if ( '#c9a96e' !== $checkout_accent ) {
		$css .= Opulentia_parse_css(
			array(
				'.so-checkout-section-title, .so-checkout-review-order__total-row--grand-total .so-checkout-review-order__total-value, .so-thankyou__detail-label, .so-thankyou__status--success .so-thankyou__status-icon, .so-checkout-payment__method.is--chosen' => array(
					'color' => $checkout_accent,
				),
				'.so-checkout-section-title'              => array(
					'border-bottom-color' => $checkout_accent,
				),
				'.so-checkout-payment__method.is--chosen' => array(
					'border-color' => $checkout_accent,
				),
				'.woocommerce-form-login .button, .woocommerce-form-coupon .button, .shipping-calculator-form button, .woocommerce-order-again .button' => array(
					'background-color' => $checkout_accent . ' !important',
				),
				'.woocommerce-form__input-checkbox, .woocommerce-form-login input[type="checkbox"]' => array(
					'accent-color' => $checkout_accent,
				),
				'.so-checkout-review-order__totals'       => array(
					'border-top-color' => $checkout_accent,
				),
			)
		);
	}

	return $css;
}

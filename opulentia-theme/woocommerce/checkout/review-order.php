<?php
/**
 * Order Review Template
 *
 * Overrides the default WooCommerce order review table with
 * dark luxury styling — gold accents, bordered rows, compact layout.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="so-checkout-review-order">
    <?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>

    <div class="so-checkout-review-order__items">
        <?php
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                ?>
                <div class="so-checkout-review-order__item">
                    <div class="so-checkout-review-order__item-image">
                        <?php echo $_product->get_image( 'thumbnail', array( 'class' => 'so-checkout-review-order__thumb' ) ); ?>
                    </div>
                    <div class="so-checkout-review-order__item-details">
                        <span class="so-checkout-review-order__item-name">
                            <?php echo esc_html( $_product->get_name() ); ?>
                        </span>
                        <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span class="so-checkout-review-order__item-qty">
                            &times; <?php echo esc_html( $cart_item['quantity'] ); ?>
                        </span>
                    </div>
                    <div class="so-checkout-review-order__item-total">
                        <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>

    <div class="so-checkout-review-order__totals">
        <div class="so-checkout-review-order__total-row">
            <span class="so-checkout-review-order__total-label"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
            <span class="so-checkout-review-order__total-value"><?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        </div>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <div class="so-checkout-review-order__total-row so-checkout-review-order__total-row--coupon">
                <span class="so-checkout-review-order__total-label">
                    <?php esc_html_e( 'Coupon:', 'woocommerce' ); ?>
                    <?php echo esc_html( $code ); ?>
                </span>
                <span class="so-checkout-review-order__total-value">
                    -<?php echo wc_cart_totals_coupon_amount_html( $coupon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
            </div>
        <?php endforeach; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <div class="so-checkout-review-order__total-row">
                <span class="so-checkout-review-order__total-label"><?php echo esc_html( $fee->name ); ?></span>
                <span class="so-checkout-review-order__total-value"><?php echo wc_cart_totals_fee_html( $fee ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            </div>
        <?php endforeach; ?>

        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach ?>
                    <div class="so-checkout-review-order__total-row">
                        <span class="so-checkout-review-order__total-label"><?php echo esc_html( $tax->label ); ?></span>
                        <span class="so-checkout-review-order__total-value"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="so-checkout-review-order__total-row">
                    <span class="so-checkout-review-order__total-label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
                    <span class="so-checkout-review-order__total-value"><?php echo wc_cart_totals_taxes_total_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

        <?php wc_cart_totals_shipping_html(); ?>

        <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

        <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        <div class="so-checkout-review-order__total-row so-checkout-review-order__total-row--grand-total">
            <span class="so-checkout-review-order__total-label"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
            <span class="so-checkout-review-order__total-value"><?php echo WC()->cart->get_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        </div>

        <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
    </div>
</div>

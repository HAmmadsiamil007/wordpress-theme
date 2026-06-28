<?php
/**
 * Checkout Page Template
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wc_print_notices();
?>

<div class="woocommerce-checkout">
    <div class="page-header">
        <h1 class="page-header__title"><?php esc_html_e( 'Checkout', 'soleorigine' ); ?></h1>
        <p class="page-header__subtitle"><?php esc_html_e( 'Complete your order', 'soleorigine' ); ?></p>
    </div>

    <?php if ( WC()->cart->is_empty() ) : ?>
        <div class="woocommerce-info">
            <?php esc_html_e( 'Your cart is currently empty.', 'soleorigine' ); ?>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="woocommerce-Button button">
                <?php esc_html_e( 'Continue Shopping', 'soleorigine' ); ?>
            </a>
        </div>
    <?php else : ?>
        <div class="checkout-content">
            <?php if ( ! is_user_logged_in() ) : ?>
                <div class="woocommerce-notices-wrapper">
                    <div class="woocommerce-message">
                        <?php
                        printf(
                            /* translators: %s: login URL */
                            esc_html__( 'Already have an account? %s', 'soleorigine' ),
                            '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">'
                        ); ?>
                            <?php esc_html_e( 'Log in', 'soleorigine' ); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <form name="checkout" method="post" class="checkout_form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

                <?php if ( WC()->cart->needs_shipping() ) : ?>
                    <div class="woocommerce-shipping-fields">
                        <h3 id="ship-to-different-address">
                            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                                <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="ship_to_different_address" type="checkbox" id="ship_to_different_address" value="1" />
                                <span><?php esc_html_e( 'Ship to a different address?', 'soleorigine' ); ?></span>
                            </label>
                        </h3>
                    </div>
                <?php endif; ?>

                <div class="woocommerce-billing-fields">
                    <h3><?php esc_html_e( 'Billing Details', 'soleorigine' ); ?></h3>
                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                </div>

                <div class="woocommerce-shipping-fields" style="display: none;">
                    <h3><?php esc_html_e( 'Shipping Details', 'soleorigine' ); ?></h3>
                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                </div>

                <div class="woocommerce-additional-fields">
                    <?php do_action( 'woocommerce_checkout_before_order_notes' ); ?>

                    <?php if ( WC()->cart->needs_shipping_address() ) : ?>
                        <div class="woocommerce-additional-fields__field-wrapper">
                            <h3><?php esc_html_e( 'Additional Information', 'soleorigine' ); ?></h3>
                            <?php do_action( 'woocommerce_checkout_order_notes' ); ?>
                        </div>
                    <?php endif; ?>

                    <?php do_action( 'woocommerce_checkout_after_order_notes' ); ?>
                </div>

                <div class="woocommerce-checkout-review-order">
                    <h3><?php esc_html_e( 'Your Order', 'soleorigine' ); ?></h3>
                    <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                    <div id="order_review" class="woocommerce-checkout-review-order-table">
                        <?php woocommerce_order_review(); ?>
                    </div>

                    <?php do_action( 'woocommerce_checkout_before_payment' ); ?>

                    <div id="payment" class="woocommerce-checkout-payment">
                        <?php woocommerce_payment_gateways(); ?>
                    </div>

                    <?php do_action( 'woocommerce_checkout_after_payment' ); ?>
                </div>

                <div class="woocommerce-checkout-submit">
                    <button type="submit" class="woocommerce-button button alt" name="woocommerce_checkout_place_order" id="place_order" value="<?php esc_attr_e( 'Place Order', 'soleorigine' ); ?>" data-value="<?php esc_attr_e( 'Place Order', 'soleorigine' ); ?>">
                        <?php esc_html_e( 'Place Order', 'soleorigine' ); ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

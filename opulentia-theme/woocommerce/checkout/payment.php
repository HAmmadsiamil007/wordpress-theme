<?php
/**
 * Payment Methods Template
 *
 * Overrides the default WooCommerce payment section with
 * dark luxury styling — gold-accented payment method cards,
 * prominent place order button.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="opulentia-checkout-payment">
    <?php if ( WC()->cart->needs_payment() ) : ?>
        <ul class="opulentia-checkout-payment__methods">
            <?php foreach ( WC()->payment_gateways->get_available_payment_gateways() as $gateway ) : ?>
                <li class="opulentia-checkout-payment__method <?php echo $gateway->chosen ? 'is--chosen' : ''; ?>">
                    <label class="opulentia-checkout-payment__method-label"
                           for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
                        <input type="radio"
                               class="opulentia-checkout-payment__method-radio"
                               name="payment_method"
                               value="<?php echo esc_attr( $gateway->id ); ?>"
                               id="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
                            <?php checked( $gateway->chosen, true ); ?>
                               data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>">
                        <span class="opulentia-checkout-payment__method-title">
                            <?php echo esc_html( $gateway->get_title() ); ?>
                        </span>
                        <?php if ( $gateway->get_icon() ) : ?>
                            <span class="opulentia-checkout-payment__method-icon">
                                <?php echo $gateway->get_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </span>
                        <?php endif; ?>
                    </label>
                    <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                        <div class="opulentia-checkout-payment__method-fields">
                            <?php
                            $gateway->payment_fields();
                            ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="opulentia-checkout-payment__place-order">
        <noscript>
            <?php
            printf(
                '<br /><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="%s">%s</button>',
                esc_attr__( 'Update totals', 'woocommerce' ),
                esc_html__( 'Update totals', 'woocommerce' )
            );
            ?>
        </noscript>

        <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

        <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="btn btn--primary opulentia-checkout-payment__submit" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( WC()->checkout()->get_order_button_text() ) . '" data-value="' . esc_attr( WC()->checkout()->get_order_button_text() ) . '">' . esc_html( WC()->checkout()->get_order_button_text() ) . '</button>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php do_action( 'woocommerce_review_order_after_submit' ); ?>
    </div>
</div>

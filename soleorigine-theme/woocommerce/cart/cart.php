<?php
/**
 * Cart Page Template
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wc_print_notices();
?>

<div class="woocommerce-cart">
    <div class="page-header">
        <h1 class="page-header__title"><?php esc_html_e( 'Shopping Cart', 'soleorigine' ); ?></h1>
        <p class="page-header__subtitle"><?php esc_html_e( 'Review your selections before checkout', 'soleorigine' ); ?></p>
    </div>

    <div class="cart-content">
        <form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php woocommerce_cart_table(); ?>

            <div class="cart-actions">
                <div class="cart-actions__update">
                    <?php wc_button( array(
                        'type'    => 'submit',
                        'name'    => 'update_cart',
                        'class'   => 'button',
                        'value'   => wc_get_cart_url(),
                        'label'   => __( 'Update Cart', 'soleorigine' ),
                    ) ); ?>
                </div>

                <div class="cart-actions__coupon">
                    <?php if ( wc_coupons_enabled() ) : ?>
                        <div class="coupon">
                            <input type="text" name="coupon_code" class="input-text" id="coupon_code" placeholder="<?php esc_attr_e( 'Coupon code', 'soleorigine' ); ?>" value="" />
                            <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply Coupon', 'soleorigine' ); ?>"><?php esc_html_e( 'Apply Coupon', 'soleorigine' ); ?></button>
                            <?php do_action( 'woocommerce_cart_coupon' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="cart-collaterals">
            <?php woocommerce_cart_totals(); ?>
        </div>

        <div class="woocommerce-proceed-to-checkout">
            <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
        </div>
    </div>
</div>

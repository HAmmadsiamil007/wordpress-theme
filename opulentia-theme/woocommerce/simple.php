<?php
/**
 * Simple Product Type Template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product_Simple' ) ) {
    return;
}
?>

<div class="cart simple-product-cart">
    <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

    <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <div class="quantity">
            <label class="screen-reader-text" for="quantity_<?php echo esc_attr( $product->get_id() ); ?>">
                <?php esc_html_e( 'Quantity', 'opulentia' ); ?>
            </label>
            <input
                type="number"
                id="quantity_<?php echo esc_attr( $product->get_id() ); ?>"
                class="input-text qty text"
                step="1"
                min="1"
                max="<?php echo esc_attr( $product->get_max_purchase_quantity() ); ?>"
                name="quantity"
                value="<?php echo esc_attr( $product->get_min_purchase_quantity() ); ?>"
                title="<?php esc_attr_x( 'Qty', 'Product quantity input tooltip', 'opulentia' ); ?>"
                inputmode="numeric"
                autocomplete="off"
            />
        </div>

        <button type="submit" class="single_add_to_cart_button button alt">
            <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
        </button>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
    </form>

    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
</div>

<?php
/**
 * Content Single Product Template
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'single-product', $product ); ?>>

    <div class="single-product__gallery">
        <?php
        /**
         * Hook: woocommerce_before_single_product_summary.
         */
        do_action( 'woocommerce_before_single_product_summary' );
        ?>
    </div>

    <div class="single-product__summary">
        <?php
        /**
         * Hook: woocommerce_single_product_summary.
         */
        do_action( 'woocommerce_single_product_summary' );
        ?>
    </div>

    <div class="single-product__tabs">
        <?php
        /**
         * Hook: woocommerce_after_single_product_summary.
         */
        do_action( 'woocommerce_after_single_product_summary' );
        ?>
    </div>

    <?php do_action( 'woocommerce_after_single_product' ); ?>

</div>

<?php
/**
 * Hook: woocommerce_after_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_after_single_product' );

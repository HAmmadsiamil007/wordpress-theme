<?php
/**
 * Content Product Template (Loop Item)
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$classes = array(
    'product',
    'product-card',
    'product-' . $product->get_id(),
);

if ( $product->is_on_sale() ) {
    $classes[] = 'product--on-sale';
}

if ( $product->is_featured() ) {
    $classes[] = 'product--featured';
}
?>

<li <?php wc_product_class( $classes, $product ); ?>>
    <div class="product-card__image-wrap">
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item_title.
         */
        do_action( 'woocommerce_before_shop_loop_item_title' );
        ?>

        <?php if ( $product->is_on_sale() ) : ?>
            <span class="product-card__badge">
                <?php
                $ratio = ( $product->get_sale_price() / $product->get_regular_price() ) * 100;
                echo esc_html( '-' . ( 100 - round( $ratio ) ) . '%' );
                ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="product-card__content">
        <div class="product-card__category">
            <?php
            $categories = get_the_terms( $product->get_id(), 'product_cat' );
            if ( $categories && ! is_wp_error( $categories ) ) :
                $category_names = wp_list_pluck( $categories, 'name' );
                echo esc_html( implode( ', ', array_slice( $category_names, 0, 2 ) ) );
            endif;
            ?>
        </div>

        <h2 class="product-card__title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>

        <?php if ( $product->get_short_description() ) : ?>
            <p class="product-card__description">
                <?php echo wp_kses_post( $product->get_short_description() ); ?>
            </p>
        <?php endif; ?>

        <div class="product-card__footer">
            <div class="product-card__price">
                <?php woocommerce_template_loop_price(); ?>
            </div>

            <a href="<?php the_permalink(); ?>" class="product-card__link">
                <?php esc_html_e( 'View Details', 'soleorigine' ); ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</li>

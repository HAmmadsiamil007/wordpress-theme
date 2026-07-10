<?php
/**
 * Content Product Template (Loop Item)
 *
 * @package Opulentia
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

		<!-- WC Pro: Quick View & Wishlist Actions -->
		<div class="product-card__actions">
			<?php if ( get_theme_mod( 'wc_enable_quick_view', true ) ) : ?>
				<button class="quick-view-btn"
						data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
						aria-label="<?php esc_attr_e( 'Quick View', 'opulentia' ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
						<circle cx="12" cy="12" r="3"/>
					</svg>
				</button>
			<?php endif; ?>

			<button class="wishlist-toggle"
					data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
					aria-label="<?php esc_attr_e( 'Add to Wishlist', 'opulentia' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
				</svg>
			</button>
		</div>
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

			<div class="product-card__footer-actions">
				<?php if ( $product->is_type( 'simple' ) ) : ?>
					<button class="Opulentia-ajax-add-to-cart product-card__add-to-cart"
							data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
							data-quantity="1">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
							<line x1="3" y1="6" x2="21" y2="6"/>
						</svg>
						<span><?php esc_html_e( 'Add to Cart', 'opulentia' ); ?></span>
					</button>
				<?php endif; ?>

				<a href="<?php the_permalink(); ?>" class="product-card__link">
					<?php esc_html_e( 'View Details', 'opulentia' ); ?>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M5 12h14M12 5l7 7-7 7"/>
					</svg>
				</a>
			</div>
		</div>
	</div>
</li>

<?php
/**
 * Template part for displaying product cards
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product_id = get_the_ID();
$product    = wc_get_product( $product_id );

if ( ! $product ) {
	return;
}
?>

<div class="product-card">
	<div class="product-card__image">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium_large' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php the_permalink(); ?>">
				<svg viewBox="0 0 200 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 160px; height: 80px;">
					<path d="M20 80 Q100 20 180 80" stroke="#8B4513" stroke-width="3" fill="none"/>
					<ellipse cx="100" cy="85" rx="80" ry="10" fill="#8B4513" opacity="0.3"/>
					<path d="M30 75 Q100 30 170 75" fill="#A0522D"/>
					<path d="M50 60 Q100 25 150 60" fill="#8B4513"/>
				</svg>
			</a>
		<?php endif; ?>

		<?php if ( $product->is_on_sale() ) : ?>
			<span class="onsale"><?php esc_html_e( 'Sale!', 'opulentia' ); ?></span>
		<?php endif; ?>
	</div>

	<div class="product-card__content">
		<h3 class="product-card__title">
			<a href="<?php the_permalink(); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>

		<p class="product-card__description">
			<?php echo esc_html( $product->get_short_description() ); ?>
		</p>

		<div class="product-card__price">
			<?php echo wp_kses_post( $product->get_price_html() ); ?>
		</div>

		<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
			class="product-card__link"
			data-product_id="<?php echo esc_attr( $product_id ); ?>"
			data-product_quantity="1">
			<?php esc_html_e( 'Add to Cart', 'opulentia' ); ?>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
				<line x1="3" y1="6" x2="21" y2="6"/>
				<path d="M16 10a4 4 0 0 1-8 0"/>
			</svg>
		</a>
	</div>
</div>

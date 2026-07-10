<?php
/**
 * Variable Product Type Template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product_Variable' ) ) {
	return;
}
?>

<div class="cart variable-product-cart">
	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $product->get_available_variations() ) ); ?>">

		<?php do_action( 'woocommerce_before_variations_form' ); ?>

		<?php if ( empty( $product->get_available_variations() ) && false !== $product->get_available_variations() ) : ?>
			<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
		<?php else : ?>
			<table class="variations" cellspacing="0" role="presentation">
				<tbody>
					<?php foreach ( $product->get_variation_attributes() as $attribute_name => $options ) : ?>
						<tr>
							<th>
								<label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
									<?php echo wc_attribute_label( $attribute_name ); ?>
								</label>
							</th>
							<td>
								<?php
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'attribute' => $attribute_name,
										'product'   => $product,
									)
								);
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php do_action( 'woocommerce_after_variations_table' ); ?>

			<div class="single_variation_wrap">
				<?php
				do_action( 'woocommerce_before_single_variation' );
				woocommerce_template_single_add_to_cart();
				do_action( 'woocommerce_after_single_variation' );
				?>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_variations_form' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
</div>

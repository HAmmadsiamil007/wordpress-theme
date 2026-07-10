<?php
/**
 * Checkout Form
 *
 * Overrides the default WooCommerce checkout with Opulentia's
 * dark luxury two-column layout. Billing/shipping left, order
 * review right (sticky sidebar).
 *
 * @package Opulentia
 * @see     https://docs.woocommerce.com/document/template-structure/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout is not possible, bail early.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="opulentia-checkout">
	<div class="opulentia-checkout__inner">
		<div class="opulentia-checkout__form">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div class="opulentia-checkout__billing" id="customer_details">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

		</div>

		<div class="opulentia-checkout__sidebar">
			<div class="opulentia-checkout__order-review">
				<div class="opulentia-checkout-section-title">
					<?php esc_html_e( 'Your Order', 'opulentia' ); ?>
				</div>
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

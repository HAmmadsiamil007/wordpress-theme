<?php
/**
 * Thank You / Order Received Template
 *
 * Overrides the default WooCommerce thank you page with
 * Opulentia's dark luxury design — order details card,
 * customer details grid, action buttons.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="opulentia-thankyou">

	<?php if ( $order ) : ?>

		<?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="opulentia-thankyou__status opulentia-thankyou__status--failed">
				<p class="opulentia-thankyou__status-icon">&#10007;</p>
				<h2 class="opulentia-thankyou__status-title">
					<?php esc_html_e( 'Payment Failed', 'woocommerce' ); ?>
				</h2>
				<p class="opulentia-thankyou__status-text">
					<?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?>
				</p>
				<div class="opulentia-thankyou__status-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn--primary">
						<?php esc_html_e( 'Try Again', 'woocommerce' ); ?>
					</a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn--outline">
						<?php esc_html_e( 'My Account', 'woocommerce' ); ?>
					</a>
				</div>
			</div>

		<?php else : ?>

			<div class="opulentia-thankyou__status opulentia-thankyou__status--success">
				<p class="opulentia-thankyou__status-icon">&#10003;</p>
				<h2 class="opulentia-thankyou__status-title">
					<?php esc_html_e( 'Order Received', 'opulentia' ); ?>
				</h2>
				<p class="opulentia-thankyou__status-thanks">
					<?php esc_html_e( 'Thank you for your order! We will process it shortly.', 'opulentia' ); ?>
				</p>
			</div>

			<div class="opulentia-thankyou__order-details">
				<div class="opulentia-thankyou__order-number">
					<span class="opulentia-thankyou__detail-label"><?php esc_html_e( 'Order Number', 'woocommerce' ); ?></span>
					<span class="opulentia-thankyou__detail-value"><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</div>
				<div class="opulentia-thankyou__order-date">
					<span class="opulentia-thankyou__detail-label"><?php esc_html_e( 'Date', 'woocommerce' ); ?></span>
					<span class="opulentia-thankyou__detail-value"><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</div>
				<div class="opulentia-thankyou__order-total">
					<span class="opulentia-thankyou__detail-label"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
					<span class="opulentia-thankyou__detail-value"><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</div>
				<?php if ( $order->get_payment_method_title() ) : ?>
					<div class="opulentia-thankyou__order-payment">
						<span class="opulentia-thankyou__detail-label"><?php esc_html_e( 'Payment Method', 'woocommerce' ); ?></span>
						<span class="opulentia-thankyou__detail-value"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
					</div>
				<?php endif; ?>
			</div>

			<div class="opulentia-thankyou__details-grid">
				<?php if ( $order->get_billing_email() || $order->get_billing_phone() ) : ?>
					<div class="opulentia-thankyou__customer-details">
						<h3 class="opulentia-thankyou__details-title">
							<?php esc_html_e( 'Customer Details', 'woocommerce' ); ?>
						</h3>
						<?php if ( $order->get_billing_email() ) : ?>
							<p><strong><?php esc_html_e( 'Email:', 'woocommerce' ); ?></strong> <?php echo esc_html( $order->get_billing_email() ); ?></p>
						<?php endif; ?>
						<?php if ( $order->get_billing_phone() ) : ?>
							<p><strong><?php esc_html_e( 'Phone:', 'woocommerce' ); ?></strong> <?php echo esc_html( $order->get_billing_phone() ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $order->get_billing_address_1() ) : ?>
					<div class="opulentia-thankyou__billing-details">
						<h3 class="opulentia-thankyou__details-title">
							<?php esc_html_e( 'Billing Address', 'woocommerce' ); ?>
						</h3>
						<address>
							<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
						</address>
					</div>
				<?php endif; ?>

				<?php if ( $order->get_shipping_address_1() ) : ?>
					<div class="opulentia-thankyou__shipping-details">
						<h3 class="opulentia-thankyou__details-title">
							<?php esc_html_e( 'Shipping Address', 'woocommerce' ); ?>
						</h3>
						<address>
							<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
						</address>
					</div>
				<?php endif; ?>
			</div>

			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<div class="opulentia-thankyou__status opulentia-thankyou__status--success">
			<p class="opulentia-thankyou__status-icon">&#10003;</p>
			<h2 class="opulentia-thankyou__status-title">
				<?php esc_html_e( 'Order Received', 'opulentia' ); ?>
			</h2>
			<p class="opulentia-thankyou__status-text">
				<?php esc_html_e( 'Thank you. Your order has been received.', 'woocommerce' ); ?>
			</p>
		</div>

	<?php endif; ?>

</div>

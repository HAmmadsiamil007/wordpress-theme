<?php
/**
 * Archive Product Template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );
?>

<div class="woocommerce-shop">
	<div class="page-header">
		<h1 class="page-header__title">
			<?php woocommerce_page_title(); ?>
		</h1>
		<?php if ( is_product_category() ) : ?>
			<?php
			$term = get_queried_object();
			if ( $term && ! empty( $term->description ) ) :
				?>
				<p class="page-header__description"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>

	<div class="shop-content">
		<?php woocommerce_output_all_notices(); ?>

		<?php if ( woocommerce_product_loop() ) : ?>

			<?php do_action( 'woocommerce_before_shop_loop' ); ?>

			<div class="shop-toolbar">
				<div class="shop-toolbar__left">
					<?php
					/**
					 * Hook: woocommerce_before_shop_loop.
					 */
					do_action( 'woocommerce_shop_loop' );
					?>
				</div>

				<div class="shop-toolbar__right">
					<?php woocommerce_catalog_ordering(); ?>
					<?php woocommerce_result_count(); ?>
				</div>
			</div>

			<?php if ( wc_get_loop_prop( 'total' ) > 0 ) : ?>

				<?php if ( wc_get_loop_prop( 'is_paginated' ) ) : ?>
					<?php woocommerce_pagination(); ?>
				<?php endif; ?>

				<ul class="products product-grid">
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; ?>
				</ul>

				<?php if ( wc_get_loop_prop( 'is_paginated' ) ) : ?>
					<?php woocommerce_pagination(); ?>
				<?php endif; ?>

			<?php else : ?>
				<div class="woocommerce-info">
					<?php esc_html_e( 'No products were found matching your selection.', 'opulentia' ); ?>
				</div>
			<?php endif; ?>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>

		<?php else : ?>
			<?php do_action( 'woocommerce_before_shop_loop' ); ?>
			<div class="woocommerce-info">
				<?php esc_html_e( 'No products found.', 'opulentia' ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer( 'shop' );

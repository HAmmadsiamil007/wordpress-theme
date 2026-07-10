<?php
/**
 * Single Product Template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );
?>

<div class="woocommerce-single-product">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<?php wc_get_template_part( 'content', 'single-product' ); ?>
	<?php endwhile; ?>
</div>

<?php
get_footer( 'shop' );

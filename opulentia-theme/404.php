<?php
/**
 * The 404 template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php do_action( 'Opulentia_404_before' ); ?>
<?php if ( has_action( 'Opulentia_404_content' ) ) : ?>
	<?php do_action( 'Opulentia_404_content' ); ?>
<?php else : ?>
<section class="error-404">
	<div class="container">
		<div class="error-404__content">
			<h1 class="error-404__title"><?php esc_html_e( '404', 'opulentia' ); ?></h1>
			<h2 class="error-404__subtitle"><?php esc_html_e( 'Page Not Found', 'opulentia' ); ?></h2>
			<p class="error-404__text">
				<?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'opulentia' ); ?>
			</p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
				<?php esc_html_e( 'Back to Homepage', 'opulentia' ); ?>
			</a>
		</div>
	</div>
</section>
<?php endif; ?>
<?php do_action( 'Opulentia_404_after' ); ?>

<?php get_footer(); ?>

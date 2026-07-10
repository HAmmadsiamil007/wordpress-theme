<?php
/**
 * Template part for displaying footer widgets
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'footer-1' ) && ! is_active_sidebar( 'footer-2' ) && ! is_active_sidebar( 'footer-3' ) ) {
	return;
}
?>

<div class="footer-widgets">
	<div class="container">
		<div class="footer-widgets__grid">
			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<div class="footer-widget-area">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
				<div class="footer-widget-area">
					<?php dynamic_sidebar( 'footer-2' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
				<div class="footer-widget-area">
					<?php dynamic_sidebar( 'footer-3' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
/**
 * Blog Layout: Classic
 *
 * Full-width featured image with full text excerpt and separator.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php do_action( 'Opulentia_entry_before' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-classic' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-classic__image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'large' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="post-classic__content">
		<div class="post-classic__meta">
			<?php Opulentia_posted_categories(); ?>
			<span class="post-classic__meta-sep">&bull;</span>
			<?php Opulentia_posted_on(); ?>
		</div>

		<h2 class="post-classic__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="post-classic__excerpt">
			<?php the_excerpt(); ?>
		</div>

		<a href="<?php the_permalink(); ?>" class="btn btn--primary btn--small post-classic__link">
			<?php
			$read_more = get_theme_mod( 'blog_read_more_text', 'Read More' );
			echo esc_html( $read_more );
			?>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
				<path d="M5 12h14M12 5l7 7-7 7"/>
			</svg>
		</a>
	</div>
</article>
<?php do_action( 'Opulentia_entry_after' ); ?>
<?php
// Add separator between posts except after the last one.
global $wp_query;
if ( $wp_query->current_post < $wp_query->post_count - 1 ) {
	echo '<hr class="post-classic__separator" aria-hidden="true">';
}
?>

<?php
/**
 * The blog page template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php do_action( 'Opulentia_blog_header_before' ); ?>
<section class="page-header">
	<div class="container">
		<h1 class="page-header__title"><?php esc_html_e( 'Blog', 'opulentia' ); ?></h1>
	</div>
</section>
<?php do_action( 'Opulentia_blog_header_after' ); ?>

<?php do_action( 'Opulentia_blog_before' ); ?>
<section class="blog-section">
	<div class="container">
		<div class="blog-grid">
			<main id="primary" class="blog-main">
				<?php do_action( 'Opulentia_content_top' ); ?>
				<?php if ( have_posts() ) : ?>

					<div class="posts-grid posts-grid--<?php echo esc_attr( Opulentia_get_blog_layout() ); ?>">
						<?php
						while ( have_posts() ) :
							the_post();
							Opulentia_render_blog_layout();
						endwhile;
						?>
					</div>

					<?php do_action( 'Opulentia_pagination_before' ); ?>
					<div class="pagination">
						<?php
						the_posts_pagination(
							array(
								'mid_size'  => 2,
								'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>',
								'next_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>',
							)
						);
						?>
					</div>
					<?php do_action( 'Opulentia_pagination_after' ); ?>

				<?php else : ?>

					<div class="no-results">
						<h2><?php esc_html_e( 'Nothing Found', 'opulentia' ); ?></h2>
						<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'opulentia' ); ?></p>
					</div>

				<?php endif; ?>
				<?php do_action( 'Opulentia_content_bottom' ); ?>
			</main>

			<?php do_action( 'Opulentia_sidebar_before' ); ?>
			<?php get_sidebar(); ?>
			<?php do_action( 'Opulentia_sidebar_after' ); ?>
		</div>
	</div>
</section>
<?php do_action( 'Opulentia_blog_after' ); ?>

<?php get_footer(); ?>

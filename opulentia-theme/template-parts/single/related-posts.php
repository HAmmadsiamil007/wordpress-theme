<?php
/**
 * Single Post: Related Posts
 *
 * Displays a grid of related posts based on shared categories.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get the current post's categories.
$categories = wp_get_post_categories( get_the_ID() );

if ( empty( $categories ) ) {
    return;
}

// Query related posts by category, excluding the current post.
$related_query = new WP_Query( array(
    'category__in'        => $categories,
    'post__not_in'        => array( get_the_ID() ),
    'posts_per_page'      => Opulentia_get_related_posts_count(),
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
) );

if ( ! $related_query->have_posts() ) {
    return;
}
?>
<section class="related-posts" aria-label="<?php esc_attr_e( 'Related Posts', 'opulentia' ); ?>">
    <h2 class="related-posts__title">
        <?php esc_html_e( 'You May Also Like', 'opulentia' ); ?>
    </h2>

    <div class="related-posts__grid">
        <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'related-post-card' ); ?>>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="related-post-card__image">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'medium' ); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="related-post-card__content">
                    <div class="related-post-card__meta">
                        <?php Opulentia_posted_on(); ?>
                    </div>
                    <h3 class="related-post-card__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>
<?php wp_reset_postdata(); ?>

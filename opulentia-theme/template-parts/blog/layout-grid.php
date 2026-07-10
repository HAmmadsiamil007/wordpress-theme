<?php
/**
 * Blog Layout: Grid
 *
 * Card-style blog posts arranged in a responsive grid.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php do_action( 'Opulentia_entry_before' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-card__image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( 'medium_large' ); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="post-card__content">
        <div class="post-card__meta">
            <?php Opulentia_posted_on(); ?>
        </div>

        <h2 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="post-card__excerpt">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="post-card__link">
            <?php
            $read_more = get_theme_mod( 'blog_read_more_text', 'Read More' );
            echo esc_html( $read_more );
            ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</article>
<?php do_action( 'Opulentia_entry_after' ); ?>

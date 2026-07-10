<?php
/**
 * Blog Layout: List
 *
 * Side-by-side layout with thumbnail on the left and content on the right.
 * Stacks vertically on mobile.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php do_action( 'Opulentia_entry_before' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-list' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-list__image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( 'medium' ); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="post-list__content">
        <div class="post-list__meta">
            <?php Opulentia_posted_categories(); ?>
            <span class="post-list__meta-sep">&bull;</span>
            <?php Opulentia_posted_on(); ?>
            <span class="post-list__meta-sep">&bull;</span>
            <?php Opulentia_posted_by(); ?>
        </div>

        <h2 class="post-list__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="post-list__excerpt">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="btn btn--primary btn--small post-list__link">
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

<?php
/**
 * The search results template
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title">
            <?php
            printf(
                esc_html__( 'Search Results for: %s', 'soleorigine' ),
                '<span class="search-query">' . esc_html( get_search_query() ) . '</span>'
            );
            ?>
        </h1>
    </div>
</section>

<section class="blog-section">
    <div class="container">
        <div class="blog-grid">
            <main id="primary" class="blog-main">
                <?php if ( have_posts() ) : ?>

                    <p class="search-results-count">
                        <?php
                        printf(
                            esc_html( _n( '%d result found', '%d results found', get_found_posts(), 'soleorigine' ) ),
                            get_found_posts()
                        );
                        ?>
                    </p>

                    <div class="posts-grid">
                        <?php
                        while ( have_posts() ) :
                            the_post();
                        ?>
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
                                        <?php soleorigine_posted_on(); ?>
                                    </div>

                                    <h2 class="post-card__title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>

                                    <div class="post-card__excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>

                                    <a href="<?php the_permalink(); ?>" class="post-card__link">
                                        <?php esc_html_e( 'Read More', 'soleorigine' ); ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <div class="pagination">
                        <?php
                        the_posts_pagination( array(
                            'mid_size'  => 2,
                            'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>',
                            'next_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>',
                        ) );
                        ?>
                    </div>

                <?php else : ?>

                    <div class="no-results">
                        <h2><?php esc_html_e( 'Nothing Found', 'soleorigine' ); ?></h2>
                        <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'soleorigine' ); ?></p>
                        <div class="search-form-wrap">
                            <?php get_search_form(); ?>
                        </div>
                    </div>

                <?php endif; ?>
            </main>

            <?php get_sidebar(); ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>

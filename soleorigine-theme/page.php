<?php
/**
 * The page template
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
        <h1 class="page-header__title"><?php the_title(); ?></h1>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="page-grid">
            <main id="primary" class="page-main">
                <?php
                while ( have_posts() ) :
                    the_post();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="page-content__image">
                                <?php the_post_thumbnail( 'large' ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="page-content__entry">
                            <?php
                            the_content();

                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'soleorigine' ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>

                        <?php
                        if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif;
                        ?>
                    </article>
                <?php endwhile; ?>
            </main>

            <?php get_sidebar(); ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>

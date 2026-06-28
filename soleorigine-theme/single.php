<?php
/**
 * The single post template
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

<section class="blog-section">
    <div class="container">
        <div class="blog-grid">
            <main id="primary" class="blog-main">
                <?php
                while ( have_posts() ) :
                    the_post();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
                        <header class="single-post__header">
                            <div class="single-post__meta">
                                <?php soleorigine_posted_on(); ?>
                                <span class="single-post__meta-sep">&bull;</span>
                                <?php soleorigine_posted_by(); ?>
                            </div>

                            <h1 class="single-post__title"><?php the_title(); ?></h1>

                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="single-post__thumbnail">
                                    <?php the_post_thumbnail( 'large' ); ?>
                                </div>
                            <?php endif; ?>
                        </header>

                        <div class="single-post__content entry-content">
                            <?php
                            the_content(
                                sprintf(
                                    wp_kses(
                                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'soleorigine' ),
                                        array( 'span' => array( 'class' => array() ) )
                                    ),
                                    get_the_title()
                                )
                            );

                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'soleorigine' ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>

                        <footer class="single-post__footer">
                            <?php soleorigine_post_tags(); ?>
                        </footer>
                    </article>

                    <nav class="post-navigation">
                        <?php
                        the_post_navigation( array(
                            'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'soleorigine' ) . '</span><span class="nav-title">%title</span>',
                            'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'soleorigine' ) . '</span><span class="nav-title">%title</span>',
                        ) );
                        ?>
                    </nav>

                    <?php
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    ?>
                <?php endwhile; ?>
            </main>

            <?php get_sidebar(); ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>

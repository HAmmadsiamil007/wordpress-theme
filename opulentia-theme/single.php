<?php
/**
 * The single post template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<?php do_action( 'Opulentia_single_before' ); ?>

<?php do_action( 'Opulentia_single_header_before' ); ?>
<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?php the_title(); ?></h1>
    </div>
</section>
<?php do_action( 'Opulentia_single_header_after' ); ?>

<section class="blog-section">
    <div class="container">
        <div class="blog-grid">
            <main id="primary" class="blog-main">
                <?php do_action( 'Opulentia_content_top' ); ?>
                <?php
                while ( have_posts() ) :
                    the_post();
                ?>
                    <?php do_action( 'Opulentia_entry_before' ); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
                        <header class="single-post__header">
                            <div class="single-post__meta">
                                <?php Opulentia_posted_on(); ?>
                                <span class="single-post__meta-sep">&bull;</span>
                                <?php Opulentia_posted_by(); ?>
                            </div>

                            <h1 class="single-post__title"><?php the_title(); ?></h1>

                            <?php if ( has_post_thumbnail() && ! Opulentia_blog_hide_featured_image() ) : ?>
                                <div class="single-post__thumbnail">
                                    <?php the_post_thumbnail( 'large' ); ?>
                                </div>
                            <?php endif; ?>
                        </header>

                        <?php do_action( 'Opulentia_entry_content_before' ); ?>
                        <div class="single-post__content entry-content">
                            <?php
                            the_content(
                                sprintf(
                                    wp_kses(
                                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'opulentia' ),
                                        array( 'span' => array( 'class' => array() ) )
                                    ),
                                    get_the_title()
                                )
                            );

                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'opulentia' ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>

                        </div>
                        <?php do_action( 'Opulentia_entry_content_after' ); ?>

                        <?php do_action( 'Opulentia_single_footer_before' ); ?>
                        <footer class="single-post__footer">
                            <?php Opulentia_post_tags(); ?>
                        </footer>
                        <?php do_action( 'Opulentia_single_footer_after' ); ?>
                    </article>
                    <?php do_action( 'Opulentia_entry_after' ); ?>

                    <?php get_template_part( 'template-parts/single/author-box' ); ?>

                    <?php Opulentia_post_navigation(); ?>

                    <?php
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    ?>
                <?php endwhile; ?>

                <?php get_template_part( 'template-parts/single/related-posts' ); ?>

                <?php do_action( 'Opulentia_content_bottom' ); ?>
            </main>

            <?php do_action( 'Opulentia_sidebar_before' ); ?>
            <?php get_sidebar(); ?>
            <?php do_action( 'Opulentia_sidebar_after' ); ?>
        </div>
    </div>
</section>
<?php do_action( 'Opulentia_single_after' ); ?>

<?php get_footer(); ?>

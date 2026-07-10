<?php
/**
 * The page template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<?php do_action( 'Opulentia_page_before' ); ?>

<?php do_action( 'Opulentia_page_header_before' ); ?>
<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?php the_title(); ?></h1>
    </div>
</section>
<?php do_action( 'Opulentia_page_header_after' ); ?>

<section class="page-section">
    <div class="container">
        <div class="page-grid">
            <main id="primary" class="page-main">
                <?php do_action( 'Opulentia_content_top' ); ?>
                <?php
                while ( have_posts() ) :
                    the_post();
                ?>
                    <?php do_action( 'Opulentia_entry_before' ); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="page-content__image">
                                <?php the_post_thumbnail( 'large' ); ?>
                            </div>
                        <?php endif; ?>

                        <?php do_action( 'Opulentia_entry_content_before' ); ?>
                        <div class="page-content__entry">
                            <?php
                            the_content();

                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'opulentia' ),
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
                    <?php do_action( 'Opulentia_entry_after' ); ?>
                <?php endwhile; ?>
                <?php do_action( 'Opulentia_content_bottom' ); ?>
            </main>

            <?php do_action( 'Opulentia_sidebar_before' ); ?>
            <?php get_sidebar(); ?>
            <?php do_action( 'Opulentia_sidebar_after' ); ?>
        </div>
    </div>
</section>
<?php do_action( 'Opulentia_page_after' ); ?>

<?php get_footer(); ?>

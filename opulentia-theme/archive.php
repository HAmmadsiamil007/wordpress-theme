<?php
/**
 * The archive template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<?php do_action( 'Opulentia_archive_before' ); ?>

<?php do_action( 'Opulentia_archive_header_before' ); ?>
<section class="page-header">
    <div class="container">
        <h1 class="page-header__title">
            <?php
            if ( is_category() ) {
                single_cat_title();
            } elseif ( is_tag() ) {
                single_tag_title();
            } elseif ( is_author() ) {
                the_author();
            } elseif ( is_year() ) {
                echo get_the_date( 'Y' );
            } elseif ( is_month() ) {
                echo get_the_date( 'F Y' );
            } elseif ( is_day() ) {
                echo get_the_date();
            } elseif ( is_post_type_archive() ) {
                post_type_archive_title();
            } else {
                esc_html_e( 'Archives', 'opulentia' );
            }
            ?>
        </h1>
        <?php
        if ( is_category() || is_tag() ) :
            $term = get_queried_object();
            if ( $term && ! empty( $term->description ) ) :
            ?>
                <p class="page-header__description"><?php echo esc_html( $term->description ); ?></p>
            <?php
            endif;
        endif;
        ?>
    </div>
</section>
<?php do_action( 'Opulentia_archive_header_after' ); ?>

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
                        the_posts_pagination( array(
                            'mid_size'  => 2,
                            'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>',
                            'next_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>',
                        ) );
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
<?php do_action( 'Opulentia_archive_after' ); ?>

<?php get_footer(); ?>

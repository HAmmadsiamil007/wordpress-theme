<?php
/**
 * Template Name: Full Width Page
 * Description: Full width page template without sidebar
 *
 * @package Opulentia
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-full-width' ); ?>>
            <div class="container--full">
                <header class="page-header">
                    <h1 class="page-header__title"><?php the_title(); ?></h1>
                </header>

                <div class="page-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();

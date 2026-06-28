<?php
/**
 * The 404 template
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<section class="error-404">
    <div class="container">
        <div class="error-404__content">
            <h1 class="error-404__title"><?php esc_html_e( '404', 'soleorigine' ); ?></h1>
            <h2 class="error-404__subtitle"><?php esc_html_e( 'Page Not Found', 'soleorigine' ); ?></h2>
            <p class="error-404__text">
                <?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'soleorigine' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
                <?php esc_html_e( 'Back to Homepage', 'soleorigine' ); ?>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>

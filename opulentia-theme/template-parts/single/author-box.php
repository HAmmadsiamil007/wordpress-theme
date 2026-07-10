<?php
/**
 * Single Post: Author Box
 *
 * Displays the author avatar, display name, and description
 * after the post content on single post pages.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$author_id      = get_the_author_meta( 'ID' );
$author_avatar  = get_avatar( $author_id, 80 );
$author_name    = get_the_author();
$author_bio     = get_the_author_meta( 'description' );

if ( empty( $author_bio ) ) {
    return;
}
?>
<aside class="author-box" itemscope itemtype="https://schema.org/Person">
    <div class="author-box__avatar">
        <?php if ( $author_avatar ) : ?>
            <?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else : ?>
            <div class="author-box__avatar-placeholder">
                <?php echo esc_html( strtoupper( substr( $author_name, 0, 1 ) ) ); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="author-box__content">
        <h3 class="author-box__name" itemprop="name">
            <span itemprop="author"><?php echo esc_html( $author_name ); ?></span>
        </h3>
        <p class="author-box__description" itemprop="description">
            <?php echo esc_html( $author_bio ); ?>
        </p>
        <a class="author-box__link" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
            <?php esc_html_e( 'View all posts by', 'opulentia' ); ?>
            <?php echo esc_html( $author_name ); ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</aside>

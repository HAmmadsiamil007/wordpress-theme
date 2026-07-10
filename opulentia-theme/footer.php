<?php
/**
 * The footer template - Powered by Footer Builder
 *
 * Renders via Opulentia_Footer_Builder which supports
 * 3 rows (above, main, below), multi-column widget grids,
 * newsletter signup, trust badges, and custom components.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php
// Render the footer using the Footer Builder (handles all rows and sections).
// Wrapped in a filter so plugins (e.g. Divi Theme Builder) can suppress it.
if ( apply_filters( 'opulentia_footer_enabled', true ) ) {
    do_action( 'Opulentia_footer_before' );
    do_action( 'Opulentia_colophon_before' );
    Opulentia_Footer_Builder::render();
    do_action( 'Opulentia_colophon_after' );
    do_action( 'Opulentia_footer_after' );
}
?>

<!-- Back to Top -->
<button class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'opulentia' ); ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>

</div><!-- #primary -->
<?php do_action( 'Opulentia_primary_content_after' ); ?>

<?php wp_footer(); ?>
<?php do_action( 'Opulentia_body_bottom' ); ?>

</body>
</html>

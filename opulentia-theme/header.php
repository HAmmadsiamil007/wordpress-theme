<?php
/**
 * The header template - Powered by Header Builder
 *
 * Renders via Opulentia_Header_Builder which supports
 * 3 layout presets (standard, centered, minimal) with
 * component-based rows configured in the Customizer.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php do_action( 'Opulentia_body_top' ); ?>

<!-- Reading Progress Bar -->
<div class="reading-progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
    <div class="reading-progress__bar"></div>
</div>

<a class="skip-link screen-reader-text" href="#primary">
    <?php esc_html_e( 'Skip to content', 'opulentia' ); ?>
</a>

<?php
// Render the full header using the Header Builder.
// Wrapped in a filter so plugins (e.g. Divi Theme Builder) can suppress it.
if ( apply_filters( 'opulentia_header_enabled', true ) ) {
    do_action( 'Opulentia_header_before' );
    Opulentia_Header_Builder::render();
    do_action( 'Opulentia_sticky_header' );
}
?>

<?php do_action( 'Opulentia_primary_content_before' ); ?>
<div id="primary" class="site-content">

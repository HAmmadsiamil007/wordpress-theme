<?php
/**
 * The sidebar template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$Opulentia_sidebar_id = apply_filters( 'Opulentia_sidebar_id', 'sidebar-1' );

if ( ! is_active_sidebar( $Opulentia_sidebar_id ) ) {
    return;
}
?>

<aside id="secondary" class="sidebar widget-area" role="complementary">
    <?php do_action( 'Opulentia_sidebar_before' ); ?>
    <?php dynamic_sidebar( $Opulentia_sidebar_id ); ?>
    <?php do_action( 'Opulentia_sidebar_after' ); ?>
</aside>

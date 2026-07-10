<?php
/**
 * Theme Info Page
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Theme Info Page
 */
/**
 * Redirect old theme info page to new dashboard.
 *
 * If someone visits the old Opulentia-info page directly,
 * redirect them to the new dashboard.
 */
function Opulentia_redirect_old_info_page() {
	if ( ! isset( $_GET['page'] ) || 'Opulentia-info' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	wp_safe_redirect( admin_url( 'themes.php?page=Opulentia' ) );
	exit;
}
add_action( 'admin_init', 'Opulentia_redirect_old_info_page' );

<?php
/**
 * The header template
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary">
    <?php esc_html_e( 'Skip to content', 'soleorigine' ); ?>
</a>

<header id="masthead" class="site-header" role="banner">
    <!-- Top Bar -->
    <div class="header-top">
        <div class="header-top__left">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <span><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
        </div>
        <div class="header-top__right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
            </svg>
            <span><?php esc_html_e( 'Free Worldwide Shipping', 'soleorigine' ); ?></span>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main">
        <!-- Logo -->
        <div class="site-logo">
            <?php soleorigine_custom_logo(); ?>
        </div>

        <!-- Navigation -->
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'soleorigine' ); ?>">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'container'      => false,
                'fallback_cb'    => false,
            ) );
            ?>
        </nav>

        <!-- Header Actions -->
        <div class="header-actions">
            <button class="header-actions__btn" aria-label="<?php esc_attr_e( 'Search', 'soleorigine' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>

            <a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_account_endpoint_url( 'dashboard' ) : '#' ); ?>"
               class="header-actions__btn"
               aria-label="<?php esc_attr_e( 'My Account', 'soleorigine' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </a>

            <a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_cart_url() : '#' ); ?>"
               class="header-actions__btn"
               aria-label="<?php esc_attr_e( 'Cart', 'soleorigine' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <?php if ( class_exists( 'WooCommerce' ) && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                    <span class="cart-count">
                        <?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?>
                    </span>
                <?php endif; ?>
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle mobile menu', 'soleorigine' ); ?>" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<div id="primary" class="site-content">

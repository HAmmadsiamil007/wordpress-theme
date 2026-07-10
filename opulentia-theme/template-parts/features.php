<?php
/**
 * Template part for displaying features section
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="features-bar">
    <div class="container">
        <div class="features-bar__grid">
            <div class="feature-item">
                <div class="feature-item__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Handcrafted Quality', 'opulentia' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Each pair meticulously crafted by skilled artisans', 'opulentia' ); ?></p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-item__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Premium Materials', 'opulentia' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Only the finest Italian leather and materials', 'opulentia' ); ?></p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-item__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Fast Delivery', 'opulentia' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Free worldwide shipping on all orders', 'opulentia' ); ?></p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-item__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Authenticity Guaranteed', 'opulentia' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Certificate of authenticity with every purchase', 'opulentia' ); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

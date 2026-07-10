<?php
/**
 * Template part for displaying hero section
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$hero_title = get_theme_mod( 'hero_title', __( 'opulentia', 'opulentia' ) );
$hero_subtitle = get_theme_mod( 'hero_subtitle', __( 'Premium Italian Footwear', 'opulentia' ) );
$hero_button_1_text = get_theme_mod( 'hero_button_1_text', __( 'Explore Collection', 'opulentia' ) );
$hero_button_1_url = get_theme_mod( 'hero_button_1_url', '/collection' );
$hero_button_2_text = get_theme_mod( 'hero_button_2_text', __( 'View Styles', 'opulentia' ) );
$hero_button_2_url = get_theme_mod( 'hero_button_2_url', '/styles' );
$hero_background = get_theme_mod( 'hero_background', '' );
?>

<section class="hero">
    <?php if ( $hero_background ) : ?>
        <img src="<?php echo esc_url( $hero_background ); ?>" alt="<?php echo esc_attr( $hero_title ); ?>" class="hero__background">
    <?php else : ?>
        <svg class="hero__background" viewBox="0 0 600 300" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
            <rect width="600" height="300" fill="#111"/>
            <path d="M50 250 Q300 100 550 250" stroke="#8B4513" stroke-width="4" fill="none"/>
            <ellipse cx="300" cy="260" rx="250" ry="30" fill="#8B4513" opacity="0.2"/>
            <path d="M80 230 Q300 120 520 230" fill="#A0522D"/>
            <path d="M120 200 Q300 130 480 200" fill="#8B4513"/>
            <path d="M180 180 Q300 140 420 180" fill="#654321"/>
            <path d="M50 250 Q300 150 550 250" stroke="#C9A96E" stroke-width="1" fill="none"/>
        </svg>
    <?php endif; ?>

    <div class="hero__content">
        <div class="hero__logo">
            <div class="hero__logo-mark">
                <svg viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 35 Q40 5 75 35" stroke="#C9A96E" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <span class="hero__logo-text">Opulentia</span>
            <span class="hero__logo-tagline">PREMIUM ITALIAN FOOTWEAR</span>
        </div>

        <h1 class="hero__title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>

        <div class="hero__buttons">
            <a href="<?php echo esc_url( $hero_button_1_url ); ?>" class="btn btn--primary">
                <?php echo esc_html( $hero_button_1_text ); ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="<?php echo esc_url( $hero_button_2_url ); ?>" class="btn btn--outline">
                <?php echo esc_html( $hero_button_2_text ); ?>
            </a>
        </div>
    </div>
</section>

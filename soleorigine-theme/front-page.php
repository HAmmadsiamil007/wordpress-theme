<?php
/**
 * The front page template
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<!-- Hero Section -->
<section class="hero" id="hero">
    <?php
    $hero_bg = get_theme_mod( 'hero_background', '' );
    if ( $hero_bg ) :
    ?>
        <img class="hero__background" src="<?php echo esc_url( $hero_bg ); ?>" alt="" loading="eager">
    <?php endif; ?>

    <div class="hero__overlay"></div>

    <div class="hero__content">
        <div class="hero__logo">
            <svg class="hero__logo-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="2"/>
                <text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>
            </svg>
            <span class="hero__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
            <span class="hero__logo-tagline"><?php esc_html_e( 'CRAFTED FROM ORIGIN. MADE TO LAST.', 'soleorigine' ); ?></span>
        </div>

        <h1 class="hero__title">
            <?php echo esc_html( get_theme_mod( 'hero_title', 'TIMELESS ELEGANCE. UNMATCHED COMFORT.' ) ); ?>
        </h1>

        <p class="hero__subtitle">
            <?php echo esc_html( get_theme_mod( 'hero_subtitle', 'Premium handcrafted shoes designed for the modern gentleman.' ) ); ?>
        </p>

        <div class="hero__buttons">
            <a href="<?php echo esc_url( get_theme_mod( 'hero_button_1_url', '#collection' ) ); ?>" class="btn btn--primary">
                <?php echo esc_html( get_theme_mod( 'hero_button_1_text', 'EXPLORE COLLECTION' ) ); ?>
            </a>
            <a href="<?php echo esc_url( get_theme_mod( 'hero_button_2_url', '#about' ) ); ?>" class="btn btn--outline">
                <?php echo esc_html( get_theme_mod( 'hero_button_2_text', 'OUR STORY' ) ); ?>
            </a>
        </div>
    </div>
</section>

<!-- Features Bar -->
<section class="features-bar">
    <div class="container">
        <div class="features-bar__grid">
            <div class="feature-item">
                <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Premium Materials', 'soleorigine' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Finest quality leather sourced ethically.', 'soleorigine' ); ?></p>
                </div>
            </div>

            <div class="feature-item">
                <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Expert Craftsmanship', 'soleorigine' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Handcrafted by skilled artisans.', 'soleorigine' ); ?></p>
                </div>
            </div>

            <div class="feature-item">
                <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Timeless Designs', 'soleorigine' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Classic styles for every occasion.', 'soleorigine' ); ?></p>
                </div>
            </div>

            <div class="feature-item">
                <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                <div class="feature-item__content">
                    <h3 class="feature-item__title"><?php esc_html_e( 'Worldwide Shipping', 'soleorigine' ); ?></h3>
                    <p class="feature-item__text"><?php esc_html_e( 'Delivered to your doorstep anywhere in the world.', 'soleorigine' ); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Collection Section -->
<section class="collection-section" id="collection">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle"><?php esc_html_e( 'Our Collection', 'soleorigine' ); ?></p>
            <h2 class="section-title"><?php esc_html_e( 'CLASSIC. REFINED. ICONIC.', 'soleorigine' ); ?></h2>
            <p class="section-description">
                <?php esc_html_e( 'Discover our range of meticulously handcrafted shoes, made for those who value quality and sophistication.', 'soleorigine' ); ?>
            </p>
        </div>

        <div class="product-grid">
            <?php
            if ( class_exists( 'WooCommerce' ) ) :
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 4,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                );

                $products = new WP_Query( $args );

                if ( $products->have_posts() ) :
                    while ( $products->have_posts() ) :
                        $products->the_post();
                        soleorigine_product_card( get_the_ID() );
                    endwhile;
                    wp_reset_postdata();
                endif;
            else :
                // Fallback products for demo purposes
                $demo_products = array(
                    array(
                        'name'        => __( 'CLASSIC OXFORD', 'soleorigine' ),
                        'description' => __( 'Dark Brown Calf Leather', 'soleorigine' ),
                        'price'       => 'PKR 48,500',
                    ),
                    array(
                        'name'        => __( 'CAP TOE DERBY', 'soleorigine' ),
                        'description' => __( 'Dark Brown Calf Leather', 'soleorigine' ),
                        'price'       => 'PKR 46,500',
                    ),
                    array(
                        'name'        => __( 'PENNY LOAFER', 'soleorigine' ),
                        'description' => __( 'Tan Brown Leather', 'soleorigine' ),
                        'price'       => 'PKR 43,500',
                    ),
                    array(
                        'name'        => __( 'MONK STRAP', 'soleorigine' ),
                        'description' => __( 'Dark Brown Calf Leather', 'soleorigine' ),
                        'price'       => 'PKR 47,500',
                    ),
                );

                foreach ( $demo_products as $product ) :
                ?>
                    <div class="product-card">
                        <div class="product-card__image">
                            <svg viewBox="0 0 200 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 160px; height: 80px;">
                                <path d="M20 80 Q100 20 180 80" stroke="#8B4513" stroke-width="3" fill="none"/>
                                <ellipse cx="100" cy="85" rx="80" ry="10" fill="#8B4513" opacity="0.3"/>
                                <path d="M30 75 Q100 30 170 75" fill="#A0522D"/>
                                <path d="M50 60 Q100 25 150 60" fill="#8B4513"/>
                            </svg>
                        </div>
                        <h3 class="product-card__title"><?php echo esc_html( $product['name'] ); ?></h3>
                        <p class="product-card__description"><?php echo esc_html( $product['description'] ); ?></p>
                        <div class="product-card__price"><?php echo esc_html( $product['price'] ); ?></div>
                        <a href="#" class="product-card__link">
                            <?php esc_html_e( 'Shop Now', 'soleorigine' ); ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                <?php
                endforeach;
            endif;
            ?>
        </div>

        <div class="text-center">
            <a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/collection' ) ); ?>" class="btn btn--outline-dark">
                <?php esc_html_e( 'View All Collection', 'soleorigine' ); ?>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-content">
                <p class="about-content__subtitle">
                    <?php echo esc_html( get_theme_mod( 'about_subtitle', 'CRAFTED WITH PASSION' ) ); ?>
                </p>

                <h2 class="about-content__title">
                    <?php echo esc_html( get_theme_mod( 'about_title', 'BUILT FROM HERITAGE. PERFECTED OVER TIME.' ) ); ?>
                </h2>

                <p class="about-content__text">
                    <?php echo esc_html( get_theme_mod( 'about_text', 'At Soleorigine, every pair is a testament to true craftsmanship. From the finest materials to the smallest details, we create shoes that stand the test of time.' ) ); ?>
                </p>

                <div class="about-features">
                    <div class="about-feature">
                        <svg class="about-feature__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                            <line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                        <h4 class="about-feature__title"><?php esc_html_e( 'Finest Leather', 'soleorigine' ); ?></h4>
                        <p class="about-feature__text"><?php esc_html_e( 'Top-grain leather for luxury & durability.', 'soleorigine' ); ?></p>
                    </div>

                    <div class="about-feature">
                        <svg class="about-feature__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                        <h4 class="about-feature__title"><?php esc_html_e( 'Handcrafted', 'soleorigine' ); ?></h4>
                        <p class="about-feature__text"><?php esc_html_e( 'Expertly handcrafted by skilled artisans.', 'soleorigine' ); ?></p>
                    </div>

                    <div class="about-feature">
                        <svg class="about-feature__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <h4 class="about-feature__title"><?php esc_html_e( 'Attention to Detail', 'soleorigine' ); ?></h4>
                        <p class="about-feature__text"><?php esc_html_e( 'Every stitch, every detail perfected.', 'soleorigine' ); ?></p>
                    </div>
                </div>

                <a href="<?php echo esc_url( home_url( '/about-us' ) ); ?>" class="btn btn--primary">
                    <?php esc_html_e( 'Learn More About Us', 'soleorigine' ); ?>
                </a>
            </div>

            <div class="about-image">
                <?php
                $about_image = get_theme_mod( 'about_image', '' );
                if ( $about_image ) :
                ?>
                    <img src="<?php echo esc_url( $about_image ); ?>" alt="<?php esc_attr_e( 'Craftsmanship', 'soleorigine' ); ?>" loading="lazy">
                <?php else : ?>
                    <svg viewBox="0 0 600 400" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto;">
                        <rect width="600" height="400" fill="#2a2a2a"/>
                        <path d="M100 350 Q300 100 500 350" stroke="#c9a96e" stroke-width="2" fill="none"/>
                        <ellipse cx="300" cy="360" rx="150" ry="20" fill="#c9a96e" opacity="0.2"/>
                        <path d="M120 330 Q300 120 480 330" fill="#8B4513"/>
                        <path d="M180 280 Q300 130 420 280" fill="#A0522D"/>
                        <circle cx="300" cy="200" r="30" fill="#c9a96e" opacity="0.3"/>
                        <text x="300" y="210" font-family="Playfair Display, serif" font-size="20" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>
                    </svg>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

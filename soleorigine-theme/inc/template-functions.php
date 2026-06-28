<?php
/**
 * Template Functions
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom body classes.
 *
 * @param array $classes Body classes.
 * @return array Modified body classes.
 */
function soleorigine_body_classes( $classes ) {
    // Add a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    // Add a class of no-sidebar when there is no sidebar.
    if ( ! is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'no-sidebar';
    }

    // Add WooCommerce body classes.
    if ( class_exists( 'WooCommerce' ) ) {
        if ( is_woocommerce() ) {
            $classes[] = 'woocommerce-page';
        }
    }

    return $classes;
}
add_filter( 'body_class', 'soleorigine_body_classes' );

/**
 * Add custom pingback url for single posts.
 */
function soleorigine_pingback_header() {
    if ( is_singular() && pingsopen() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'soleorigine_pingback_header' );

/**
 * Custom excerpt length.
 *
 * @param int $length Excerpt length.
 * @return int Modified excerpt length.
 */
function soleorigine_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'soleorigine_excerpt_length', 999 );

/**
 * Custom excerpt more.
 *
 * @param string $more Excerpt more text.
 * @return string Modified excerpt more text.
 */
function soleorigine_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'soleorigine_excerpt_more' );

/**
 * Add custom image sizes to WooCommerce product images.
 *
 * @param array $sizes Available image sizes.
 * @return array Modified image sizes.
 */
function soleorigine_woocommerce_image_sizes( $sizes ) {
    unset( $sizes['shop_catalog'] );
    unset( $sizes['shop_single'] );
    $sizes['shop_catalog'] = array(
        'height' => 600,
        'width'  => 600,
        'crop'   => 1,
    );
    $sizes['shop_single'] = array(
        'height' => 600,
        'width'  => 600,
        'crop'   => 1,
    );

    return $sizes;
}
add_filter( 'woocommerce_add_image_sizes', 'soleorigine_woocommerce_image_sizes' );

/**
 * Display the post thumbnail.
 */
function soleorigine_post_thumbnail() {
    if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
        return;
    }

    if ( is_singular() ) {
        printf(
            '<div class="post__thumbnail">%s</div>',
            get_the_post_thumbnail( null, 'large', array( 'loading' => 'lazy' ) )
        );
    } else {
        printf(
            '<a class="post__thumbnail" href="%s" aria-hidden="true">%s</a>',
            esc_url( get_the_permalink() ),
            get_the_post_thumbnail( null, 'soleorigine-blog', array( 'loading' => 'lazy' ) )
        );
    }
}

/**
 * Add schema markup for products.
 */
function soleorigine_product_schema() {
    if ( ! is_singular( 'product' ) ) {
        return;
    }

    global $post;
    $product = wc_get_product( $post->ID );

    if ( ! $product ) {
        return;
    }

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product->get_name(),
        'description' => $product->get_short_description(),
        'image'       => wp_get_attachment_url( $product->get_image_id() ),
        'brand'       => array(
            '@type' => 'Brand',
            'name'  => 'SoleOrigine',
        ),
        'offers'      => array(
            '@type'           => 'Offer',
            'url'             => get_permalink(),
            'priceCurrency'   => 'PKR',
            'price'           => $product->get_price(),
            'availability'    => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition'   => 'https://schema.org/NewCondition',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
}
add_action( 'wp_head', 'soleorigine_product_schema' );

/**
 * Add schema markup for organization.
 */
function soleorigine_organization_schema() {
    if ( ! is_front_page() ) {
        return;
    }

    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'Organization',
        'name'          => 'SoleOrigine',
        'url'           => home_url( '/' ),
        'logo'          => has_custom_logo() ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : '',
        'sameAs'        => array(
            get_theme_mod( 'social_facebook', '#' ),
            get_theme_mod( 'social_twitter', '#' ),
            get_theme_mod( 'social_instagram', '#' ),
            get_theme_mod( 'social_pinterest', '#' ),
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
}
add_action( 'wp_head', 'soleorigine_organization_schema' );

<?php
/**
 * Theme Setup
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function soleorigine_setup() {
    /**
     * Make theme available for translation.
     */
    load_theme_textdomain( 'soleorigine', SOLEORIGINE_DIR . '/languages' );

    /**
     * Add default posts and comments RSS feed links to head.
     */
    add_theme_support( 'automatic-feed-links' );

    /**
     * Let WordPress manage the document title.
     */
    add_theme_support( 'title-tag' );

    /**
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 630, true );
    add_image_size( 'soleorigine-product', 600, 600, true );
    add_image_size( 'soleorigine-blog', 800, 450, true );

    /**
     * Register navigation menus.
     */
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'soleorigine' ),
        'footer'  => esc_html__( 'Footer Menu', 'soleorigine' ),
    ) );

    /**
     * Switch default core markup to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    /**
     * Add support for custom logo.
     */
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    /**
     * Add support for selective refresh for widgets.
     */
    add_theme_support( 'customize-selective-refresh-widgets' );

    /**
     * Add support for custom background.
     */
    add_theme_support( 'custom-background', apply_filters( 'soleorigine_custom_background_args', array(
        'default-color' => '1a1a1a',
        'default-image' => '',
    ) ) );

    /**
     * Add support for WooCommerce.
     */
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 400,
        'single_image_width'    => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 1,
            'default_columns' => 4,
            'min_columns'     => 1,
            'max_columns'     => 4,
        ),
    ) );

    /**
     * Add support for Gutenberg block styles.
     */
    add_theme_support( 'wp-block-styles' );

    /**
     * Add support for responsive embedded content.
     */
    add_theme_support( 'responsive-embeds' );

    /**
     * Add support for editor styles.
     */
    add_theme_support( 'editor-styles' );
}
add_action( 'after_setup_theme', 'soleorigine_setup' );

/**
 * Register widget areas.
 */
function soleorigine_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Sidebar', 'soleorigine' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here.', 'soleorigine' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget__title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Footer Column 1', 'soleorigine' ),
        'id'            => 'footer-1',
        'description'   => esc_html__( 'First footer column.', 'soleorigine' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4 class="footer-column__title">',
        'after_title'   => '</h4>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Footer Column 2', 'soleorigine' ),
        'id'            => 'footer-2',
        'description'   => esc_html__( 'Second footer column.', 'soleorigine' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4 class="footer-column__title">',
        'after_title'   => '</h4>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Footer Column 3', 'soleorigine' ),
        'id'            => 'footer-3',
        'description'   => esc_html__( 'Third footer column.', 'soleorigine' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4 class="footer-column__title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'soleorigine_widgets_init' );

/**
 * Set the content width based on the theme's design.
 */
function soleorigine_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'soleorigine_content_width', 1200 );
}
add_action( 'after_setup_theme', 'soleorigine_content_width', 0 );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function soleorigine_pingback_header() {
    if ( is_singular() && pingsopen() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'soleorigine_pingback_header' );

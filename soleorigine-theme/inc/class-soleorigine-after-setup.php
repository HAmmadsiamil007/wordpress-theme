<?php
/**
 * After Setup Theme — Singleton
 *
 * Wraps after_setup_theme, widgets_init, CPT registration,
 * taxonomy registration, and pingback header logic.
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SoleOrigine_After_Setup class.
 */
class SoleOrigine_After_Setup {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Returns the singleton instance.
     *
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor — registers hooks.
     */
    private function __construct() {
        add_action( 'after_setup_theme', array( $this, 'setup' ) );
        add_action( 'after_setup_theme', array( $this, 'content_width' ), 0 );
        add_action( 'widgets_init', array( $this, 'widgets_init' ) );
        add_action( 'wp_head', array( $this, 'pingback_header' ) );
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
    }

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     */
    public function setup() {
        load_theme_textdomain( 'soleorigine', SOLEORIGINE_DIR . '/languages' );

        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );

        set_post_thumbnail_size( 1200, 630, true );
        add_image_size( 'soleorigine-product', 600, 600, true );
        add_image_size( 'soleorigine-blog', 800, 450, true );

        register_nav_menus( array(
            'primary' => esc_html__( 'Primary Menu', 'soleorigine' ),
            'footer'  => esc_html__( 'Footer Menu', 'soleorigine' ),
        ) );

        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ) );

        add_theme_support( 'custom-logo', array(
            'height'      => 80,
            'width'       => 200,
            'flex-height' => true,
            'flex-width'  => true,
        ) );

        add_theme_support( 'customize-selective-refresh-widgets' );

        add_theme_support( 'custom-background', apply_filters( 'soleorigine_custom_background_args', array(
            'default-color' => '1a1a1a',
            'default-image' => '',
        ) ) );

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

        add_theme_support( 'wp-block-styles' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'editor-styles' );
    }

    /**
     * Set the content width based on the theme's design.
     */
    public function content_width() {
        $GLOBALS['content_width'] = apply_filters( 'soleorigine_content_width', 1200 );
    }

    /**
     * Register widget areas.
     */
    public function widgets_init() {
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

    /**
     * Add a pingback url auto-discovery header for single posts, pages, or attachments.
     */
    public function pingback_header() {
        if ( is_singular() && pingsopen() ) {
            printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
        }
    }

    /**
     * Register Custom Post Types: Collections, Styles, Brands.
     */
    public function register_post_types() {
        register_post_type( 'collection', array(
            'labels'       => array(
                'name'               => esc_html_x( 'Collections', 'post type general name', 'soleorigine' ),
                'singular_name'      => esc_html_x( 'Collection', 'post type singular name', 'soleorigine' ),
                'menu_name'          => esc_html_x( 'Collections', 'admin menu', 'soleorigine' ),
                'add_new'            => esc_html__( 'Add New', 'soleorigine' ),
                'add_new_item'       => esc_html__( 'Add New Collection', 'soleorigine' ),
                'edit_item'          => esc_html__( 'Edit Collection', 'soleorigine' ),
                'new_item'           => esc_html__( 'New Collection', 'soleorigine' ),
                'view_item'          => esc_html__( 'View Collection', 'soleorigine' ),
                'search_items'       => esc_html__( 'Search Collections', 'soleorigine' ),
                'not_found'          => esc_html__( 'No collections found.', 'soleorigine' ),
                'not_found_in_trash' => esc_html__( 'No collections found in Trash.', 'soleorigine' ),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array( 'slug' => 'collections' ),
            'menu_icon'    => 'dashicons-layout',
            'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'show_in_rest' => true,
        ) );

        register_post_type( 'style', array(
            'labels'       => array(
                'name'               => esc_html_x( 'Styles', 'post type general name', 'soleorigine' ),
                'singular_name'      => esc_html_x( 'Style', 'post type singular name', 'soleorigine' ),
                'menu_name'          => esc_html_x( 'Styles', 'admin menu', 'soleorigine' ),
                'add_new'            => esc_html__( 'Add New', 'soleorigine' ),
                'add_new_item'       => esc_html__( 'Add New Style', 'soleorigine' ),
                'edit_item'          => esc_html__( 'Edit Style', 'soleorigine' ),
                'new_item'           => esc_html__( 'New Style', 'soleorigine' ),
                'view_item'          => esc_html__( 'View Style', 'soleorigine' ),
                'search_items'       => esc_html__( 'Search Styles', 'soleorigine' ),
                'not_found'          => esc_html__( 'No styles found.', 'soleorigine' ),
                'not_found_in_trash' => esc_html__( 'No styles found in Trash.', 'soleorigine' ),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array( 'slug' => 'styles' ),
            'menu_icon'    => 'dashicons-admin-appearance',
            'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'show_in_rest' => true,
        ) );

        register_post_type( 'brand', array(
            'labels'       => array(
                'name'               => esc_html_x( 'Brands', 'post type general name', 'soleorigine' ),
                'singular_name'      => esc_html_x( 'Brand', 'post type singular name', 'soleorigine' ),
                'menu_name'          => esc_html_x( 'Brands', 'admin menu', 'soleorigine' ),
                'add_new'            => esc_html__( 'Add New', 'soleorigine' ),
                'add_new_item'       => esc_html__( 'Add New Brand', 'soleorigine' ),
                'edit_item'          => esc_html__( 'Edit Brand', 'soleorigine' ),
                'new_item'           => esc_html__( 'New Brand', 'soleorigine' ),
                'view_item'          => esc_html__( 'View Brand', 'soleorigine' ),
                'search_items'       => esc_html__( 'Search Brands', 'soleorigine' ),
                'not_found'          => esc_html__( 'No brands found.', 'soleorigine' ),
                'not_found_in_trash' => esc_html__( 'No brands found in Trash.', 'soleorigine' ),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array( 'slug' => 'brands' ),
            'menu_icon'    => 'dashicons-shield',
            'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'show_in_rest' => true,
        ) );
    }

    /**
     * Register Taxonomies for Custom Post Types.
     */
    public function register_taxonomies() {
        register_taxonomy( 'collection_category', 'collection', array(
            'labels'            => array(
                'name'          => esc_html_x( 'Collection Categories', 'taxonomy general name', 'soleorigine' ),
                'singular_name' => esc_html_x( 'Collection Category', 'taxonomy singular name', 'soleorigine' ),
                'search_items'  => esc_html__( 'Search Collection Categories', 'soleorigine' ),
                'all_items'     => esc_html__( 'All Collection Categories', 'soleorigine' ),
                'parent_item'   => esc_html__( 'Parent Category', 'soleorigine' ),
                'edit_item'     => esc_html__( 'Edit Category', 'soleorigine' ),
                'update_item'   => esc_html__( 'Update Category', 'soleorigine' ),
                'add_new_item'  => esc_html__( 'Add New Category', 'soleorigine' ),
                'new_item_name' => esc_html__( 'New Category Name', 'soleorigine' ),
                'menu_name'     => esc_html__( 'Categories', 'soleorigine' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'collection-category' ),
            'show_admin_column' => true,
        ) );

        register_taxonomy( 'style_category', 'style', array(
            'labels'            => array(
                'name'          => esc_html_x( 'Style Categories', 'taxonomy general name', 'soleorigine' ),
                'singular_name' => esc_html_x( 'Style Category', 'taxonomy singular name', 'soleorigine' ),
                'search_items'  => esc_html__( 'Search Style Categories', 'soleorigine' ),
                'all_items'     => esc_html__( 'All Style Categories', 'soleorigine' ),
                'parent_item'   => esc_html__( 'Parent Category', 'soleorigine' ),
                'edit_item'     => esc_html__( 'Edit Category', 'soleorigine' ),
                'update_item'   => esc_html__( 'Update Category', 'soleorigine' ),
                'add_new_item'  => esc_html__( 'Add New Category', 'soleorigine' ),
                'new_item_name' => esc_html__( 'New Category Name', 'soleorigine' ),
                'menu_name'     => esc_html__( 'Categories', 'soleorigine' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'style-category' ),
            'show_admin_column' => true,
        ) );

        register_taxonomy( 'brand_category', 'brand', array(
            'labels'            => array(
                'name'          => esc_html_x( 'Brand Categories', 'taxonomy general name', 'soleorigine' ),
                'singular_name' => esc_html_x( 'Brand Category', 'taxonomy singular name', 'soleorigine' ),
                'search_items'  => esc_html__( 'Search Brand Categories', 'soleorigine' ),
                'all_items'     => esc_html__( 'All Brand Categories', 'soleorigine' ),
                'parent_item'   => esc_html__( 'Parent Category', 'soleorigine' ),
                'edit_item'     => esc_html__( 'Edit Category', 'soleorigine' ),
                'update_item'   => esc_html__( 'Update Category', 'soleorigine' ),
                'add_new_item'  => esc_html__( 'Add New Category', 'soleorigine' ),
                'new_item_name' => esc_html__( 'New Category Name', 'soleorigine' ),
                'menu_name'     => esc_html__( 'Categories', 'soleorigine' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'brand-category' ),
            'show_admin_column' => true,
        ) );
    }
}

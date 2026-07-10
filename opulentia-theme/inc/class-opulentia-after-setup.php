<?php
/**
 * After Setup Theme — Singleton
 *
 * Wraps after_setup_theme, widgets_init, CPT registration,
 * taxonomy registration, and pingback header logic.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_After_Setup class.
 */
class Opulentia_After_Setup {

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
        load_theme_textdomain( 'opulentia', Opulentia_DIR . '/languages' );

        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );

        set_post_thumbnail_size( 1200, 630, true );
        add_image_size( 'opulentia-product', 600, 600, true );
        add_image_size( 'opulentia-blog', 800, 450, true );
        add_image_size( 'opulentia-nav-thumb', 70, 70, true );

        register_nav_menus( array(
            'primary'      => esc_html__( 'Primary Menu', 'opulentia' ),
            'above-header' => esc_html__( 'Above Header Menu', 'opulentia' ),
            'below-header' => esc_html__( 'Below Header Menu', 'opulentia' ),
            'footer'       => esc_html__( 'Footer Menu', 'opulentia' ),
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

        add_theme_support( 'custom-background', apply_filters( 'Opulentia_custom_background_args', array(
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

        // Register Gutenberg editor color palette from the 9-color global palette.
        $this->register_editor_color_palette();
    }

    /**
     * Set the content width based on the theme's design.
     */
    public function content_width() {
        $GLOBALS['content_width'] = apply_filters( 'Opulentia_content_width', 1200 );
    }

    /**
     * Register widget areas.
     */
    public function widgets_init() {
        register_sidebar( array(
            'name'          => esc_html__( 'Sidebar', 'opulentia' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Add widgets here.', 'opulentia' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget__title">',
            'after_title'   => '</h3>',
        ) );

        register_sidebar( array(
            'name'          => esc_html__( 'Footer Column 1', 'opulentia' ),
            'id'            => 'footer-1',
            'description'   => esc_html__( 'First footer column.', 'opulentia' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h4 class="footer-column__title">',
            'after_title'   => '</h4>',
        ) );

        register_sidebar( array(
            'name'          => esc_html__( 'Footer Column 2', 'opulentia' ),
            'id'            => 'footer-2',
            'description'   => esc_html__( 'Second footer column.', 'opulentia' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h4 class="footer-column__title">',
            'after_title'   => '</h4>',
        ) );

        register_sidebar( array(
            'name'          => esc_html__( 'Footer Column 3', 'opulentia' ),
            'id'            => 'footer-3',
            'description'   => esc_html__( 'Third footer column.', 'opulentia' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h4 class="footer-column__title">',
            'after_title'   => '</h4>',
        ) );
    }

    /**
     * Register Gutenberg editor color palette based on the global 9-color system.
     *
     * Makes --opulentia-global-color-0 through 8 available in the block editor
     * as named color swatches, matching values set via the Customizer.
     */
    private function register_editor_color_palette() {
        $palette = array();
        $labels  = Opulentia_get_global_palette_labels();

        // Resolve fallback palette once (shared across all 9 color slots).
        $scheme  = get_theme_mod( 'color_scheme_preset', 'dark-luxury' );
        $default = Opulentia_get_global_palette_by_preset( $scheme );

        for ( $i = 0; $i <= 8; $i++ ) {
            $color = get_theme_mod( 'global-color-' . $i );
            if ( empty( $color ) ) {
                $color = $default[ $i ] ?? '#1a1a1a';
            }

            $palette[] = array(
                'name'  => $labels[ $i ] ?? sprintf( __( 'Global Color %d', 'opulentia' ), $i ),
'slug' => 'opulentia-global-' . $i,
                'color' => $color,
            );
        }

        add_theme_support( 'editor-color-palette', $palette );
    }

    /**
     * Add a pingback url auto-discovery header for single posts, pages, or attachments.
     */
    public function pingback_header() {
        if ( is_singular() && pings_open() ) {
            printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
        }
    }

    /**
     * Register Custom Post Types: Collections, Styles, Brands.
     */
    public function register_post_types() {
        register_post_type( 'collection', array(
            'labels'       => array(
                'name'               => esc_html_x( 'Collections', 'post type general name', 'opulentia' ),
                'singular_name'      => esc_html_x( 'Collection', 'post type singular name', 'opulentia' ),
                'menu_name'          => esc_html_x( 'Collections', 'admin menu', 'opulentia' ),
                'add_new'            => esc_html__( 'Add New', 'opulentia' ),
                'add_new_item'       => esc_html__( 'Add New Collection', 'opulentia' ),
                'edit_item'          => esc_html__( 'Edit Collection', 'opulentia' ),
                'new_item'           => esc_html__( 'New Collection', 'opulentia' ),
                'view_item'          => esc_html__( 'View Collection', 'opulentia' ),
                'search_items'       => esc_html__( 'Search Collections', 'opulentia' ),
                'not_found'          => esc_html__( 'No collections found.', 'opulentia' ),
                'not_found_in_trash' => esc_html__( 'No collections found in Trash.', 'opulentia' ),
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
                'name'               => esc_html_x( 'Styles', 'post type general name', 'opulentia' ),
                'singular_name'      => esc_html_x( 'Style', 'post type singular name', 'opulentia' ),
                'menu_name'          => esc_html_x( 'Styles', 'admin menu', 'opulentia' ),
                'add_new'            => esc_html__( 'Add New', 'opulentia' ),
                'add_new_item'       => esc_html__( 'Add New Style', 'opulentia' ),
                'edit_item'          => esc_html__( 'Edit Style', 'opulentia' ),
                'new_item'           => esc_html__( 'New Style', 'opulentia' ),
                'view_item'          => esc_html__( 'View Style', 'opulentia' ),
                'search_items'       => esc_html__( 'Search Styles', 'opulentia' ),
                'not_found'          => esc_html__( 'No styles found.', 'opulentia' ),
                'not_found_in_trash' => esc_html__( 'No styles found in Trash.', 'opulentia' ),
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
                'name'               => esc_html_x( 'Brands', 'post type general name', 'opulentia' ),
                'singular_name'      => esc_html_x( 'Brand', 'post type singular name', 'opulentia' ),
                'menu_name'          => esc_html_x( 'Brands', 'admin menu', 'opulentia' ),
                'add_new'            => esc_html__( 'Add New', 'opulentia' ),
                'add_new_item'       => esc_html__( 'Add New Brand', 'opulentia' ),
                'edit_item'          => esc_html__( 'Edit Brand', 'opulentia' ),
                'new_item'           => esc_html__( 'New Brand', 'opulentia' ),
                'view_item'          => esc_html__( 'View Brand', 'opulentia' ),
                'search_items'       => esc_html__( 'Search Brands', 'opulentia' ),
                'not_found'          => esc_html__( 'No brands found.', 'opulentia' ),
                'not_found_in_trash' => esc_html__( 'No brands found in Trash.', 'opulentia' ),
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
                'name'          => esc_html_x( 'Collection Categories', 'taxonomy general name', 'opulentia' ),
                'singular_name' => esc_html_x( 'Collection Category', 'taxonomy singular name', 'opulentia' ),
                'search_items'  => esc_html__( 'Search Collection Categories', 'opulentia' ),
                'all_items'     => esc_html__( 'All Collection Categories', 'opulentia' ),
                'parent_item'   => esc_html__( 'Parent Category', 'opulentia' ),
                'edit_item'     => esc_html__( 'Edit Category', 'opulentia' ),
                'update_item'   => esc_html__( 'Update Category', 'opulentia' ),
                'add_new_item'  => esc_html__( 'Add New Category', 'opulentia' ),
                'new_item_name' => esc_html__( 'New Category Name', 'opulentia' ),
                'menu_name'     => esc_html__( 'Categories', 'opulentia' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'collection-category' ),
            'show_admin_column' => true,
        ) );

        register_taxonomy( 'style_category', 'style', array(
            'labels'            => array(
                'name'          => esc_html_x( 'Style Categories', 'taxonomy general name', 'opulentia' ),
                'singular_name' => esc_html_x( 'Style Category', 'taxonomy singular name', 'opulentia' ),
                'search_items'  => esc_html__( 'Search Style Categories', 'opulentia' ),
                'all_items'     => esc_html__( 'All Style Categories', 'opulentia' ),
                'parent_item'   => esc_html__( 'Parent Category', 'opulentia' ),
                'edit_item'     => esc_html__( 'Edit Category', 'opulentia' ),
                'update_item'   => esc_html__( 'Update Category', 'opulentia' ),
                'add_new_item'  => esc_html__( 'Add New Category', 'opulentia' ),
                'new_item_name' => esc_html__( 'New Category Name', 'opulentia' ),
                'menu_name'     => esc_html__( 'Categories', 'opulentia' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'style-category' ),
            'show_admin_column' => true,
        ) );

        register_taxonomy( 'brand_category', 'brand', array(
            'labels'            => array(
                'name'          => esc_html_x( 'Brand Categories', 'taxonomy general name', 'opulentia' ),
                'singular_name' => esc_html_x( 'Brand Category', 'taxonomy singular name', 'opulentia' ),
                'search_items'  => esc_html__( 'Search Brand Categories', 'opulentia' ),
                'all_items'     => esc_html__( 'All Brand Categories', 'opulentia' ),
                'parent_item'   => esc_html__( 'Parent Category', 'opulentia' ),
                'edit_item'     => esc_html__( 'Edit Category', 'opulentia' ),
                'update_item'   => esc_html__( 'Update Category', 'opulentia' ),
                'add_new_item'  => esc_html__( 'Add New Category', 'opulentia' ),
                'new_item_name' => esc_html__( 'New Category Name', 'opulentia' ),
                'menu_name'     => esc_html__( 'Categories', 'opulentia' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'brand-category' ),
            'show_admin_column' => true,
        ) );
    }
}

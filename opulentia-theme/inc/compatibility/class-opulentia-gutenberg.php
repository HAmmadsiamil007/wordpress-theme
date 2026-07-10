<?php
/**
 * Gutenberg Block Compatibility — Singleton
 *
 * Registers a custom Opulentia block category and dynamic
 * Gutenberg blocks that mirror the theme sections (Hero,
 * Features, About/Brand Story, Testimonials, Product Grid).
 *
 * Each block is server-rendered via render_callback for full
 * integration with theme CSS.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Gutenberg class.
 */
class Opulentia_Gutenberg {

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
        add_action( 'init', array( $this, 'register_blocks' ) );
        add_filter( 'block_categories_all', array( $this, 'register_category' ), 10, 2 );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_styles' ) );
    }

    /**
     * Register the Opulentia block category.
     *
     * @param array   $categories Registered block categories.
     * @param WP_Post $post       Current post object.
     * @return array Modified categories.
     */
    public function register_category( $categories, $post ) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug'  => 'opulentia',
                    'title' => esc_html__( 'opulentia', 'opulentia' ),
                    'icon'  => 'star-filled',
                ),
            )
        );
    }

    /**
     * Register server-rendered dynamic blocks.
     */
    public function register_blocks() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        // Hero Block.
        register_block_type( 'Opulentia/hero', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Hero Banner', 'opulentia' ),
            'description'     => esc_html__( 'A full-screen hero banner with title, subtitle, and buttons.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="8" rx="1"/><rect x="3" y="14" width="14" height="2" rx="1"/><rect x="3" y="18" width="9" height="2" rx="1"/></svg>',
            ),
            'keywords'        => array( 'hero', 'banner', 'opulentia' ),
            'attributes'      => array(
                'title'       => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'TIMELESS ELEGANCE. UNMATCHED COMFORT.', 'opulentia' ),
                ),
                'subtitle'    => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Premium handcrafted shoes designed for the modern gentleman.', 'opulentia' ),
                ),
                'button1Text' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'EXPLORE COLLECTION', 'opulentia' ),
                ),
                'button1Url'  => array(
                    'type'    => 'string',
                    'default' => '#collection',
                ),
                'button2Text' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'OUR STORY', 'opulentia' ),
                ),
                'button2Url'  => array(
                    'type'    => 'string',
                    'default' => '#about',
                ),
                'backgroundId' => array(
                    'type' => 'number',
                ),
                'backgroundUrl' => array(
                    'type' => 'string',
                ),
            ),
            'render_callback' => array( $this, 'render_hero_block' ),
        ) );

        // Features Block.
        register_block_type( 'Opulentia/features', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Features Bar', 'opulentia' ),
            'description'     => esc_html__( 'A row of feature items with icons and descriptions.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="6" cy="6" r="2"/><rect x="10" y="5" width="11" height="2" rx="1"/><circle cx="6" cy="12" r="2"/><rect x="10" y="11" width="11" height="2" rx="1"/><circle cx="6" cy="18" r="2"/><rect x="10" y="17" width="11" height="2" rx="1"/></svg>',
            ),
            'keywords'        => array( 'features', 'icons', 'benefits', 'opulentia' ),
            'attributes'      => array(
                'features' => array(
                    'type'    => 'array',
                    'default' => array(
                        array(
                            'icon'        => 'tag',
                            'title'       => esc_html__( 'Premium Materials', 'opulentia' ),
                            'description' => esc_html__( 'Finest quality leather sourced ethically.', 'opulentia' ),
                        ),
                        array(
                            'icon'        => 'layers',
                            'title'       => esc_html__( 'Expert Craftsmanship', 'opulentia' ),
                            'description' => esc_html__( 'Handcrafted by skilled artisans.', 'opulentia' ),
                        ),
                        array(
                            'icon'        => 'clock',
                            'title'       => esc_html__( 'Timeless Designs', 'opulentia' ),
                            'description' => esc_html__( 'Classic styles for every occasion.', 'opulentia' ),
                        ),
                        array(
                            'icon'        => 'globe',
                            'title'       => esc_html__( 'Worldwide Shipping', 'opulentia' ),
                            'description' => esc_html__( 'Delivered to your doorstep anywhere.', 'opulentia' ),
                        ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'icon'        => array( 'type' => 'string' ),
                            'title'       => array( 'type' => 'string' ),
                            'description' => array( 'type' => 'string' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_features_block' ),
        ) );

        // Brand Story Block.
        register_block_type( 'Opulentia/brand-story', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Brand Story', 'opulentia' ),
            'description'     => esc_html__( 'Brand story section with image, content, and statistics.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4 4h7v16H4V4zm9 0h7v16h-7V4z"/></svg>',
            ),
            'keywords'        => array( 'about', 'brand', 'story', 'opulentia' ),
            'attributes'      => array(
                'subtitle'    => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'OUR HERITAGE', 'opulentia' ),
                ),
                'title'       => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'BUILT FROM HERITAGE. PERFECTED OVER TIME.', 'opulentia' ),
                ),
                'description' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'At Opulentia, every pair is a testament to true craftsmanship. From the finest materials to the smallest details, we create shoes that stand the test of time.', 'opulentia' ),
                ),
                'imageId'     => array( 'type' => 'number' ),
                'imageUrl'    => array( 'type' => 'string' ),
                'buttonText'  => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'DISCOVER OUR STORY', 'opulentia' ),
                ),
                'buttonUrl'   => array(
                    'type'    => 'string',
                    'default' => '/about-us',
                ),
                'stats'       => array(
                    'type'    => 'array',
                    'default' => array(
                        array(
                            'number' => '500+',
                            'label'  => esc_html__( 'Happy Clients', 'opulentia' ),
                        ),
                        array(
                            'number' => '100%',
                            'label'  => esc_html__( 'Italian Leather', 'opulentia' ),
                        ),
                        array(
                            'number' => '50+',
                            'label'  => esc_html__( 'Unique Designs', 'opulentia' ),
                        ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'number' => array( 'type' => 'string' ),
                            'label'  => array( 'type' => 'string' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_brand_story_block' ),
        ) );

        // Testimonials Block.
        register_block_type( 'Opulentia/testimonials', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Testimonials', 'opulentia' ),
            'description'     => esc_html__( 'Client testimonials grid with ratings.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 7H5v6h4V7zm10 0h-4v6h4V7zM8 19l-2-4h4l-2 4zm10 0l-2-4h4l-2 4z"/></svg>',
            ),
            'keywords'        => array( 'testimonial', 'review', 'client', 'opulentia' ),
            'attributes'      => array(
                'sectionSubtitle' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Client Stories', 'opulentia' ),
                ),
                'sectionTitle'    => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'WHAT OUR CLIENTS SAY', 'opulentia' ),
                ),
                'testimonials'    => array(
                    'type'    => 'array',
                    'default' => array(
                        array(
                            'text'   => '"The attention to detail is remarkable."',
                            'author' => esc_html__( 'Ahmed Hassan', 'opulentia' ),
                            'role'   => esc_html__( 'Business Executive', 'opulentia' ),
                            'rating' => 5,
                        ),
                        array(
                            'text'   => '"I\'ve never worn shoes this comfortable."',
                            'author' => esc_html__( 'Faisal Khan', 'opulentia' ),
                            'role'   => esc_html__( 'Entrepreneur', 'opulentia' ),
                            'rating' => 5,
                        ),
                        array(
                            'text'   => '"Worth every penny."',
                            'author' => esc_html__( 'Muhammad Shah', 'opulentia' ),
                            'role'   => esc_html__( 'Architect', 'opulentia' ),
                            'rating' => 5,
                        ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'text'   => array( 'type' => 'string' ),
                            'author' => array( 'type' => 'string' ),
                            'role'   => array( 'type' => 'string' ),
                            'rating' => array( 'type' => 'integer' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_testimonials_block' ),
        ) );

        // Product Grid Block.
        register_block_type( 'Opulentia/product-grid', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Product Grid', 'opulentia' ),
            'description'     => esc_html__( 'Display WooCommerce products in a grid layout.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/><path d="M5.5 17l2 2 4-4"/></svg>',
            ),
            'keywords'        => array( 'products', 'woocommerce', 'grid', 'shop', 'opulentia' ),
            'attributes'      => array(
                'sectionSubtitle'   => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Our Collection', 'opulentia' ),
                ),
                'sectionTitle'      => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'CLASSIC. REFINED. ICONIC.', 'opulentia' ),
                ),
                'sectionDescription' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Discover our range of meticulously handcrafted shoes.', 'opulentia' ),
                ),
                'productCount'      => array(
                    'type'    => 'number',
                    'default' => 4,
                ),
                'columns'           => array(
                    'type'    => 'number',
                    'default' => 4,
                ),
                'orderby'           => array(
                    'type'    => 'string',
                    'default' => 'menu_order',
                ),
                'showButton'        => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'buttonText'        => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'View All Collection', 'opulentia' ),
                ),
            ),
            'render_callback' => array( $this, 'render_product_grid_block' ),
        ) );

        // Counter Block.
        register_block_type( 'Opulentia/counter', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Counter', 'opulentia' ),
            'description'     => esc_html__( 'Animated number counter with label and suffix.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="5" width="14" height="14" rx="2"/><path d="M12 9v6M9 12h6"/></svg>',
            ),
            'keywords'        => array( 'counter', 'number', 'stat', 'opulentia' ),
            'attributes'      => array(
                'number'       => array(
                    'type'    => 'number',
                    'default' => 100,
                ),
                'label'        => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Satisfied Clients', 'opulentia' ),
                ),
                'suffix'       => array(
                    'type'    => 'string',
                    'default' => '+',
                ),
                'prefix'       => array(
                    'type'    => 'string',
                    'default' => '',
                ),
                'duration'     => array(
                    'type'    => 'number',
                    'default' => 2000,
                ),
                'icon'         => array(
                    'type'    => 'string',
                    'default' => 'users',
                ),
            ),
            'render_callback' => array( $this, 'render_counter_block' ),
        ) );

        // Pricing Table Block.
        register_block_type( 'Opulentia/pricing-table', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Pricing Table', 'opulentia' ),
            'description'     => esc_html__( 'Pricing plan with title, price, features, and button.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
            ),
            'keywords'        => array( 'pricing', 'plan', 'price', 'table', 'opulentia' ),
            'attributes'      => array(
                'planName'      => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Standard', 'opulentia' ),
                ),
                'price'         => array(
                    'type'    => 'string',
                    'default' => '$99',
                ),
                'currency'      => array(
                    'type'    => 'string',
                    'default' => '$',
                ),
                'interval'      => array(
                    'type'    => 'string',
                    'default' => esc_html__( '/ month', 'opulentia' ),
                ),
                'description'   => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Perfect for small businesses.', 'opulentia' ),
                ),
                'features'      => array(
                    'type'    => 'array',
                    'default' => array(
                        array( 'text' => esc_html__( 'Feature 1', 'opulentia' ), 'included' => true ),
                        array( 'text' => esc_html__( 'Feature 2', 'opulentia' ), 'included' => true ),
                        array( 'text' => esc_html__( 'Feature 3', 'opulentia' ), 'included' => false ),
                        array( 'text' => esc_html__( 'Feature 4', 'opulentia' ), 'included' => false ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'text'     => array( 'type' => 'string' ),
                            'included' => array( 'type' => 'boolean' ),
                        ),
                    ),
                ),
                'buttonText'    => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Get Started', 'opulentia' ),
                ),
                'buttonUrl'     => array(
                    'type'    => 'string',
                    'default' => '#',
                ),
                'featured'      => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
            ),
            'render_callback' => array( $this, 'render_pricing_table_block' ),
        ) );

        // Team Block.
        register_block_type( 'Opulentia/team', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Team', 'opulentia' ),
            'description'     => esc_html__( 'Team member cards with image, name, role, and social links.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="7" r="3"/><circle cx="16" cy="7" r="3"/><path d="M8 13c-2.7 0-5 1.3-5 4h10c0-2.7-2.3-4-5-4zm8 0c-2.7 0-5 1.3-5 4h10c0-2.7-2.3-4-5-4z"/></svg>',
            ),
            'keywords'        => array( 'team', 'member', 'staff', 'people', 'opulentia' ),
            'attributes'      => array(
                'sectionTitle'   => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Our Team', 'opulentia' ),
                ),
                'sectionSubtitle' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Meet the Experts', 'opulentia' ),
                ),
                'columns'        => array(
                    'type'    => 'number',
                    'default' => 3,
                ),
                'members'        => array(
                    'type'    => 'array',
                    'default' => array(
                        array(
                            'name'    => esc_html__( 'John Doe', 'opulentia' ),
                            'role'    => esc_html__( 'CEO & Founder', 'opulentia' ),
                            'bio'     => esc_html__( 'Visionary leader with 15+ years of experience.', 'opulentia' ),
                            'imageUrl' => '',
                            'social'  => array( 'facebook' => '#', 'twitter' => '#', 'linkedin' => '#' ),
                        ),
                        array(
                            'name'    => esc_html__( 'Jane Smith', 'opulentia' ),
                            'role'    => esc_html__( 'Lead Designer', 'opulentia' ),
                            'bio'     => esc_html__( 'Award-winning designer specializing in luxury.', 'opulentia' ),
                            'imageUrl' => '',
                            'social'  => array( 'facebook' => '#', 'twitter' => '#', 'linkedin' => '#' ),
                        ),
                        array(
                            'name'    => esc_html__( 'Mike Johnson', 'opulentia' ),
                            'role'    => esc_html__( 'Master Craftsman', 'opulentia' ),
                            'bio'     => esc_html__( '25 years of artisanal shoemaking experience.', 'opulentia' ),
                            'imageUrl' => '',
                            'social'  => array( 'facebook' => '#', 'twitter' => '#', 'linkedin' => '#' ),
                        ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'name'     => array( 'type' => 'string' ),
                            'role'     => array( 'type' => 'string' ),
                            'bio'      => array( 'type' => 'string' ),
                            'imageUrl' => array( 'type' => 'string' ),
                            'social'   => array( 'type' => 'object' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_team_block' ),
        ) );

        // CTA Block.
        register_block_type( 'Opulentia/cta', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Call to Action', 'opulentia' ),
            'description'     => esc_html__( 'Eye-catching call-to-action section with title, text, and button.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 3l-3 3h-4l-3-3H4v18h6l3-3h4l3 3V3z"/><path d="M13 7v10M9 7v10"/></svg>',
            ),
            'keywords'        => array( 'cta', 'call', 'action', 'banner', 'opulentia' ),
            'attributes'      => array(
                'title'       => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Ready to Elevate Your Style?', 'opulentia' ),
                ),
                'description' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Join thousands of satisfied customers and experience the difference.', 'opulentia' ),
                ),
                'buttonText'  => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Get Started', 'opulentia' ),
                ),
                'buttonUrl'   => array(
                    'type'    => 'string',
                    'default' => '#',
                ),
                'alignment'   => array(
                    'type'    => 'string',
                    'default' => 'center',
                ),
                'bgImageUrl'  => array(
                    'type'    => 'string',
                    'default' => '',
                ),
            ),
            'render_callback' => array( $this, 'render_cta_block' ),
        ) );

        // Logo Showcase Block.
        register_block_type( 'Opulentia/logo-showcase', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Logo Showcase', 'opulentia' ),
            'description'     => esc_html__( 'Display a grid of client or partner logos.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="5" height="5" rx="1"/><rect x="10" y="4" width="5" height="5" rx="1"/><rect x="17" y="4" width="5" height="5" rx="1"/><rect x="3" y="14" width="5" height="5" rx="1"/><rect x="10" y="14" width="5" height="5" rx="1"/><rect x="17" y="14" width="5" height="5" rx="1"/></svg>',
            ),
            'keywords'        => array( 'logo', 'client', 'partner', 'showcase', 'opulentia' ),
            'attributes'      => array(
                'sectionTitle'   => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Trusted By', 'opulentia' ),
                ),
                'columns'        => array(
                    'type'    => 'number',
                    'default' => 4,
                ),
                'logos'          => array(
                    'type'    => 'array',
                    'default' => array(
                        array( 'title' => 'Brand 1', 'imageUrl' => '', 'link' => '' ),
                        array( 'title' => 'Brand 2', 'imageUrl' => '', 'link' => '' ),
                        array( 'title' => 'Brand 3', 'imageUrl' => '', 'link' => '' ),
                        array( 'title' => 'Brand 4', 'imageUrl' => '', 'link' => '' ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'title'    => array( 'type' => 'string' ),
                            'imageUrl' => array( 'type' => 'string' ),
                            'link'     => array( 'type' => 'string' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_logo_showcase_block' ),
        ) );

        // FAQ Block.
        register_block_type( 'Opulentia/faq', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'FAQ', 'opulentia' ),
            'description'     => esc_html__( 'Frequently asked questions with accordion.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"/><path d="M12 16v1m0-13c-2.76 0-5 2.24-5 5h2c0-1.66 1.34-3 3-3s3 1.34 3 3c0 1.33-1.07 2-2 3-2 1-3 0-3 2h2c0-1 1-1 2-2s4-1.5 4-4.5S14.76 4 12 4z"/></svg>',
            ),
            'keywords'        => array( 'faq', 'questions', 'accordion', 'help', 'opulentia' ),
            'attributes'      => array(
                'sectionTitle'    => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Frequently Asked Questions', 'opulentia' ),
                ),
                'sectionSubtitle' => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Everything you need to know.', 'opulentia' ),
                ),
                'items'           => array(
                    'type'    => 'array',
                    'default' => array(
                        array(
                            'question' => esc_html__( 'What materials do you use?', 'opulentia' ),
                            'answer'   => esc_html__( 'We use the finest Italian leather sourced from sustainable tanneries.', 'opulentia' ),
                        ),
                        array(
                            'question' => esc_html__( 'How long does shipping take?', 'opulentia' ),
                            'answer'   => esc_html__( 'Domestic shipping takes 3-5 business days. International takes 7-14.', 'opulentia' ),
                        ),
                        array(
                            'question' => esc_html__( 'What is your return policy?', 'opulentia' ),
                            'answer'   => esc_html__( 'We offer free returns within 30 days of delivery.', 'opulentia' ),
                        ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'question' => array( 'type' => 'string' ),
                            'answer'   => array( 'type' => 'string' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_faq_block' ),
        ) );

        // Tabs Block.
        register_block_type( 'Opulentia/tabs', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Tabs', 'opulentia' ),
            'description'     => esc_html__( 'Tabbed content section.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><rect x="5" y="4" width="6" height="4" rx="1"/></svg>',
            ),
            'keywords'        => array( 'tabs', 'tabbed', 'content', 'opulentia' ),
            'attributes'      => array(
                'tabs' => array(
                    'type'    => 'array',
                    'default' => array(
                        array(
                            'title'   => esc_html__( 'Tab 1', 'opulentia' ),
                            'content' => esc_html__( 'Content for tab 1 goes here.', 'opulentia' ),
                        ),
                        array(
                            'title'   => esc_html__( 'Tab 2', 'opulentia' ),
                            'content' => esc_html__( 'Content for tab 2 goes here.', 'opulentia' ),
                        ),
                        array(
                            'title'   => esc_html__( 'Tab 3', 'opulentia' ),
                            'content' => esc_html__( 'Content for tab 3 goes here.', 'opulentia' ),
                        ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'title'   => array( 'type' => 'string' ),
                            'content' => array( 'type' => 'string' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_tabs_block' ),
        ) );

        // Progress Bar Block.
        register_block_type( 'Opulentia/progress-bar', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Progress Bar', 'opulentia' ),
            'description'     => esc_html__( 'Animated progress bars with labels and percentages.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5" width="18" height="4" rx="2" opacity="0.3"/><rect x="3" y="5" width="12" height="4" rx="2"/><rect x="3" y="15" width="18" height="4" rx="2" opacity="0.3"/><rect x="3" y="15" width="8" height="4" rx="2"/></svg>',
            ),
            'keywords'        => array( 'progress', 'bar', 'skill', 'percent', 'opulentia' ),
            'attributes'      => array(
                'items' => array(
                    'type'    => 'array',
                    'default' => array(
                        array(
                            'label'      => esc_html__( 'Quality', 'opulentia' ),
                            'percentage' => 95,
                            'color'      => '#c9a96e',
                        ),
                        array(
                            'label'      => esc_html__( 'Craftsmanship', 'opulentia' ),
                            'percentage' => 90,
                            'color'      => '#c9a96e',
                        ),
                        array(
                            'label'      => esc_html__( 'Customer Satisfaction', 'opulentia' ),
                            'percentage' => 98,
                            'color'      => '#c9a96e',
                        ),
                    ),
                    'items'   => array(
                        'type'       => 'object',
                        'properties' => array(
                            'label'      => array( 'type' => 'string' ),
                            'percentage' => array( 'type' => 'number' ),
                            'color'      => array( 'type' => 'string' ),
                        ),
                    ),
                ),
            ),
            'render_callback' => array( $this, 'render_progress_bar_block' ),
        ) );

        // Video Popup Block.
        register_block_type( 'Opulentia/video-popup', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Video Popup', 'opulentia' ),
            'description'     => esc_html__( 'Video thumbnail with play button that opens in a lightbox modal.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"/><polygon points="10,8 16,12 10,16"/></svg>',
            ),
            'keywords'        => array( 'video', 'popup', 'lightbox', 'youtube', 'vimeo', 'opulentia' ),
            'attributes'      => array(
                'videoUrl'      => array(
                    'type'    => 'string',
                    'default' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ),
                'thumbnailUrl'  => array(
                    'type'    => 'string',
                    'default' => '',
                ),
                'title'         => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Watch Our Story', 'opulentia' ),
                ),
                'description'   => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'See the craftsmanship behind every pair.', 'opulentia' ),
                ),
                'aspectRatio'   => array(
                    'type'    => 'string',
                    'default' => '16/9',
                ),
            ),
            'render_callback' => array( $this, 'render_video_popup_block' ),
        ) );

        // Icon Box Block.
        register_block_type( 'Opulentia/icon-box', array(
            'api_version'     => 2,
            'title'           => esc_html__( 'Icon Box', 'opulentia' ),
            'description'     => esc_html__( 'Icon with title and description in a card layout.', 'opulentia' ),
            'category'        => 'opulentia',
            'icon'            => array(
                'background' => '#1a1a1a',
                'foreground' => '#c9a96e',
                'src'        => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="18" rx="2"/><polygon points="12,6 15,10 12,14 9,10"/></svg>',
            ),
            'keywords'        => array( 'icon', 'box', 'feature', 'service', 'opulentia' ),
            'attributes'      => array(
                'icon'         => array(
                    'type'    => 'string',
                    'default' => 'shield',
                ),
                'title'        => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Premium Quality', 'opulentia' ),
                ),
                'description'  => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Finest materials and expert craftsmanship in every product.', 'opulentia' ),
                ),
                'showButton'   => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
                'buttonText'   => array(
                    'type'    => 'string',
                    'default' => esc_html__( 'Learn More', 'opulentia' ),
                ),
                'buttonUrl'    => array(
                    'type'    => 'string',
                    'default' => '#',
                ),
                'iconColor'    => array(
                    'type'    => 'string',
                    'default' => '#c9a96e',
                ),
            ),
            'render_callback' => array( $this, 'render_icon_box_block' ),
        ) );
    }

    /**
     * Render the Hero block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_hero_block( $attributes ) {
        $title        = isset( $attributes['title'] ) ? $attributes['title'] : '';
        $subtitle     = isset( $attributes['subtitle'] ) ? $attributes['subtitle'] : '';
        $btn1_txt     = isset( $attributes['button1Text'] ) ? $attributes['button1Text'] : '';
        $btn1_url     = isset( $attributes['button1Url'] ) ? $attributes['button1Url'] : '';
        $btn2_txt     = isset( $attributes['button2Text'] ) ? $attributes['button2Text'] : '';
        $btn2_url     = isset( $attributes['button2Url'] ) ? $attributes['button2Url'] : '';
        $bg_image     = isset( $attributes['backgroundUrl'] ) ? $attributes['backgroundUrl'] : '';

        ob_start();
        ?>
        <section class="hero">
            <?php if ( $bg_image ) : ?>
                <img class="hero__background" src="<?php echo esc_url( $bg_image ); ?>" alt="" loading="eager">
            <?php endif; ?>
            <div class="hero__overlay"></div>
            <div class="hero__content">
                <div class="hero__logo">
                    <svg class="hero__logo-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="1.5"/>
                        <text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>
                    </svg>
                    <span class="hero__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
                    <span class="hero__logo-tagline"><?php esc_html_e( 'CRAFTED FROM ORIGIN. MADE TO LAST.', 'opulentia' ); ?></span>
                </div>
                <?php if ( $title ) : ?>
                    <h1 class="hero__title"><?php echo esc_html( $title ); ?></h1>
                <?php endif; ?>
                <?php if ( $subtitle ) : ?>
                    <p class="hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>
                <div class="hero__buttons">
                    <?php if ( $btn1_txt && $btn1_url ) : ?>
                        <a href="<?php echo esc_url( $btn1_url ); ?>" class="btn btn--primary">
                            <?php echo esc_html( $btn1_txt ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $btn2_txt && $btn2_url ) : ?>
                        <a href="<?php echo esc_url( $btn2_url ); ?>" class="btn btn--outline">
                            <?php echo esc_html( $btn2_txt ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the Features block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_features_block( $attributes ) {
        $features = isset( $attributes['features'] ) ? $attributes['features'] : array();

        if ( empty( $features ) ) {
            return '';
        }

        ob_start();
        ?>
        <section class="features-bar">
            <div class="container">
                <div class="features-bar__grid">
                    <?php foreach ( $features as $feature ) : ?>
                        <div class="feature-item">
                            <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <?php $this->feature_icon_path( isset( $feature['icon'] ) ? $feature['icon'] : 'tag' ); ?>
                            </svg>
                            <div class="feature-item__content">
                                <h3 class="feature-item__title"><?php echo esc_html( $feature['title'] ); ?></h3>
                                <p class="feature-item__text"><?php echo esc_html( $feature['description'] ); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the Brand Story block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_brand_story_block( $attributes ) {
        $subtitle    = isset( $attributes['subtitle'] ) ? $attributes['subtitle'] : '';
        $title       = isset( $attributes['title'] ) ? $attributes['title'] : '';
        $description = isset( $attributes['description'] ) ? $attributes['description'] : '';
        $image_url   = isset( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
        $btn_text    = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
        $btn_url     = isset( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '';
        $stats       = isset( $attributes['stats'] ) ? $attributes['stats'] : array();

        ob_start();
        ?>
        <section class="brand-story">
            <div class="container">
                <div class="brand-story__grid">
                    <div class="brand-story__image">
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Craftsmanship', 'opulentia' ); ?>" loading="lazy">
                        <?php else : ?>
                            <img src="https://images.unsplash.com/photo-1473188588951-666fce8e7c68?w=800&h=1000&fit=crop" alt="<?php esc_attr_e( 'Artisan Craftsmanship', 'opulentia' ); ?>" loading="lazy">
                        <?php endif; ?>
                        <div class="brand-story__image-overlay">
                            <span class="brand-story__year"><?php esc_html_e( 'EST. 2019', 'opulentia' ); ?></span>
                        </div>
                    </div>
                    <div class="brand-story__content">
                        <?php if ( $subtitle ) : ?>
                            <p class="brand-story__subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                        <?php if ( $title ) : ?>
                            <h2 class="brand-story__title"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                        <?php if ( $description ) : ?>
                            <p class="brand-story__text"><?php echo esc_html( $description ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $stats ) ) : ?>
                            <div class="brand-story__stats">
                                <?php foreach ( $stats as $stat ) : ?>
                                    <div class="brand-story__stat">
                                        <span class="brand-story__stat-number"><?php echo esc_html( $stat['number'] ); ?></span>
                                        <span class="brand-story__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $btn_text && $btn_url ) : ?>
                            <a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn--primary">
                                <?php echo esc_html( $btn_text ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the Testimonials block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_testimonials_block( $attributes ) {
        $subtitle     = isset( $attributes['sectionSubtitle'] ) ? $attributes['sectionSubtitle'] : '';
        $title        = isset( $attributes['sectionTitle'] ) ? $attributes['sectionTitle'] : '';
        $testimonials = isset( $attributes['testimonials'] ) ? $attributes['testimonials'] : array();

        if ( empty( $testimonials ) ) {
            return '';
        }

        ob_start();
        ?>
        <section class="testimonials">
            <div class="container">
                <div class="section-header">
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                    <div class="gold-line"></div>
                    <?php if ( $title ) : ?>
                        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                </div>
                <div class="testimonials__grid">
                    <?php foreach ( $testimonials as $testimonial ) : ?>
                        <div class="testimonial-card">
                            <div class="testimonial-card__stars">
                                <?php for ( $i = 0; $i < absint( $testimonial['rating'] ); $i++ ) : ?>
                                    <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <?php endfor; ?>
                            </div>
                            <p class="testimonial-card__text"><?php echo esc_html( $testimonial['text'] ); ?></p>
                            <div class="testimonial-card__author">
                                <div class="testimonial-card__avatar"><?php echo esc_html( strtoupper( substr( $testimonial['author'], 0, 2 ) ) ); ?></div>
                                <div class="testimonial-card__info">
                                    <h4 class="testimonial-card__name"><?php echo esc_html( $testimonial['author'] ); ?></h4>
                                    <p class="testimonial-card__role"><?php echo esc_html( $testimonial['role'] ); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the Product Grid block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_product_grid_block( $attributes ) {
        $subtitle    = isset( $attributes['sectionSubtitle'] ) ? $attributes['sectionSubtitle'] : '';
        $title       = isset( $attributes['sectionTitle'] ) ? $attributes['sectionTitle'] : '';
        $description = isset( $attributes['sectionDescription'] ) ? $attributes['sectionDescription'] : '';
        $count       = isset( $attributes['productCount'] ) ? absint( $attributes['productCount'] ) : 4;
        $columns     = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 4;
        $orderby     = isset( $attributes['orderby'] ) ? sanitize_text_field( $attributes['orderby'] ) : 'menu_order';
        $show_btn    = isset( $attributes['showButton'] ) ? (bool) $attributes['showButton'] : true;
        $btn_text    = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : esc_html__( 'View All Collection', 'opulentia' );

        ob_start();
        ?>
        <section class="collection-section">
            <div class="container">
                <div class="section-header">
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                    <div class="gold-line"></div>
                    <?php if ( $title ) : ?>
                        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                    <?php if ( $description ) : ?>
                        <p class="section-description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <div class="product-grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
                        <?php
                        $products = new \WP_Query( array(
                            'post_type'      => 'product',
                            'posts_per_page' => $count,
                            'orderby'        => $orderby,
                            'order'          => 'ASC',
                        ) );

                        if ( $products->have_posts() ) :
                            while ( $products->have_posts() ) :
                                $products->the_post();
                                wc_get_template_part( 'content', 'product' );
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>

                    <?php if ( $show_btn && $btn_text ) : ?>
                        <div class="text-center">
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn--outline-dark">
                                <?php echo esc_html( $btn_text ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'Please install WooCommerce to display products.', 'opulentia' ); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Counter Block
    // -------------------------------------------------------------------------

    /**
     * Render the Counter block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_counter_block( $attributes ) {
        $number   = isset( $attributes['number'] ) ? absint( $attributes['number'] ) : 100;
        $label    = isset( $attributes['label'] ) ? $attributes['label'] : '';
        $suffix   = isset( $attributes['suffix'] ) ? $attributes['suffix'] : '+';
        $prefix   = isset( $attributes['prefix'] ) ? $attributes['prefix'] : '';
        $duration = isset( $attributes['duration'] ) ? absint( $attributes['duration'] ) : 2000;
        $icon     = isset( $attributes['icon'] ) ? $attributes['icon'] : 'users';

        ob_start();
        ?>
        <div class="so-counter" data-count="<?php echo esc_attr( $number ); ?>" data-duration="<?php echo esc_attr( $duration ); ?>">
            <div class="so-counter__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                    <?php $this->feature_icon_path( $icon ); ?>
                </svg>
            </div>
            <div class="so-counter__number">
                <span class="so-counter__prefix"><?php echo esc_html( $prefix ); ?></span>
                <span class="so-counter__value">0</span>
                <span class="so-counter__suffix"><?php echo esc_html( $suffix ); ?></span>
            </div>
            <?php if ( $label ) : ?>
                <p class="so-counter__label"><?php echo esc_html( $label ); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Pricing Table Block
    // -------------------------------------------------------------------------

    /**
     * Render the Pricing Table block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_pricing_table_block( $attributes ) {
        $plan       = isset( $attributes['planName'] ) ? $attributes['planName'] : '';
        $price      = isset( $attributes['price'] ) ? $attributes['price'] : '0';
        $currency   = isset( $attributes['currency'] ) ? $attributes['currency'] : '$';
        $interval   = isset( $attributes['interval'] ) ? $attributes['interval'] : '';
        $desc       = isset( $attributes['description'] ) ? $attributes['description'] : '';
        $features   = isset( $attributes['features'] ) ? $attributes['features'] : array();
        $btn_text   = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
        $btn_url    = isset( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '#';
        $featured   = isset( $attributes['featured'] ) ? (bool) $attributes['featured'] : false;

        $classes = array( 'so-pricing-card' );
        if ( $featured ) {
            $classes[] = 'so-pricing-card--featured';
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <?php if ( $featured ) : ?>
                <span class="so-pricing-card__badge"><?php esc_html_e( 'Popular', 'opulentia' ); ?></span>
            <?php endif; ?>
            <h3 class="so-pricing-card__plan"><?php echo esc_html( $plan ); ?></h3>
            <?php if ( $desc ) : ?>
                <p class="so-pricing-card__desc"><?php echo esc_html( $desc ); ?></p>
            <?php endif; ?>
            <div class="so-pricing-card__price">
                <span class="so-pricing-card__currency"><?php echo esc_html( $currency ); ?></span>
                <span class="so-pricing-card__amount"><?php echo esc_html( $price ); ?></span>
                <?php if ( $interval ) : ?>
                    <span class="so-pricing-card__interval"><?php echo esc_html( $interval ); ?></span>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $features ) ) : ?>
                <ul class="so-pricing-card__features">
                    <?php foreach ( $features as $feature ) : ?>
                        <li class="so-pricing-card__feature <?php echo ! empty( $feature['included'] ) ? 'is--included' : 'is--excluded'; ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <?php if ( ! empty( $feature['included'] ) ) : ?>
                                    <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
                                <?php else : ?>
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                <?php endif; ?>
                            </svg>
                            <span><?php echo esc_html( $feature['text'] ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ( $btn_text ) : ?>
                <a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn--primary so-pricing-card__btn">
                    <?php echo esc_html( $btn_text ); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Team Block
    // -------------------------------------------------------------------------

    /**
     * Render the Team block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_team_block( $attributes ) {
        $title    = isset( $attributes['sectionTitle'] ) ? $attributes['sectionTitle'] : '';
        $subtitle = isset( $attributes['sectionSubtitle'] ) ? $attributes['sectionSubtitle'] : '';
        $columns  = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 3;
        $members  = isset( $attributes['members'] ) ? $attributes['members'] : array();

        if ( empty( $members ) ) {
            return '';
        }

        ob_start();
        ?>
        <section class="so-team">
            <div class="container">
                <?php if ( $title || $subtitle ) : ?>
                    <div class="section-header">
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                        <div class="gold-line"></div>
                        <?php if ( $title ) : ?>
                            <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="so-team__grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
                    <?php foreach ( $members as $member ) : ?>
                        <div class="so-team__card">
                            <div class="so-team__image">
                                <?php if ( ! empty( $member['imageUrl'] ) ) : ?>
                                    <img src="<?php echo esc_url( $member['imageUrl'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy">
                                <?php else : ?>
                                    <div class="so-team__avatar"><?php echo esc_html( strtoupper( substr( $member['name'], 0, 2 ) ) ); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="so-team__info">
                                <h3 class="so-team__name"><?php echo esc_html( $member['name'] ); ?></h3>
                                <p class="so-team__role"><?php echo esc_html( $member['role'] ); ?></p>
                                <?php if ( ! empty( $member['bio'] ) ) : ?>
                                    <p class="so-team__bio"><?php echo esc_html( $member['bio'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $member['social'] ) ) : ?>
                                    <div class="so-team__social">
                                        <?php foreach ( $member['social'] as $platform => $url ) : ?>
                                            <?php if ( $url ) : ?>
                                                <a href="<?php echo esc_url( $url ); ?>" class="so-team__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $platform ); ?>">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                                        <?php if ( 'facebook' === $platform ) : ?>
                                                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                                        <?php elseif ( 'twitter' === $platform ) : ?>
                                                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                                                        <?php elseif ( 'linkedin' === $platform ) : ?>
                                                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>
                                                        <?php else : ?>
                                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                                        <?php endif; ?>
                                                    </svg>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // CTA Block
    // -------------------------------------------------------------------------

    /**
     * Render the CTA block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_cta_block( $attributes ) {
        $title       = isset( $attributes['title'] ) ? $attributes['title'] : '';
        $description = isset( $attributes['description'] ) ? $attributes['description'] : '';
        $btn_text    = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
        $btn_url     = isset( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '#';
        $alignment   = isset( $attributes['alignment'] ) ? $attributes['alignment'] : 'center';
        $bg_image    = isset( $attributes['bgImageUrl'] ) ? $attributes['bgImageUrl'] : '';

        $classes = array( 'so-cta', 'so-cta--' . $alignment );
        if ( $bg_image ) {
            $classes[] = 'so-cta--has-bg';
        }

        ob_start();
        ?>
        <section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <?php if ( $bg_image ) : ?>
                <div class="so-cta__bg" style="background-image: url(<?php echo esc_url( $bg_image ); ?>);"></div>
                <div class="so-cta__overlay"></div>
            <?php endif; ?>
            <div class="container">
                <div class="so-cta__content">
                    <?php if ( $title ) : ?>
                        <h2 class="so-cta__title"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                    <?php if ( $description ) : ?>
                        <p class="so-cta__description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                    <?php if ( $btn_text ) : ?>
                        <a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn--primary so-cta__btn">
                            <?php echo esc_html( $btn_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Logo Showcase Block
    // -------------------------------------------------------------------------

    /**
     * Render the Logo Showcase block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_logo_showcase_block( $attributes ) {
        $title    = isset( $attributes['sectionTitle'] ) ? $attributes['sectionTitle'] : '';
        $columns  = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 4;
        $logos    = isset( $attributes['logos'] ) ? $attributes['logos'] : array();

        if ( empty( $logos ) ) {
            return '';
        }

        ob_start();
        ?>
        <section class="so-logo-showcase">
            <div class="container">
                <?php if ( $title ) : ?>
                    <h2 class="so-logo-showcase__title"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
                <div class="so-logo-showcase__grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
                    <?php foreach ( $logos as $logo ) : ?>
                        <?php
                        $logo_html = '';
                        if ( ! empty( $logo['imageUrl'] ) ) {
                            $logo_html = '<img src="' . esc_url( $logo['imageUrl'] ) . '" alt="' . esc_attr( $logo['title'] ) . '" loading="lazy">';
                        } else {
                            $logo_html = '<span class="so-logo-showcase__placeholder">' . esc_html( $logo['title'] ) . '</span>';
                        }
                        ?>
                        <div class="so-logo-showcase__item">
                            <?php if ( ! empty( $logo['link'] ) ) : ?>
                                <a href="<?php echo esc_url( $logo['link'] ); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </a>
                            <?php else : ?>
                                <?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // FAQ Block
    // -------------------------------------------------------------------------

    /**
     * Render the FAQ block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_faq_block( $attributes ) {
        $title    = isset( $attributes['sectionTitle'] ) ? $attributes['sectionTitle'] : '';
        $subtitle = isset( $attributes['sectionSubtitle'] ) ? $attributes['sectionSubtitle'] : '';
        $items    = isset( $attributes['items'] ) ? $attributes['items'] : array();

        if ( empty( $items ) ) {
            return '';
        }

        ob_start();
        ?>
        <section class="so-faq">
            <div class="container">
                <?php if ( $title || $subtitle ) : ?>
                    <div class="section-header">
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                        <div class="gold-line"></div>
                        <?php if ( $title ) : ?>
                            <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="so-faq__list" itemscope="" itemtype="https://schema.org/FAQPage">
                    <?php foreach ( $items as $index => $item ) : ?>
                        <div class="so-faq__item" itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                            <button class="so-faq__question" aria-expanded="false" aria-controls="so-faq-answer-<?php echo esc_attr( $index ); ?>">
                                <span itemprop="name"><?php echo esc_html( $item['question'] ); ?></span>
                                <svg class="so-faq__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M9 18l6-6-6-6"/></svg>
                            </button>
                            <div id="so-faq-answer-<?php echo esc_attr( $index ); ?>" class="so-faq__answer" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" role="region">
                                <div itemprop="text"><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Tabs Block
    // -------------------------------------------------------------------------

    /**
     * Render the Tabs block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_tabs_block( $attributes ) {
        $tabs = isset( $attributes['tabs'] ) ? $attributes['tabs'] : array();

        if ( empty( $tabs ) ) {
            return '';
        }

        ob_start();
        ?>
        <div class="so-tabs">
            <div class="so-tabs__nav" role="tablist">
                <?php foreach ( $tabs as $index => $tab ) : ?>
                    <button class="so-tabs__tab <?php echo 0 === $index ? 'is-active' : ''; ?>"
                            role="tab"
                            aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                            aria-controls="so-tab-panel-<?php echo esc_attr( $index ); ?>"
                            id="so-tab-<?php echo esc_attr( $index ); ?>">
                        <?php echo esc_html( $tab['title'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php foreach ( $tabs as $index => $tab ) : ?>
                <div class="so-tabs__panel <?php echo 0 === $index ? 'is-active' : ''; ?>"
                     role="tabpanel"
                     aria-labelledby="so-tab-<?php echo esc_attr( $index ); ?>"
                     id="so-tab-panel-<?php echo esc_attr( $index ); ?>">
                    <?php echo wp_kses_post( wpautop( $tab['content'] ) ); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Progress Bar Block
    // -------------------------------------------------------------------------

    /**
     * Render the Progress Bar block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_progress_bar_block( $attributes ) {
        $items = isset( $attributes['items'] ) ? $attributes['items'] : array();

        if ( empty( $items ) ) {
            return '';
        }

        ob_start();
        ?>
        <div class="so-progress-bars">
            <?php foreach ( $items as $item ) : ?>
                <?php
                $label     = isset( $item['label'] ) ? $item['label'] : '';
                $percent   = isset( $item['percentage'] ) ? absint( $item['percentage'] ) : 0;
                $color     = isset( $item['color'] ) ? $item['color'] : '#c9a96e';
                $bar_id    = 'so-progress-' . wp_rand( 1000, 9999 );
                ?>
                <div class="so-progress">
                    <?php if ( $label ) : ?>
                        <div class="so-progress__header">
                            <span class="so-progress__label"><?php echo esc_html( $label ); ?></span>
                            <span class="so-progress__percent"><?php echo esc_html( $percent ); ?>%</span>
                        </div>
                    <?php endif; ?>
                    <div class="so-progress__track">
                        <div class="so-progress__bar"
                             id="<?php echo esc_attr( $bar_id ); ?>"
                             data-width="<?php echo esc_attr( $percent ); ?>"
                             style="width: 0%; background-color: <?php echo esc_attr( $color ); ?>;">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Video Popup Block
    // -------------------------------------------------------------------------

    /**
     * Render the Video Popup block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_video_popup_block( $attributes ) {
        $video_url   = isset( $attributes['videoUrl'] ) ? $attributes['videoUrl'] : '';
        $thumb_url   = isset( $attributes['thumbnailUrl'] ) ? $attributes['thumbnailUrl'] : '';
        $title       = isset( $attributes['title'] ) ? $attributes['title'] : '';
        $description = isset( $attributes['description'] ) ? $attributes['description'] : '';
        $aspect      = isset( $attributes['aspectRatio'] ) ? $attributes['aspectRatio'] : '16/9';

        // Extract video ID for YouTube/Vimeo embed.
        $embed_url   = '';
        if ( preg_match( '/(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/', $video_url, $matches ) ) {
            $embed_url = 'https://www.youtube.com/embed/' . $matches[1];
        } elseif ( preg_match( '/(?:vimeo\.com)\/(.+)/', $video_url, $matches ) ) {
            $embed_url = 'https://player.vimeo.com/video/' . $matches[1];
        } else {
            $embed_url = $video_url;
        }

        ob_start();
        ?>
        <div class="so-video-popup">
            <div class="so-video-popup__wrapper" style="aspect-ratio: <?php echo esc_attr( $aspect ); ?>;">
                <div class="so-video-popup__thumbnail">
                    <?php if ( $thumb_url ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                    <?php else : ?>
                        <div class="so-video-popup__placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="64" height="64"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16" fill="currentColor" stroke="none"/></svg>
                        </div>
                    <?php endif; ?>
                    <button class="so-video-popup__play" aria-label="<?php esc_attr_e( 'Play video', 'opulentia' ); ?>" data-embed="<?php echo esc_url( $embed_url ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="60" height="60">
                            <circle cx="12" cy="12" r="10" fill="rgba(0,0,0,0.6)" stroke="#fff" stroke-width="1.5"/>
                            <polygon points="10 8 16 12 10 16" fill="#fff" stroke="none"/>
                        </svg>
                    </button>
                </div>
                <?php if ( $title || $description ) : ?>
                    <div class="so-video-popup__content">
                        <?php if ( $title ) : ?>
                            <h3 class="so-video-popup__title"><?php echo esc_html( $title ); ?></h3>
                        <?php endif; ?>
                        <?php if ( $description ) : ?>
                            <p class="so-video-popup__desc"><?php echo esc_html( $description ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="so-video-popup__modal" aria-hidden="true">
                <div class="so-video-popup__modal-overlay"></div>
                <div class="so-video-popup__modal-content">
                    <button class="so-video-popup__modal-close" aria-label="<?php esc_attr_e( 'Close', 'opulentia' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="so-video-popup__modal-embed">
                        <iframe src="" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Icon Box Block
    // -------------------------------------------------------------------------

    /**
     * Render the Icon Box block.
     *
     * @param array $attributes Block attributes.
     * @return string HTML output.
     */
    public function render_icon_box_block( $attributes ) {
        $icon        = isset( $attributes['icon'] ) ? $attributes['icon'] : 'shield';
        $title       = isset( $attributes['title'] ) ? $attributes['title'] : '';
        $description = isset( $attributes['description'] ) ? $attributes['description'] : '';
        $show_btn    = isset( $attributes['showButton'] ) ? (bool) $attributes['showButton'] : false;
        $btn_text    = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
        $btn_url     = isset( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '#';
        $icon_color  = isset( $attributes['iconColor'] ) ? $attributes['iconColor'] : '#c9a96e';

        ob_start();
        ?>
        <div class="so-icon-box">
            <div class="so-icon-box__icon" style="color: <?php echo esc_attr( $icon_color ); ?>;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="40" height="40">
                    <?php $this->feature_icon_path( $icon ); ?>
                </svg>
            </div>
            <?php if ( $title ) : ?>
                <h3 class="so-icon-box__title"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            <?php if ( $description ) : ?>
                <p class="so-icon-box__desc"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
            <?php if ( $show_btn && $btn_text ) : ?>
                <a href="<?php echo esc_url( $btn_url ); ?>" class="so-icon-box__link">
                    <?php echo esc_html( $btn_text ); ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue editor-only block preview styles.
     *
     * Makes Opulentia Gutenberg blocks visible and properly styled
     * in the block editor canvas and sidebar.
     */
    public function enqueue_editor_styles() {
        wp_enqueue_style(
            'opulentia-editor',
            Opulentia_URI . '/css/admin.css',
            array(),
            Opulentia_VERSION
        );

        // Enqueue wp-blocks so we can attach inline script.
        wp_enqueue_script( 'wp-blocks' );

        wp_add_inline_script(
            'wp-blocks',
            '( function() {
    function setProgressBars() {
        var bars = document.querySelectorAll( ".so-progress__bar" );
        bars.forEach( function( bar ) {
            var width = bar.getAttribute( "data-width" );
            if ( width ) {
                bar.style.setProperty( "width", width + "%", "important" );
            }
        } );
    }
    if ( document.readyState === "loading" ) {
        document.addEventListener( "DOMContentLoaded", setProgressBars );
    } else {
        setProgressBars();
    }
    var observer = new MutationObserver( function() {
        setTimeout( setProgressBars, 50 );
    } );
    if ( document.body ) {
        observer.observe( document.body, { childList: true, subtree: true } );
    }
} )();'
        );

        wp_add_inline_style(
            'opulentia-editor',
            '
                /* ── Editor-only root variables ── */
                .editor-styles-wrapper .so-counter,
                .editor-styles-wrapper .so-pricing-card,
                .editor-styles-wrapper .so-team,
                .editor-styles-wrapper .so-cta,
                .editor-styles-wrapper .so-faq,
                .editor-styles-wrapper .so-tabs,
                .editor-styles-wrapper .so-progress-bars,
                .editor-styles-wrapper .so-video-popup,
                .editor-styles-wrapper .so-icon-box,
                .editor-styles-wrapper .so-logo-showcase {
                    --color-primary-dark: #1a1a1a;
                    --color-secondary-dark: #111111;
                    --color-gold: #c9a96e;
                    --color-gold-hover: #b8944f;
                    --color-accent: #b8860b;
                    --color-light-gold: #e8d5a3;
                    --color-white: #ffffff;
                    --color-off-white: #1e1e1e;
                    --color-light-gray: #2a2a2a;
                    --color-medium-gray: #999999;
                    --color-dark-gray: #666666;
                    --color-text: #f5f5f5;
                    --color-text-muted: #999999;
                    --color-border: #333333;
                    --font-heading: "Playfair Display", Georgia, serif;
                    --font-body: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                    --spacing-xs: 4px;
                    --spacing-sm: 8px;
                    --spacing-md: 16px;
                    --spacing-lg: 24px;
                    --spacing-xl: 32px;
                    --spacing-2xl: 48px;
                    --spacing-3xl: 64px;
                    --spacing-4xl: 80px;
                    --spacing-5xl: 100px;
                    --container-max: 1200px;
                    --radius-sm: 2px;
                    --radius-md: 4px;
                    --radius-lg: 8px;
                    --transition-fast: 0.2s ease;
                    --transition-normal: 0.3s ease;
                    --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
                }

                /* ── Editor-width constraints ── */
                .editor-styles-wrapper .wp-block[class*="opulentia"] {
                    max-width: 1200px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .editor-styles-wrapper .so-counter {
                    max-width: 320px;
                    margin-left: auto;
                    margin-right: auto;
                    background: #1a1a1a;
                    border-radius: 8px;
                    padding: 24px;
                }

                .editor-styles-wrapper .so-pricing-card {
                    max-width: 380px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .editor-styles-wrapper .so-icon-box {
                    max-width: 360px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .editor-styles-wrapper .so-team {
                    padding: 48px 0;
                }

                .editor-styles-wrapper .so-team__grid {
                    grid-template-columns: repeat(3, 1fr) !important;
                }

                .editor-styles-wrapper .so-cta {
                    border-radius: 8px;
                }

                .editor-styles-wrapper .so-faq {
                    max-width: 800px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .editor-styles-wrapper .so-tabs {
                    max-width: 760px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .editor-styles-wrapper .so-progress-bars {
                    max-width: 600px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .editor-styles-wrapper .so-logo-showcase__grid {
                    grid-template-columns: repeat(4, 1fr) !important;
                }

                .editor-styles-wrapper .so-video-popup__wrapper {
                    max-width: 700px;
                    margin-left: auto;
                    margin-right: auto;
                }

                /* ── Full-width blocks span editor width ── */
                .editor-styles-wrapper .hero,
                .editor-styles-wrapper .features-bar,
                .editor-styles-wrapper .brand-story {
                    max-width: 100% !important;
                }

                /* ── Hero block resizing for editor preview ── */
                .editor-styles-wrapper .hero {
                    min-height: 350px !important;
                    padding-top: 40px !important;
                }

                .editor-styles-wrapper .hero__title {
                    font-size: 2.5rem !important;
                }

                .editor-styles-wrapper .brand-story__grid,
                .editor-styles-wrapper .about-grid {
                    grid-template-columns: 1fr 1fr !important;
                }

                /* ── Interactive block visual hints in editor ── */
                .editor-styles-wrapper .so-faq__item::after {
                    content: "\25B6 Click to toggle";
                    display: block;
                    font-size: 0.75rem;
                    color: #666;
                    text-align: center;
                    padding: 4px 16px 8px;
                    background: rgba(201, 169, 110, 0.08);
                    font-style: italic;
                }

                .editor-styles-wrapper .so-faq__item.is-open::after {
                    display: none;
                }

                .editor-styles-wrapper .so-tabs__panel {
                    min-height: 60px;
                }

                .editor-styles-wrapper .so-video-popup__play {
                    cursor: default;
                }

                /* ── Placeholder text for empty content ── */
                .editor-styles-wrapper .so-counter__value:empty::before {
                    content: "100";
                    opacity: 0.4;
                }

                .editor-styles-wrapper .so-counter__label:empty::before {
                    content: "Counter Label";
                    opacity: 0.4;
                }

                /* ── Sidebar block preview sizing ── */
                .block-editor-block-preview__content .hero {
                    min-height: 200px !important;
                }

                .block-editor-block-preview__content .hero__content {
                    padding: 24px !important;
                }

                .block-editor-block-preview__content .so-team__grid {
                    grid-template-columns: repeat(2, 1fr) !important;
                }

                .block-editor-block-preview__content .so-logo-showcase__grid {
                    grid-template-columns: repeat(3, 1fr) !important;
                }

                .block-editor-block-preview__content .so-pricing-card {
                    padding: 24px !important;
                }

                .block-editor-block-preview__content .so-pricing-card__amount {
                    font-size: 2rem !important;
                }

                /* ── Narrow container (≤ 640px) adaptations ── */
                @container narrow (max-width: 640px) {
                    .editor-styles-wrapper .so-team__grid {
                        grid-template-columns: repeat(2, 1fr) !important;
                    }
                    .editor-styles-wrapper .so-logo-showcase__grid {
                        grid-template-columns: repeat(3, 1fr) !important;
                    }
                    .editor-styles-wrapper .features-bar__grid {
                        grid-template-columns: repeat(2, 1fr) !important;
                    }
                    .editor-styles-wrapper .testimonials__grid {
                        grid-template-columns: repeat(2, 1fr) !important;
                    }
                    .editor-styles-wrapper .product-grid {
                        grid-template-columns: repeat(2, 1fr) !important;
                    }
                    .editor-styles-wrapper .brand-story__grid {
                        grid-template-columns: 1fr !important;
                    }
                    .editor-styles-wrapper .so-pricing-card {
                        max-width: 100% !important;
                    }
                    .editor-styles-wrapper .so-counter {
                        max-width: 100% !important;
                    }
                    .editor-styles-wrapper .so-icon-box {
                        max-width: 100% !important;
                    }
                }

                @container narrow (max-width: 480px) {
                    .editor-styles-wrapper .so-team__grid {
                        grid-template-columns: 1fr !important;
                    }
                    .editor-styles-wrapper .so-logo-showcase__grid {
                        grid-template-columns: repeat(2, 1fr) !important;
                    }
                    .editor-styles-wrapper .features-bar__grid {
                        grid-template-columns: 1fr !important;
                    }
                    .editor-styles-wrapper .testimonials__grid {
                        grid-template-columns: 1fr !important;
                    }
                    .editor-styles-wrapper .product-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            '
        );
    }

    /**
     * Output SVG path for a feature icon by name.
     *
     * @param string $name Icon name.
     */
    private function feature_icon_path( $name ) {
        $icons = array(
            'tag'       => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
            'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            'clock'     => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            'checkmark' => '<path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M9 12l2 2 4-4"/>',
            'globe'     => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
            'heart'     => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
            'diamond'   => '<path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83M16.62 12l-5.74 9.94"/>',
            'layers'    => '<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
            'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
            'play'      => '<circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16" fill="currentColor" stroke="none"/>',
            'close'     => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
            'chevron-right' => '<path d="M9 18l6-6-6-6"/>',
            'arrow-right'   => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        );

        if ( isset( $icons[ $name ] ) ) {
            echo $icons[ $name ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            echo $icons['tag']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}

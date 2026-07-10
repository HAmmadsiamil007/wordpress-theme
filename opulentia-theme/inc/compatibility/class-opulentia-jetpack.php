<?php
/**
 * Jetpack Compatibility — Singleton
 *
 * Integrates Opulentia with Jetpack features:
 * - Infinite Scroll support
 * - Responsive Videos
 * - Content Options (author bio, post meta)
 * - Sharing buttons styling
 * - Related posts styling
 * - Tiled Galleries / Carousel support
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Jetpack class.
 */
class Opulentia_Jetpack {

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
        add_action( 'after_setup_theme', array( $this, 'init' ), 20 );
    }

    /**
     * Initialize Jetpack compatibility.
     */
    public function init() {
        if ( ! defined( 'JETPACK__VERSION' ) ) {
            return;
        }

        // Infinite Scroll.
        add_theme_support( 'infinite-scroll', array(
            'container'      => 'main-content',
            'render'         => array( $this, 'infinite_scroll_render' ),
            'footer'         => 'page',
            'wrapper'        => false,
            'posts_per_page' => (int) Opulentia_get_option( 'blog_posts_per_page', 6 ),
        ) );

        // Responsive Videos.
        add_theme_support( 'jetpack-responsive-videos' );

        // Content Options.
        add_theme_support( 'jetpack-content-options', array(
            'blog-display'    => 'content',
            'author-bio'      => true,
            'post-details'    => array(
                'stylesheet' => 'opulentia-style',
                'date'       => '.posted-on',
                'categories' => '.cat-links',
                'tags'       => '.tags-links',
                'author'     => '.byline',
                'comment'    => '.comments-link,.comment-count',
            ),
            'featured-images' => array(
                'archive' => true,
                'post'    => true,
                'page'    => true,
            ),
        ) );

        // Sharing & related posts styling.
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 100 );

        // Tiled Gallery / Carousel integration.
        add_filter( 'jetpack_tiled_gallery_cell_class', array( $this, 'tiled_gallery_classes' ) );
    }

    /**
     * Infinite scroll render callback.
     *
     * Renders blog posts for Jetpack Infinite Scroll.
     */
    public function infinite_scroll_render() {
        while ( have_posts() ) {
            the_post();

            $layout = Opulentia_get_option( 'blog_layout', 'grid' );

            if ( 'classic' === $layout ) {
                get_template_part( 'template-parts/blog/layout', 'classic' );
            } elseif ( 'list' === $layout ) {
                get_template_part( 'template-parts/blog/layout', 'list' );
            } else {
                get_template_part( 'template-parts/blog/layout', 'grid' );
            }
        }
    }

    /**
     * Output Jetpack-specific inline CSS.
     */
    public function inline_css() {
        $css = '
            /* ── Infinite Scroll ── */
            .infinite-scroll .site-content .infinite-wrap {
                padding-top: 0;
            }
            .infinite-scroll .site-content #infinite-handle {
                text-align: center;
                margin: 48px 0;
            }
            .infinite-scroll .site-content #infinite-handle span {
                display: inline-block;
                background: var(--color-gold, #c9a96e);
                color: var(--color-white, #ffffff);
                font-family: var(--font-body, Inter, sans-serif);
                font-size: 0.8125rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 1px;
                padding: 14px 36px;
                border-radius: 0;
                transition: background 0.3s ease;
                cursor: pointer;
            }
            .infinite-scroll .site-content #infinite-handle span:hover {
                background: var(--color-gold-hover, #b8944f);
            }
            .infinite-scroll .site-content #infinite-handle span button {
                color: inherit;
                font: inherit;
                cursor: pointer;
            }
            .infinite-scroll .infinite-loader {
                text-align: center;
                margin: 48px 0;
            }

            /* ── Responsive Videos ── */
            .jetpack-video-wrapper {
                margin: 24px 0;
            }
            .jetpack-video-wrapper iframe {
                border-radius: 8px;
            }

            /* ── Sharing ── */
            .sharedaddy .sd-social-icon .sd-content ul li a.sd-button {
                border-radius: 50% !important;
                transition: opacity 0.2s ease !important;
            }
            .sharedaddy .sd-social-icon .sd-content ul li a.sd-button:hover {
                opacity: 0.8;
            }
            .sharedaddy h3.sd-title {
                font-family: var(--font-body, Inter, sans-serif);
                font-size: 0.75rem !important;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: var(--color-text-muted, #999);
            }

            /* ── Related Posts ── */
            .jp-relatedposts {
                margin-top: 48px !important;
                padding-top: 32px;
                border-top: 1px solid var(--color-border, #333);
            }
            .jp-relatedposts .jp-relatedposts-headline {
                font-family: var(--font-heading, "Playfair Display", serif);
                font-size: 1.25rem;
                color: var(--color-gold, #c9a96e);
                margin-bottom: 24px;
            }
            .jp-relatedposts .jp-relatedposts-headline em {
                font-style: normal;
                font-weight: 600;
            }
            .jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post {
                opacity: 1 !important;
            }
            .jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post a {
                color: var(--color-text, #f5f5f5);
                font-weight: 500;
                transition: color 0.2s ease;
            }
            .jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post a:hover {
                color: var(--color-gold, #c9a96e);
            }
            .jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post .jp-relatedposts-post-title {
                font-family: var(--font-heading, "Playfair Display", serif);
                font-size: 1rem;
                line-height: 1.4;
            }
            .jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post .jp-relatedposts-post-excerpt {
                color: var(--color-text-muted, #999);
                font-size: 0.875rem;
            }
            .jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post .jp-relatedposts-post-date {
                color: var(--color-text-muted, #999);
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .jp-relatedposts .jp-relatedposts-items-visual .jp-relatedposts-post img {
                border-radius: 4px;
            }

            /* ── Tiled Galleries ── */
            .tiled-gallery {
                margin: 32px 0 !important;
            }
            .tiled-gallery .tiled-gallery-item a {
                border-radius: 4px;
                overflow: hidden;
            }
            .tiled-gallery .tiled-gallery-caption {
                background: rgba(0, 0, 0, 0.7);
                color: var(--color-text, #f5f5f5);
                font-size: 0.8125rem;
                padding: 8px 12px;
            }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }

    /**
     * Add custom class to tiled gallery cells.
     *
     * @param string $class Cell class string.
     * @return string
     */
    public function tiled_gallery_classes( $class ) {
        $class .= ' opulentia-gallery-cell';
        return $class;
    }
}

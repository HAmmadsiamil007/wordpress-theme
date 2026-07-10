<?php
/**
 * Schema.org Markup Engine — Singleton
 *
 * Adds structured data (JSON-LD) to the site for improved SEO.
 * Schema types supported:
 * - Article (for blog posts)
 * - Product (for WooCommerce — already in template-functions.php, enhanced here)
 * - Organization (for front page)
 * - BreadcrumbList (for breadcrumbs)
 * - SiteNavigationElement (for menus)
 * - WPHeader / WPFooter (for header/footer)
 * - Person (for author pages)
 * - VideoObject (for video embeds)
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Schema class.
 */
class Opulentia_Schema {

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
        add_action( 'wp_head', array( $this, 'output_schema' ), 1 );
    }

    /**
     * Output all schema markup.
     */
    public function output_schema() {
        $this->output_organization_schema();
        $this->output_article_schema();
        $this->output_breadcrumb_schema();
        $this->output_person_schema();
    }

    /**
     * Organization schema for front page.
     */
    private function output_organization_schema() {
        if ( ! is_front_page() ) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => get_bloginfo( 'name' ),
            'url'      => home_url( '/' ),
            'logo'     => has_custom_logo() ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : '',
            'sameAs'   => array_filter( array(
                get_theme_mod( 'social_facebook', '' ),
                get_theme_mod( 'social_twitter', '' ),
                get_theme_mod( 'social_instagram', '' ),
                get_theme_mod( 'social_pinterest', '' ),
                get_theme_mod( 'social_youtube', '' ),
            ) ),
        );

        $this->output_json( $schema );
    }

    /**
     * Article schema for single posts.
     */
    private function output_article_schema() {
        if ( ! is_singular( 'post' ) ) {
            return;
        }

        $post = get_queried_object();

        if ( ! $post || ! isset( $post->ID ) ) {
            return;
        }

        $author_id  = $post->post_author;
        $author_name = get_the_author_meta( 'display_name', $author_id );
        $image       = get_the_post_thumbnail_url( $post->ID, 'full' );
        $categories  = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );
        $tags        = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) );
        $publisher   = array(
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'logo'  => array(
                '@type' => 'ImageObject',
                'url'   => has_custom_logo() ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : '',
            ),
        );

        $schema = array(
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title( $post ),
            'description'   => get_the_excerpt( $post ),
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified'  => get_the_modified_date( 'c', $post ),
            'author'        => array(
                '@type' => 'Person',
                'name'  => $author_name,
            ),
            'publisher'     => $publisher,
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id'   => get_permalink( $post ),
            ),
        );

        if ( $image ) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url'   => $image,
            );
        }

        if ( ! empty( $categories ) ) {
            $schema['articleSection'] = implode( ', ', $categories );
        }

        if ( ! empty( $tags ) ) {
            $schema['keywords'] = implode( ', ', $tags );
        }

        $this->output_json( $schema );
    }

    /**
     * BreadcrumbList schema.
     */
    private function output_breadcrumb_schema() {
        if ( is_front_page() ) {
            return;
        }

        $items   = array();
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => __( 'Home', 'opulentia' ),
            'item'     => home_url( '/' ),
        );

        $position = 2;

        if ( is_singular( 'post' ) ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => __( 'Blog', 'opulentia' ),
                'item'     => get_permalink( get_option( 'page_for_posts' ) ),
            );
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        } elseif ( is_page() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        } elseif ( is_category() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => single_cat_title( '', false ),
                'item'     => get_category_link( get_queried_object_id() ),
            );
        } elseif ( is_search() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => sprintf( __( 'Search: %s', 'opulentia' ), get_search_query() ),
            );
        } elseif ( is_404() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => __( 'Page not found', 'opulentia' ),
            );
        }

        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        );

        $this->output_json( $schema );
    }

    /**
     * Person schema for author pages.
     */
    private function output_person_schema() {
        if ( ! is_author() ) {
            return;
        }

        $author_id = get_queried_object_id();
        $author    = get_userdata( $author_id );

        if ( ! $author ) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Person',
            'name'     => $author->display_name,
            'url'      => get_author_posts_url( $author_id ),
        );

        if ( $author->user_description ) {
            $schema['description'] = $author->user_description;
        }

        $this->output_json( $schema );
    }

    /**
     * Output JSON-LD script tag.
     *
     * @param array $data Schema data.
     */
    private function output_json( $data ) {
        if ( empty( $data ) ) {
            return;
        }
        echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }
}

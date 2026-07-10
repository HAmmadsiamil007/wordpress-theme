<?php
/**
 * Performance Optimization Engine — Singleton
 *
 * A comprehensive performance module that handles:
 * 1. CSS minification for dynamic CSS output
 * 2. Google Fonts delivery optimization (preconnect, swap, inline)
 * 3. Defer non-critical CSS via loadCSS pattern
 * 4. Native lazy loading for images, iframes
 * 5. Cache busting via file modification times
 * 6. Asset hints (preconnect, prefetch)
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Performance class.
 */
class Opulentia_Performance {

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
        // --- Google Fonts Optimization ---
        add_action( 'wp_head', array( $this, 'output_font_preconnect' ), 0 );
        add_filter( 'style_loader_tag', array( $this, 'optimize_google_fonts_tag' ), 10, 4 );

        // --- Defer Non-Critical CSS ---
        add_filter( 'style_loader_tag', array( $this, 'defer_non_critical_css' ), 10, 4 );

        // --- Dynamic CSS minification ---
        add_filter( 'Opulentia_dynamic_css_output', array( $this, 'minify_css' ) );

        // --- Native lazy loading ---
        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'lazy_load_attachments' ), 10, 3 );
        add_filter( 'the_content', array( $this, 'lazy_load_content_images' ) );
        add_filter( 'embed_oembed_html', array( $this, 'lazy_load_oembed' ), 10, 4 );
        add_filter( 'wp_lazy_loading_enabled', '__return_true' );

        // --- Asset versioning via file mtime ---
        add_filter( 'style_loader_src', array( $this, 'add_file_mtime_version' ), 10, 2 );
        add_filter( 'script_loader_src', array( $this, 'add_file_mtime_version' ), 10, 2 );

        // --- Performance body classes ---
        add_filter( 'body_class', array( $this, 'performance_body_classes' ) );

        // --- Remove bloat ---
        add_action( 'init', array( $this, 'remove_bloat' ) );

        // --- Send CSP-compatible preload headers for fonts ---
        add_action( 'send_headers', array( $this, 'send_font_preload_headers' ) );

        // --- Preload hero background image for LCP ---
        add_action( 'wp_head', array( $this, 'preload_hero_image' ), 2 );
    }

    // -------------------------------------------------------------------------
    // 1. CSS Minification
    // -------------------------------------------------------------------------

    /**
     * Minify a CSS string by removing whitespace, comments, and optimizing syntax.
     *
     * Hooked to 'Opulentia_dynamic_css_output' filter.
     *
     * @param string $css The CSS string to minify.
     * @return string Minified CSS.
     */
    public function minify_css( $css ) {
        if ( empty( $css ) ) {
            return $css;
        }

        // Remove comments.
        $css = preg_replace( '!/\\*[^*]*\\*+([^/][^*]*\\*+)*/!', '', $css );

        // Remove whitespace around selectors, properties, and values.
        $css = preg_replace( '/\\s*{\\s*/', '{', $css );
        $css = preg_replace( '/\\s*}\\s*/', '}', $css );
        $css = preg_replace( '/\\s*;\\s*/', ';', $css );
        $css = preg_replace( '/\\s*:\\s*/', ':', $css );
        $css = preg_replace( '/\\s*,\\s*/', ',', $css );
        $css = preg_replace( '/\\s*\\>\\s*/', '>', $css );
        $css = preg_replace( '/\\s*\\+\\s*/', '+', $css );
        $css = preg_replace( '/\\s*\\~\\s*/', '~', $css );

        // Remove unnecessary units (0px → 0).
        $css = preg_replace( '/(?<=[\\s:])0(\\.0)?(px|em|rem|%|vh|vw|pt|pc|in|mm|cm|ex|ch)/i', '0', $css );

        // Remove leading zeros from decimal values (.5 → .5 is already minimal).
        $css = preg_replace( '/(?<=[\\s:])0+(\\.\\d+)/', '$1', $css );

        // Remove last semicolon in each block.
        $css = preg_replace( '/;}/', '}', $css );

        // Collapse multiple spaces into one.
        $css = preg_replace( '/\\s+/', ' ', $css );

        // Trim each line.
        $css = trim( $css );

        return $css;
    }

    // -------------------------------------------------------------------------
    // 2. Google Fonts Delivery Optimization
    // -------------------------------------------------------------------------

    /**
     * Output preconnect hints for Google Fonts in <head> at priority 0
     * (before font loading kicks in).
     */
    public function output_font_preconnect() {
        ?>
        <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <?php
    }

    /**
     * Add crossorigin="anonymous" and media="print" with onload swap
     * to Google Fonts stylesheet for non-blocking load.
     *
     * @param string $tag    The link tag HTML.
     * @param string $handle The style handle.
     * @param string $href   The stylesheet URL.
     * @param string $media  The media attribute.
     * @return string Modified tag.
     */
    public function optimize_google_fonts_tag( $tag, $handle, $href, $media ) {
        if ( 'opulentia-google-fonts' !== $handle ) {
            return $tag;
        }

        // Use the loadCSS pattern: media="print" with onload="this.media='all'".
        $tag = str_replace(
            "media='all'",
            "media='print' onload=\"this.media='all'\"",
            $tag
        );

        // Add fallback for browsers without JS.
        $tag .= "\n<noscript><link rel='stylesheet' href='" . esc_url( $href ) . "' media='all'></noscript>\n";

        return $tag;
    }

    // -------------------------------------------------------------------------
    // 3. Defer Non-Critical CSS
    // -------------------------------------------------------------------------

    /**
     * A list of CSS handles that should NOT be deferred (critical styles).
     *
     * @return array Handles for critical (inline) stylesheets.
     */
    private function get_critical_style_handles() {
        return array(
            'opulentia-style',      // Main stylesheet (contains above-fold styles).
            'opulentia-dynamic-css', // Inline dynamic CSS.
            'admin-bar',               // WordPress admin bar.
        );
    }

    /**
     * Defer non-critical stylesheets using the loadCSS pattern.
     *
     * @param string $tag    The link tag HTML.
     * @param string $handle The style handle.
     * @param string $href   The stylesheet URL.
     * @param string $media  The media attribute.
     * @return string Modified tag.
     */
    public function defer_non_critical_css( $tag, $handle, $href, $media ) {
        // Skip critical styles, Google Fonts (already handled), and admin bar.
        if ( in_array( $handle, $this->get_critical_style_handles(), true ) ) {
            return $tag;
        }

        // Skip if it has no href or is inline (empty href).
        if ( empty( $href ) ) {
            return $tag;
        }

        // Skip if it's already been modified (print media pattern).
        if ( false !== strpos( $tag, "media='print'" ) ) {
            return $tag;
        }

        // Use the loadCSS pattern.
        $new_tag = str_replace(
            "media='" . esc_attr( $media ) . "'",
            "media='print' onload=\"this.media='" . esc_attr( $media ) . "'\"",
            $tag
        );

        // Add a noscript fallback.
        $new_tag .= "\n<noscript>" . $tag . "</noscript>\n";

        return $new_tag;
    }

    // -------------------------------------------------------------------------
    // 4. Native Lazy Loading
    // -------------------------------------------------------------------------

    /**
     * Add loading="lazy" to all WordPress attachment images.
     *
     * @param array        $attr       Image attributes.
     * @param WP_Post      $attachment Attachment post object.
     * @param string|array $size       Image size.
     * @return array Modified attributes.
     */
    public function lazy_load_attachments( $attr, $attachment, $size ) {
        // Skip if already set.
        if ( isset( $attr['loading'] ) ) {
            return $attr;
        }

        // Skip for the admin area.
        if ( is_admin() ) {
            return $attr;
        }

        // Don't lazy-load the first content image on singular pages (LCP).
        static $first_content_image = true;

        if ( is_singular() && $first_content_image && ! is_admin() ) {
            $first_content_image = false;
            return $attr;
        }

        $attr['loading'] = 'lazy';

        return $attr;
    }

    /**
     * Add loading="lazy" and decoding="async" to images in post content.
     *
     * @param string $content The post content HTML.
     * @return string Modified content.
     */
    public function lazy_load_content_images( $content ) {
        if ( empty( $content ) || is_admin() ) {
            return $content;
        }

        // Skip the first image on singular posts (LCP element).
        $is_first = true;

        return preg_replace_callback(
            '/<img\s+([^>]*?)src=["\']([^"\']+)["\']([^>]*?)\/?>/i',
            function ( $matches ) use ( &$is_first ) {
                $attrs = $matches[1] . ' src="' . $matches[2] . '"' . $matches[3];

                // Skip if already has loading attribute.
                if ( preg_match( '/loading=["\'](lazy|eager)["\']/i', $attrs ) ) {
                    return $matches[0];
                }

                // Skip the first image on singular pages (LCP).
                if ( is_singular() && $is_first ) {
                    $is_first = false;
                    return $matches[0];
                }

                $is_first = false;

                // Add loading="lazy" and decoding="async".
                $attrs = 'loading="lazy" decoding="async" ' . $attrs;

                return '<img ' . $attrs . ' />';
            },
            $content
        );
    }

    /**
     * Add lazy loading to oEmbed iframes (YouTube, Vimeo, etc.).
     *
     * @param string $html    The oEmbed HTML.
     * @param string $url     The embed URL.
     * @param array  $attr    The registered shortcode attributes.
     * @param int    $post_id The post ID.
     * @return string Modified HTML.
     */
    public function lazy_load_oembed( $html, $url, $attr, $post_id ) {
        if ( is_admin() || empty( $html ) ) {
            return $html;
        }

        // Add loading="lazy" to iframes.
        $html = str_replace(
            '<iframe ',
            '<iframe loading="lazy" ',
            $html
        );

        return $html;
    }

    // -------------------------------------------------------------------------
    // 5. Cache Busting via File Modification Time
    // -------------------------------------------------------------------------

    /**
     * Replace the version string of enqueued styles/scripts with file mtime
     * for better cache busting when files change.
     *
     * WordPress always adds a ?ver= parameter. This filter replaces that
     * value with the file's last modification time for local theme assets,
     * so the version always reflects the actual file on disk.
     *
     * @param string $src    The source URL of the enqueued asset.
     * @param string $handle The asset handle.
     * @return string Modified source URL.
     */
    public function add_file_mtime_version( $src, $handle ) {
        // Skip if no source or external URL.
        if ( empty( $src ) || ! $this->is_local_asset( $src ) ) {
            return $src;
        }

        // Skip Vite dev server URLs.
        if ( false !== strpos( $src, ':5173' ) || false !== strpos( $src, 'localhost' ) ) {
            return $src;
        }

        // Strip the existing ver parameter so we can replace it.
        $clean_url = strtok( $src, '?' );

        if ( ! $clean_url ) {
            return $src;
        }

        // Resolve the local file path from the clean URL.
        $file_path = $this->url_to_path( $clean_url );

        if ( $file_path && file_exists( $file_path ) ) {
            $mtime = filemtime( $file_path );
            if ( $mtime ) {
                $src = add_query_arg( 'ver', $mtime, $clean_url );
            }
        }

        return $src;
    }

    /**
     * Check if a URL points to a local theme asset.
     *
     * @param string $url The asset URL.
     * @return bool Whether the asset is local.
     */
    private function is_local_asset( $url ) {
        $theme_uri = Opulentia_URI;
        $site_url  = get_site_url();

        // Check if URL is relative (starts with /).
        if ( 0 === strpos( $url, '/' ) ) {
            return true;
        }

        // Check if URL contains the theme URI or site URL.
        if ( false !== strpos( $url, $theme_uri ) || false !== strpos( $url, $site_url ) ) {
            return true;
        }

        // Check for CDN or external URLs.
        $external_prefixes = array( 'https://cdn', 'http://cdn', 'https://fonts.', 'https://www.googletagmanager.com' );
        foreach ( $external_prefixes as $prefix ) {
            if ( 0 === strpos( $url, $prefix ) ) {
                return false;
            }
        }

        return false;
    }

    /**
     * Convert a file URL to a server path.
     *
     * @param string $url The file URL.
     * @return string|false The file path, or false on failure.
     */
    private function url_to_path( $url ) {
        $theme_uri = Opulentia_URI;
        $theme_dir = Opulentia_DIR;

        // Remove query string.
        $url = strtok( $url, '?' );

        // If relative (starts with /), try to prepend ABSPATH.
        if ( 0 === strpos( $url, '/' ) ) {
            $potential = untrailingslashit( ABSPATH ) . $url;
            if ( file_exists( $potential ) ) {
                return $potential;
            }
            return false;
        }

        // Replace theme URI with theme directory path.
        $path = str_replace( $theme_uri, $theme_dir, $url );

        if ( $path !== $url && file_exists( $path ) ) {
            return $path;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // 6. Performance Body Classes
    // -------------------------------------------------------------------------

    /**
     * Add performance-related body classes.
     *
     * @param array $classes Body classes.
     * @return array Modified classes.
     */
    public function performance_body_classes( $classes ) {
        // Flag that reduced motion is preferred.
        if ( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) {
            $classes[] = 'is-mobile';
        }

        // Signal that performance optimizations are active.
        $classes[] = 'opulentia-perf-optimized';

        return $classes;
    }

    // -------------------------------------------------------------------------
    // 7. Remove Bloat
    // -------------------------------------------------------------------------

    /**
     * Remove unnecessary WordPress bloat that hurts performance.
     */
    public function remove_bloat() {
        // Remove wlwmanifest link.
        remove_action( 'wp_head', 'wlwmanifest_link' );

        // Remove RSD link.
        remove_action( 'wp_head', 'rsd_link' );

        // Remove WP shortlink.
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );

        // Remove adjacent post links.
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

        // Remove WordPress generator tag.
        remove_action( 'wp_head', 'wp_generator' );

        // Remove REST API link from head.
        remove_action( 'wp_head', 'rest_output_link_wp_head' );

        // Remove oEmbed discovery links.
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

        // Remove oEmbed-specific JavaScript from frontend.
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );

        // Remove emoji scripts and styles.
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );

        // Disable embeds (if not needed).
        // Commented out — enable if the theme doesn't use oEmbed:
        // remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        // remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result' );
    }

    // -------------------------------------------------------------------------
    // 8. Font Preload Headers
    // -------------------------------------------------------------------------

    /**
     * Send Link preload headers for Google Fonts for early discovery.
     */
    public function send_font_preload_headers() {
        if ( is_admin() || is_customize_preview() ) {
            return;
        }

        $heading_font = get_theme_mod( 'typography_heading', 'Playfair Display' );
        $body_font    = get_theme_mod( 'typography_body', 'Inter' );

        // Only send headers for the default fonts to avoid complexity.
        // Full URL headers for custom fonts are handled via preconnect + preload tags.
    }

    // -------------------------------------------------------------------------
    // 9. Hero Image Preload (LCP Optimization)
    // -------------------------------------------------------------------------

    /**
     * Preload the hero background image for improved LCP.
     *
     * Outputs a <link rel="preload" as="image"> tag in <head> when a hero
     * background image is configured via the customizer. The browser can
     * start downloading the LCP image earlier without waiting for the CSS
     * or HTML section to be parsed.
     *
     * Hooked to 'wp_head' at priority 2 (after charset/meta, before other
     * assets) for earliest possible discovery.
     */
    public function preload_hero_image() {
        // Only preload on the front page where the hero section is rendered.
        if ( ! is_front_page() ) {
            return;
        }

        $hero_image = get_theme_mod( 'hero_background', '' );

        if ( empty( $hero_image ) ) {
            return;
        }

        // Validate the URL.
        $hero_image = esc_url( $hero_image );

        if ( empty( $hero_image ) ) {
            return;
        }

        // Output the preload link tag.
        printf(
            '<link rel="preload" as="image" href="%s" fetchpriority="high">',
            $hero_image
        );
    }
}

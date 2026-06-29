<?php
/**
 * Enqueue scripts and styles
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// New SoleOrigine_Enqueue singleton handles this when active.
if ( class_exists( 'SoleOrigine_Enqueue' ) ) {
    return;
}

require_once SOLEORIGINE_DIR . '/inc/vite.php';

/**
 * Enqueue front-end styles and scripts.
 */
function soleorigine_scripts() {
    // Google Fonts
    wp_enqueue_style(
        'soleorigine-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'soleorigine-style',
        get_stylesheet_uri(),
        array(),
        SOLEORIGINE_VERSION
    );

    // GSAP Core + ScrollTrigger from CDN
    wp_enqueue_script(
        'gsap-core',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/gsap.min.js',
        array(),
        '3.12.7',
        true
    );
    wp_enqueue_script(
        'gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/ScrollTrigger.min.js',
        array( 'gsap-core' ),
        '3.12.7',
        true
    );
    wp_enqueue_script(
        'gsap-scrollto',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/ScrollToPlugin.min.js',
        array( 'gsap-core' ),
        '3.12.7',
        true
    );

    // Enqueue via Vite if dev server is running or manifest exists
    $is_vite  = soleorigine_is_vite_running();
    $manifest_path = SOLEORIGINE_DIR . '/dist/.vite/manifest.json';

    if ( $is_vite || file_exists( $manifest_path ) ) {
        soleorigine_enqueue_vite_assets();
    } else {
        soleorigine_enqueue_direct();
    }

    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'soleorigine_scripts' );

/**
 * Fallback: enqueue assets directly (no Vite).
 */
function soleorigine_enqueue_direct() {
    // WooCommerce styles
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_style(
            'soleorigine-woocommerce',
            SOLEORIGINE_URI . '/css/woocommerce.css',
            array( 'soleorigine-style' ),
            SOLEORIGINE_VERSION
        );
    }

    // Responsive styles
    wp_enqueue_style(
        'soleorigine-responsive',
        SOLEORIGINE_URI . '/css/responsive.css',
        array( 'soleorigine-style' ),
        SOLEORIGINE_VERSION
    );

    // Navigation script
    wp_enqueue_script(
        'soleorigine-navigation',
        SOLEORIGINE_URI . '/js/navigation.js',
        array(),
        SOLEORIGINE_VERSION,
        true
    );

    // Custom script
    wp_enqueue_script(
        'soleorigine-custom',
        SOLEORIGINE_URI . '/js/custom.js',
        array(),
        SOLEORIGINE_VERSION,
        true
    );
}

/**
 * Enqueue admin styles and scripts.
 */
function soleorigine_admin_scripts() {
    $manifest_path = SOLEORIGINE_DIR . '/dist/.vite/manifest.json';

    if ( file_exists( $manifest_path ) ) {
        $data     = json_decode( file_get_contents( $manifest_path ), true );
        $css_key  = 'css/admin.css';

        if ( isset( $data[ $css_key ] ) ) {
            $uri = SOLEORIGINE_URI . '/dist/' . $data[ $css_key ]['file'];
            wp_enqueue_style(
                'soleorigine-admin',
                $uri,
                array(),
                hash( 'crc32b', $data[ $css_key ]['file'] )
            );
            return;
        }
    }

    wp_enqueue_style(
        'soleorigine-admin',
        SOLEORIGINE_URI . '/css/admin.css',
        array(),
        SOLEORIGINE_VERSION
    );
}
add_action( 'admin_enqueue_scripts', 'soleorigine_admin_scripts' );

/**
 * Add defer attributes to scripts.
 */
function soleorigine_script_loader_tag( $tag, $handle, $src ) {
    $defer_scripts = array( 'soleorigine-navigation', 'soleorigine-custom', 'gsap-core', 'gsap-scrolltrigger', 'gsap-scrollto' );

    if ( in_array( $handle, $defer_scripts ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }

    return $tag;
}
add_filter( 'script_loader_tag', 'soleorigine_script_loader_tag', 10, 3 );

/**
 * Preload critical fonts.
 */
function soleorigine_preload_fonts() {
    ?>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <?php
}
add_action( 'wp_head', 'soleorigine_preload_fonts', 1 );

<?php
/**
 * Vite Hot Module Replacement (HMR) helper
 *
 * During development, assets are served from the Vite dev server
 * for hot reload. In production, built assets from /dist/ are used.
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SOLEORIGINE_VITE_PORT', '5173' );
define( 'SOLEORIGINE_VITE_HOST', 'http://localhost:' . SOLEORIGINE_VITE_PORT );

/**
 * Check if the Vite dev server is running.
 */
function soleorigine_is_vite_running(): bool {
    $cache_key = 'soleorigine_vite_check';
    $cached    = get_transient( $cache_key );

    if ( false !== $cached ) {
        return 'yes' === $cached;
    }

    $handle = @fsockopen( 'localhost', (int) SOLEORIGINE_VITE_PORT, $errno, $errstr, 0.5 );

    if ( $handle ) {
        fclose( $handle );
        set_transient( $cache_key, 'yes', 15 );
        return true;
    }

    set_transient( $cache_key, 'no', 15 );
    return false;
}

/**
 * Enqueue assets via Vite (dev) or built files (production).
 */
function soleorigine_enqueue_vite_assets(): void {
    $is_vite = soleorigine_is_vite_running();

    if ( $is_vite ) {
        soleorigine_enqueue_vite_dev();
    } else {
        soleorigine_enqueue_vite_prod();
    }
}

/**
 * Enqueue assets from Vite dev server (HMR enabled).
 */
function soleorigine_enqueue_vite_dev(): void {
    // Inject Vite client for HMR
    add_action( 'wp_head', function () {
        ?>
        <script type="module" src="<?php echo esc_url( SOLEORIGINE_VITE_HOST ); ?>/@vite/client"></script>
        <?php
    }, 1 );

    // Enqueue entry scripts from Vite dev server
    $entries = array(
        'custom'     => '/js/custom.js',
        'navigation' => '/js/navigation.js',
    );

    foreach ( $entries as $handle => $path ) {
        wp_enqueue_script(
            "soleorigine-{$handle}",
            SOLEORIGINE_VITE_HOST . $path,
            array(),
            null,
            true
        );
    }

    // Enqueue CSS from Vite dev server (imported as JS modules)
    $css_entries = array(
        'soleorigine-woocommerce' => '/css/woocommerce.css',
        'soleorigine-responsive'  => '/css/responsive.css',
        'soleorigine-admin'       => '/css/admin.css',
    );

    foreach ( $css_entries as $handle => $path ) {
        wp_enqueue_script(
            "{$handle}-vite",
            SOLEORIGINE_VITE_HOST . $path,
            array(),
            null,
            true
        );
    }
}

/**
 * Enqueue assets from built /dist/ directory (production).
 */
function soleorigine_enqueue_vite_prod(): void {
    $manifest_path = SOLEORIGINE_DIR . '/dist/.vite/manifest.json';

    if ( ! file_exists( $manifest_path ) ) {
        return;
    }

    $manifest = json_decode( file_get_contents( $manifest_path ), true );

    if ( ! is_array( $manifest ) ) {
        return;
    }

    $entries = array(
        'custom'     => 'js/custom.js',
        'navigation' => 'js/navigation.js',
    );

    foreach ( $entries as $handle => $key ) {
        if ( ! isset( $manifest[ $key ] ) ) {
            continue;
        }

        $entry   = $manifest[ $key ];
        $js_uri  = SOLEORIGINE_URI . '/dist/' . $entry['file'];
        $version = hash( 'crc32b', $entry['file'] );

        wp_enqueue_script(
            "soleorigine-{$handle}",
            $js_uri,
            array(),
            $version,
            true
        );

        if ( ! empty( $entry['css'] ) ) {
            foreach ( $entry['css'] as $css_file ) {
                wp_enqueue_style(
                    "soleorigine-{$handle}-css",
                    SOLEORIGINE_URI . '/dist/' . $css_file,
                    array(),
                    $version
                );
            }
        }
    }

    // Enqueue CSS entries from manifest
    $css_keys = array(
        'woocommerce' => 'css/woocommerce.css',
        'responsive'  => 'css/responsive.css',
        'admin'       => 'css/admin.css',
    );

    foreach ( $css_keys as $handle => $key ) {
        if ( ! isset( $manifest[ $key ] ) ) {
            continue;
        }

        $entry   = $manifest[ $key ];
        $uri     = SOLEORIGINE_URI . '/dist/' . $entry['file'];
        $version = hash( 'crc32b', $entry['file'] );

        wp_enqueue_style(
            "soleorigine-{$handle}",
            $uri,
            array( 'soleorigine-style' ),
            $version
        );
    }
}

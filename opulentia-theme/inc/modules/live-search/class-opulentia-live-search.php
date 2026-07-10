<?php
/**
 * Live Search / Advanced Search Module — Singleton
 *
 * Provides:
 * - AJAX live search with results dropdown
 * - Search result: title, image, price (for products), excerpt
 * - Debounced input handling
 * - Loading indicator
 * - No results message
 * - Click-outside to close
 * - Keyboard navigation (arrow keys, escape)
 * - Search style options (icon, slide, dropdown, full-screen)
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Live_Search class.
 */
class Opulentia_Live_Search {

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
        add_action( 'wp_ajax_Opulentia_live_search', array( $this, 'ajax_search' ) );
        add_action( 'wp_ajax_nopriv_Opulentia_live_search', array( $this, 'ajax_search' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 100 );
        add_action( 'wp_footer', array( $this, 'render_search_panel' ), 50 );
    }

    /**
     * Check if live search is enabled.
     *
     * @return bool
     */
    private function is_enabled() {
        return (bool) Opulentia_get_option( 'enable-live-search', true );
    }

    /**
     * Get the search style.
     *
     * @return string 'dropdown', 'slide', 'full-screen'
     */
    private function get_style() {
        $style = Opulentia_get_option( 'search-style', 'dropdown' );
        return in_array( $style, array( 'dropdown', 'slide', 'full-screen' ), true ) ? $style : 'dropdown';
    }

    /**
     * Render the search panel HTML in footer.
     */
    public function render_search_panel() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $style = $this->get_style();

        if ( 'dropdown' === $style ) {
            // Dropdown search is rendered inline in header.
            return;
        }

        $classes = array(
            'search-panel',
            'search-panel--' . $style,
        );
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" id="search-panel" aria-hidden="true">
            <div class="search-panel__overlay"></div>
            <div class="search-panel__content">
                <button class="search-panel__close" aria-label="<?php esc_attr_e( 'Close search', 'opulentia' ); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="search-panel__form">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="search" class="search-panel__input" placeholder="<?php esc_attr_e( 'Search products, posts...', 'opulentia' ); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
                        <button type="submit" class="search-panel__submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        </button>
                    </form>
                    <div class="search-panel__results" id="live-search-results"></div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX search handler.
     */
    public function ajax_search() {
        check_ajax_referer( 'Opulentia_live_search', 'nonce' );

        $search_term = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
        $results     = array();

        if ( empty( $search_term ) || strlen( $search_term ) < 2 ) {
            wp_send_json_success( array( 'results' => array() ) );
        }

        $post_types = (array) Opulentia_get_option( 'live-search-post-types', array( 'post', 'product' ) );

        $args = array(
            's'              => $search_term,
            'post_type'      => $post_types,
            'posts_per_page' => (int) Opulentia_get_option( 'live-search-count', 6 ),
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        );

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $product_price = '';
                $image_url     = '';

                if ( 'product' === get_post_type() && class_exists( 'WooCommerce' ) ) {
                    $product       = wc_get_product( get_the_ID() );
                    $product_price = $product ? $product->get_price_html() : '';
                }

                if ( has_post_thumbnail() ) {
                    $image_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
                }

                $results[] = array(
                    'id'      => get_the_ID(),
                    'title'   => get_the_title(),
                    'url'     => get_permalink(),
                    'type'    => get_post_type(),
                    'image'   => $image_url,
                    'price'   => $product_price,
                    'excerpt' => wp_trim_words( get_the_excerpt(), 15 ),
                );
            }
            wp_reset_postdata();
        }

        wp_send_json_success( array( 'results' => $results ) );
    }

    /**
     * Enqueue live search assets.
     */
    public function enqueue_assets() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        // Enqueue the external live-search.js file.
        $js_url  = Opulentia_URI . '/js/live-search.js';
        $version = Opulentia_VERSION;

        // Check for Vite-built version.
        $manifest_path = Opulentia_DIR . '/dist/.vite/manifest.json';
        if ( file_exists( $manifest_path ) ) {
            $manifest = json_decode( file_get_contents( $manifest_path ), true );
            if ( isset( $manifest['js/live-search.js'] ) ) {
                $js_url  = Opulentia_URI . '/dist/' . $manifest['js/live-search.js']['file'];
                $version = hash( 'crc32b', $manifest['js/live-search.js']['file'] );
            }
        }

        wp_enqueue_script(
            'opulentia-live-search',
            $js_url,
            array(),
            $version,
            true
        );

        // Localize with AJAX URL and nonce.
        wp_localize_script( 'opulentia-live-search', 'OpulentiaLiveSearch', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'Opulentia_live_search' ),
        ) );

        // Panel open/close script (kept inline since it's small).
        $panel_script = '
        document.addEventListener("DOMContentLoaded", function() {
            var searchPanel = document.getElementById("search-panel");
            if ( ! searchPanel ) return;
            var searchToggles = document.querySelectorAll(".js-search-toggle");
            var closeButtons = document.querySelectorAll(".search-panel__close, .search-panel__overlay");

            function openSearch() {
                searchPanel.setAttribute("aria-hidden", "false");
                searchPanel.classList.add("is-open");
                document.body.classList.add("search-open");
                setTimeout(function() {
                    var input = searchPanel.querySelector(".search-panel__input");
                    if (input) input.focus();
                }, 100);
            }

            function closeSearch() {
                searchPanel.setAttribute("aria-hidden", "true");
                searchPanel.classList.remove("is-open");
                document.body.classList.remove("search-open");
            }

            searchToggles.forEach(function(toggle) {
                toggle.addEventListener("click", function(e) {
                    e.preventDefault();
                    openSearch();
                });
            });

            closeButtons.forEach(function(btn) {
                btn.addEventListener("click", closeSearch);
            });

            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape") closeSearch();
            });
        });';

        wp_add_inline_script( 'opulentia-live-search', $panel_script );
    }
}

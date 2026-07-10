<?php
/**
 * Advanced Hooks Renderer — Singleton
 *
 * Handles frontend rendering of advanced hooks:
 * 1. Queries active hook posts
 * 2. Checks display conditions per post
 * 3. Binds code output to the selected hook location
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Advanced_Hooks_Render class.
 */
class Opulentia_Advanced_Hooks_Render {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Cached active hooks.
     *
     * @var array|null
     */
    private static $active_hooks = null;

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
     * Constructor — registers hooks for frontend rendering.
     */
    private function __construct() {
        // Bind all active hooks to their locations.
        add_action( 'init', array( $this, 'bind_hooks' ), 20 );
    }

    /**
     * Query all active hook posts and bind them to their locations.
     */
    public function bind_hooks() {
        if ( is_admin() && ! wp_doing_ajax() ) {
            return;
        }

        $hooks = $this->get_active_hooks();

        if ( empty( $hooks ) ) {
            return;
        }

        foreach ( $hooks as $hook_post ) {
            $location = get_post_meta( $hook_post->ID, '_opulentia_hook_location', true );
            $priority = absint( get_post_meta( $hook_post->ID, '_opulentia_hook_priority', true ) ?: 10 );

            if ( empty( $location ) ) {
                continue;
            }

            // Check display conditions for each hook.
            if ( ! $this->check_conditions( $hook_post->ID ) ) {
                continue;
            }

            // Bind the hook output.
            add_action( $location, function () use ( $hook_post ) {
                $this->render_hook( $hook_post->ID );
            }, $priority );
        }
    }

    /**
     * Get all published advanced hook posts.
     *
     * @return array Array of WP_Post objects.
     */
    private function get_active_hooks() {
        if ( ! is_null( self::$active_hooks ) ) {
            return self::$active_hooks;
        }

        $query = new WP_Query( array(
            'post_type'      => 'opulentia_hook',
            'posts_per_page' => 100,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ) );

        self::$active_hooks = $query->have_posts() ? $query->posts : array();

        wp_reset_postdata();

        return self::$active_hooks;
    }

    /**
     * Filter hooks by their display conditions.
     *
     * @param int $hook_id The hook post ID.
     * @return bool Whether the hook should render on the current page.
     */
    private function check_conditions( $hook_id ) {
        $conditions_raw = get_post_meta( $hook_id, '_opulentia_hook_display_on', true );

        if ( empty( $conditions_raw ) ) {
            return false;
        }

        $conditions = json_decode( $conditions_raw, true );

        if ( ! is_array( $conditions ) || empty( $conditions ) ) {
            return false;
        }

        // If "Entire Site" is checked, always render.
        if ( in_array( 'entire_site', $conditions, true ) ) {
            return true;
        }

        // Check individual conditions.
        $matches = false;

        foreach ( $conditions as $condition ) {
            switch ( $condition ) {
                case 'front_page':
                    $matches = $matches || ( is_front_page() && ! is_home() );
                    $matches = $matches || ( is_home() && is_front_page() );
                    break;

                case 'blog_index':
                    $matches = $matches || ( is_home() && ! is_front_page() );
                    break;

                case 'singular':
                    $matches = $matches || is_singular();
                    break;

                case 'archive':
                    $matches = $matches || is_archive();
                    break;

                case 'single_post':
                    $matches = $matches || is_singular( 'post' );
                    break;

                case 'single_page':
                    $matches = $matches || is_singular( 'page' );
                    break;

                case 'search':
                    $matches = $matches || is_search();
                    break;

                case '404':
                    $matches = $matches || is_404();
                    break;

                default:
                    // Check custom post type conditions (cpt_product, etc.)
                    if ( strpos( $condition, 'cpt_' ) === 0 ) {
                        $cpt = substr( $condition, 4 );
                        $matches = $matches || is_singular( $cpt );
                    }
                    break;
            }
        }

        return $matches;
    }

    /**
     * Render the hook output based on its code type.
     *
     * @param int $hook_id The hook post ID.
     */
    public function render_hook( $hook_id ) {
        $code_type = get_post_meta( $hook_id, '_opulentia_hook_code_type', true ) ?: 'html';
        $code      = get_post_meta( $hook_id, '_opulentia_hook_code', true );

        if ( empty( $code ) ) {
            return;
        }

        switch ( $code_type ) {
            case 'css':
                echo '<style id="opulentia-hook-css-' . esc_attr( $hook_id ) . '" type="text/css">' . "\n";
                echo $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</style>' . "\n";
                break;

            case 'js':
                echo '<script id="opulentia-hook-js-' . esc_attr( $hook_id ) . '">' . "\n";
                echo $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</script>' . "\n";
                break;

            case 'php':
                // PHP code is evaluated. Only for users with proper permissions.
                if ( current_user_can( 'unfiltered_html' ) ) {
                    // Wrap in output buffering to capture any echoed content.
                    ob_start();
                    eval( '?>' . $code . '<?php ' );
                    $output = ob_get_clean();
                    echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
                break;

            case 'html':
            default:
                echo wp_kses_post( $code ) . "\n";
                break;
        }
    }
}

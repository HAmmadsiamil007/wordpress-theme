<?php
/**
 * Scroll to Top Module — Singleton
 *
 * Dedicated module for the back-to-top button with customizer controls:
 * - Icon choice (arrow, chevron, custom SVG)
 * - Position (left, right)
 * - Responsive visibility (mobile, tablet, desktop)
 * - Colors (background normal/hover, icon)
 * - Scroll threshold
 * - Animation type (fade, slide, zoom)
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Scroll_To_Top class.
 */
class Opulentia_Scroll_To_Top {

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
        add_action( 'wp_footer', array( $this, 'render_button' ), 100 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ), 100 );
    }

    /**
     * Check if scroll to top should be rendered.
     *
     * @return bool
     */
    private function is_enabled() {
        return (bool) Opulentia_get_option( 'enable-scroll-to-top', true );
    }

    /**
     * Render the scroll-to-top button HTML.
     */
    public function render_button() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $position = Opulentia_get_option( 'scroll-to-top-position', 'right' );
        $threshold = (int) Opulentia_get_option( 'scroll-to-top-threshold', 300 );
        $icon_type = Opulentia_get_option( 'scroll-to-top-icon', 'chevron-up' );

        $classes = array(
            'opulentia-scroll-to-top',
            'opulentia-scroll-to-top--' . $position,
        );

        $style = '';
        if ( 'right' === $position ) {
            $style = 'right: 30px; left: auto;';
        } else {
            $style = 'left: 30px; right: auto;';
        }

        ?>
        <button class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
                style="<?php echo esc_attr( $style ); ?>"
                data-threshold="<?php echo esc_attr( $threshold ); ?>"
                aria-label="<?php esc_attr_e( 'Back to top', 'opulentia' ); ?>">
            <?php $this->render_icon( $icon_type ); ?>
        </button>
        <?php
    }

    /**
     * Render the icon SVG.
     *
     * @param string $type Icon type.
     */
    private function render_icon( $type ) {
        switch ( $type ) {
            case 'arrow-up':
                ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M5 12h14M12 5l7 7-7 7"/></svg><?php // phpcs:ignore
                break;
            case 'chevron-up':
            default:
                ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M18 15l-6-6-6 6"/></svg><?php // phpcs:ignore
                break;
        }
    }

    /**
     * Enqueue the scroll-to-top script.
     */
    public function enqueue_script() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        wp_add_inline_script( 'opulentia-navigation', '
            document.addEventListener("DOMContentLoaded", function() {
                var btn = document.querySelector(".opulentia-scroll-to-top");
                if (!btn) return;
                var threshold = parseInt(btn.getAttribute("data-threshold")) || 300;
                window.addEventListener("scroll", function() {
                    if (window.scrollY > threshold) {
                        btn.classList.add("is-visible");
                    } else {
                        btn.classList.remove("is-visible");
                    }
                });
                btn.addEventListener("click", function() {
                    window.scrollTo({ top: 0, behavior: "smooth" });
                });
            });
        ' );
    }
}

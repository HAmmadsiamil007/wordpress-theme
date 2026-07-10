<?php
/**
 * Sticky Header Module — Singleton
 *
 * Enables sticky header on scroll with:
 * - Enable/disable on desktop, tablet, mobile
 * - Sticky on scroll up (auto-hide)
 * - Separate sticky logo image
 * - Sticky background color
 * - Sticky box shadow
 * - Customizer controls for all settings
 * - Integration with the header builder
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Sticky_Header class.
 */
class Opulentia_Sticky_Header {

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
        add_action( 'Opulentia_sticky_header', array( $this, 'render' ) );
        add_filter( 'body_class', array( $this, 'body_class' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 100 );
    }

    /**
     * Check if sticky header is enabled.
     *
     * @return bool
     */
    private function is_enabled() {
        return (bool) Opulentia_get_option( 'sticky-header', true );
    }

    /**
     * Check if sticky header is enabled for the current device.
     *
     * @return bool
     */
    private function is_enabled_for_device() {
        if ( ! $this->is_enabled() ) {
            return false;
        }

        $devices = Opulentia_get_option( 'sticky-header-devices', array( 'desktop', 'tablet', 'mobile' ) );

        if ( wp_is_mobile() ) {
            return in_array( 'mobile', $devices, true );
        }

        if ( wp_is_tablet() ) {
            return in_array( 'tablet', $devices, true );
        }

        return in_array( 'desktop', $devices, true );
    }

    /**
     * Render the sticky header wrapper.
     */
    public function render() {
        if ( ! $this->is_enabled_for_device() ) {
            return;
        }

        $hide_scroll_up = (bool) Opulentia_get_option( 'sticky-header-hide-on-scroll-up', false );
        $sticky_class   = 'opulentia-sticky-header';

        if ( $hide_scroll_up ) {
            $sticky_class .= ' sticky-hide-on-scroll-up';
        }
        ?>
        <div class="<?php echo esc_attr( $sticky_class ); ?>" id="opulentia-sticky-header">
            <div class="opulentia-sticky-header__inner">
                <?php
                /**
                 * Render the header builder markup inside the sticky wrapper.
                 */
                Opulentia_Header_Builder::render_sticky();
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Add sticky header body classes.
     *
     * @param array $classes Body classes.
     * @return array
     */
    public function body_class( $classes ) {
        if ( ! $this->is_enabled() ) {
            return $classes;
        }

        $classes[] = 'sticky-header-enabled';

        $devices = Opulentia_get_option( 'sticky-header-devices', array( 'desktop', 'tablet', 'mobile' ) );

        if ( in_array( 'desktop', $devices, true ) ) {
            $classes[] = 'sticky-header-desktop';
        }
        if ( in_array( 'tablet', $devices, true ) ) {
            $classes[] = 'sticky-header-tablet';
        }
        if ( in_array( 'mobile', $devices, true ) ) {
            $classes[] = 'sticky-header-mobile';
        }

        $hide_scroll_up = (bool) Opulentia_get_option( 'sticky-header-hide-on-scroll-up', false );
        if ( $hide_scroll_up ) {
            $classes[] = 'sticky-hide-on-scroll-up';
        }

        return $classes;
    }

    /**
     * Enqueue front-end CSS and JS for sticky header.
     */
    public function enqueue_assets() {
        if ( ! $this->is_enabled_for_device() ) {
            return;
        }

        $this->inline_css();
        $this->enqueue_script();
    }

    /**
     * Output sticky header inline CSS.
     */
    private function inline_css() {
        $bg_color       = Opulentia_get_option( 'sticky-header-bg-color', 'var(--color-primary-dark)' );
        $box_shadow     = Opulentia_get_option( 'sticky-header-box-shadow', '0 2px 10px rgba(0,0,0,0.3)' );
        $sticky_logo    = Opulentia_get_option( 'sticky-header-logo', '' );
        $logo_css       = '';

        if ( ! empty( $sticky_logo ) ) {
            $logo_css = '
            .opulentia-sticky-header .custom-logo-sticky {
                display: inline-block;
            }
            .opulentia-sticky-header .custom-logo:not(.custom-logo-sticky) {
                display: none;
            }
            ';
        }

        $css = '
        .opulentia-sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            background-color: ' . $bg_color . ';
            box-shadow: ' . $box_shadow . ';
            transform: translateY(0);
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .opulentia-sticky-header.sticky-hidden {
            transform: translateY(-100%);
        }
        .admin-bar .opulentia-sticky-header {
            top: 32px;
        }
        ' . $logo_css;

        wp_add_inline_style( 'opulentia-style', $css );
    }

    /**
     * Enqueue sticky header JavaScript.
     */
    private function enqueue_script() {
        wp_add_inline_script(
            'opulentia-custom',
            '
            (function() {
                var stickyHeader = document.getElementById("opulentia-sticky-header");
                if ( ! stickyHeader ) return;

                var lastScroll = 0;
                var ticking    = false;
                var hideOnUp   = stickyHeader.classList.contains("sticky-hide-on-scroll-up");

                window.addEventListener("scroll", function() {
                    if ( ! ticking ) {
                        window.requestAnimationFrame(function() {
                            var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

                            if ( currentScroll > 50 ) {
                                stickyHeader.classList.add("sticky-scrolled");
                            } else {
                                stickyHeader.classList.remove("sticky-scrolled");
                            }

                            if ( hideOnUp && currentScroll > 200 ) {
                                if ( currentScroll > lastScroll && ! stickyHeader.classList.contains("sticky-hidden") ) {
                                    stickyHeader.classList.add("sticky-hidden");
                                } else if ( currentScroll < lastScroll && stickyHeader.classList.contains("sticky-hidden") ) {
                                    stickyHeader.classList.remove("sticky-hidden");
                                }
                            }

                            lastScroll = currentScroll;
                            ticking = false;
                        });
                        ticking = true;
                    }
                });
            })();
            '
        );
    }
}

<?php
/**
 * Advanced Headers / Page Banner System — Singleton
 *
 * Renders page header banners with title, subtitle, breadcrumbs,
 * background images/colors, overlay, responsive padding, and
 * per-post meta box overrides.
 *
 * Supports conditional display per page type:
 * home, blog/archive, single post, single page, search, 404, WooCommerce.
 *
 * @package Opulentia
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Advanced_Headers class.
 */
class Opulentia_Advanced_Headers {

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
        add_action( 'Opulentia_primary_content_before', array( $this, 'render' ), 0 );
    }

    // -------------------------------------------------------------------------
    // Main Render
    // -------------------------------------------------------------------------

    /**
     * Render the page header/banner.
     */
    public function render() {
        if ( ! $this->should_render() ) {
            return;
        }

        $title     = $this->get_title();
        $subtitle  = $this->get_subtitle();
        $bg_image  = $this->get_background_image();
        $bg_color  = $this->get_background_color();
        $overlay   = $this->get_overlay_color();
        $alignment = $this->get_alignment();
        $show_breadcrumbs = $this->show_breadcrumbs();
        $padding_top    = $this->get_padding_top();
        $padding_bottom = $this->get_padding_bottom();

        $classes = array(
            'page-header',
            'page-header--' . $alignment,
        );

        $styles = array();

        if ( $bg_color ) {
            $styles[] = 'background-color: ' . esc_attr( $bg_color );
        }

        if ( $padding_top ) {
            $styles[] = 'padding-top: ' . esc_attr( $padding_top ) . 'px';
        }

        if ( $padding_bottom ) {
            $styles[] = 'padding-bottom: ' . esc_attr( $padding_bottom ) . 'px';
        }

        do_action( 'Opulentia_page_header_before' );
        ?>
        <section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo ! empty( $styles ) ? ' style="' . esc_attr( implode( '; ', $styles ) ) . '"' : ''; ?>>
            <?php if ( $bg_image ) : ?>
                <div class="page-header__background">
                    <img src="<?php echo esc_url( $bg_image ); ?>" alt="" aria-hidden="true" loading="eager">
                </div>
            <?php endif; ?>
            <?php if ( $overlay ) : ?>
                <div class="page-header__overlay" style="background-color: <?php echo esc_attr( $overlay ); ?>;"></div>
            <?php endif; ?>
            <div class="container">
                <div class="page-header__content">
                    <?php if ( $show_breadcrumbs ) : ?>
                        <?php do_action( 'Opulentia_page_header_breadcrumbs' ); ?>
                    <?php endif; ?>
                    <?php if ( $title ) : ?>
                        <h1 class="page-header__title"><?php echo wp_kses_post( $title ); ?></h1>
                    <?php endif; ?>
                    <?php if ( $subtitle ) : ?>
                        <p class="page-header__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        do_action( 'Opulentia_page_header_after' );
    }

    // -------------------------------------------------------------------------
    // Conditional Display
    // -------------------------------------------------------------------------

    /**
     * Check if the page header should render on the current page.
     *
     * @return bool
     */
    private function should_render() {
        // Check master enable/disable setting.
        if ( ! Opulentia_get_option( 'page-header-enabled', true ) ) {
            return false;
        }

        // Check per-page meta override.
        if ( is_singular() ) {
            $post_id    = get_the_ID();
            $meta_value = get_post_meta( $post_id, '_Opulentia_disable_page_header', true );
            if ( '1' === $meta_value ) {
                return false;
            }
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // Title
    // -------------------------------------------------------------------------

    /**
     * Get the page header title.
     *
     * @return string
     */
    private function get_title() {
        $title = '';

        if ( is_front_page() && is_home() ) {
            $title = Opulentia_get_option( 'page-header-home-title', get_bloginfo( 'name' ) );
        } elseif ( is_home() ) {
            $title = Opulentia_get_option( 'page-header-blog-title', __( 'Blog', 'opulentia' ) );
        } elseif ( is_singular() ) {
            $post_id = get_the_ID();
            $meta_title = get_post_meta( $post_id, '_Opulentia_page_header_title', true );
            $title = $meta_title ? $meta_title : get_the_title();
        } elseif ( is_archive() ) {
            if ( is_category() ) {
                $title = single_cat_title( '', false );
            } elseif ( is_tag() ) {
                $title = single_tag_title( '', false );
            } elseif ( is_tax() ) {
                $title = single_term_title( '', false );
            } elseif ( is_post_type_archive() ) {
                $title = post_type_archive_title( '', false );
            } elseif ( is_author() ) {
                $title = get_the_author();
            } elseif ( is_date() ) {
                $title = get_the_archive_title();
            } else {
                $title = get_the_archive_title();
            }
        } elseif ( is_search() ) {
            $title = sprintf( __( 'Search Results: %s', 'opulentia' ), get_search_query() );
        } elseif ( is_404() ) {
            $title = Opulentia_get_option( 'page-header-404-title', __( 'Page Not Found', 'opulentia' ) );
        } elseif ( function_exists( 'is_shop' ) && is_shop() ) {
            $title = get_the_title( wc_get_page_id( 'shop' ) );
        }

        return apply_filters( 'Opulentia_page_header_title', $title );
    }

    /**
     * Get the page header subtitle.
     *
     * @return string
     */
    private function get_subtitle() {
        $subtitle = '';

        if ( is_singular() ) {
            $post_id = get_the_ID();
            $subtitle = get_post_meta( $post_id, '_Opulentia_page_header_subtitle', true );
        }

        if ( ! $subtitle ) {
            $subtitle = Opulentia_get_option( 'page-header-subtitle', '' );
        }

        return apply_filters( 'Opulentia_page_header_subtitle', $subtitle );
    }

    // -------------------------------------------------------------------------
    // Background
    // -------------------------------------------------------------------------

    /**
     * Get the background image URL.
     *
     * @return string
     */
    private function get_background_image() {
        $image = '';

        if ( is_singular() ) {
            $post_id = get_the_ID();
            $meta_image = get_post_meta( $post_id, '_Opulentia_page_header_bg_image', true );
            if ( $meta_image ) {
                return $meta_image;
            }
            // Fall back to featured image.
            if ( has_post_thumbnail( $post_id ) ) {
                $image = get_the_post_thumbnail_url( $post_id, 'full' );
            }
        }

        if ( ! $image ) {
            $image = Opulentia_get_option( 'page-header-bg-image', '' );
        }

        return $image;
    }

    /**
     * Get the background color.
     *
     * @return string
     */
    private function get_background_color() {
        return Opulentia_get_option( 'page-header-bg-color', '#111111' );
    }

    /**
     * Get the overlay color with opacity.
     *
     * @return string
     */
    private function get_overlay_color() {
        return Opulentia_get_option( 'page-header-overlay-color', 'rgba(0,0,0,0.5)' );
    }

    /**
     * Get the alignment.
     *
     * @return string 'left', 'center', 'right'.
     */
    private function get_alignment() {
        $alignment = Opulentia_get_option( 'page-header-alignment', 'center' );
        return in_array( $alignment, array( 'left', 'center', 'right' ), true ) ? $alignment : 'center';
    }

    /**
     * Whether to show breadcrumbs in the page header.
     *
     * @return bool
     */
    private function show_breadcrumbs() {
        return (bool) Opulentia_get_option( 'page-header-show-breadcrumbs', true );
    }

    /**
     * Get padding top value.
     *
     * @return int
     */
    private function get_padding_top() {
        return (int) Opulentia_get_option( 'page-header-padding-top', 100 );
    }

    /**
     * Get padding bottom value.
     *
     * @return int
     */
    private function get_padding_bottom() {
        return (int) Opulentia_get_option( 'page-header-padding-bottom', 60 );
    }
}

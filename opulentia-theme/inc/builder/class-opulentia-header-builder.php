<?php
/**
 * Header Builder — Full Suite
 *
 * Renders the site header based on customizer settings.
 * Supports 5 layout presets (standard, centered, minimal, stacked, off-canvas)
 * with 3 rows (above, main, below) and 12 configurable components.
 *
 * Each row and component supports per-device visibility (desktop/tablet/mobile).
 * All settings read via Opulentia_get_option() for Theme Options API compat.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Header_Builder class.
 */
class Opulentia_Header_Builder {

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
     * Constructor — no hook registrations needed.
     * header.php calls Opulentia_Header_Builder::render() directly.
     */
    private function __construct() {}

    // -------------------------------------------------------------------------
    // Main Render
    // -------------------------------------------------------------------------

    /**
     * Render the full header based on customizer settings.
     */
    public static function render() {
        $layout = self::get_layout();
        $classes = array(
            'site-header',
            'site-header--' . $layout,
            self::get_row_visibility_class( 'above' ),
            self::get_row_visibility_class( 'main' ),
            self::get_row_visibility_class( 'below' ),
        );
        ?>
        <header id="masthead" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" role="banner" itemscope="itemscope" itemtype="https://schema.org/WPHeader">
        <?php
        do_action( 'Opulentia_masthead_before' );

        self::render_row( 'above', $layout );
        self::render_main_header( $layout );
        self::render_row( 'below', $layout );

        do_action( 'Opulentia_masthead_after' );
        ?>
        </header>
        <?php
        // Off-canvas panel rendered outside header for z-index stacking.
        if ( 'off-canvas' === $layout ) {
            self::render_off_canvas_panel();
        }
        do_action( 'Opulentia_header_after' );
    }

    /**
     * Get the header layout preset.
     *
     * @return string 'standard', 'centered', 'minimal', 'stacked', or 'off-canvas'.
     */
    public static function get_layout() {
        $layout = Opulentia_get_option( 'header-layout', 'standard' );
        $valid  = array( 'standard', 'centered', 'minimal', 'stacked', 'off-canvas' );
        if ( ! in_array( $layout, $valid, true ) ) {
            $layout = 'standard';
        }
        return $layout;
    }

    /**
     * Check if a row should be displayed based on device visibility.
     *
     * @param string $row    'above', 'main', 'below'.
     * @param string $device 'desktop', 'tablet', 'mobile'.
     * @return bool
     */
    public static function is_row_visible( $row, $device = 'desktop' ) {
        $setting = 'header-row-' . $row . '-visibility';
        $visibility = Opulentia_get_option( $setting, array( 'desktop' => true, 'tablet' => true, 'mobile' => true ) );
        return isset( $visibility[ $device ] ) ? (bool) $visibility[ $device ] : true;
    }

    /**
     * Get the CSS class for row visibility.
     *
     * @param string $row Row name.
     * @return string CSS class.
     */
    private static function get_row_visibility_class( $row ) {
        $classes = array();
        foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
            if ( ! self::is_row_visible( $row, $device ) ) {
                $classes[] = 'hide-' . $device . '-' . $row;
            }
        }
        return implode( ' ', $classes );
    }

    // -------------------------------------------------------------------------
    // Row Rendering
    // -------------------------------------------------------------------------

    /**
     * Render a header row with its configured components.
     *
     * @param string $row    Row identifier: 'above', 'main', 'below'.
     * @param string $layout Current layout preset.
     */
    private static function render_row( $row, $layout ) {
        if ( 'main' === $row ) {
            return; // Rendered separately via render_main_header().
        }

        $components = self::get_row_components( $row );
        if ( empty( $components ) ) {
            return;
        }

        do_action( 'Opulentia_header_' . $row . '_before' );
        ?>
        <div class="header-row header-row--<?php echo esc_attr( $row ); ?>">
            <div class="header-row__inner container">
                <?php
                foreach ( $components as $component ) {
                    self::render_component( $component, $layout );
                }
                ?>
            </div>
        </div>
        <?php
        do_action( 'Opulentia_header_' . $row . '_after' );
    }

    /**
     * Get the list of enabled components for a given row.
     *
     * @param string $row Row name.
     * @return array Component IDs.
     */
    private static function get_row_components( $row ) {
        $setting    = 'header-row-' . $row . '-components';
        $defaults   = array( 'above' => array( 'top-bar-left', 'top-bar-right' ), 'below' => array() );
        $components = Opulentia_get_option( $setting, isset( $defaults[ $row ] ) ? $defaults[ $row ] : array() );
        return is_array( $components ) ? $components : array();
    }

    /**
     * Render the main header row based on layout preset.
     *
     * @param string $layout Layout preset.
     */
    private static function render_main_header( $layout ) {
        do_action( 'Opulentia_header_main_before' );
        ?>
        <div class="header-main header-row--main">
            <div class="header-main__inner container">
                <?php
                switch ( $layout ) {
                    case 'centered':
                        self::render_centered_main();
                        break;
                    case 'minimal':
                        self::render_minimal_main();
                        break;
                    case 'stacked':
                        self::render_stacked_main();
                        break;
                    case 'off-canvas':
                        self::render_off_canvas_main();
                        break;
                    default:
                        self::render_standard_main();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
        do_action( 'Opulentia_header_main_after' );
    }

    // -------------------------------------------------------------------------
    // Layout Presets
    // -------------------------------------------------------------------------

    /**
     * Standard layout: Logo left | Nav center | Actions right
     */
    private static function render_standard_main() {
        echo '<div class="header-standard-grid">';
        echo '<div class="header-col header-col--left">';
        self::render_component( 'logo', 'standard' );
        echo '</div>';
        echo '<div class="header-col header-col--center">';
        if ( self::component_enabled( 'primary-menu' ) ) {
            self::render_component( 'primary-menu', 'standard' );
        }
        echo '</div>';
        echo '<div class="header-col header-col--right">';
        self::render_main_actions();
        echo '</div>';
        echo '</div>';
    }

    /**
     * Centered layout: Logo centered | Nav centered below | Actions on sides
     */
    private static function render_centered_main() {
        echo '<div class="header-centered-grid">';
        echo '<div class="header-col header-col--left-actions">';
        foreach ( array( 'search', 'account', 'wishlist' ) as $comp ) {
            if ( self::component_enabled( $comp ) ) {
                self::render_component( $comp, 'centered' );
            }
        }
        echo '</div>';
        echo '<div class="header-col header-col--logo">';
        self::render_component( 'logo', 'centered' );
        echo '</div>';
        echo '<div class="header-col header-col--right-actions">';
        foreach ( array( 'cart', 'mobile-toggle' ) as $comp ) {
            if ( self::component_enabled( $comp ) ) {
                self::render_component( $comp, 'centered' );
            }
        }
        echo '</div>';
        echo '<div class="header-col header-col--nav-full">';
        if ( self::component_enabled( 'primary-menu' ) ) {
            self::render_component( 'primary-menu', 'centered' );
        }
        echo '</div>';
        echo '</div>';
    }

    /**
     * Minimal layout: Logo left | Nav + Actions right (streamlined)
     */
    private static function render_minimal_main() {
        echo '<div class="header-minimal-grid">';
        echo '<div class="header-col header-col--left">';
        self::render_component( 'logo', 'minimal' );
        echo '</div>';
        echo '<div class="header-col header-col--right">';
        if ( self::component_enabled( 'primary-menu' ) ) {
            self::render_component( 'primary-menu', 'minimal' );
        }
        self::render_main_actions();
        echo '</div>';
        echo '</div>';
    }

    /**
     * Stacked layout: Logo + HTML top row | Nav middle | Actions bottom
     */
    private static function render_stacked_main() {
        echo '<div class="header-stacked-grid">';
        echo '<div class="header-stacked-row header-stacked-row--top">';
        echo '<div class="header-col header-col--left">';
        self::render_component( 'logo', 'stacked' );
        echo '</div>';
        echo '<div class="header-col header-col--right">';
        if ( self::component_enabled( 'html-block' ) ) {
            self::render_component( 'html-block', 'stacked' );
        }
        if ( self::component_enabled( 'social-icons' ) ) {
            self::render_component( 'social-icons', 'stacked' );
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="header-stacked-row header-stacked-row--middle">';
        if ( self::component_enabled( 'primary-menu' ) ) {
            self::render_component( 'primary-menu', 'stacked' );
        }
        echo '</div>';
        echo '<div class="header-stacked-row header-stacked-row--bottom">';
        self::render_main_actions();
        if ( self::component_enabled( 'custom-button' ) ) {
            self::render_component( 'custom-button', 'stacked' );
        }
        echo '</div>';
        echo '</div>';
    }

    /**
     * Off-canvas layout: Logo left | Minimal actions | Hamburger toggle
     */
    private static function render_off_canvas_main() {
        echo '<div class="header-off-canvas-grid">';
        echo '<div class="header-col header-col--left">';
        self::render_component( 'logo', 'off-canvas' );
        echo '</div>';
        echo '<div class="header-col header-col--right">';
        self::render_component( 'search', 'off-canvas' );
        self::render_component( 'cart', 'off-canvas' );
        self::render_component( 'mobile-toggle', 'off-canvas' );
        echo '</div>';
        echo '</div>';
    }

    /**
     * Render the off-canvas navigation panel (fullscreen overlay).
     */
    private static function render_off_canvas_panel() {
        ?>
        <div class="off-canvas-panel" id="off-canvas-panel" aria-hidden="true">
            <div class="off-canvas-panel__overlay"></div>
            <div class="off-canvas-panel__content">
                <button class="off-canvas-panel__close" aria-label="<?php esc_attr_e( 'Close menu', 'opulentia' ); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <nav class="off-canvas-panel__nav" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'opulentia' ); ?>">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'off-canvas-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 2,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    ) );
                    ?>
                </nav>
                <div class="off-canvas-panel__actions">
                    <?php
                    self::render_component( 'search', 'off-canvas' );
                    self::render_component( 'account', 'off-canvas' );
                    self::render_component( 'social-icons', 'off-canvas' );
                    self::render_component( 'custom-button', 'off-canvas' );
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render common main actions row.
     */
    private static function render_main_actions() {
        foreach ( array( 'search', 'account', 'wishlist', 'cart', 'custom-button', 'social-icons', 'html-block', 'mobile-toggle' ) as $comp ) {
            if ( self::component_enabled( $comp ) && in_array( $comp, array( 'search', 'account', 'wishlist', 'cart', 'mobile-toggle' ), true ) ) {
                self::render_component( $comp, 'standard' );
            }
        }
    }

    // -------------------------------------------------------------------------
    // Component System
    // -------------------------------------------------------------------------

    /**
     * Check if a component is enabled in the customizer.
     *
     * @param string $component Component ID.
     * @return bool
     */
    private static function component_enabled( $component ) {
        $setting = 'header-show-' . str_replace( '_', '-', $component );
        // Always show logo and mobile-toggle.
        if ( in_array( $component, array( 'logo', 'mobile-toggle' ), true ) ) {
            return true;
        }
        return (bool) Opulentia_get_option( $setting, true );
    }

    /**
     * Render a component by ID.
     *
     * @param string $component Component ID.
     * @param string $layout    Current layout context.
     */
    private static function render_component( $component, $layout ) {
        switch ( $component ) {
            case 'logo':             self::render_logo(); break;
            case 'primary-menu':     self::render_primary_menu(); break;
            case 'search':           self::render_action_search(); break;
            case 'account':          self::render_action_account(); break;
            case 'cart':             self::render_action_cart(); break;
            case 'wishlist':         self::render_action_wishlist(); break;
            case 'mobile-toggle':    self::render_mobile_toggle(); break;
            case 'custom-button':    self::render_custom_button(); break;
            case 'html-block':       self::render_html_block(); break;
            case 'social-icons':     self::render_social_icons(); break;
            case 'top-bar-left':     self::render_top_bar_left(); break;
            case 'top-bar-right':    self::render_top_bar_right(); break;
        }
    }

    // -------------------------------------------------------------------------
    // Logo Component
    // -------------------------------------------------------------------------

    /**
     * Render the site logo or title.
     */
    private static function render_logo() {
        if ( has_custom_logo() ) {
            the_custom_logo();
        } else {
            ?>
            <div class="site-logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-logo__link">
                    <span class="site-logo__text"><?php bloginfo( 'name' ); ?></span>
                </a>
                <?php
                $show_tagline = Opulentia_get_option( 'header-show-tagline', false );
                if ( $show_tagline ) {
                    ?>
                    <span class="site-logo__tagline"><?php bloginfo( 'description' ); ?></span>
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }

    // -------------------------------------------------------------------------
    // Primary Menu Component
    // -------------------------------------------------------------------------

    /**
     * Render the primary navigation.
     */
    private static function render_primary_menu() {
        ?>
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'opulentia' ); ?>" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'container'      => false,
                'fallback_cb'    => false,
                'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            ) );
            ?>
        </nav>
        <?php
    }

    // -------------------------------------------------------------------------
    // Action Components
    // -------------------------------------------------------------------------

    /**
     * Render the search action button.
     */
    private static function render_action_search() {
        if ( ! self::component_enabled( 'search' ) ) {
            return;
        }
        ?>
        <button class="header-actions__btn header-actions__btn--search js-search-toggle" aria-label="<?php esc_attr_e( 'Search', 'opulentia' ); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
        </button>
        <?php
    }

    /**
     * Render the account action link.
     */
    private static function render_action_account() {
        if ( ! self::component_enabled( 'account' ) ) {
            return;
        }
        $url = class_exists( 'WooCommerce' ) ? wc_get_account_endpoint_url( 'dashboard' ) : admin_url( 'profile.php' );
        ?>
        <a href="<?php echo esc_url( $url ); ?>" class="header-actions__btn header-actions__btn--account" aria-label="<?php esc_attr_e( 'My Account', 'opulentia' ); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </a>
        <?php
    }

    /**
     * Render the cart action link with mini cart dropdown.
     */
    private static function render_action_cart() {
        if ( ! self::component_enabled( 'cart' ) ) {
            return;
        }
        $has_wc     = class_exists( 'WooCommerce' );
        $cart_count = $has_wc ? WC()->cart->get_cart_contents_count() : 0;
        $cart_url   = $has_wc ? wc_get_cart_url() : '#';
        ?>
        <span class="header-cart-wrapper">
            <a href="<?php echo esc_url( $cart_url ); ?>" class="header-actions__btn header-actions__btn--cart" aria-label="<?php esc_attr_e( 'Cart', 'opulentia' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <?php if ( $cart_count > 0 ) : ?>
                    <span class="header-cart-count"><?php echo esc_html( $cart_count ); ?></span>
                <?php endif; ?>
            </a>
            <div class="mini-cart-dropdown">
                <?php if ( $has_wc && ! WC()->cart->is_empty() ) : ?>
                    <div class="mini-cart-dropdown__header"><?php printf( esc_html__( 'Shopping Cart (%d)', 'opulentia' ), $cart_count ); ?></div>
                    <ul class="mini-cart-dropdown__items">
                        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                            $_product = $cart_item['data'];
                            if ( ! $_product || ! $_product->exists() ) { continue; }
                        ?>
                        <li class="mini-cart-dropdown__item">
                            <span class="mini-cart-dropdown__item-image"><?php echo $_product->get_image( 'thumbnail' ); ?></span>
                            <span class="mini-cart-dropdown__item-details">
                                <a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo esc_html( $_product->get_name() ); ?></a>
                                <span><?php echo esc_html( $cart_item['quantity'] ); ?> &times; <?php echo WC()->cart->get_product_price( $_product ); ?></span>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="mini-cart-dropdown__footer">
                        <span class="mini-cart-dropdown__subtotal"><?php esc_html_e( 'Subtotal', 'opulentia' ); ?>: <?php echo WC()->cart->get_cart_subtotal(); ?></span>
                        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn--primary btn--small"><?php esc_html_e( 'View Cart', 'opulentia' ); ?></a>
                        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn--outline btn--small"><?php esc_html_e( 'Checkout', 'opulentia' ); ?></a>
                    </div>
                <?php else : ?>
                    <div class="mini-cart-dropdown__empty"><?php esc_html_e( 'Your cart is empty.', 'opulentia' ); ?></div>
                <?php endif; ?>
            </div>
        </span>
        <?php
    }

    /**
     * Render the mobile menu toggle button.
     */
    private static function render_mobile_toggle() {
        ?>
        <button class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle mobile menu', 'opulentia' ); ?>" aria-expanded="false">
            <span class="mobile-menu-toggle__line"></span>
            <span class="mobile-menu-toggle__line"></span>
            <span class="mobile-menu-toggle__line"></span>
        </button>
        <?php
    }

    /**
     * Render the wishlist icon.
     */
    private static function render_action_wishlist() {
        if ( ! self::component_enabled( 'wishlist' ) ) {
            return;
        }
        ?>
        <span class="header-actions__btn header-actions__btn--wishlist">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </span>
        <?php
    }

    // -------------------------------------------------------------------------
    // Extra Components
    // -------------------------------------------------------------------------

    /**
     * Render the custom button component.
     */
    private static function render_custom_button() {
        $text = Opulentia_get_option( 'header-custom-button-text', '' );
        $url  = Opulentia_get_option( 'header-custom-button-url', '#' );
        $style = Opulentia_get_option( 'header-custom-button-style', 'outline' );

        if ( empty( $text ) ) {
            return;
        }

        $btn_class = 'btn--primary';
        if ( 'outline' === $style ) {
            $btn_class = 'btn--outline';
        } elseif ( 'minimal' === $style ) {
            $btn_class = 'btn--minimal';
        }
        ?>
        <a href="<?php echo esc_url( $url ); ?>" class="header-custom-btn btn btn--small <?php echo esc_attr( $btn_class ); ?>">
            <?php echo esc_html( $text ); ?>
        </a>
        <?php
    }

    /**
     * Render the HTML block component.
     */
    private static function render_html_block() {
        $html = Opulentia_get_option( 'header-html-block', '' );
        if ( empty( $html ) ) {
            return;
        }
        ?>
        <div class="header-html-block">
            <?php echo wp_kses_post( $html ); ?>
        </div>
        <?php
    }

    /**
     * Render the social icons component.
     */
    private static function render_social_icons() {
        $socials = array(
            'facebook'  => Opulentia_get_option( 'social-facebook', '' ),
            'twitter'   => Opulentia_get_option( 'social-twitter', '' ),
            'instagram' => Opulentia_get_option( 'social-instagram', '' ),
            'pinterest' => Opulentia_get_option( 'social-pinterest', '' ),
            'youtube'   => Opulentia_get_option( 'social-youtube', '' ),
        );

        $has_any = false;
        foreach ( $socials as $url ) {
            if ( ! empty( $url ) ) {
                $has_any = true;
                break;
            }
        }

        if ( ! $has_any ) {
            return;
        }
        ?>
        <div class="header-social-icons">
            <?php foreach ( $socials as $name => $url ) : ?>
                <?php if ( ! empty( $url ) ) : ?>
                    <a href="<?php echo esc_url( $url ); ?>" class="header-social-icons__link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $name ); ?>">
                        <?php self::render_social_icon( $name ); ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render a social icon SVG.
     *
     * @param string $name Social network name.
     */
    private static function render_social_icon( $name ) {
        switch ( $name ) {
            case 'facebook':
                ?><svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg><?php // phpcs:ignore
                break;
            case 'twitter':
                ?><svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg><?php // phpcs:ignore
                break;
            case 'instagram':
                ?><svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0z"/><path d="M12 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/><circle cx="18.406" cy="5.595" r="1.44"/></svg><?php // phpcs:ignore
                break;
            case 'pinterest':
                ?><svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg><?php // phpcs:ignore
                break;
            case 'youtube':
                ?><svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg><?php // phpcs:ignore
                break;
        }
    }

    // -------------------------------------------------------------------------
    // Sticky Header Rendering
    // -------------------------------------------------------------------------

    /**
     * Render the header for the sticky variant.
     *
     * Mirrors the main render() output but without the sticky wrapper
     * (the wrapper is provided by Opulentia_Sticky_Header).
     * Called by the sticky header module via 'Opulentia_sticky_header' action.
     */
    public static function render_sticky() {
        $layout = self::get_layout();
        ?>
        <?php
        do_action( 'Opulentia_masthead_before' );

        self::render_row( 'above', $layout );
        self::render_main_header( $layout );
        self::render_row( 'below', $layout );

        do_action( 'Opulentia_masthead_after' );
        ?>
        <?php
    }

    // -------------------------------------------------------------------------
    // Top Bar Components
    // -------------------------------------------------------------------------

    /**
     * Render top bar left content (tagline).
     */
    private static function render_top_bar_left() {
        $show_tagline = Opulentia_get_option( 'header-show-top-bar-tagline', true );
        if ( $show_tagline ) {
            ?>
            <span class="top-bar-item top-bar-item--tagline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span><?php echo esc_html( get_bloginfo( 'description' ) ?: __( 'Premium Handcrafted Footwear', 'opulentia' ) ); ?></span>
            </span>
            <?php
        }
    }

    /**
     * Render top bar right content (shipping info).
     */
    private static function render_top_bar_right() {
        $show_shipping = Opulentia_get_option( 'header-show-top-bar-shipping', true );
        if ( $show_shipping ) {
            ?>
            <span class="top-bar-item top-bar-item--shipping">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14">
                    <circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                <span><?php esc_html_e( 'Free Worldwide Shipping', 'opulentia' ); ?></span>
            </span>
            <?php
        }
    }
}

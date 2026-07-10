<?php
/**
 * Opulentia WooCommerce Integration
 *
 * Central hub for all WooCommerce enhancements:
 * - Product catalog (columns, per page, image ratio)
 * - Single product (gallery, zoom, related products, tabs)
 * - Cart & checkout customization
 * - Quick view, mini cart, wishlist integration
 * - Shop page layout controls
 * - Variation swatches delegation
 * - WooCommerce breadcrumbs integration
 *
 * @package Opulentia
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Opulentia_WooCommerce' ) ) {

    /**
     * Opulentia_WooCommerce class.
     */
    class Opulentia_WooCommerce {

        /**
         * Singleton instance.
         *
         * @var self|null
         */
        private static $instance;

        /**
         * Returns the singleton instance.
         *
         * @return self
         */
        public static function get_instance() {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor — registers all hooks.
         */
        private function __construct() {
            // Bail if WooCommerce is not active.
            if ( ! class_exists( 'WooCommerce' ) ) {
                return;
            }

            // Init variation swatches (self-activates via setting check internally).
            $this->init_variation_swatches();

            // Product catalog.
            add_filter( 'loop_shop_columns', array( $this, 'product_columns' ), 20 );
            add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ), 20 );
            add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
            add_filter( 'woocommerce_upsell_display_args', array( $this, 'upsell_products_args' ) );
            add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sells_columns' ) );
            add_filter( 'woocommerce_cross_sells_total', array( $this, 'cross_sells_total' ) );

            // Single product.
            add_action( 'woocommerce_before_single_product_summary', array( $this, 'single_product_gallery_wrap_start' ), 5 );
            add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_product_gallery_wrap_end' ), 5 );
            add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'gallery_classes' ) );
            add_filter( 'woocommerce_gallery_thumbnail_size', array( $this, 'gallery_thumbnail_size' ) );
            add_filter( 'woocommerce_gallery_image_size', array( $this, 'gallery_image_size' ) );
            add_filter( 'woocommerce_gallery_full_size', array( $this, 'gallery_full_size' ) );

            // Product tabs.
            add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs' ), 98 );

            // Cart.
            add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 10, 3 );
            add_action( 'woocommerce_before_cart_table', array( $this, 'cart_wrap_start' ), 5 );
            add_action( 'woocommerce_after_cart_table', array( $this, 'cart_wrap_end' ), 5 );

            // Checkout.
            add_action( 'woocommerce_before_checkout_form', array( $this, 'checkout_wrap_start' ), 5 );
            add_action( 'woocommerce_after_checkout_form', array( $this, 'checkout_wrap_end' ), 5 );
            add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ) );

            // My Account.
            add_action( 'woocommerce_before_my_account', array( $this, 'my_account_wrap_start' ), 5 );
            add_action( 'woocommerce_after_my_account', array( $this, 'my_account_wrap_end' ), 5 );

            // Checkout enhancements.
            add_filter( 'woocommerce_checkout_coupon_message', array( $this, 'checkout_coupon_message' ) );
            add_filter( 'woocommerce_order_button_html', array( $this, 'checkout_order_button' ) );

            // Thank you page.
            add_action( 'woocommerce_before_thankyou', array( $this, 'thankyou_wrap_start' ), 5 );
            add_action( 'woocommerce_after_thankyou', array( $this, 'thankyou_wrap_end' ), 5 );

            // Breadcrumbs.
            add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'breadcrumb_defaults' ) );

            // Body classes.
            add_filter( 'body_class', array( $this, 'body_classes' ) );

            // Product card hover effect.
            add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_card_image_wrap_start' ), 5 );
            add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_card_image_wrap_end' ), 12 );

            // WooCommerce wrappers are handled by the theme's templates directly.
        }

        // ---------------------------------------------------------------------
        // Checkout Enhancements
        // ---------------------------------------------------------------------

        /**
         * Customize the coupon toggle message.
         *
         * @param  string $message Default message.
         * @return string
         */
        public function checkout_coupon_message( $message ) {
            $message = __( 'Have a coupon?', 'woocommerce' ) . ' <span class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</span>';
            return $message;
        }

        /**
         * Customize the place order button HTML.
         *
         * @param  string $button Default button HTML.
         * @return string
         */
        public function checkout_order_button( $button ) {
            // The button is fully styled via the payment.php template override,
            // but this filter ensures consistency if the template fallback is used.
            return $button;
        }

        // ---------------------------------------------------------------------
        // Thank You Page
        // ---------------------------------------------------------------------

        /**
         * Open the thank you page wrapper.
         */
        public function thankyou_wrap_start() {
            echo '<div class="opulentia-thankyou-wrap">';
        }

        /**
         * Close the thank you page wrapper.
         */
        public function thankyou_wrap_end() {
            echo '</div>';
        }

        /**
         * Initialize variation swatches.
         */
        private function init_variation_swatches() {
            $swatches_file = Opulentia_DIR . '/inc/woocommerce/class-opulentia-wc-variation-swatches.php';
            if ( file_exists( $swatches_file ) ) {
                require_once $swatches_file;
                if ( class_exists( 'Opulentia_WC_Variation_Swatches' ) ) {
                    Opulentia_WC_Variation_Swatches::get_instance();
                }
            }
        }

        // ---------------------------------------------------------------------
        // Product Catalog
        // ---------------------------------------------------------------------

        /**
         * Set product grid columns.
         *
         * @param  int $columns Default columns.
         * @return int
         */
        public function product_columns( $columns ) {
            $custom = (int) Opulentia_get_option( 'wc-product-columns', 4 );
            return max( 1, min( 6, $custom ) );
        }

        /**
         * Set products per page.
         *
         * @param  int $per_page Default per page.
         * @return int
         */
        public function products_per_page( $per_page ) {
            $custom = (int) Opulentia_get_option( 'wc-products-per-page', 12 );
            return max( 4, min( 48, $custom ) );
        }

        /**
         * Related products args.
         *
         * @param  array $args Default args.
         * @return array
         */
        public function related_products_args( $args ) {
            $args['posts_per_page'] = (int) Opulentia_get_option( 'wc-related-count', 4 );
            $args['columns']        = (int) Opulentia_get_option( 'wc-related-columns', 4 );
            return $args;
        }

        /**
         * Upsell products args.
         *
         * @param  array $args Default args.
         * @return array
         */
        public function upsell_products_args( $args ) {
            $args['posts_per_page'] = (int) Opulentia_get_option( 'wc-upsell-count', 4 );
            $args['columns']        = (int) Opulentia_get_option( 'wc-upsell-columns', 4 );
            return $args;
        }

        /**
         * Cross-sells columns.
         *
         * @param  int $columns Default columns.
         * @return int
         */
        public function cross_sells_columns( $columns ) {
            return (int) Opulentia_get_option( 'wc-cross-sells-columns', 2 );
        }

        /**
         * Cross-sells total.
         *
         * @param  int $total Default total.
         * @return int
         */
        public function cross_sells_total( $total ) {
            return (int) Opulentia_get_option( 'wc-cross-sells-total', 4 );
        }

        // ---------------------------------------------------------------------
        // Single Product
        // ---------------------------------------------------------------------

        /**
         * Open gallery wrapper markup.
         */
        public function single_product_gallery_wrap_start() {
            echo '<div class="single-product__gallery-inner">';
        }

        /**
         * Close gallery wrapper markup.
         */
        public function single_product_gallery_wrap_end() {
            echo '</div>';
        }

        /**
         * Add custom gallery classes.
         *
         * @param  array $classes Gallery classes.
         * @return array
         */
        public function gallery_classes( $classes ) {
            $layout = Opulentia_get_option( 'wc-gallery-layout', 'stacked' );
            $classes[] = 'gallery-layout--' . esc_attr( $layout );

            if ( Opulentia_get_option( 'wc-gallery-zoom', true ) ) {
                $classes[] = 'gallery-zoom-enabled';
            }

            return $classes;
        }

        /**
         * Gallery thumbnail size.
         *
         * @return array
         */
        public function gallery_thumbnail_size() {
            return array( 120, 120, true );
        }

        /**
         * Gallery image size.
         *
         * @return string
         */
        public function gallery_image_size() {
            return 'woocommerce_single';
        }

        /**
         * Gallery full size.
         *
         * @return string
         */
        public function gallery_full_size() {
            return 'full';
        }

        /**
         * Customize product tabs.
         *
         * @param  array $tabs Default tabs.
         * @return array
         */
        public function product_tabs( $tabs ) {
            // Reorder: description first, additional info second, reviews third.
            $priority = 10;
            foreach ( $tabs as $key => &$tab ) {
                $tab['priority'] = $priority;
                $priority += 10;
            }
            return $tabs;
        }

        // ---------------------------------------------------------------------
        // Cart
        // ---------------------------------------------------------------------

        /**
         * Open cart wrapper.
         */
        public function cart_wrap_start() {
            echo '<div class="opulentia-cart-wrap">';
        }

        /**
         * Close cart wrapper.
         */
        public function cart_wrap_end() {
            echo '</div>';
        }

        /**
         * Custom cart item name with thumbnail.
         *
         * @param  string $name      Item name HTML.
         * @param  array  $cart_item Cart item data.
         * @param  string $cart_item_key Cart item key.
         * @return string
         */
        public function cart_item_name( $name, $cart_item, $cart_item_key ) {
            $_product = $cart_item['data'];
            if ( ! $_product ) {
                return $name;
            }
            $thumbnail = $_product->get_image( 'thumbnail', array( 'class' => 'cart-item__thumbnail' ) );
            return $thumbnail . '<span class="cart-item__name">' . $name . '</span>';
        }

        // ---------------------------------------------------------------------
        // Checkout
        // ---------------------------------------------------------------------

        /**
         * Open checkout wrapper.
         */
        public function checkout_wrap_start() {
            echo '<div class="opulentia-checkout-wrap">';
        }

        /**
         * Close checkout wrapper.
         */
        public function checkout_wrap_end() {
            echo '</div>';
        }

        /**
         * Customize checkout fields.
         *
         * @param  array $fields Checkout fields.
         * @return array
         */
        public function checkout_fields( $fields ) {
            // Add custom classes to fields.
            foreach ( $fields as $section => $section_fields ) {
                foreach ( $section_fields as $key => $field ) {
                    if ( isset( $field['class'] ) ) {
                        $fields[ $section ][ $key ]['class'][] = 'opulentia-checkout-field';
                    }
                }
            }
            return $fields;
        }

        // ---------------------------------------------------------------------
        // My Account
        // ---------------------------------------------------------------------

        /**
         * Open My Account wrapper.
         */
        public function my_account_wrap_start() {
            echo '<div class="opulentia-my-account-wrap">';
        }

        /**
         * Close My Account wrapper.
         */
        public function my_account_wrap_end() {
            echo '</div>';
        }

        // ---------------------------------------------------------------------
        // Breadcrumbs
        // ---------------------------------------------------------------------

        /**
         * Customize WooCommerce breadcrumb defaults.
         *
         * @param  array $defaults Default breadcrumb args.
         * @return array
         */
        public function breadcrumb_defaults( $defaults ) {
            $defaults['delimiter']   = '<span class="breadcrumb-separator">›</span>';
            $defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb opulentia-breadcrumb" itemprop="breadcrumb">';
            $defaults['wrap_after']  = '</nav>';
            $defaults['before']      = '<span class="breadcrumb-item">';
            $defaults['after']       = '</span>';
            return $defaults;
        }

        // ---------------------------------------------------------------------
        // Body Classes
        // ---------------------------------------------------------------------

        /**
         * Add WooCommerce-specific body classes.
         *
         * @param  array $classes Body classes.
         * @return array
         */
        public function body_classes( $classes ) {
            if ( is_shop() || is_product_category() || is_product_tag() ) {
                $columns = (int) Opulentia_get_option( 'wc-product-columns', 4 );
                $classes[] = 'wc-columns-' . absint( $columns );
            }

            if ( is_singular( 'product' ) ) {
                $gallery_layout = Opulentia_get_option( 'wc-gallery-layout', 'stacked' );
                $classes[] = 'wc-gallery-' . esc_attr( $gallery_layout );
            }

            return $classes;
        }

        // ---------------------------------------------------------------------
        // Product Card Image Wrapper
        // ---------------------------------------------------------------------

        /**
         * Open product card image wrapper.
         */
        public function product_card_image_wrap_start() {
            global $product;
            if ( ! $product ) {
                return;
            }
            echo '<div class="product-card__image-wrap">';
            echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' );
            $this->render_product_card_badges( $product );
        }

        /**
         * Close product card image wrapper.
         */
        public function product_card_image_wrap_end() {
            echo '</div>';
        }

        /**
         * Render product card badges (sale, featured, new).
         *
         * @param WC_Product $product Product object.
         */
        private function render_product_card_badges( $product ) {
            if ( ! $product ) {
                return;
            }

            if ( $product->is_on_sale() ) {
                $ratio = $product->get_sale_price() / $product->get_regular_price();
                $percent = '-' . ( 100 - round( $ratio * 100 ) ) . '%';
                echo '<span class="product-card__badge product-card__badge--sale">' . esc_html( $percent ) . '</span>';
            }

            if ( $product->is_featured() ) {
                echo '<span class="product-card__badge product-card__badge--featured">' . esc_html__( 'Featured', 'opulentia' ) . '</span>';
            }

            $post_date = get_the_time( 'U' );
            $days_old  = (int) ( ( time() - $post_date ) / DAY_IN_SECONDS );
            if ( $days_old < 7 ) {
                echo '<span class="product-card__badge product-card__badge--new">' . esc_html__( 'New', 'opulentia' ) . '</span>';
            }
        }
    }

    // Kick off the singleton.
    Opulentia_WooCommerce::get_instance();
}

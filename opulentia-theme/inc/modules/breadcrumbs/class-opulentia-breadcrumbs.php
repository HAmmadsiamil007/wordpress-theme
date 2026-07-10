<?php
/**
 * Breadcrumbs Module — Singleton
 *
 * Native breadcrumb generation with Schema.org BreadcrumbList markup.
 * Integrates with Yoast SEO and Rank Math breadcrumbs when available.
 * Provides customizer controls for position, typography, and colors.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Breadcrumbs class.
 */
class Opulentia_Breadcrumbs {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Breadcrumb trail items for schema output.
     *
     * @var array
     */
    private $items = array();

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
     * Constructor — registers init hook.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    /**
     * Initialize breadcrumb hooks.
     */
    public function init() {
        add_action( 'Opulentia_breadcrumbs', array( $this, 'render' ) );
        add_action( 'Opulentia_page_header_breadcrumbs', array( $this, 'render_in_header' ) );

        $position = $this->get_position();
        if ( in_array( $position, array( 'above-content', 'both' ), true ) ) {
            add_action( 'Opulentia_content_top', array( $this, 'render_above_content' ), 10 );
        }

        if ( $this->is_rank_math_active() ) {
            add_filter( 'rank_math/frontend/breadcrumb/args', array( $this, 'rank_math_args' ) );
        }

        add_action( 'wp_head', array( $this, 'inline_css' ), 100 );
        add_action( 'wp_footer', array( $this, 'output_schema' ), 0 );
    }

    /**
     * Main render entry point for the Opulentia_breadcrumbs action.
     */
    public function render() {
        if ( ! $this->should_display() ) {
            return;
        }

        $this->output_breadcrumbs();
    }

    /**
     * Render breadcrumbs inside the page header.
     */
    public function render_in_header() {
        if ( ! $this->should_display() ) {
            return;
        }

        $position = $this->get_position();
        if ( ! in_array( $position, array( 'page-header', 'both' ), true ) ) {
            return;
        }

        if ( ! Opulentia_get_option( 'page-header-show-breadcrumbs', true ) ) {
            return;
        }

        $this->output_breadcrumbs();
    }

    /**
     * Render breadcrumbs above the content area.
     */
    public function render_above_content() {
        if ( ! $this->should_display() ) {
            return;
        }

        $position = $this->get_position();
        if ( ! in_array( $position, array( 'above-content', 'both' ), true ) ) {
            return;
        }

        $this->output_breadcrumbs();
    }

    /**
     * Output breadcrumbs — delegates to active source.
     */
    private function output_breadcrumbs() {
        if ( $this->is_yoast_active() && function_exists( 'yoast_breadcrumb' ) ) {
            $this->render_yoast();
        } elseif ( $this->is_rank_math_active() && function_exists( 'rank_math_get_breadcrumbs' ) ) {
            $this->render_rank_math();
        } else {
            $this->render_native();
        }
    }

    // ─── Source Renderers ──────────────────────────────────────────────

    /**
     * Render Yoast SEO breadcrumbs.
     */
    private function render_yoast() {
        yoast_breadcrumb(
            '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'opulentia' ) . '">',
            '</nav>'
        );
    }

    /**
     * Render Rank Math breadcrumbs.
     */
    private function render_rank_math() {
        echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'opulentia' ) . '">';
        rank_math_get_breadcrumbs();
        echo '</nav>';
    }

    /**
     * Filter Rank Math breadcrumb args to match theme styling.
     *
     * @param array $args Rank Math breadcrumb args.
     * @return array
     */
    public function rank_math_args( $args ) {
        $separator = $this->get_separator();

        $args['wrap_before'] = '<ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';
        $args['wrap_after']  = '</ol>';
        $args['before']      = '<li class="breadcrumbs__item">';
        $args['after']       = '</li>';
        $args['delimiter']   = '<li class="breadcrumbs__separator" aria-hidden="true"><span class="breadcrumbs__sep">' . esc_html( $separator ) . '</span></li>';

        return $args;
    }

    /**
     * Render native breadcrumb trail.
     */
    private function render_native() {
        $this->items = $this->get_native_items();

        if ( empty( $this->items ) ) {
            return;
        }

        $separator = $this->get_separator();
        $total     = count( $this->items );

        ?>
        <nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'opulentia' ); ?>">
            <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
                <?php foreach ( $this->items as $index => $item ) : ?>
                    <?php $position = $index + 1; ?>
                    <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <?php if ( ! empty( $item['url'] ) && $position < $total ) : ?>
                            <a class="breadcrumbs__link" href="<?php echo esc_url( $item['url'] ); ?>" itemprop="item">
                                <span itemprop="name"><?php echo esc_html( $item['label'] ); ?></span>
                            </a>
                            <span class="breadcrumbs__separator" aria-hidden="true"><?php echo esc_html( $separator ); ?></span>
                        <?php else : ?>
                            <span class="breadcrumbs__current" itemprop="name"><?php echo esc_html( $item['label'] ); ?></span>
                        <?php endif; ?>
                        <meta itemprop="position" content="<?php echo esc_attr( $position ); ?>" />
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <?php
    }

    // ─── Trail Builder ─────────────────────────────────────────────────

    /**
     * Build native breadcrumb trail items.
     *
     * @return array
     */
    private function get_native_items() {
        $items     = array();
        $home_text = $this->get_home_text();

        $items[] = array(
            'label' => $home_text,
            'url'   => home_url( '/' ),
        );

        if ( is_home() && ! is_front_page() ) {
            $this->maybe_add_blog_item( $items );
        } elseif ( is_singular( 'post' ) ) {
            $this->add_single_post_items( $items );
        } elseif ( is_page() ) {
            $this->add_page_items( $items );
        } elseif ( is_category() ) {
            $this->add_category_archive_item( $items );
        } elseif ( is_tag() ) {
            $this->add_tag_archive_item( $items );
        } elseif ( is_tax() ) {
            $this->add_tax_archive_item( $items );
        } elseif ( is_author() ) {
            $this->add_author_archive_item( $items );
        } elseif ( is_date() ) {
            $this->add_date_archive_items( $items );
        } elseif ( is_search() ) {
            $this->add_search_item( $items );
        } elseif ( is_404() ) {
            $this->add_404_item( $items );
        } elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
            $this->add_woocommerce_items( $items );
        } elseif ( is_post_type_archive() ) {
            $this->add_post_type_archive_item( $items );
        }

        return $items;
    }

    /**
     * Add blog page item.
     *
     * @param array &$items Trail items.
     */
    private function maybe_add_blog_item( &$items ) {
        if ( ! $this->show_current() ) {
            return;
        }

        $blog_title = get_the_title( get_option( 'page_for_posts' ) );
        if ( empty( $blog_title ) ) {
            $blog_title = __( 'Blog', 'opulentia' );
        }

        $items[] = array(
            'label' => $blog_title,
            'url'   => '',
        );
    }

    /**
     * Add single post breadcrumb items.
     *
     * @param array &$items Trail items.
     */
    private function add_single_post_items( &$items ) {
        $categories = get_the_category();

        if ( ! empty( $categories ) ) {
            $category = $categories[0];
            $this->add_category_ancestors( $items, $category );
            $items[] = array(
                'label' => $category->name,
                'url'   => get_category_link( $category->term_id ),
            );
        }

        if ( $this->show_current() ) {
            $items[] = array(
                'label' => get_the_title(),
                'url'   => '',
            );
        }
    }

    /**
     * Add page breadcrumb items (respects hierarchy).
     *
     * @param array &$items Trail items.
     */
    private function add_page_items( &$items ) {
        global $post;

        if ( $post && $post->post_parent ) {
            $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
            foreach ( $ancestors as $ancestor_id ) {
                $items[] = array(
                    'label' => get_the_title( $ancestor_id ),
                    'url'   => get_permalink( $ancestor_id ),
                );
            }
        }

        if ( $this->show_current() ) {
            $items[] = array(
                'label' => get_the_title(),
                'url'   => '',
            );
        }
    }

    /**
     * Add category archive item with ancestors.
     *
     * @param array &$items Trail items.
     */
    private function add_category_archive_item( &$items ) {
        $category = get_queried_object();
        if ( ! $category ) {
            return;
        }

        $this->add_category_ancestors( $items, $category );

        $items[] = array(
            'label' => single_cat_title( '', false ),
            'url'   => '',
        );
    }

    /**
     * Add tag archive item.
     *
     * @param array &$items Trail items.
     */
    private function add_tag_archive_item( &$items ) {
        $items[] = array(
            'label' => single_tag_title( '', false ),
            'url'   => '',
        );
    }

    /**
     * Add custom taxonomy archive item.
     *
     * @param array &$items Trail items.
     */
    private function add_tax_archive_item( &$items ) {
        $term = get_queried_object();

        if ( ! $term || ! is_a( $term, 'WP_Term' ) ) {
            return;
        }

        $taxonomy = $term->taxonomy;

        if ( in_array( $taxonomy, array( 'product_cat', 'product_tag' ), true ) && function_exists( 'is_woocommerce' ) ) {
            $this->prepend_shop_link( $items );
        }

        $this->add_term_ancestors( $items, $term );

        $items[] = array(
            'label' => $term->name,
            'url'   => '',
        );
    }

    /**
     * Add author archive item.
     *
     * @param array &$items Trail items.
     */
    private function add_author_archive_item( &$items ) {
        $items[] = array(
            'label' => get_the_author(),
            'url'   => '',
        );
    }

    /**
     * Add date archive items (year, month, day).
     *
     * @param array &$items Trail items.
     */
    private function add_date_archive_items( &$items ) {
        $year = get_query_var( 'year' );

        if ( $year ) {
            $items[] = array(
                'label' => $year,
                'url'   => get_year_link( $year ),
            );
        }

        $month = get_query_var( 'monthnum' );
        if ( $month ) {
            $month_name = get_the_date( 'F' );
            $items[] = array(
                'label' => $month_name,
                'url'   => get_month_link( $year, $month ),
            );
        }

        $day = get_query_var( 'day' );
        if ( $day ) {
            $items[] = array(
                'label' => get_the_date( 'j' ),
                'url'   => '',
            );
        }
    }

    /**
     * Add search results item.
     *
     * @param array &$items Trail items.
     */
    private function add_search_item( &$items ) {
        $items[] = array(
            'label' => sprintf(
                /* translators: %s: search query. */
                __( 'Search: %s', 'opulentia' ),
                get_search_query()
            ),
            'url'   => '',
        );
    }

    /**
     * Add 404 page item.
     *
     * @param array &$items Trail items.
     */
    private function add_404_item( &$items ) {
        $items[] = array(
            'label' => __( '404 - Page Not Found', 'opulentia' ),
            'url'   => '',
        );
    }

    /**
     * Add WooCommerce breadcrumb items.
     *
     * @param array &$items Trail items.
     */
    private function add_woocommerce_items( &$items ) {
        if ( is_shop() ) {
            if ( $this->show_current() ) {
                $items[] = array(
                    'label' => get_the_title( wc_get_page_id( 'shop' ) ),
                    'url'   => '',
                );
            }
        } elseif ( is_product_category() ) {
            $this->prepend_shop_link( $items );
            $term = get_queried_object();
            if ( $term && is_a( $term, 'WP_Term' ) ) {
                $this->add_term_ancestors( $items, $term );
                $items[] = array(
                    'label' => $term->name,
                    'url'   => '',
                );
            }
        } elseif ( is_product_tag() ) {
            $this->prepend_shop_link( $items );
            $items[] = array(
                'label' => single_tag_title( '', false ),
                'url'   => '',
            );
        } elseif ( is_product() ) {
            $this->prepend_shop_link( $items );
            $terms = get_the_terms( get_the_ID(), 'product_cat' );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $term = $terms[0];
                $this->add_term_ancestors( $items, $term );
                $items[] = array(
                    'label' => $term->name,
                    'url'   => get_term_link( $term->term_id, 'product_cat' ),
                );
            }
            if ( $this->show_current() ) {
                $items[] = array(
                    'label' => get_the_title(),
                    'url'   => '',
                );
            }
        } elseif ( is_cart() ) {
            $items[] = array(
                'label' => __( 'Cart', 'opulentia' ),
                'url'   => '',
            );
        } elseif ( is_checkout() ) {
            $items[] = array(
                'label' => __( 'Checkout', 'opulentia' ),
                'url'   => '',
            );
        } elseif ( is_account_page() ) {
            $items[] = array(
                'label' => __( 'My Account', 'opulentia' ),
                'url'   => '',
            );
        }
    }

    /**
     * Add post type archive item.
     *
     * @param array &$items Trail items.
     */
    private function add_post_type_archive_item( &$items ) {
        $post_type = get_queried_object();

        if ( $post_type && is_a( $post_type, 'WP_Post_Type' ) ) {
            $items[] = array(
                'label' => $post_type->labels->name,
                'url'   => '',
            );
        }
    }

    /**
     * Prepend shop page link before other WooCommerce items.
     *
     * @param array &$items Trail items.
     */
    private function prepend_shop_link( &$items ) {
        $shop_id    = wc_get_page_id( 'shop' );
        $shop_title = get_the_title( $shop_id );

        if ( empty( $shop_title ) ) {
            $shop_title = __( 'Shop', 'opulentia' );
        }

        $items[] = array(
            'label' => $shop_title,
            'url'   => get_permalink( $shop_id ),
        );
    }

    /**
     * Add category ancestor terms to trail.
     *
     * @param array    &$items   Trail items.
     * @param \WP_Term $category Category term.
     */
    private function add_category_ancestors( &$items, $category ) {
        if ( empty( $category->parent ) ) {
            return;
        }

        $ancestors = array_reverse( get_ancestors( $category->term_id, 'category' ) );
        foreach ( $ancestors as $ancestor_id ) {
            $ancestor = get_term( $ancestor_id, 'category' );
            if ( $ancestor && ! is_wp_error( $ancestor ) ) {
                $items[] = array(
                    'label' => $ancestor->name,
                    'url'   => get_term_link( $ancestor ),
                );
            }
        }
    }

    /**
     * Add term ancestors to trail.
     *
     * @param array    &$items Trail items.
     * @param \WP_Term $term   Term object.
     */
    private function add_term_ancestors( &$items, $term ) {
        if ( empty( $term->parent ) ) {
            return;
        }

        $ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) );
        foreach ( $ancestors as $ancestor_id ) {
            $ancestor = get_term( $ancestor_id, $term->taxonomy );
            if ( $ancestor && ! is_wp_error( $ancestor ) ) {
                $items[] = array(
                    'label' => $ancestor->name,
                    'url'   => get_term_link( $ancestor ),
                );
            }
        }
    }

    // ─── Helpers ───────────────────────────────────────────────────────

    /**
     * Check if breadcrumbs should be displayed on the current page.
     *
     * @return bool
     */
    private function should_display() {
        if ( ! Opulentia_get_option( 'enable-breadcrumbs', true ) ) {
            return false;
        }

        if ( is_front_page() && is_home() ) {
            return false;
        }

        return true;
    }

    /**
     * Get breadcrumb position setting.
     *
     * @return string
     */
    private function get_position() {
        return Opulentia_get_option( 'breadcrumbs-position', 'page-header' );
    }

    /**
     * Get the breadcrumb separator character.
     *
     * @return string
     */
    private function get_separator() {
        $separator = Opulentia_get_option( 'breadcrumbs-separator', '/' );
        if ( '' === $separator ) {
            $separator = Opulentia_get_option( 'breadcrumb_separator', '/' );
        }
        return $separator;
    }

    /**
     * Get the home link text.
     *
     * @return string
     */
    private function get_home_text() {
        $text = Opulentia_get_option( 'breadcrumb_home_text', '' );
        if ( empty( $text ) ) {
            $text = __( 'Home', 'opulentia' );
        }
        return $text;
    }

    /**
     * Whether to show the current page title as the last breadcrumb item.
     *
     * @return bool
     */
    private function show_current() {
        return (bool) Opulentia_get_option( 'breadcrumb_show_current', true );
    }

    /**
     * Check if Yoast SEO is active.
     *
     * @return bool
     */
    private function is_yoast_active() {
        return defined( 'WPSEO_VERSION' ) || defined( 'WPSEO_PREMIUM_VERSION' );
    }

    /**
     * Check if Rank Math is active.
     *
     * @return bool
     */
    private function is_rank_math_active() {
        return defined( 'RANK_MATH_VERSION' );
    }

    // ─── Schema Output ─────────────────────────────────────────────────

    /**
     * Output Schema.org BreadcrumbList JSON-LD.
     */
    public function output_schema() {
        if ( empty( $this->items ) ) {
            return;
        }

        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array(),
        );

        foreach ( $this->items as $index => $item ) {
            $position  = $index + 1;
            $element   = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $item['label'],
            );
            if ( ! empty( $item['url'] ) ) {
                $element['item'] = $item['url'];
            }
            $schema['itemListElement'][] = $element;
        }

        ?>
        <script type="application/ld+json">
        <?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
        </script>
        <?php
    }

    // ─── CSS Output ────────────────────────────────────────────────────

    /**
     * Output inline CSS from customizer settings.
     */
    public function inline_css() {
        $color       = Opulentia_get_option( 'breadcrumbs-color', '' );
        $color_hover = Opulentia_get_option( 'breadcrumbs-color-hover', '' );
        $font_size   = Opulentia_get_option( 'breadcrumbs-font-size', '13' );

        if ( empty( $color ) ) {
            $color = Opulentia_get_option( 'breadcrumb_font_color', '' );
        }
        if ( empty( $font_size ) || '0' === $font_size ) {
            $font_size = Opulentia_get_option( 'breadcrumb_font_size', '14' );
        }

        $link_color      = $color ? $color : 'var(--color-medium-gray)';
        $hover_color     = $color_hover ? $color_hover : 'var(--color-gold)';
        $current_color   = $color ? $color : 'var(--color-text)';
        $separator_color = $color ? $color : 'var(--color-text-muted)';
        $size_value      = $font_size ? $font_size . 'px' : 'inherit';

        $css = '
        .breadcrumbs {
            font-size: ' . $size_value . ';
            line-height: 1.6;
        }
        .breadcrumbs__list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        .breadcrumbs__item {
            display: inline-flex;
            align-items: center;
        }
        .breadcrumbs__link {
            color: ' . $link_color . ';
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .breadcrumbs__link:hover {
            color: ' . $hover_color . ';
        }
        .breadcrumbs__separator {
            margin: 0 10px;
            color: ' . $separator_color . ';
            user-select: none;
        }
        .breadcrumbs__current {
            color: ' . $current_color . ';
            font-weight: 500;
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

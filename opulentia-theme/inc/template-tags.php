<?php
/**
 * Template Tags
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Display the posted date.
 */
function Opulentia_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated sr-only" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( get_the_date() ),
        esc_attr( get_the_modified_date( DATE_W3C ) ),
        esc_html( get_the_modified_date() )
    );

    printf(
        '<span class="posted-on">%s</span>',
        $time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    );
}

/**
 * Display the post author.
 */
function Opulentia_posted_by() {
    printf(
        '<span class="byline"><span class="author-name">%s</span></span>',
        esc_html( get_the_author() )
    );
}

/**
 * Display post categories.
 */
function Opulentia_posted_categories() {
    $categories_list = get_the_category_list( esc_html__( ', ', 'opulentia' ) );

    if ( $categories_list ) {
        printf(
            '<span class="cat-links">%s</span>',
            $categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
    }
}

/**
 * Display post tags.
 */
function Opulentia_posted_tags() {
    $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'opulentia' ) );

    if ( $tags_list ) {
        printf(
            '<div class="tags-links">%s</div>',
            $tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
    }
}

/**
 * Get the primary taxonomy for a given post type.
 *
 * Maps registered CPTs to their hierarchical taxonomies.
 * Falls back to 'category' for standard posts and empty string for others.
 *
 * @param string $post_type Post type slug.
 * @return string Taxonomy slug or empty string.
 */
function Opulentia_get_post_type_taxonomy( $post_type ) {
    $tax_map = array(
        'post'       => 'category',
        'collection' => 'collection_category',
        'style'      => 'style_category',
        'brand'      => 'brand_category',
    );

    return isset( $tax_map[ $post_type ] ) ? $tax_map[ $post_type ] : '';
}

/**
 * Get the adjacent post (previous or next) with taxonomy support.
 *
 * For standard posts and CPTs with a mapped taxonomy, retrieves the
 * adjacent post within the same taxonomy terms. Falls back to
 * simple date-based adjacency for post types without a taxonomy.
 *
 * @param bool  $previous Whether to get the previous (true) or next (false) post.
 * @param string $taxonomy Optional. Taxonomy slug. If empty, uses date-based adjacence.
 * @return WP_Post|null Adjacent post object or null.
 */
function Opulentia_get_adjacent_post( $previous = true, $taxonomy = '' ) {
    global $post;

    if ( ! $post ) {
        return null;
    }

    if ( ! empty( $taxonomy ) ) {
        // Get the terms for the current post in this taxonomy.
        $terms = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            // Use get_adjacent_post with in_same_term=true and taxonomy constraint.
            $adjacent = get_adjacent_post( $previous, true, '', $taxonomy );
            if ( $adjacent ) {
                return $adjacent;
            }
        }
    }

    // Fallback: date-based adjacency (no taxonomy filter).
    $adjacent = get_adjacent_post( $previous, '', '' );
    return $adjacent ? $adjacent : null;
}

/**
 * Display enhanced post navigation with thumbnail/card support.
 *
 * Works across all post types (standard posts and CPTs) with
 * taxonomy-based adjacent posts for supported post types.
 * Supports two styles from the customizer:
 * - 'default': Inline prev/next links.
 * - 'thumbnail': Card-style with post thumbnails and titles.
 *
 * Respects the blog_single_show_navigation and blog_post_nav_style settings.
 */
function Opulentia_post_navigation() {
    $show      = get_theme_mod( 'blog_single_show_navigation', true );
    $nav_style = get_theme_mod( 'blog_post_nav_style', 'default' );

    if ( ! $show ) {
        return;
    }

    $post_type = get_post_type();
    $taxonomy  = Opulentia_get_post_type_taxonomy( $post_type );

    $prev_post = Opulentia_get_adjacent_post( true, $taxonomy );
    $next_post = Opulentia_get_adjacent_post( false, $taxonomy );

    if ( ! $prev_post && ! $next_post ) {
        return;
    }

    $nav_class = 'post-navigation post-navigation--' . esc_attr( $nav_style );
    ?>
    <nav class="<?php echo esc_attr( $nav_class ); ?>" aria-label="<?php esc_attr_e( 'Post navigation', 'opulentia' ); ?>">
        <div class="nav-links">
            <?php if ( $prev_post ) : ?>
                <div class="nav-previous">
                    <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" rel="prev">
                        <?php if ( 'thumbnail' === $nav_style && has_post_thumbnail( $prev_post->ID ) ) : ?>
                            <div class="nav-thumb">
                                <?php echo get_the_post_thumbnail( $prev_post->ID, 'opulentia-nav-thumb' ); ?>
                            </div>
                        <?php endif; ?>
                        <span class="nav-direction"><?php esc_html_e( 'Previous Post', 'opulentia' ); ?></span>
                        <span class="nav-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ( $next_post ) : ?>
                <div class="nav-next">
                    <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" rel="next">
                        <?php if ( 'thumbnail' === $nav_style && has_post_thumbnail( $next_post->ID ) ) : ?>
                            <div class="nav-thumb">
                                <?php echo get_the_post_thumbnail( $next_post->ID, 'opulentia-nav-thumb' ); ?>
                            </div>
                        <?php endif; ?>
                        <span class="nav-direction"><?php esc_html_e( 'Next Post', 'opulentia' ); ?></span>
                        <span class="nav-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <?php
}

/**
 * Display breadcrumbs.
 *
 * Legacy wrapper that delegates to the Breadcrumbs module.
 * Fires the 'Opulentia_breadcrumbs' action which triggers
 * the module's render() method — handles source detection,
 * should_display() check, and proper output.
 */
function Opulentia_breadcrumbs() {
    do_action( 'Opulentia_breadcrumbs' );
}

/**
 * Display pagination.
 */
function Opulentia_pagination() {
    $pagination = paginate_links( array(
        'type'      => 'array',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ) );

    if ( is_array( $pagination ) ) {
        echo '<nav class="pagination" aria-label="' . esc_attr__( 'Page navigation', 'opulentia' ) . '">';
        echo '<ul class="pagination__list">';

        foreach ( $pagination as $page ) {
            echo '<li>' . $page . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        echo '</ul>';
        echo '</nav>';
    }
}

/**
 * Display custom logo.
 */
function Opulentia_custom_logo() {
    if ( has_custom_logo() ) {
        the_custom_logo();
    } else {
        echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="site-logo">';
        echo '<svg class="site-logo__icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">';
        echo '<circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="2"/>';
        echo '<text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>';
        echo '</svg>';
        echo '<span class="site-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
        echo '</a>';
    }
}

/**
 * Display the product card.
 */
function Opulentia_product_card( $product_id = null ) {
    if ( ! $product_id ) {
        return;
    }

    $product = wc_get_product( $product_id );

    if ( ! $product ) {
        return;
    }

    $image_id  = $product->get_image_id();
    $image_url = wp_get_attachment_image_url( $image_id, 'opulentia-product' );

    if ( ! $image_url ) {
        $image_url = wc_placeholder_img_src( 'opulentia-product' );
    }
    ?>
    <div class="product-card">
        <div class="product-card__image">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="product-card__badge"><?php esc_html_e( 'Sale', 'opulentia' ); ?></span>
            <?php endif; ?>
            <img src="<?php echo esc_url( $image_url ); ?>"
                 alt="<?php echo esc_attr( $product->get_name() ); ?>"
                 loading="lazy"
                 width="600"
                 height="600">
        </div>
        <h3 class="product-card__title"><?php echo esc_html( $product->get_name() ); ?></h3>
        <p class="product-card__description"><?php echo esc_html( $product->get_short_description() ); ?></p>
        <div class="product-card__price">
            <?php echo $product->get_price_html(); ?>
        </div>
        <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-card__link">
            <?php esc_html_e( 'Shop Now', 'opulentia' ); ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <?php
}

<?php
/**
 * Template Tags
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Display the posted date.
 */
function soleorigine_posted_on() {
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
function soleorigine_posted_by() {
    printf(
        '<span class="byline"><span class="author-name">%s</span></span>',
        esc_html( get_the_author() )
    );
}

/**
 * Display post categories.
 */
function soleorigine_posted_categories() {
    $categories_list = get_the_category_list( esc_html__( ', ', 'soleorigine' ) );

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
function soleorigine_posted_tags() {
    $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'soleorigine' ) );

    if ( $tags_list ) {
        printf(
            '<div class="tags-links">%s</div>',
            $tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
    }
}

/**
 * Display post navigation.
 */
function soleorigine_post_navigation() {
    $navigation = '';

    // Don't print empty navigation.
    if ( get_the_post_type() === 'post' ) {
        $navigation = get_previous_post_link(
            '<div class="nav-previous">%link</div>',
            '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'soleorigine' ) . '</span> <span class="nav-title">%title</span>'
        );

        $navigation .= get_next_post_link(
            '<div class="nav-next">%link</div>',
            '<span class="nav-subtitle">' . esc_html__( 'Next:', 'soleorigine' ) . '</span> <span class="nav-title">%title</span>'
        );
    }

    if ( $navigation ) {
        printf(
            '<nav class="post-navigation">%s</nav>',
            $navigation // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
    }
}

/**
 * Display breadcrumbs.
 */
function soleorigine_breadcrumbs() {
    if ( is_front_page() ) {
        return;
    }

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'soleorigine' ) . '">';
    echo '<div class="container">';
    echo '<ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';

    // Home
    echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '" itemprop="item"><span itemprop="name">' . esc_html__( 'Home', 'soleorigine' ) . '</span></a>';
    echo '<meta itemprop="position" content="1" />';
    echo '</li>';

    $position = 2;

    if ( is_singular( 'post' ) ) {
        echo '<li class="breadcrumbs__separator" aria-hidden="true">/</li>';
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<a href="' . esc_url( get_permalink( get_option( 'page_for_posts' ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html__( 'Blog', 'soleorigine' ) . '</span></a>';
        echo '<meta itemprop="position" content="' . esc_attr( $position ) . '" />';
        echo '</li>';
        $position++;

        echo '<li class="breadcrumbs__separator" aria-hidden="true">/</li>';
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumbs__current">';
        echo '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
        echo '<meta itemprop="position" content="' . esc_attr( $position ) . '" />';
        echo '</li>';
    } elseif ( is_category() || is_single() ) {
        if ( is_single() ) {
            $categories = get_the_category();
            if ( ! empty( $categories ) ) {
                $category = $categories[0];
                echo '<li class="breadcrumbs__separator" aria-hidden="true">/</li>';
                echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
                echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $category->name ) . '</span></a>';
                echo '<meta itemprop="position" content="' . esc_attr( $position ) . '" />';
                echo '</li>';
                $position++;
            }
        }
    } elseif ( is_page() ) {
        echo '<li class="breadcrumbs__separator" aria-hidden="true">/</li>';
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumbs__current">';
        echo '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
        echo '<meta itemprop="position" content="' . esc_attr( $position ) . '" />';
        echo '</li>';
    } elseif ( is_search() ) {
        echo '<li class="breadcrumbs__separator" aria-hidden="true">/</li>';
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumbs__current">';
        echo '<span itemprop="name">' . esc_html( sprintf( __( 'Search: %s', 'soleorigine' ), get_search_query() ) ) . '</span>';
        echo '<meta itemprop="position" content="' . esc_attr( $position ) . '" />';
        echo '</li>';
    } elseif ( is_404() ) {
        echo '<li class="breadcrumbs__separator" aria-hidden="true">/</li>';
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumbs__current">';
        echo '<span itemprop="name">' . esc_html__( 'Page not found', 'soleorigine' ) . '</span>';
        echo '<meta itemprop="position" content="' . esc_attr( $position ) . '" />';
        echo '</li>';
    }

    echo '</ol>';
    echo '</div>';
    echo '</nav>';
}

/**
 * Display pagination.
 */
function soleorigine_pagination() {
    $pagination = paginate_links( array(
        'type'      => 'array',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ) );

    if ( is_array( $pagination ) ) {
        echo '<nav class="pagination" aria-label="' . esc_attr__( 'Page navigation', 'soleorigine' ) . '">';
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
function soleorigine_custom_logo() {
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
function soleorigine_product_card( $product_id = null ) {
    if ( ! $product_id ) {
        return;
    }

    $product = wc_get_product( $product_id );

    if ( ! $product ) {
        return;
    }

    $image_id  = $product->get_image_id();
    $image_url = wp_get_attachment_image_url( $image_id, 'soleorigine-product' );

    if ( ! $image_url ) {
        $image_url = wc_placeholder_img_src( 'soleorigine-product' );
    }
    ?>
    <div class="product-card">
        <div class="product-card__image">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="product-card__badge"><?php esc_html_e( 'Sale', 'soleorigine' ); ?></span>
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
            <?php esc_html_e( 'Shop Now', 'soleorigine' ); ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <?php
}

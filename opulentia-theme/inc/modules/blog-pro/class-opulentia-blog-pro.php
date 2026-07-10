<?php
/**
 * Blog Pro Module — Singleton
 *
 * Advanced blog features:
 * - Infinite scroll (load more button, auto-scroll)
 * - Read time estimation
 * - Enhanced author box (avatar, bio, social)
 * - Enhanced related posts with filtering
 * - Post navigation with thumbnails
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Blog_Pro class.
 */
class Opulentia_Blog_Pro {

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
		// Read time
		add_filter( 'the_content', array( $this, 'add_read_time' ), 15 );

		// Enhanced author box
		add_action( 'Opulentia_single_footer_before', array( $this, 'render_author_box' ) );

		// Enhanced related posts
		add_action( 'Opulentia_single_footer_before', array( $this, 'render_related_posts' ) );

		// Infinite scroll AJAX
		add_action( 'wp_ajax_Opulentia_load_more_posts', array( $this, 'ajax_load_more' ) );
		add_action( 'wp_ajax_nopriv_Opulentia_load_more_posts', array( $this, 'ajax_load_more' ) );

		// Enqueue infinite scroll script
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	// -------------------------------------------------------------------------
	// Read Time
	// -------------------------------------------------------------------------

	/**
	 * Add read time estimation before post content.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function add_read_time( $content ) {
		if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		if ( ! Opulentia_get_option( 'blog-pro-read-time', true ) ) {
			return $content;
		}

		$word_count      = str_word_count( wp_strip_all_tags( $content ) );
		$words_per_min   = (int) Opulentia_get_option( 'blog-pro-wpm', 200 );
		$read_time       = max( 1, ceil( $word_count / $words_per_min ) );
		$read_time_html  = '<div class="post-read-time">';
		$read_time_html .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
		$read_time_html .= '<span>' . sprintf( _n( '%d min read', '%d min read', $read_time, 'opulentia' ), $read_time ) . '</span>';
		$read_time_html .= '</div>';

		return $read_time_html . $content;
	}

	// -------------------------------------------------------------------------
	// Author Box
	// -------------------------------------------------------------------------

	/**
	 * Render enhanced author box on single posts.
	 */
	public function render_author_box() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		if ( ! Opulentia_get_option( 'blog-single-show-author', true ) ) {
			return;
		}

		$author_id    = get_the_author_meta( 'ID' );
		$avatar       = get_avatar( $author_id, 80 );
		$display_name = get_the_author();
		$description  = get_the_author_meta( 'description' );
		$author_url   = get_author_posts_url( $author_id );
		$website      = get_the_author_meta( 'user_url' );
		?>
		<div class="author-box">
			<div class="author-box__avatar">
				<?php if ( $avatar ) : ?>
					<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<div class="author-box__avatar-placeholder"><?php echo esc_html( strtoupper( substr( $display_name, 0, 2 ) ) ); ?></div>
				<?php endif; ?>
			</div>
			<div class="author-box__content">
				<h3 class="author-box__name"><?php echo esc_html( $display_name ); ?></h3>
				<?php if ( $description ) : ?>
					<p class="author-box__description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
				<div class="author-box__links">
					<a href="<?php echo esc_url( $author_url ); ?>" class="author-box__link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
						<?php esc_html_e( 'View All Posts', 'opulentia' ); ?>
					</a>
					<?php if ( $website ) : ?>
						<a href="<?php echo esc_url( $website ); ?>" class="author-box__link" target="_blank" rel="noopener noreferrer">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
							<?php esc_html_e( 'Website', 'opulentia' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Related Posts
	// -------------------------------------------------------------------------

	/**
	 * Render enhanced related posts on single posts.
	 */
	public function render_related_posts() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		if ( ! Opulentia_get_option( 'blog-single-show-related', true ) ) {
			return;
		}

		$post_id    = get_the_ID();
		$filter_by  = Opulentia_get_option( 'blog-related-filter', 'category' );
		$count      = (int) Opulentia_get_option( 'blog-related-count', 3 );
		$columns    = min( $count, 3 );
		$show_image = Opulentia_get_option( 'blog-related-show-image', true );
		$show_date  = Opulentia_get_option( 'blog-related-show-date', true );

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $count,
			'post__not_in'   => array( $post_id ),
			'no_found_rows'  => true,
		);

		if ( 'category' === $filter_by ) {
			$categories = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
			if ( ! empty( $categories ) ) {
				$args['category__in'] = $categories;
			}
		} elseif ( 'tag' === $filter_by ) {
			$tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
			if ( ! empty( $tags ) ) {
				$args['tag__in'] = $tags;
			}
		} elseif ( 'both' === $filter_by ) {
			$categories = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
			$tags       = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
			if ( ! empty( $categories ) || ! empty( $tags ) ) {
				$args['tax_query'] = array(
					'relation' => 'OR',
				);
				if ( ! empty( $categories ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $categories,
					);
				}
				if ( ! empty( $tags ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'post_tag',
						'field'    => 'term_id',
						'terms'    => $tags,
					);
				}
			}
		}

		$related = new WP_Query( $args );

		if ( ! $related->have_posts() ) {
			return;
		}
		?>
		<div class="related-posts">
			<h3 class="related-posts__title"><?php esc_html_e( 'Related Posts', 'opulentia' ); ?></h3>
			<div class="related-posts__grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
				<?php
				while ( $related->have_posts() ) :
					$related->the_post();
					?>
					<article class="related-post-card">
						<?php if ( $show_image && has_post_thumbnail() ) : ?>
							<div class="related-post-card__image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium' ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="related-post-card__content">
							<?php if ( $show_date ) : ?>
								<div class="related-post-card__meta">
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								</div>
							<?php endif; ?>
							<h4 class="related-post-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h4>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Infinite Scroll
	// -------------------------------------------------------------------------

	/**
	 * AJAX handler for loading more posts.
	 */
	public function ajax_load_more() {
		check_ajax_referer( 'Opulentia_blog_pro_nonce', 'nonce' );

		$page     = absint( $_POST['page'] );
		$per_page = absint( $_POST['per_page'] );
		$layout   = sanitize_text_field( $_POST['layout'] );

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $per_page ? $per_page : 6,
			'paged'          => $page ? $page : 2,
		);

		$query = new WP_Query( $args );

		ob_start();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				get_template_part( 'template-parts/blog/layout', $layout );
			}
		}

		$html = ob_get_clean();
		wp_reset_postdata();

		wp_send_json_success(
			array(
				'html'    => $html,
				'hasMore' => $query->max_num_pages > $page,
				'page'    => $page + 1,
			)
		);
	}

	/**
	 * Enqueue infinite scroll script.
	 */
	public function enqueue_scripts() {
		if ( ! is_home() && ! is_archive() ) {
			return;
		}

		$scroll_type = Opulentia_get_option( 'blog-pro-infinite-scroll', 'pagination' );

		if ( 'pagination' === $scroll_type ) {
			return;
		}

		wp_localize_script(
			'opulentia-navigation',
			'OpulentiaBlogPro',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'Opulentia_blog_pro_nonce' ),
				'scrollType'   => $scroll_type,
				'layout'       => Opulentia_get_blog_layout(),
				'perPage'      => (int) get_option( 'posts_per_page', 6 ),
				'loadMoreText' => __( 'Load More Posts', 'opulentia' ),
				'loadingText'  => __( 'Loading...', 'opulentia' ),
				'noMoreText'   => __( 'No more posts to load.', 'opulentia' ),
			)
		);
	}
}

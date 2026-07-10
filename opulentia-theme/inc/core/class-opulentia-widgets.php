<?php
/**
 * Custom Widget Manager — Singleton
 *
 * Registers all Opulentia custom widgets from a single entry point.
 * Replaces the procedural widgets.php with a class-based approach
 * that integrates with the new architecture.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Widgets class.
 */
class Opulentia_Widgets {

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
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register all custom widgets.
	 */
	public function register_widgets() {
		register_widget( 'Opulentia_Social_Links_Widget' );
		register_widget( 'Opulentia_About_Widget' );
		register_widget( 'Opulentia_Newsletter_Widget' );
		register_widget( 'Opulentia_Related_Posts_Widget' );
	}
}

/**
 * Social Links Widget
 */
class Opulentia_Social_Links_Widget extends WP_Widget {

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'Opulentia_social_links',
			__( 'Opulentia Social Links', 'opulentia' ),
			array(
				'description' => __( 'Displays social media links.', 'opulentia' ),
			)
		);
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$social_links = array(
			'facebook'  => __( 'Facebook', 'opulentia' ),
			'instagram' => __( 'Instagram', 'opulentia' ),
			'twitter'   => __( 'Twitter', 'opulentia' ),
			'youtube'   => __( 'YouTube', 'opulentia' ),
			'pinterest' => __( 'Pinterest', 'opulentia' ),
		);

		echo '<div class="social-links">';
		foreach ( $social_links as $social => $label ) {
			$url = get_theme_mod( 'social_' . $social, '' );
			if ( ! empty( $url ) ) {
				printf(
					'<a href="%s" target="_blank" rel="noopener noreferrer" class="social-links__item" aria-label="%s">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>',
					esc_url( $url ),
					esc_attr( $label )
				);
			}
		}
		echo '</div>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Follow Us', 'opulentia' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p><?php esc_html_e( 'Social media URLs are configured in the Customizer.', 'opulentia' ); ?></p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}
}

/**
 * About Widget
 */
class Opulentia_About_Widget extends WP_Widget {

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'Opulentia_about',
			__( 'Opulentia About', 'opulentia' ),
			array(
				'description' => __( 'Displays about information with image.', 'opulentia' ),
			)
		);
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$image = ! empty( $instance['image'] ) ? $instance['image'] : '';
		$text  = ! empty( $instance['text'] ) ? $instance['text'] : '';

		echo '<div class="about-widget">';
		if ( $image ) {
			printf(
				'<img src="%s" alt="%s" class="about-widget__image">',
				esc_url( $image ),
				esc_attr( $instance['title'] )
			);
		}
		if ( $text ) {
			printf(
				'<p class="about-widget__text">%s</p>',
				wp_kses_post( $text )
			);
		}
		echo '</div>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'About Us', 'opulentia' );
		$image = ! empty( $instance['image'] ) ? $instance['image'] : '';
		$text  = ! empty( $instance['text'] ) ? $instance['text'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Image URL:', 'opulentia' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="url" value="<?php echo esc_url( $image ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text:', 'opulentia' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="4"><?php echo esc_textarea( $text ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['image'] = esc_url_raw( $new_instance['image'] );
		$instance['text']  = wp_kses_post( $new_instance['text'] );
		return $instance;
	}
}

/**
 * Newsletter Widget
 */
class Opulentia_Newsletter_Widget extends WP_Widget {

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'Opulentia_newsletter',
			__( 'Opulentia Newsletter', 'opulentia' ),
			array(
				'description' => __( 'Displays newsletter signup form.', 'opulentia' ),
			)
		);
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$text = ! empty( $instance['text'] ) ? $instance['text'] : __( 'Subscribe to our newsletter for the latest updates and exclusive offers.', 'opulentia' );

		echo '<div class="newsletter-widget">';
		printf(
			'<p class="newsletter-widget__text">%s</p>',
			esc_html( $text )
		);
		?>
		<form class="newsletter-widget__form" action="#" method="post">
			<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email address', 'opulentia' ); ?>" required>
			<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Subscribe', 'opulentia' ); ?></button>
		</form>
		<?php
		echo '</div>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Newsletter', 'opulentia' );
		$text  = ! empty( $instance['text'] ) ? $instance['text'] : __( 'Subscribe to our newsletter for the latest updates and exclusive offers.', 'opulentia' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text:', 'opulentia' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="3"><?php echo esc_textarea( $text ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['text']  = sanitize_textarea_field( $new_instance['text'] );
		return $instance;
	}
}

/**
 * Related Posts Widget
 *
 * Displays posts related to the current post by shared tags and categories.
 */
class Opulentia_Related_Posts_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'Opulentia_related_posts',
			__( 'Opulentia Related Posts', 'opulentia' ),
			array(
				'description' => __( 'Displays posts related by shared tags and categories.', 'opulentia' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		if ( ! is_single() ) {
			return;
		}

		echo $args['before_widget'];

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Related Posts', 'opulentia' );
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];
		}

		$limit        = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 5;
		$show_tags    = ! empty( $instance['show_tags'] );
		$show_cats    = ! empty( $instance['show_cats'] );
		$use_tags     = ! empty( $instance['use_tags'] );
		$use_cats     = ! empty( $instance['use_cats'] );
		$show_thumb   = ! empty( $instance['show_thumb'] );
		$show_date    = ! empty( $instance['show_date'] );
		$show_excerpt = ! empty( $instance['show_excerpt'] );

		$post_id = get_the_ID();
		$related = $this->get_related_posts( $post_id, $limit, $use_tags, $use_cats );

		if ( $related->have_posts() ) {
			echo '<div class="opulentia-related-posts">';
			while ( $related->have_posts() ) {
				$related->the_post();
				echo '<div class="opulentia-related-post">';

				if ( $show_thumb && has_post_thumbnail() ) {
					echo '<div class="opulentia-related-post__thumb">';
					the_post_thumbnail( 'thumbnail', array( 'class' => 'opulentia-related-post__image' ) );
					echo '</div>';
				}

				echo '<div class="opulentia-related-post__content">';
				printf(
					'<h4 class="opulentia-related-post__title"><a href="%s">%s</a></h4>',
					esc_url( get_permalink() ),
					esc_html( get_the_title() )
				);

				$meta = array();
				if ( $show_date ) {
					$meta[] = sprintf(
						'<span class="opulentia-related-post__date">%s</span>',
						esc_html( get_the_date() )
					);
				}
				if ( $show_cats ) {
					$cats = get_the_category_list( ', ' );
					if ( $cats ) {
						$meta[] = sprintf(
							'<span class="opulentia-related-post__cats">%s</span>',
							$cats
						);
					}
				}
				if ( $show_tags ) {
					$tags = get_the_tag_list( '', ', ' );
					if ( $tags ) {
						$meta[] = sprintf(
							'<span class="opulentia-related-post__tags">%s</span>',
							$tags
						);
					}
				}
				if ( ! empty( $meta ) ) {
					echo '<div class="opulentia-related-post__meta">' . implode( ' &middot; ', $meta ) . '</div>';
				}

				if ( $show_excerpt ) {
					echo '<div class="opulentia-related-post__excerpt">' . wp_trim_words( get_the_excerpt(), 15 ) . '</div>';
				}

				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p class="opulentia-related-posts__none">' . esc_html__( 'No related posts found.', 'opulentia' ) . '</p>';
		}

		echo $args['after_widget'];
	}

	private function get_related_posts( $post_id, $max, $use_tags, $use_cats ) {
		$tax_query = array( 'relation' => 'OR' );

		if ( $use_tags ) {
			$tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
			if ( ! empty( $tags ) ) {
				$tax_query[] = array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => $tags,
				);
			}
		}

		if ( $use_cats ) {
			$categories = wp_get_object_terms( $post_id, 'category', array( 'fields' => 'ids' ) );
			if ( ! empty( $categories ) ) {
				$tax_query[] = array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $categories,
				);
			}
		}

		if ( count( $tax_query ) < 2 ) {
			return new WP_Query( array( 'post__in' => array( 0 ) ) );
		}

		return new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => $max,
				'post__not_in'   => array( $post_id ),
				'tax_query'      => $tax_query,
				'no_found_rows'  => true,
			)
		);
	}

	public function form( $instance ) {
		$title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Related Posts', 'opulentia' );
		$limit        = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 5;
		$use_tags     = ! empty( $instance['use_tags'] );
		$use_cats     = ! empty( $instance['use_cats'] );
		$show_thumb   = ! empty( $instance['show_thumb'] );
		$show_date    = ! empty( $instance['show_date'] );
		$show_cats    = ! empty( $instance['show_cats'] );
		$show_tags    = ! empty( $instance['show_tags'] );
		$show_excerpt = ! empty( $instance['show_excerpt'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'opulentia' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" step="1" min="1" max="20" value="<?php echo esc_attr( $limit ); ?>">
		</p>
		<h4><?php esc_html_e( 'Relation Criteria', 'opulentia' ); ?></h4>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'use_tags' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_tags' ) ); ?>" type="checkbox" value="1" <?php checked( $use_tags ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'use_tags' ) ); ?>"><?php esc_html_e( 'Match by tags', 'opulentia' ); ?></label>
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'use_cats' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_cats' ) ); ?>" type="checkbox" value="1" <?php checked( $use_cats ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'use_cats' ) ); ?>"><?php esc_html_e( 'Match by categories', 'opulentia' ); ?></label>
		</p>
		<h4><?php esc_html_e( 'Display Options', 'opulentia' ); ?></h4>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumb' ) ); ?>" type="checkbox" value="1" <?php checked( $show_thumb ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>"><?php esc_html_e( 'Show featured image', 'opulentia' ); ?></label>
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" type="checkbox" value="1" <?php checked( $show_date ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>"><?php esc_html_e( 'Show post date', 'opulentia' ); ?></label>
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_cats' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_cats' ) ); ?>" type="checkbox" value="1" <?php checked( $show_cats ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_cats' ) ); ?>"><?php esc_html_e( 'Show category names', 'opulentia' ); ?></label>
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_tags' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_tags' ) ); ?>" type="checkbox" value="1" <?php checked( $show_tags ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_tags' ) ); ?>"><?php esc_html_e( 'Show tag names', 'opulentia' ); ?></label>
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>" type="checkbox" value="1" <?php checked( $show_excerpt ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>"><?php esc_html_e( 'Show excerpt', 'opulentia' ); ?></label>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                 = array();
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['limit']        = absint( $new_instance['limit'] );
		$instance['use_tags']     = ! empty( $new_instance['use_tags'] ) ? 1 : 0;
		$instance['use_cats']     = ! empty( $new_instance['use_cats'] ) ? 1 : 0;
		$instance['show_thumb']   = ! empty( $new_instance['show_thumb'] ) ? 1 : 0;
		$instance['show_date']    = ! empty( $new_instance['show_date'] ) ? 1 : 0;
		$instance['show_cats']    = ! empty( $new_instance['show_cats'] ) ? 1 : 0;
		$instance['show_tags']    = ! empty( $new_instance['show_tags'] ) ? 1 : 0;
		$instance['show_excerpt'] = ! empty( $new_instance['show_excerpt'] ) ? 1 : 0;
		return $instance;
	}
}

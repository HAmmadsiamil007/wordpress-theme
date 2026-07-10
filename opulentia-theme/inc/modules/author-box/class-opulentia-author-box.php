<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Author_Box {

	private static $instance = null;

	private $social_networks = array();

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->social_networks = array(
			'twitter'   => __( 'X (Twitter)', 'opulentia' ),
			'facebook'  => __( 'Facebook', 'opulentia' ),
			'linkedin'  => __( 'LinkedIn', 'opulentia' ),
			'instagram' => __( 'Instagram', 'opulentia' ),
			'youtube'   => __( 'YouTube', 'opulentia' ),
			'github'    => __( 'GitHub', 'opulentia' ),
		);

		add_filter( 'the_content', array( $this, 'append_author_box' ) );
		add_shortcode( 'op_author_box', array( $this, 'shortcode' ) );
		add_action( 'show_user_profile', array( $this, 'add_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_profile_fields' ) );
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
	}

	private function is_enabled() {
		return (bool) Opulentia_get_option( 'author-box-enable', true );
	}

	public function append_author_box( $content ) {
		if ( ! is_single() || is_page() || is_admin() ) {
			return $content;
		}

		if ( ! $this->is_enabled() ) {
			return $content;
		}

		$author_id = get_post_field( 'post_author', get_the_ID() );
		if ( ! $author_id ) {
			return $content;
		}

		$description = get_the_author_meta( 'description', $author_id );
		if ( empty( $description ) ) {
			return $content;
		}

		return $content . $this->render_author_box( $author_id );
	}

	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'author_id' => 0,
			),
			$atts,
			'op_author_box'
		);

		$author_id = absint( $atts['author_id'] );
		if ( $author_id < 1 ) {
			$author_id = get_post_field( 'post_author', get_the_ID() );
		}
		if ( ! $author_id ) {
			return '';
		}

		return $this->render_author_box( $author_id );
	}

	private function render_author_box( $author_id ) {
		$author_id = absint( $author_id );
		if ( $author_id < 1 ) {
			return '';
		}

		$author_name  = get_the_author_meta( 'display_name', $author_id );
		$author_title = get_the_author_meta( 'op_author_title', $author_id );
		$description  = get_the_author_meta( 'description', $author_id );
		$author_url   = get_author_posts_url( $author_id );
		$avatar       = get_avatar( $author_id, 80, '', esc_attr( $author_name ) );

		$show_avatar  = (bool) Opulentia_get_option( 'author-box-show-avatar', true );
		$show_recent  = (bool) Opulentia_get_option( 'author-box-show-recent', true );
		$recent_count = (int) Opulentia_get_option( 'author-box-recent-count', 3 );

		$html = '<div class="op-author-box">';

		if ( $show_avatar ) {
			$html .= '<div class="op-author-box-avatar">' . $avatar . '</div>';
		}

		$html .= '<div class="op-author-box-content">';
		$html .= '<h4 class="op-author-box-name">' . esc_html( $author_name ) . '</h4>';

		if ( ! empty( $author_title ) ) {
			$html .= '<div class="op-author-box-title">' . esc_html( $author_title ) . '</div>';
		}

		if ( ! empty( $description ) ) {
			$html .= '<p class="op-author-box-bio">' . wp_kses_post( $description ) . '</p>';
		}

		$social_html = $this->get_social_links( $author_id );
		if ( ! empty( $social_html ) ) {
			$html .= '<div class="op-author-box-social">' . $social_html . '</div>';
		}

		$html .= '<a class="op-author-box-link" href="' . esc_url( $author_url ) . '">' . esc_html__( 'View All Posts', 'opulentia' ) . ' &rarr;</a>';
		$html .= '</div>';

		if ( $show_recent ) {
			$recent_posts = $this->get_recent_posts( $author_id, $recent_count );
			if ( ! empty( $recent_posts ) ) {
				$html .= '<div class="op-author-box-recent">';
				$html .= '<h5>' . esc_html__( 'Recent Posts', 'opulentia' ) . '</h5>';
				$html .= '<ul>';
				foreach ( $recent_posts as $post_item ) {
					$html .= '<li>';
					$html .= '<a href="' . esc_url( get_permalink( $post_item->ID ) ) . '">' . esc_html( get_the_title( $post_item->ID ) ) . '</a>';
					$html .= '<span class="post-date">' . esc_html( get_the_date( '', $post_item->ID ) ) . '</span>';
					$html .= '</li>';
				}
				$html .= '</ul>';
				$html .= '</div>';
			}
		}

		$html .= '</div>';

		return $html;
	}

	private function get_social_links( $author_id ) {
		$output = '';

		foreach ( $this->social_networks as $key => $label ) {
			$url = get_the_author_meta( 'op_author_' . $key, $author_id );
			if ( ! empty( $url ) ) {
				$aria_label = sprintf(
					/* translators: %s: Social network name */
					__( 'Follow on %s', 'opulentia' ),
					$label
				);
				$output .= '<a href="' . esc_url( $url ) . '" class="op-author-box-social-link op-author-box-social--' . esc_attr( $key ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $aria_label ) . '">' . $this->get_social_svg( $key ) . '</a>';
			}
		}

		return $output;
	}

	private function get_social_svg( $network ) {
		$svgs = array(
			'twitter'   => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
			'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
			'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
			'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
			'github'    => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>',
		);

		return isset( $svgs[ $network ] ) ? $svgs[ $network ] : '';
	}

	private function get_recent_posts( $author_id, $count = 3 ) {
		$count = max( 1, min( 5, absint( $count ) ) );

		$posts = get_posts(
			array(
				'author'              => $author_id,
				'posts_per_page'      => $count,
				'post_status'         => 'publish',
				'post_type'           => 'post',
				'ignore_sticky_posts' => true,
			)
		);

		return $posts;
	}

	public function add_user_profile_fields( $user ) {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}
		?>
		<h3><?php esc_html_e( 'Opulentia Author Box Settings', 'opulentia' ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="op-author-title"><?php esc_html_e( 'Author Title/Role', 'opulentia' ); ?></label></th>
					<td>
						<?php wp_nonce_field( 'opulentia_author_box_user_fields', 'opulentia_author_box_nonce' ); ?>
						<input type="text" name="op_author_title" id="op-author-title" value="<?php echo esc_attr( get_the_author_meta( 'op_author_title', $user->ID ) ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Senior Developer', 'opulentia' ); ?>" />
						<p class="description"><?php esc_html_e( 'Displayed below the author name in the author box.', 'opulentia' ); ?></p>
					</td>
				</tr>
				<?php foreach ( $this->social_networks as $key => $label ) : ?>
					<tr>
						<th><label for="op-author-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td>
							<input type="url" name="op_author_<?php echo esc_attr( $key ); ?>" id="op-author-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_url( get_the_author_meta( 'op_author_' . $key, $user->ID ) ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'https://', 'opulentia' ); ?>" />
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	public function save_user_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( ! isset( $_POST['opulentia_author_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['opulentia_author_box_nonce'] ) ), 'opulentia_author_box_user_fields' ) ) {
			return;
		}

		if ( isset( $_POST['op_author_title'] ) ) {
			update_user_meta( $user_id, 'op_author_title', sanitize_text_field( wp_unslash( $_POST['op_author_title'] ) ) );
		}

		foreach ( $this->social_networks as $key => $label ) {
			$meta_key = 'op_author_' . $key;
			if ( isset( $_POST[ $meta_key ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $meta_key ] ) );
				if ( ! empty( $value ) ) {
					update_user_meta( $user_id, $meta_key, esc_url_raw( $value ) );
				} else {
					delete_user_meta( $user_id, $meta_key );
				}
			}
		}
	}

	public function inline_css() {
		$css = '
        .op-author-box {
            display: flex;
            gap: 24px;
            padding: 30px;
            margin: 40px 0;
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-radius: 12px;
            flex-wrap: wrap;
        }
        .op-author-box-avatar {
            flex-shrink: 0;
        }
        .op-author-box-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid var(--color-gold, #c9a96e);
        }
        .op-author-box-content {
            flex: 1;
            min-width: 200px;
        }
        .op-author-box-name {
            font-family: var(--font-heading, \'Playfair Display\');
            font-size: 1.2rem;
            color: var(--color-text, #f5f5f5);
            margin: 0 0 2px;
        }
        .op-author-box-title {
            font-family: var(--font-body, Inter);
            font-size: 0.8rem;
            color: var(--color-gold, #c9a96e);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .op-author-box-bio {
            font-family: var(--font-body, Inter);
            font-size: 0.85rem;
            color: var(--color-text-muted, #999);
            line-height: 1.6;
            margin: 0 0 12px;
        }
        .op-author-box-social {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }
        .op-author-box-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-border, #333);
            color: var(--color-text, #f5f5f5);
            transition: background 0.3s ease, color 0.3s ease;
        }
        .op-author-box-social a:hover {
            background: var(--color-accent, #b8860b);
            color: #fff;
        }
        .op-author-box-link {
            font-family: var(--font-body, Inter);
            font-size: 0.85rem;
            color: var(--color-gold, #c9a96e);
            text-decoration: none;
            font-weight: 600;
        }
        .op-author-box-link:hover {
            color: var(--color-accent-hover, #d4a843);
        }
        .op-author-box-recent {
            width: 100%;
            border-top: 1px solid var(--color-border, #333);
            padding-top: 20px;
            margin-top: 10px;
        }
        .op-author-box-recent h5 {
            font-family: var(--font-heading, \'Playfair Display\');
            color: var(--color-text, #f5f5f5);
            margin: 0 0 12px;
            font-size: 1rem;
        }
        .op-author-box-recent ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .op-author-box-recent li {
            padding: 6px 0;
        }
        .op-author-box-recent a {
            color: var(--color-text, #f5f5f5);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .op-author-box-recent a:hover {
            color: var(--color-gold, #c9a96e);
        }
        .op-author-box-recent .post-date {
            font-size: 0.75rem;
            color: var(--color-text-muted, #999);
            margin-left: 8px;
        }
        @media (max-width: 600px) {
            .op-author-box { flex-direction: column; align-items: center; text-align: center; }
            .op-author-box-social { justify-content: center; }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_author_box',
			array(
				'title'    => __( 'Author Box', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 130,
			)
		);

		$wp_customize->add_setting(
			'author-box-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'author-box-enable',
			array(
				'label'   => __( 'Enable Author Box on Single Posts', 'opulentia' ),
				'section' => 'opulentia_author_box',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'author-box-show-avatar',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'author-box-show-avatar',
			array(
				'label'   => __( 'Show Avatar', 'opulentia' ),
				'section' => 'opulentia_author_box',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'author-box-show-recent',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'author-box-show-recent',
			array(
				'label'   => __( 'Show Recent Posts', 'opulentia' ),
				'section' => 'opulentia_author_box',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'author-box-recent-count',
			array(
				'default'           => 3,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'author-box-recent-count',
			array(
				'label'       => __( 'Number of Recent Posts', 'opulentia' ),
				'section'     => 'opulentia_author_box',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 1,
					'max'  => 5,
					'step' => 1,
				),
			)
		);
	}
}

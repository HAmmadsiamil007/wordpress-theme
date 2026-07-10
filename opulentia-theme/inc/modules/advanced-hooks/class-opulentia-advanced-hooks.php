<?php
/**
 * Advanced Hooks Module — Singleton
 *
 * Registers the `opulentia_hook` custom post type for creating
 * custom content injection points (PHP/JS/CSS/HTML) at predefined
 * theme hook locations with display conditions.
 *
 * Hook Locations:
 *   - opulentia_head_top / opulentia_head_bottom
 *   - opulentia_body_top / opulentia_body_bottom
 *   - opulentia_header_before / opulentia_header_after
 *   - opulentia_content_before / opulentia_content_after
 *   - opulentia_entry_before / opulentia_entry_after
 *   - opulentia_entry_content_before / opulentia_entry_content_after
 *   - opulentia_sidebar_before / opulentia_sidebar_after
 *   - opulentia_footer_before / opulentia_footer_after
 *   - wp_head / wp_footer
 *
 * Display Conditions:
 *   - Entire site, front page, blog index
 *   - All archives, all posts, all pages
 *   - Specific post types, taxonomies, terms
 *   - 404 page, search results, singular
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Advanced_Hooks class.
 */
class Opulentia_Advanced_Hooks {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Available hook locations for injection.
	 *
	 * @var array
	 */
	private static $hook_locations = array();

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
		$this->init_hook_locations();

		add_action( 'init', array( $this, 'register_post_type' ), 5 );
		add_action( 'init', array( $this, 'register_meta_fields' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		add_filter( 'manage_opulentia_hook_posts_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_opulentia_hook_posts_custom_column', array( $this, 'admin_column_content' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		// Frontend rendering is delegated to the render class.
	}

	// -------------------------------------------------------------------------
	// Hook Locations Registry
	// -------------------------------------------------------------------------

	/**
	 * Initialize the available hook locations.
	 */
	private function init_hook_locations() {
		self::$hook_locations = array(
			// Head hooks
			'opulentia_head_top'       => __( 'Head — Top', 'opulentia' ),
			'opulentia_head_bottom'    => __( 'Head — Bottom', 'opulentia' ),

			// Body hooks
			'opulentia_body_top'       => __( 'Body — After Open', 'opulentia' ),
			'opulentia_body_bottom'    => __( 'Body — Before Close', 'opulentia' ),

			// Header hooks
			'opulentia_header_before'  => __( 'Header — Before', 'opulentia' ),
			'opulentia_header_after'   => __( 'Header — After', 'opulentia' ),
			'opulentia_header_top'     => __( 'Header — Top', 'opulentia' ),
			'opulentia_header_bottom'  => __( 'Header — Bottom', 'opulentia' ),

			// Content hooks
			'opulentia_content_before' => __( 'Content — Before Container', 'opulentia' ),
			'opulentia_content_after'  => __( 'Content — After Container', 'opulentia' ),
			'opulentia_entry_before'   => __( 'Content — Before Entry', 'opulentia' ),
			'opulentia_entry_after'    => __( 'Content — After Entry', 'opulentia' ),

			// Sidebar hooks
			'opulentia_sidebar_before' => __( 'Sidebar — Before', 'opulentia' ),
			'opulentia_sidebar_after'  => __( 'Sidebar — After', 'opulentia' ),

			// Footer hooks
			'opulentia_footer_before'  => __( 'Footer — Before', 'opulentia' ),
			'opulentia_footer_after'   => __( 'Footer — After', 'opulentia' ),
			'opulentia_footer_top'     => __( 'Footer — Top', 'opulentia' ),
			'opulentia_footer_bottom'  => __( 'Footer — Bottom', 'opulentia' ),

			// WordPress native
			'wp_head'                  => __( 'wp_head (Legacy)', 'opulentia' ),
			'wp_footer'                => __( 'wp_footer (Legacy)', 'opulentia' ),
		);

		/**
		 * Filter the available hook locations for advanced hooks.
		 *
		 * @param array $hook_locations Hook location key => label pairs.
		 */
		self::$hook_locations = apply_filters( 'opulentia_advanced_hook_locations', self::$hook_locations );
	}

	/**
	 * Get all registered hook locations.
	 *
	 * @return array
	 */
	public static function get_hook_locations() {
		if ( empty( self::$hook_locations ) ) {
			$instance = self::get_instance();
		}
		return self::$hook_locations;
	}

	// -------------------------------------------------------------------------
	// Custom Post Type
	// -------------------------------------------------------------------------

	/**
	 * Register the `opulentia_hook` custom post type.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Advanced Hooks', 'opulentia' ),
			'singular_name'      => __( 'Advanced Hook', 'opulentia' ),
			'add_new'            => __( 'Add New Hook', 'opulentia' ),
			'add_new_item'       => __( 'Add New Advanced Hook', 'opulentia' ),
			'edit_item'          => __( 'Edit Advanced Hook', 'opulentia' ),
			'new_item'           => __( 'New Advanced Hook', 'opulentia' ),
			'view_item'          => __( 'View Advanced Hook', 'opulentia' ),
			'search_items'       => __( 'Search Advanced Hooks', 'opulentia' ),
			'not_found'          => __( 'No advanced hooks found.', 'opulentia' ),
			'not_found_in_trash' => __( 'No advanced hooks found in Trash.', 'opulentia' ),
			'all_items'          => __( 'All Hooks', 'opulentia' ),
			'menu_name'          => __( 'Advanced Hooks', 'opulentia' ),
		);

		$args = array(
			'labels'           => $labels,
			'public'           => false,
			'show_ui'          => true,
			'show_in_menu'     => true,
			'menu_position'    => 65,
			'menu_icon'        => 'dashicons-editor-code',
			'capability_type'  => 'post',
			'map_meta_cap'     => true,
			'supports'         => array( 'title' ),
			'rewrite'          => false,
			'query_var'        => false,
			'can_export'       => true,
			'delete_with_user' => false,
			'show_in_rest'     => false,
		);

		register_post_type( 'opulentia_hook', $args );
	}

	// -------------------------------------------------------------------------
	// Meta Fields
	// -------------------------------------------------------------------------

	/**
	 * Register post meta fields for hooks.
	 */
	public function register_meta_fields() {
		$meta_fields = array(
			'_opulentia_hook_location'      => 'sanitize_text_field',
			'_opulentia_hook_code_type'     => 'sanitize_text_field',
			'_opulentia_hook_code'          => 'wp_unslash',
			'_opulentia_hook_priority'      => 'absint',
			'_opulentia_hook_display_on'    => array( $this, 'sanitize_display_conditions' ),
			'_opulentia_hook_enable_header' => 'absint',
			'_opulentia_hook_enable_footer' => 'absint',
			'_opulentia_hook_enable_title'  => 'absint',
		);

		foreach ( $meta_fields as $key => $sanitize_cb ) {
			register_post_meta(
				'opulentia_hook',
				$key,
				array(
					'show_in_rest'  => false,
					'single'        => true,
					'type'          => 'string',
					'auth_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}

	/**
	 * Sanitize display condition array.
	 *
	 * @param mixed $value Raw conditions input.
	 * @return string JSON-encoded array.
	 */
	public function sanitize_display_conditions( $value ) {
		if ( is_array( $value ) ) {
			return wp_json_encode( $value );
		}
		$decoded = json_decode( $value, true );
		if ( is_array( $decoded ) ) {
			return $value;
		}
		return wp_json_encode( array( 'entire_site' ) );
	}

	// -------------------------------------------------------------------------
	// Meta Boxes (Admin UI)
	// -------------------------------------------------------------------------

	/**
	 * Register meta boxes for the hook CPT.
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'opulentia_hook_settings',
			__( 'Hook Settings', 'opulentia' ),
			array( $this, 'render_hook_settings_meta_box' ),
			'opulentia_hook',
			'normal',
			'high'
		);

		add_meta_box(
			'opulentia_hook_display',
			__( 'Display Conditions', 'opulentia' ),
			array( $this, 'render_display_meta_box' ),
			'opulentia_hook',
			'side',
			'default'
		);

		add_meta_box(
			'opulentia_hook_layout',
			__( 'Layout Overrides', 'opulentia' ),
			array( $this, 'render_layout_meta_box' ),
			'opulentia_hook',
			'side',
			'default'
		);
	}

	/**
	 * Render the main hook settings meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_hook_settings_meta_box( $post ) {
		wp_nonce_field( 'opulentia_advanced_hook', 'opulentia_advanced_hook_nonce' );

		$location  = get_post_meta( $post->ID, '_opulentia_hook_location', true );
		$code_type = get_post_meta( $post->ID, '_opulentia_hook_code_type', true ) ?: 'html';
		$code      = get_post_meta( $post->ID, '_opulentia_hook_code', true );
		$priority  = get_post_meta( $post->ID, '_opulentia_hook_priority', true ) ?: 10;
		?>
		<div class="opulentia-advanced-hook-editor">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="opulentia_hook_location"><?php esc_html_e( 'Hook Location', 'opulentia' ); ?></label>
						</th>
						<td>
							<select id="opulentia_hook_location" name="_opulentia_hook_location" style="width:100%;max-width:400px;">
								<option value=""><?php esc_html_e( '— Select Hook Location —', 'opulentia' ); ?></option>
								<?php foreach ( self::get_hook_locations() as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $location, $key ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<p class="description">
								<?php esc_html_e( 'Where should this code be injected? Choose the theme action hook.', 'opulentia' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="opulentia_hook_code_type"><?php esc_html_e( 'Code Type', 'opulentia' ); ?></label>
						</th>
						<td>
							<select id="opulentia_hook_code_type" name="_opulentia_hook_code_type">
								<option value="html" <?php selected( $code_type, 'html' ); ?>><?php esc_html_e( 'HTML', 'opulentia' ); ?></option>
								<option value="css" <?php selected( $code_type, 'css' ); ?>><?php esc_html_e( 'CSS', 'opulentia' ); ?></option>
								<option value="js" <?php selected( $code_type, 'js' ); ?>><?php esc_html_e( 'JavaScript', 'opulentia' ); ?></option>
								<option value="php" <?php selected( $code_type, 'php' ); ?>><?php esc_html_e( 'PHP', 'opulentia' ); ?></option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Select the type of code to inject. PHP will be evaluated on output.', 'opulentia' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="opulentia_hook_priority"><?php esc_html_e( 'Priority', 'opulentia' ); ?></label>
						</th>
						<td>
							<input type="number" id="opulentia_hook_priority" name="_opulentia_hook_priority"
									value="<?php echo esc_attr( $priority ); ?>" min="1" max="100" step="1"
									style="width:80px;">
							<p class="description">
								<?php esc_html_e( 'Lower numbers execute first. Default: 10.', 'opulentia' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="opulentia_hook_code"><?php esc_html_e( 'Code', 'opulentia' ); ?></label>
						</th>
						<td>
							<textarea id="opulentia_hook_code" name="_opulentia_hook_code"
										rows="12" style="width:100%;font-family:monospace;font-size:13px;"
										placeholder="<?php esc_attr_e( 'Enter your HTML, CSS, JS, or PHP code here...', 'opulentia' ); ?>"><?php echo esc_textarea( $code ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'For PHP: use opening &lt;?php and closing ?&gt; tags as needed.', 'opulentia' ); ?>
								<?php esc_html_e( 'For JS: use &lt;script&gt; tags. For CSS: use &lt;style&gt; tags or raw CSS.', 'opulentia' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the display conditions meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_display_meta_box( $post ) {
		$conditions = get_post_meta( $post->ID, '_opulentia_hook_display_on', true );
		if ( is_string( $conditions ) ) {
			$conditions = json_decode( $conditions, true );
		}
		if ( ! is_array( $conditions ) ) {
			$conditions = array( 'entire_site' );
		}

		$condition_options = array(
			'entire_site' => __( 'Entire Site', 'opulentia' ),
			'front_page'  => __( 'Front Page Only', 'opulentia' ),
			'blog_index'  => __( 'Blog Index', 'opulentia' ),
			'singular'    => __( 'All Singular', 'opulentia' ),
			'archive'     => __( 'All Archives', 'opulentia' ),
			'single_post' => __( 'Single Posts', 'opulentia' ),
			'single_page' => __( 'Single Pages', 'opulentia' ),
			'search'      => __( 'Search Results', 'opulentia' ),
			'404'         => __( '404 Page', 'opulentia' ),
		);

		// Add registered public post types.
		$post_types = get_post_types(
			array(
				'public'   => true,
				'_builtin' => false,
			),
			'objects'
		);
		foreach ( $post_types as $pt => $pt_obj ) {
			if ( 'opulentia_hook' === $pt ) {
				continue;
			}
			$condition_options[ 'cpt_' . $pt ] = sprintf(
				/* translators: %s: Custom post type label */
				__( 'CPT: %s', 'opulentia' ),
				$pt_obj->labels->singular_name
			);
		}
		?>
		<div class="opulentia-hook-conditions">
			<p><?php esc_html_e( 'Choose where this hook should appear:', 'opulentia' ); ?></p>
			<?php foreach ( $condition_options as $key => $label ) : ?>
				<label style="display:block;margin-bottom:6px;font-size:13px;">
					<input type="checkbox"
							name="_opulentia_hook_display_on[]"
							value="<?php echo esc_attr( $key ); ?>"
							<?php checked( in_array( $key, $conditions, true ) ); ?>>
					<?php echo esc_html( $label ); ?>
				</label>
			<?php endforeach; ?>
			<p class="description" style="margin-top:8px;">
				<?php esc_html_e( 'Leave all unchecked = disabled. Check "Entire Site" to show everywhere.', 'opulentia' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the layout overrides meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_layout_meta_box( $post ) {
		$enable_header = get_post_meta( $post->ID, '_opulentia_hook_enable_header', true );
		$enable_footer = get_post_meta( $post->ID, '_opulentia_hook_enable_footer', true );
		$enable_title  = get_post_meta( $post->ID, '_opulentia_hook_enable_title', true );
		?>
		<div class="opulentia-hook-layout">
			<p><?php esc_html_e( 'Override visibility of theme elements when this hook is active:', 'opulentia' ); ?></p>
			<label style="display:block;margin-bottom:8px;font-size:13px;">
				<input type="checkbox" name="_opulentia_hook_enable_header" value="1" <?php checked( $enable_header, '1' ); ?>>
				<?php esc_html_e( 'Enable Header', 'opulentia' ); ?>
			</label>
			<label style="display:block;margin-bottom:8px;font-size:13px;">
				<input type="checkbox" name="_opulentia_hook_enable_footer" value="1" <?php checked( $enable_footer, '1' ); ?>>
				<?php esc_html_e( 'Enable Footer', 'opulentia' ); ?>
			</label>
			<label style="display:block;margin-bottom:8px;font-size:13px;">
				<input type="checkbox" name="_opulentia_hook_enable_title" value="1" <?php checked( $enable_title, '1' ); ?>>
				<?php esc_html_e( 'Enable Page Title', 'opulentia' ); ?>
			</label>
			<p class="description" style="margin-top:8px;">
				<?php esc_html_e( 'Uncheck to hide the element. Requires render class support.', 'opulentia' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Save meta box values.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_meta_boxes( $post_id ) {
		if ( ! isset( $_POST['opulentia_advanced_hook_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['opulentia_advanced_hook_nonce'] ) ),
			'opulentia_advanced_hook'
		) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save hook location.
		if ( isset( $_POST['_opulentia_hook_location'] ) ) {
			update_post_meta(
				$post_id,
				'_opulentia_hook_location',
				sanitize_text_field( wp_unslash( $_POST['_opulentia_hook_location'] ) )
			);
		}

		// Save code type.
		if ( isset( $_POST['_opulentia_hook_code_type'] ) ) {
			update_post_meta(
				$post_id,
				'_opulentia_hook_code_type',
				sanitize_text_field( wp_unslash( $_POST['_opulentia_hook_code_type'] ) )
			);
		}

		// Save code.
		if ( isset( $_POST['_opulentia_hook_code'] ) ) {
			update_post_meta(
				$post_id,
				'_opulentia_hook_code',
				wp_unslash( $_POST['_opulentia_hook_code'] )
			);
		}

		// Save priority.
		if ( isset( $_POST['_opulentia_hook_priority'] ) ) {
			update_post_meta(
				$post_id,
				'_opulentia_hook_priority',
				absint( $_POST['_opulentia_hook_priority'] )
			);
		}

		// Save display conditions.
		if ( isset( $_POST['_opulentia_hook_display_on'] ) && is_array( $_POST['_opulentia_hook_display_on'] ) ) {
			$conditions = array_map( 'sanitize_text_field', wp_unslash( $_POST['_opulentia_hook_display_on'] ) );
			update_post_meta( $post_id, '_opulentia_hook_display_on', wp_json_encode( $conditions ) );
		} else {
			update_post_meta( $post_id, '_opulentia_hook_display_on', wp_json_encode( array() ) );
		}

		// Save layout overrides.
		$layout_fields = array(
			'_opulentia_hook_enable_header',
			'_opulentia_hook_enable_footer',
			'_opulentia_hook_enable_title',
		);

		foreach ( $layout_fields as $field ) {
			update_post_meta(
				$post_id,
				$field,
				isset( $_POST[ $field ] ) ? '1' : '0'
			);
		}
	}

	// -------------------------------------------------------------------------
	// Admin Columns
	// -------------------------------------------------------------------------

	/**
	 * Add custom columns to the hooks list table.
	 *
	 * @param array $columns Default columns.
	 * @return array
	 */
	public function admin_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( 'title' === $key ) {
				$new_columns['hook_location']   = __( 'Location', 'opulentia' );
				$new_columns['hook_type']       = __( 'Type', 'opulentia' );
				$new_columns['hook_conditions'] = __( 'Display On', 'opulentia' );
			}
		}

		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 */
	public function admin_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'hook_location':
				$location  = get_post_meta( $post_id, '_opulentia_hook_location', true );
				$locations = self::get_hook_locations();
				echo isset( $locations[ $location ] ) ? esc_html( $locations[ $location ] ) : '<em>' . esc_html( $location ) . '</em>';
				break;

			case 'hook_type':
				$type = get_post_meta( $post_id, '_opulentia_hook_code_type', true ) ?: 'html';
				echo esc_html( strtoupper( $type ) );
				break;

			case 'hook_conditions':
				$conditions = get_post_meta( $post_id, '_opulentia_hook_display_on', true );
				if ( is_string( $conditions ) ) {
					$conditions = json_decode( $conditions, true );
				}
				if ( is_array( $conditions ) && ! empty( $conditions ) ) {
					$labels  = array(
						'entire_site' => __( 'Entire Site', 'opulentia' ),
						'front_page'  => __( 'Front Page', 'opulentia' ),
						'blog_index'  => __( 'Blog', 'opulentia' ),
						'singular'    => __( 'Singular', 'opulentia' ),
						'archive'     => __( 'Archives', 'opulentia' ),
						'single_post' => __( 'Posts', 'opulentia' ),
						'single_page' => __( 'Pages', 'opulentia' ),
						'search'      => __( 'Search', 'opulentia' ),
						'404'         => __( '404', 'opulentia' ),
					);
					$display = array();
					foreach ( $conditions as $cond ) {
						$display[] = isset( $labels[ $cond ] ) ? $labels[ $cond ] : $cond;
					}
					echo esc_html( implode( ', ', $display ) );
				} else {
					echo '<em>' . esc_html__( 'Disabled', 'opulentia' ) . '</em>';
				}
				break;
		}
	}

	// -------------------------------------------------------------------------
	// Admin Styles
	// -------------------------------------------------------------------------

	/**
	 * Enqueue admin styles for the hook editor.
	 *
	 * @param string $hook Current admin page.
	 */
	public function admin_enqueue( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		global $post;
		if ( ! $post || 'opulentia_hook' !== get_post_type( $post ) ) {
			return;
		}

		wp_add_inline_style(
			'common',
			'
            .opulentia-advanced-hook-editor textarea {
                min-height: 200px;
            }
            .opulentia-advanced-hook-editor .description,
            .opulentia-hook-conditions .description,
            .opulentia-hook-layout .description {
                font-style: italic;
                color: #666;
                margin-top: 4px;
            }
            .opulentia-hook-conditions label:hover,
            .opulentia-hook-layout label:hover {
                color: #1a1a1a;
            }
        '
		);
	}
}

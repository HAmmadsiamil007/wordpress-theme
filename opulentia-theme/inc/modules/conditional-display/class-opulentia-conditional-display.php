<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Conditional_Display {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_condition_set_cpt' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_filter( 'opulentia_header_enabled', array( $this, 'filter_header' ) );
		add_filter( 'opulentia_footer_enabled', array( $this, 'filter_footer' ) );
		add_filter( 'opulentia_sidebar_enabled', array( $this, 'filter_sidebar' ) );
		add_filter( 'opulentia_breadcrumbs_enabled', array( $this, 'filter_breadcrumbs' ) );
		add_filter( 'opulentia_scroll_to_top_enabled', array( $this, 'filter_scroll_to_top' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function register_condition_set_cpt() {
		register_post_type(
			'op_condition_set',
			array(
				'labels'          => array(
					'name'          => __( 'Condition Sets', 'opulentia' ),
					'singular_name' => __( 'Condition Set', 'opulentia' ),
					'add_new_item'  => __( 'Add New Condition Set', 'opulentia' ),
					'edit_item'     => __( 'Edit Condition Set', 'opulentia' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => 'themes.php',
				'supports'        => array( 'title' ),
				'capability_type' => 'manage_options',
			)
		);
	}

	public function add_meta_box() {
		$post_types = apply_filters( 'opulentia_conditional_display_post_types', array( 'post', 'page' ) );

		foreach ( $post_types as $pt ) {
			add_meta_box(
				'opulentia_conditional_display',
				__( 'Conditional Display', 'opulentia' ),
				array( $this, 'render_meta_box' ),
				$pt,
				'side',
				'default'
			);
		}
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'opulentia_conditional_display', 'opulentia_conditional_display_nonce' );

		$hide_header      = get_post_meta( $post->ID, '_op_hide_header', true );
		$hide_footer      = get_post_meta( $post->ID, '_op_hide_footer', true );
		$hide_sidebar     = get_post_meta( $post->ID, '_op_hide_sidebar', true );
		$hide_breadcrumbs = get_post_meta( $post->ID, '_op_hide_breadcrumbs', true );
		$hide_scroll_top  = get_post_meta( $post->ID, '_op_hide_scroll_top', true );
		$custom_classes   = get_post_meta( $post->ID, '_op_custom_classes', true );
		$condition_role   = get_post_meta( $post->ID, '_op_condition_role', '' );
		$condition_device = get_post_meta( $post->ID, '_op_condition_device', '' );
		$condition_set_id = get_post_meta( $post->ID, '_op_condition_set', '' );
		?>
		<p style="margin-top:0;font-weight:600;"><?php esc_html_e( 'Element Visibility', 'opulentia' ); ?></p>
		<p>
			<label>
				<input type="checkbox" name="_op_hide_header" value="1" <?php checked( $hide_header, '1' ); ?>>
				<?php esc_html_e( 'Hide Header', 'opulentia' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="_op_hide_footer" value="1" <?php checked( $hide_footer, '1' ); ?>>
				<?php esc_html_e( 'Hide Footer', 'opulentia' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="_op_hide_sidebar" value="1" <?php checked( $hide_sidebar, '1' ); ?>>
				<?php esc_html_e( 'Hide Sidebar', 'opulentia' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="_op_hide_breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, '1' ); ?>>
				<?php esc_html_e( 'Hide Breadcrumbs', 'opulentia' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="_op_hide_scroll_top" value="1" <?php checked( $hide_scroll_top, '1' ); ?>>
				<?php esc_html_e( 'Hide Scroll to Top', 'opulentia' ); ?>
			</label>
		</p>
		<hr>
		<p style="font-weight:600;"><?php esc_html_e( 'Custom CSS Classes', 'opulentia' ); ?></p>
		<p>
			<input type="text" name="_op_custom_classes" value="<?php echo esc_attr( $custom_classes ); ?>" style="width:100%" placeholder="<?php esc_attr_e( 'e.g. dark-theme no-margin', 'opulentia' ); ?>">
		</p>
		<hr>
		<p style="font-weight:600;"><?php esc_html_e( 'Conditions', 'opulentia' ); ?></p>
		<p>
			<label for="_op_condition_role"><?php esc_html_e( 'User Role', 'opulentia' ); ?></label>
			<select name="_op_condition_role" id="_op_condition_role" style="width:100%">
				<option value="" <?php selected( $condition_role, '' ); ?>><?php esc_html_e( 'All Users', 'opulentia' ); ?></option>
				<option value="logged-in" <?php selected( $condition_role, 'logged-in' ); ?>><?php esc_html_e( 'Logged-In', 'opulentia' ); ?></option>
				<option value="logged-out" <?php selected( $condition_role, 'logged-out' ); ?>><?php esc_html_e( 'Logged-Out', 'opulentia' ); ?></option>
				<?php
				foreach ( wp_roles()->get_names() as $role_key => $role_name ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $role_key ),
						selected( $condition_role, $role_key, false ),
						esc_html( $role_name )
					);
				}
				?>
			</select>
		</p>
		<p>
			<label for="_op_condition_device"><?php esc_html_e( 'Device', 'opulentia' ); ?></label>
			<select name="_op_condition_device" id="_op_condition_device" style="width:100%">
				<option value="" <?php selected( $condition_device, '' ); ?>><?php esc_html_e( 'All Devices', 'opulentia' ); ?></option>
				<option value="desktop" <?php selected( $condition_device, 'desktop' ); ?>><?php esc_html_e( 'Desktop', 'opulentia' ); ?></option>
				<option value="mobile" <?php selected( $condition_device, 'mobile' ); ?>><?php esc_html_e( 'Mobile', 'opulentia' ); ?></option>
			</select>
		</p>
		<?php
		$condition_sets = get_posts(
			array(
				'post_type'      => 'op_condition_set',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		if ( $condition_sets ) :
			?>
		<p>
			<label for="_op_condition_set"><?php esc_html_e( 'Condition Set', 'opulentia' ); ?></label>
			<select name="_op_condition_set" id="_op_condition_set" style="width:100%">
				<option value="" <?php selected( $condition_set_id, '' ); ?>><?php esc_html_e( 'None', 'opulentia' ); ?></option>
				<?php foreach ( $condition_sets as $set ) : ?>
					<option value="<?php echo esc_attr( $set->ID ); ?>" <?php selected( $condition_set_id, $set->ID ); ?>>
						<?php echo esc_html( $set->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php endif; ?>
		<p style="font-size:12px;color:#666;">
			<?php esc_html_e( 'Conditions must be met for the visibility settings above to apply.', 'opulentia' ); ?>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=op_condition_set' ) ); ?>"><?php esc_html_e( 'Manage Condition Sets', 'opulentia' ); ?></a>
		</p>
		<?php
	}

	public function save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['opulentia_conditional_display_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['opulentia_conditional_display_nonce'] ) ), 'opulentia_conditional_display' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'_op_hide_header'      => 'sanitize_text_field',
			'_op_hide_footer'      => 'sanitize_text_field',
			'_op_hide_sidebar'     => 'sanitize_text_field',
			'_op_hide_breadcrumbs' => 'sanitize_text_field',
			'_op_hide_scroll_top'  => 'sanitize_text_field',
			'_op_custom_classes'   => 'sanitize_text_field',
			'_op_condition_role'   => 'sanitize_text_field',
			'_op_condition_device' => 'sanitize_text_field',
			'_op_condition_set'    => 'absint',
		);

		foreach ( $fields as $key => $sanitize ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) );
				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	private function conditions_met( $post_id ) {
		if ( ! $post_id ) {
			return false;
		}

		$role   = get_post_meta( $post_id, '_op_condition_role', true );
		$device = get_post_meta( $post_id, '_op_condition_device', true );
		$set_id = get_post_meta( $post_id, '_op_condition_set', true );

		if ( $role ) {
			if ( 'logged-in' === $role && ! is_user_logged_in() ) {
				return false;
			}
			if ( 'logged-out' === $role && is_user_logged_in() ) {
				return false;
			}
			if ( ! in_array( $role, array( 'logged-in', 'logged-out' ), true ) ) {
				$user = wp_get_current_user();
				if ( ! $user || ! in_array( $role, (array) $user->roles, true ) ) {
					return false;
				}
			}
		}

		if ( $device ) {
			$is_mobile = wp_is_mobile();
			if ( 'desktop' === $device && $is_mobile ) {
				return false;
			}
			if ( 'mobile' === $device && ! $is_mobile ) {
				return false;
			}
		}

		if ( $set_id ) {
			$set_rules = get_post_meta( $set_id, '_op_condition_set_rules', true );
			if ( is_array( $set_rules ) ) {
				foreach ( $set_rules as $rule ) {
					if ( ! $this->evaluate_rule( $rule, $post_id ) ) {
						return false;
					}
				}
			}
		}

		return true;
	}

	private function evaluate_rule( $rule, $post_id ) {
		if ( empty( $rule['type'] ) || empty( $rule['operator'] ) ) {
			return true;
		}

		$type     = $rule['type'];
		$operator = $rule['operator'];
		$value    = isset( $rule['value'] ) ? $rule['value'] : '';

		switch ( $type ) {
			case 'page_template':
				$template = get_page_template_slug( $post_id );
				if ( 'equals' === $operator ) {
					return $template === $value;
				}
				return $template !== $value;

			case 'category':
				$cats = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
				if ( 'equals' === $operator ) {
					return in_array( (int) $value, $cats, true );
				}
				return ! in_array( (int) $value, $cats, true );

			case 'tag':
				$tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
				if ( 'equals' === $operator ) {
					return in_array( (int) $value, $tags, true );
				}
				return ! in_array( (int) $value, $tags, true );

			case 'url_param':
				$param_value = isset( $_GET[ $value ] ) ? sanitize_text_field( wp_unslash( $_GET[ $value ] ) ) : '';
				if ( 'equals' === $operator ) {
					return '' !== $param_value;
				}
				return '' === $param_value;
		}

		return true;
	}

	private function should_hide( $element, $post_id = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return false;
		}

		$meta_key = '_op_hide_' . $element;

		if ( ! $this->conditions_met( $post_id ) ) {
			return false;
		}

		return (bool) get_post_meta( $post_id, $meta_key, true );
	}

	public function filter_header( $enabled ) {
		if ( $this->should_hide( 'header' ) ) {
			return false;
		}
		return $enabled;
	}

	public function filter_footer( $enabled ) {
		if ( $this->should_hide( 'footer' ) ) {
			return false;
		}
		return $enabled;
	}

	public function filter_sidebar( $enabled ) {
		if ( $this->should_hide( 'sidebar' ) ) {
			return false;
		}
		return $enabled;
	}

	public function filter_breadcrumbs( $enabled ) {
		if ( $this->should_hide( 'breadcrumbs' ) ) {
			return false;
		}
		return $enabled;
	}

	public function filter_scroll_to_top( $enabled ) {
		if ( $this->should_hide( 'scroll_top' ) ) {
			return false;
		}
		return $enabled;
	}

	public function body_class( $classes ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $classes;
		}

		$custom = get_post_meta( $post_id, '_op_custom_classes', true );
		if ( $custom ) {
			if ( $this->conditions_met( $post_id ) ) {
				$custom_classes = explode( ' ', $custom );
				foreach ( $custom_classes as $c ) {
					$c = trim( $c );
					if ( $c ) {
						$classes[] = $c;
					}
				}
			}
		}

		if ( $this->should_hide( 'header' ) ) {
			$classes[] = 'op-header-hidden';
		}
		if ( $this->should_hide( 'footer' ) ) {
			$classes[] = 'op-footer-hidden';
		}
		if ( $this->should_hide( 'sidebar' ) ) {
			$classes[] = 'op-sidebar-hidden';
		}
		if ( $this->should_hide( 'breadcrumbs' ) ) {
			$classes[] = 'op-breadcrumbs-hidden';
		}
		if ( $this->should_hide( 'scroll_top' ) ) {
			$classes[] = 'op-scroll-top-hidden';
		}

		return $classes;
	}
}

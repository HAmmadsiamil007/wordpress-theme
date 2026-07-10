<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Mega_Menu {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'register_widget_areas' ) );
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'menu_item_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'save_menu_item_fields' ), 10, 3 );
		add_filter( 'wp_nav_menu_args', array( $this, 'nav_menu_args' ) );
		add_filter( 'nav_menu_css_class', array( $this, 'menu_item_classes' ), 10, 4 );
		add_filter( 'nav_menu_link_attributes', array( $this, 'menu_link_attributes' ), 10, 4 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'start_el' ), 10, 4 );
		add_filter( 'wp_nav_menu_objects', array( $this, 'filter_menu_objects' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 100 );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
	}

	private function is_enabled() {
		return (bool) Opulentia_get_option( 'enable-mega-menu', false );
	}

	public function register_widget_areas() {
		for ( $i = 1; $i <= 6; $i++ ) {
			register_sidebar(
				array(
					'name'          => sprintf( __( 'Mega Menu Column %d', 'opulentia' ), $i ),
					'id'            => 'mega-menu-col-' . $i,
					'before_widget' => '<div id="%1$s" class="op-mega-widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h4 class="op-mega-widget__title">',
					'after_title'   => '</h4>',
				)
			);
		}
	}

	public function menu_item_fields( $item_id, $item, $depth, $args ) {
		$columns      = get_post_meta( $item_id, '_Opulentia_mega_menu_columns', true );
		$icon         = get_post_meta( $item_id, '_Opulentia_menu_icon', true );
		$icon_library = get_post_meta( $item_id, '_Opulentia_menu_icon_library', true );
		$badge        = get_post_meta( $item_id, '_Opulentia_menu_badge', true );
		$bg_image     = get_post_meta( $item_id, '_Opulentia_mega_bg_image', true );
		$tabbed       = get_post_meta( $item_id, '_Opulentia_mega_tabbed', true );
		$widget_area  = get_post_meta( $item_id, '_Opulentia_mega_widget_area', true );
		$hide_label   = get_post_meta( $item_id, '_Opulentia_mega_hide_label', true );
		$content      = get_post_meta( $item_id, '_Opulentia_mega_content', true );
		?>
		<div class="opulentia-mega-menu-fields" style="clear:both;padding:8px 0;border-top:1px solid #eee;margin-top:6px">
			<p style="margin:4px 0;font-weight:600;color:#666"><?php esc_html_e( 'Opulentia Mega Menu', 'opulentia' ); ?></p>

			<?php if ( 0 === $depth ) : ?>
			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Mega Menu Columns:', 'opulentia' ); ?>
					<select name="Opulentia_mega_menu_columns[<?php echo $item_id; ?>]" style="width:80px">
						<option value="0">—</option>
						<?php foreach ( array( 2, 3, 4, 5, 6 ) as $c ) : ?>
							<option value="<?php echo $c; ?>" <?php selected( $columns, $c ); ?>><?php echo $c; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>

			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Tabbed Layout:', 'opulentia' ); ?>
					<input type="checkbox" name="Opulentia_mega_tabbed[<?php echo $item_id; ?>]" value="1" <?php checked( $tabbed, '1' ); ?>>
				</label>
			</p>

			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Background Image:', 'opulentia' ); ?>
					<input type="text" name="Opulentia_mega_bg_image[<?php echo $item_id; ?>]" value="<?php echo esc_url( $bg_image ); ?>" style="width:200px" placeholder="https://...">
					<button type="button" class="button op-upload-mega-bg" data-target="<?php echo $item_id; ?>"><?php esc_html_e( 'Upload', 'opulentia' ); ?></button>
				</label>
			</p>

			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Widget Area:', 'opulentia' ); ?>
					<select name="Opulentia_mega_widget_area[<?php echo $item_id; ?>]">
						<option value="">—</option>
						<?php for ( $i = 1; $i <= 6; $i++ ) : ?>
							<option value="mega-menu-col-<?php echo $i; ?>" <?php selected( $widget_area, 'mega-menu-col-' . $i ); ?>><?php printf( __( 'Column %d', 'opulentia' ), $i ); ?></option>
						<?php endfor; ?>
					</select>
				</label>
			</p>

			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Custom Content:', 'opulentia' ); ?><br>
					<textarea name="Opulentia_mega_content[<?php echo $item_id; ?>]" rows="3" style="width:100%;max-width:400px"><?php echo esc_textarea( $content ); ?></textarea>
				</label>
			</p>
			<?php endif; ?>

			<?php if ( $depth >= 1 ) : ?>
			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Hide Column Label:', 'opulentia' ); ?>
					<input type="checkbox" name="Opulentia_mega_hide_label[<?php echo $item_id; ?>]" value="1" <?php checked( $hide_label, '1' ); ?>>
				</label>
			</p>
			<?php endif; ?>

			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Icon Class:', 'opulentia' ); ?>
					<input type="text" name="Opulentia_menu_icon[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $icon ); ?>" style="width:160px" placeholder="dashicons-admin-home">
				</label>
				<label style="margin-left:10px"><?php esc_html_e( 'Library:', 'opulentia' ); ?>
					<select name="Opulentia_menu_icon_library[<?php echo $item_id; ?>]">
						<option value="dashicons" <?php selected( $icon_library, 'dashicons' ); ?>>Dashicons</option>
						<option value="opulentia" <?php selected( $icon_library, 'opulentia' ); ?>>Opulentia SVG</option>
					</select>
				</label>
			</p>

			<p style="margin:4px 0">
				<label style="display:inline-block;min-width:140px"><?php esc_html_e( 'Badge Text:', 'opulentia' ); ?>
					<input type="text" name="Opulentia_menu_badge[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $badge ); ?>" style="width:120px" placeholder="New">
				</label>
			</p>
		</div>
		<?php
	}

	public function save_menu_item_fields( $menu_id, $menu_item_db_id, $args ) {
		$fields = array(
			'Opulentia_mega_menu_columns',
			'Opulentia_menu_icon',
			'Opulentia_menu_icon_library',
			'Opulentia_menu_badge',
			'Opulentia_mega_bg_image',
			'Opulentia_mega_tabbed',
			'Opulentia_mega_widget_area',
			'Opulentia_mega_hide_label',
			'Opulentia_mega_content',
		);

		foreach ( $fields as $field ) {
			$key = str_replace( 'Opulentia_', '_Opulentia_', $field );
			if ( isset( $_POST[ $field ][ $menu_item_db_id ] ) ) {
				update_post_meta( $menu_item_db_id, $key, sanitize_text_field( $_POST[ $field ][ $menu_item_db_id ] ) );
			} else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	public function filter_menu_objects( $items, $args ) {
		if ( ! $this->is_enabled() || 'primary' !== $args->theme_location ) {
			return $items;
		}

		$parents = array();
		foreach ( $items as $item ) {
			if ( 0 === $item->menu_item_parent ) {
				$cols = get_post_meta( $item->ID, '_Opulentia_mega_menu_columns', true );
				if ( $cols > 0 ) {
					$parents[ $item->ID ] = (int) $cols;
				}
			}
		}

		if ( empty( $parents ) ) {
			return $items;
		}

		foreach ( $items as $item ) {
			if ( $item->menu_item_parent && isset( $parents[ $item->menu_item_parent ] ) ) {
				$tabbed = get_post_meta( $item->menu_item_parent, '_Opulentia_mega_tabbed', true );
				if ( $tabbed ) {
					$item->classes[] = 'op-mega-tab-item';
				}
			}
		}

		return $items;
	}

	public function nav_menu_args( $args ) {
		return $args;
	}

	public function menu_item_classes( $classes, $item, $args, $depth ) {
		if ( ! $this->is_enabled() || 'primary' !== $args->theme_location ) {
			return $classes;
		}

		if ( 0 === $depth && in_array( 'menu-item-has-children', $classes, true ) ) {
			$columns = (int) get_post_meta( $item->ID, '_Opulentia_mega_menu_columns', true );
			if ( $columns > 0 ) {
				$classes[] = 'opulentia-mega-menu-parent';
				$classes[] = 'opulentia-mega-col-' . $columns;
				$tabbed    = get_post_meta( $item->ID, '_Opulentia_mega_tabbed', true );
				if ( $tabbed ) {
					$classes[] = 'opulentia-mega-tabbed';
				}
			}
		}

		if ( 1 === $depth ) {
			$parent_id = $item->menu_item_parent;
			$columns   = (int) get_post_meta( $parent_id, '_Opulentia_mega_menu_columns', true );
			if ( $columns > 0 ) {
				$classes[] = 'opulentia-mega-menu-column';
				$hide      = get_post_meta( $item->ID, '_Opulentia_mega_hide_label', true );
				if ( $hide ) {
					$classes[] = 'op-mega-col-hidden';
				}
			}
		}

		$badge = get_post_meta( $item->ID, '_Opulentia_menu_badge', true );
		if ( $badge ) {
			$classes[] = 'menu-item-has-badge';
		}

		return $classes;
	}

	public function menu_link_attributes( $atts, $item, $args, $depth ) {
		return $atts;
	}

	public function start_el( $item_output, $item, $depth, $args ) {
		if ( 'primary' !== $args->theme_location ) {
			return $item_output;
		}

		if ( 0 === $depth ) {
			$columns = (int) get_post_meta( $item->ID, '_Opulentia_mega_menu_columns', true );
			if ( $columns > 0 ) {
				$bg_image    = get_post_meta( $item->ID, '_Opulentia_mega_bg_image', true );
				$widget_area = get_post_meta( $item->ID, '_Opulentia_mega_widget_area', true );
				$content     = get_post_meta( $item->ID, '_Opulentia_mega_content', true );
				$tabbed      = get_post_meta( $item->ID, '_Opulentia_mega_tabbed', true );

				$style        = $bg_image ? ' style="background:url(' . esc_url( $bg_image ) . ') center/cover no-repeat;background-blend-mode:overlay;background-color:rgba(0,0,0,0.85)"' : '';
				$item_output .= '<div class="op-mega-dropdown"' . $style . '>';

				if ( $tabbed ) {
					$item_output .= '<div class="op-mega-tabs">';
					$item_output .= '<div class="op-mega-tabs__nav"></div>';
					$item_output .= '<div class="op-mega-tabs__panels">';
				}

				$item_output .= '<ul class="op-mega-grid op-mega-grid--' . $columns . 'col">';
			}
		}

		$icon         = get_post_meta( $item->ID, '_Opulentia_menu_icon', true );
		$icon_library = get_post_meta( $item->ID, '_Opulentia_menu_icon_library', true );

		if ( $icon ) {
			if ( 'opulentia' === $icon_library ) {
				$icon_html = '<span class="menu-item-icon op-mega-icon-svg">' . $this->get_svg_icon( $icon ) . '</span> ';
			} else {
				$icon_html = '<span class="menu-item-icon dashicons dashicons-' . esc_attr( $icon ) . '"></span> ';
			}
			$item_output = preg_replace( '/(<a[^>]*>)/', '$1' . $icon_html, $item_output );
		}

		$badge = get_post_meta( $item->ID, '_Opulentia_menu_badge', true );
		if ( $badge ) {
			$badge_html  = '<span class="menu-item-badge">' . esc_html( $badge ) . '</span>';
			$item_output = str_replace( '</a>', $badge_html . '</a>', $item_output );
		}

		if ( 0 === $depth ) {
			$widget_area = get_post_meta( $item->ID, '_Opulentia_mega_widget_area', true );
			if ( $widget_area && is_active_sidebar( $widget_area ) ) {
				ob_start();
				dynamic_sidebar( $widget_area );
				$item_output .= ob_get_clean();
			}

			$content = get_post_meta( $item->ID, '_Opulentia_mega_content', true );
			if ( $content ) {
				$item_output .= '<div class="op-mega-custom-content">' . do_shortcode( $content ) . '</div>';
			}
		}

		return $item_output;
	}

	private function get_svg_icon( $icon_name ) {
		if ( class_exists( 'Opulentia_Icons' ) && function_exists( 'get_icon' ) ) {
			return get_icon( $icon_name, 16, '#c9a96e' );
		}
		return '';
	}

	public function enqueue_assets() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		wp_add_inline_script(
			'Opulentia-navigation',
			'
document.addEventListener("click",function(e){var t=e.target.closest(".opulentia-mega-menu-parent");document.querySelectorAll(".opulentia-mega-menu-parent.active-mega").forEach(function(m){m!==t&&m.classList.remove("active-mega")});if(t){t.classList.toggle("active-mega")}});
        '
		);

		if ( is_admin() ) {
			wp_add_inline_script(
				'jquery',
				'
jQuery(document).ready(function($){$(".op-upload-mega-bg").on("click",function(){var t=$(this).data("target"),frame=wp.media({title:"Select Background Image",multiple:false});frame.on("select",function(){var url=frame.state().get("selection").first().toJSON().url;$("input[name=\"Opulentia_mega_bg_image["+t+"]\"]").val(url)});frame.open()})});
            '
			);
		}
	}

	public function dynamic_css( $css ) {
		if ( ! $this->is_enabled() ) {
			return $css;
		}

		$animation = Opulentia_get_option( 'mega-menu-animation', 'fade' );

		$css .= '
.opulentia-mega-menu-parent{position:static}
.opulentia-mega-menu-parent>.sub-menu{display:none}
.opulentia-mega-menu-parent.opulentia-mega-col-2>.sub-menu>.op-mega-dropdown>.op-mega-grid{grid-template-columns:repeat(2,1fr)}
.opulentia-mega-menu-parent.opulentia-mega-col-3>.sub-menu>.op-mega-dropdown>.op-mega-grid{grid-template-columns:repeat(3,1fr)}
.opulentia-mega-menu-parent.opulentia-mega-col-4>.sub-menu>.op-mega-dropdown>.op-mega-grid{grid-template-columns:repeat(4,1fr)}
.opulentia-mega-menu-parent.opulentia-mega-col-5>.sub-menu>.op-mega-dropdown>.op-mega-grid{grid-template-columns:repeat(5,1fr)}
.opulentia-mega-menu-parent.opulentia-mega-col-6>.sub-menu>.op-mega-dropdown>.op-mega-grid{grid-template-columns:repeat(6,1fr)}
.op-mega-dropdown{position:absolute;left:0;right:0;top:100%;padding:30px;min-width:600px;background:var(--color-secondary-dark);border:1px solid var(--color-border);border-top:3px solid var(--color-gold);box-shadow:0 20px 40px rgba(0,0,0,0.3);z-index:9999}
.op-mega-grid{display:grid;gap:20px;list-style:none;margin:0;padding:0}
.op-mega-grid>.opulentia-mega-menu-column{break-inside:avoid;padding:0;border:none}
.op-mega-grid>.opulentia-mega-menu-column>a{font-weight:600;color:var(--color-gold);text-transform:uppercase;font-size:0.8125rem;letter-spacing:1px;margin-bottom:8px;display:block;pointer-events:none}
.op-mega-grid>.opulentia-mega-menu-column>.sub-menu{position:static;background:none;box-shadow:none;border:none;padding:0;margin:0;opacity:1;visibility:visible;transform:none}
.op-mega-grid>.opulentia-mega-menu-column>.sub-menu li{padding:4px 0}
.op-mega-grid>.opulentia-mega-menu-column>.sub-menu a{font-size:0.875rem;font-weight:400;color:var(--color-text-muted);text-transform:none;letter-spacing:0}
.op-mega-grid>.opulentia-mega-menu-column>.sub-menu a:hover{color:var(--color-gold)}
.op-mega-col-hidden>a{display:none}
.menu-item-badge{display:inline-block;background:var(--color-gold);color:#fff;font-size:0.625rem;font-weight:600;padding:2px 6px;border-radius:3px;margin-left:6px;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4}
.menu-item-icon{margin-right:6px;font-size:16px;width:16px;height:16px;vertical-align:middle;display:inline-block}
.op-mega-icon-svg{display:inline-flex;align-items:center}
.op-mega-custom-content{padding:10px 0}
.op-mega-widget{margin-bottom:12px}
.op-mega-widget__title{font-size:0.8125rem;color:var(--color-gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px}
';

		if ( 'slide' === $animation ) {
			$css .= '
.op-mega-dropdown{transform:translateY(10px);transition:transform 0.3s ease,opacity 0.3s ease;opacity:0;visibility:hidden;pointer-events:none}
.opulentia-mega-menu-parent:hover>.sub-menu>.op-mega-dropdown,.opulentia-mega-menu-parent.active-mega>.sub-menu>.op-mega-dropdown{transform:translateY(0);opacity:1;visibility:visible;pointer-events:all}
';
		} elseif ( 'grow' === $animation ) {
			$css .= '
.op-mega-dropdown{transform:scale(0.95);transform-origin:top center;transition:transform 0.3s ease,opacity 0.3s ease;opacity:0;visibility:hidden;pointer-events:none}
.opulentia-mega-menu-parent:hover>.sub-menu>.op-mega-dropdown,.opulentia-mega-menu-parent.active-mega>.sub-menu>.op-mega-dropdown{transform:scale(1);opacity:1;visibility:visible;pointer-events:all}
';
		} else {
			$css .= '
.op-mega-dropdown{transition:opacity 0.3s ease;opacity:0;visibility:hidden;pointer-events:none}
.opulentia-mega-menu-parent:hover>.sub-menu>.op-mega-dropdown,.opulentia-mega-menu-parent.active-mega>.sub-menu>.op-mega-dropdown{opacity:1;visibility:visible;pointer-events:all}
';
		}

		$css .= '
@media(max-width:992px){.op-mega-dropdown{position:static;min-width:auto;border:none;box-shadow:none;padding:15px}.op-mega-grid{grid-template-columns:1fr!important}.opulentia-mega-menu-parent>.sub-menu{display:none}.opulentia-mega-menu-parent.active-mega>.sub-menu{display:block}.op-mega-grid>.opulentia-mega-menu-column>a{pointer-events:auto}}
';

		return $css;
	}
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Custom_Fonts_Uploader {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_font_cpt' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_filter( 'opulentia_font_families', array( $this, 'add_to_font_selector' ) );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
	}

	public function register_font_cpt() {
		register_post_type(
			'op_custom_font',
			array(
				'labels'       => array(
					'name'          => __( 'Custom Fonts', 'opulentia' ),
					'singular_name' => __( 'Custom Font', 'opulentia' ),
					'add_new_item'  => __( 'Add New Font', 'opulentia' ),
					'edit_item'     => __( 'Edit Font', 'opulentia' ),
					'not_found'     => __( 'No fonts found.', 'opulentia' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => 'themes.php',
				'supports'     => array( 'title' ),
			)
		);
	}

	public function add_meta_box() {
		add_meta_box(
			'op_custom_font_files',
			__( 'Font Files', 'opulentia' ),
			array( $this, 'render_meta_box' ),
			'op_custom_font',
			'normal',
			'default'
		);
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'op_custom_font_files', 'op_custom_font_files_nonce' );

		$variants = (array) get_post_meta( $post->ID, '_op_font_variants', true );
		if ( empty( $variants ) ) {
			$variants = array(
				array(
					'weight' => '400',
					'style'  => 'normal',
					'woff2'  => '',
					'woff'   => '',
					'ttf'    => '',
					'otf'    => '',
				),
			);
		}

		$font_name = get_the_title( $post->ID );
		?>
		<p><?php esc_html_e( 'Upload font files for each weight/style combination. At minimum, provide a WOFF2 file.', 'opulentia' ); ?></p>
		<table class="wp-list-table widefat striped" style="margin-top:10px">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Weight', 'opulentia' ); ?></th>
					<th><?php esc_html_e( 'Style', 'opulentia' ); ?></th>
					<th><?php esc_html_e( 'WOFF2', 'opulentia' ); ?></th>
					<th><?php esc_html_e( 'WOFF', 'opulentia' ); ?></th>
					<th><?php esc_html_e( 'TTF', 'opulentia' ); ?></th>
					<th><?php esc_html_e( 'OTF', 'opulentia' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'opulentia' ); ?></th>
				</tr>
			</thead>
			<tbody id="op-font-variants">
				<?php foreach ( $variants as $i => $v ) : ?>
				<tr>
					<td>
						<select name="_op_font_variants[<?php echo $i; ?>][weight]" style="width:100%">
							<?php foreach ( array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ) as $w ) : ?>
								<option value="<?php echo $w; ?>" <?php selected( $v['weight'], $w ); ?>><?php echo $w; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<select name="_op_font_variants[<?php echo $i; ?>][style]" style="width:100%">
							<option value="normal" <?php selected( $v['style'], 'normal' ); ?>><?php esc_html_e( 'Normal', 'opulentia' ); ?></option>
							<option value="italic" <?php selected( $v['style'], 'italic' ); ?>><?php esc_html_e( 'Italic', 'opulentia' ); ?></option>
						</select>
					</td>
					<td><input type="url" name="_op_font_variants[<?php echo $i; ?>][woff2]" value="<?php echo esc_url( $v['woff2'] ); ?>" style="width:100%" placeholder="url"></td>
					<td><input type="url" name="_op_font_variants[<?php echo $i; ?>][woff]" value="<?php echo esc_url( $v['woff'] ); ?>" style="width:100%" placeholder="url"></td>
					<td><input type="url" name="_op_font_variants[<?php echo $i; ?>][ttf]" value="<?php echo esc_url( $v['ttf'] ); ?>" style="width:100%" placeholder="url"></td>
					<td><input type="url" name="_op_font_variants[<?php echo $i; ?>][otf]" value="<?php echo esc_url( $v['otf'] ); ?>" style="width:100%" placeholder="url"></td>
					<td><button type="button" class="button op-font-remove-row" <?php echo 1 === count( $variants ) ? 'disabled' : ''; ?>><?php esc_html_e( 'Remove', 'opulentia' ); ?></button></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p><button type="button" class="button" id="op-font-add-row"><?php esc_html_e( 'Add Variant', 'opulentia' ); ?></button></p>
		<div style="margin-top:15px;padding:15px;background:#f0f0f1;border-radius:4px">
			<p><strong><?php esc_html_e( 'Preview', 'opulentia' ); ?></strong></p>
			<p style="font-size:24px;font-family:<?php echo esc_attr( $font_name ?: 'inherit' ); ?>, serif"><?php esc_html_e( 'The quick brown fox jumps over the lazy dog. 0123456789', 'opulentia' ); ?></p>
			<p style="font-size:18px;font-family:<?php echo esc_attr( $font_name ?: 'inherit' ); ?>, serif"><?php esc_html_e( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz', 'opulentia' ); ?></p>
		</div>
		<script>
		(function($){
			var idx = $('#op-font-variants tr').length;
			$('#op-font-add-row').on('click', function(){
				var html = '<tr>' +
					'<td><select name="_op_font_variants[' + idx + '][weight]" style="width:100%">
					<?php
					foreach ( array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ) as $w ) :
						?>
						<option value="<?php echo $w; ?>"><?php echo $w; ?></option><?php endforeach; ?></select></td>' +
					'<td><select name="_op_font_variants[' + idx + '][style]" style="width:100%"><option value="normal"><?php esc_html_e( 'Normal', 'opulentia' ); ?></option><option value="italic"><?php esc_html_e( 'Italic', 'opulentia' ); ?></option></select></td>' +
					'<td><input type="url" name="_op_font_variants[' + idx + '][woff2]" style="width:100%" placeholder="url"></td>' +
					'<td><input type="url" name="_op_font_variants[' + idx + '][woff]" style="width:100%" placeholder="url"></td>' +
					'<td><input type="url" name="_op_font_variants[' + idx + '][ttf]" style="width:100%" placeholder="url"></td>' +
					'<td><input type="url" name="_op_font_variants[' + idx + '][otf]" style="width:100%" placeholder="url"></td>' +
					'<td><button type="button" class="button op-font-remove-row"><?php esc_html_e( 'Remove', 'opulentia' ); ?></button></td>' +
					'</tr>';
				$('#op-font-variants').append(html);
				idx++;
			});
			$(document).on('click', '.op-font-remove-row', function(){
				$(this).closest('tr').remove();
			});
		})(jQuery);
		</script>
		<?php
	}

	public function save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return; }
		if ( ! isset( $_POST['op_custom_font_files_nonce'] ) ) {
			return; }
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['op_custom_font_files_nonce'] ) ), 'op_custom_font_files' ) ) {
			return; }
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return; }

		if ( ! isset( $_POST['_op_font_variants'] ) || ! is_array( $_POST['_op_font_variants'] ) ) {
			delete_post_meta( $post_id, '_op_font_variants' );
			return;
		}

		$variants = array();
		foreach ( wp_unslash( $_POST['_op_font_variants'] ) as $v ) {
			$variants[] = array(
				'weight' => sanitize_text_field( $v['weight'] ),
				'style'  => sanitize_text_field( $v['style'] ),
				'woff2'  => esc_url_raw( $v['woff2'] ),
				'woff'   => esc_url_raw( $v['woff'] ),
				'ttf'    => esc_url_raw( $v['ttf'] ),
				'otf'    => esc_url_raw( $v['otf'] ),
			);
		}

		update_post_meta( $post_id, '_op_font_variants', $variants );
		update_post_meta( $post_id, '_op_font_preload', isset( $_POST['_op_font_preload'] ) ? '1' : '' );
	}

	public function admin_enqueue( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return; }
		if ( 'op_custom_font' !== get_post_type() ) {
			return; }
	}

	public function enqueue_fonts() {
		$fonts = get_posts(
			array(
				'post_type'      => 'op_custom_font',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		$css = '';

		foreach ( $fonts as $font ) {
			$variants  = (array) get_post_meta( $font->ID, '_op_font_variants', true );
			$font_name = get_the_title( $font->ID );

			foreach ( $variants as $v ) {
				$src = array();
				if ( ! empty( $v['woff2'] ) ) {
					$src[] = 'url(' . esc_url( $v['woff2'] ) . ") format('woff2')";
				}
				if ( ! empty( $v['woff'] ) ) {
					$src[] = 'url(' . esc_url( $v['woff'] ) . ") format('woff')";
				}
				if ( ! empty( $v['ttf'] ) ) {
					$src[] = 'url(' . esc_url( $v['ttf'] ) . ") format('truetype')";
				}
				if ( ! empty( $v['otf'] ) ) {
					$src[] = 'url(' . esc_url( $v['otf'] ) . ") format('opentype')";
				}

				if ( empty( $src ) ) {
					continue; }

				$css .= '@font-face{';
				$css .= 'font-family:"' . esc_attr( $font_name ) . '";';
				$css .= 'font-weight:' . esc_attr( $v['weight'] ) . ';';
				$css .= 'font-style:' . esc_attr( $v['style'] ) . ';';
				$css .= 'src:' . implode( ',', $src ) . ';';
				$css .= 'font-display:swap;';
				$css .= '}';
			}
		}

		if ( $css ) {
			wp_add_inline_style( 'Opulentia-theme', $css );
		}

		$this->output_preload_hints( $fonts );
	}

	private function output_preload_hints( $fonts ) {
		foreach ( $fonts as $font ) {
			if ( ! get_post_meta( $font->ID, '_op_font_preload', true ) ) {
				continue; }
			$variants = (array) get_post_meta( $font->ID, '_op_font_variants', true );
			foreach ( $variants as $v ) {
				$url = ! empty( $v['woff2'] ) ? $v['woff2'] : ( ! empty( $v['woff'] ) ? $v['woff'] : '' );
				if ( $url ) {
					echo '<link rel="preload" href="' . esc_url( $url ) . '" as="font" type="font/woff2" crossorigin>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}

	public function add_to_font_selector( $families ) {
		$fonts = get_posts(
			array(
				'post_type'      => 'op_custom_font',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		foreach ( $fonts as $font ) {
			$families[ get_the_title( $font->ID ) ] = get_the_title( $font->ID );
		}

		return $families;
	}

	public function dynamic_css( $css ) {
		$fonts = get_posts(
			array(
				'post_type'      => 'op_custom_font',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		foreach ( $fonts as $font ) {
			$variants  = (array) get_post_meta( $font->ID, '_op_font_variants', true );
			$font_name = get_the_title( $font->ID );

			foreach ( $variants as $v ) {
				$src = array();
				if ( ! empty( $v['woff2'] ) ) {
					$src[] = 'url(' . esc_url( $v['woff2'] ) . ") format('woff2')";
				}
				if ( ! empty( $v['woff'] ) ) {
					$src[] = 'url(' . esc_url( $v['woff'] ) . ") format('woff')";
				}
				if ( ! empty( $v['ttf'] ) ) {
					$src[] = 'url(' . esc_url( $v['ttf'] ) . ") format('truetype')";
				}
				if ( ! empty( $v['otf'] ) ) {
					$src[] = 'url(' . esc_url( $v['otf'] ) . ") format('opentype')";
				}

				if ( empty( $src ) ) {
					continue; }

				$css .= '@font-face{';
				$css .= 'font-family:"' . esc_attr( $font_name ) . '";';
				$css .= 'font-weight:' . esc_attr( $v['weight'] ) . ';';
				$css .= 'font-style:' . esc_attr( $v['style'] ) . ';';
				$css .= 'src:' . implode( ',', $src ) . ';';
				$css .= 'font-display:swap;';
				$css .= '}';
			}
		}

		return $css;
	}
}

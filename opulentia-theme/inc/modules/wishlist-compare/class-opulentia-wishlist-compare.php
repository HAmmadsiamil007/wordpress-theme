<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Wishlist_Compare {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_action( 'wp_ajax_op_wishlist_toggle', array( $this, 'ajax_wishlist_toggle' ) );
		add_action( 'wp_ajax_nopriv_op_wishlist_toggle', array( $this, 'ajax_wishlist_toggle' ) );
		add_action( 'wp_ajax_op_compare_toggle', array( $this, 'ajax_compare_toggle' ) );
		add_action( 'wp_ajax_nopriv_op_compare_toggle', array( $this, 'ajax_compare_toggle' ) );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'wishlist_button' ) );
		add_action( 'woocommerce_product_meta_end', array( $this, 'compare_button' ) );
		add_shortcode( 'op_wishlist', array( $this, 'shortcode_wishlist' ) );
		add_shortcode( 'op_compare', array( $this, 'shortcode_compare' ) );
	}

	private function get_wishlist() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return array();
		}
		if ( WC()->session ) {
			$wishlist = WC()->session->get( 'op_wishlist' );
			if ( is_array( $wishlist ) ) {
				return array_map( 'absint', $wishlist );
			}
		}
		if ( isset( $_COOKIE['op_wishlist'] ) ) {
			$ids = json_decode( stripslashes( $_COOKIE['op_wishlist'] ), true );
			return is_array( $ids ) ? array_map( 'absint', $ids ) : array();
		}
		return array();
	}

	private function set_wishlist( $ids ) {
		$ids = array_map( 'absint', $ids );
		if ( WC()->session ) {
			WC()->session->set( 'op_wishlist', $ids );
		}
		setcookie( 'op_wishlist', json_encode( $ids ), time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}

	private function get_compare() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return array();
		}
		if ( WC()->session ) {
			$compare = WC()->session->get( 'op_compare' );
			if ( is_array( $compare ) ) {
				return array_map( 'absint', $compare );
			}
		}
		return array();
	}

	private function set_compare( $ids ) {
		$ids = array_map( 'absint', $ids );
		if ( WC()->session ) {
			WC()->session->set( 'op_compare', $ids );
		}
	}

	public function ajax_wishlist_toggle() {
		check_ajax_referer( 'op_wishlist_nonce', 'nonce' );
		$product_id = absint( $_POST['product_id'] );
		if ( ! $product_id ) {
			wp_send_json_error();
		}
		$wishlist = $this->get_wishlist();
		$active   = in_array( $product_id, $wishlist, true );
		if ( $active ) {
			$wishlist = array_values( array_diff( $wishlist, array( $product_id ) ) );
		} else {
			$wishlist[] = $product_id;
		}
		$this->set_wishlist( $wishlist );
		wp_send_json_success(
			array(
				'active' => ! $active,
				'count'  => count( $wishlist ),
			)
		);
	}

	public function ajax_compare_toggle() {
		check_ajax_referer( 'op_compare_nonce', 'nonce' );
		$product_id = absint( $_POST['product_id'] );
		if ( ! $product_id ) {
			wp_send_json_error();
		}
		$compare = $this->get_compare();
		$max     = absint( Opulentia_get_option( 'compare-max-products', 4 ) );
		$active  = in_array( $product_id, $compare, true );
		if ( $active ) {
			$compare = array_values( array_diff( $compare, array( $product_id ) ) );
		} else {
			if ( count( $compare ) >= $max ) {
				wp_send_json_error( array( 'message' => __( 'Maximum comparison limit reached.', 'opulentia' ) ) );
			}
			$compare[] = $product_id;
		}
		$this->set_compare( $compare );
		wp_send_json_success(
			array(
				'active' => ! $active,
				'count'  => count( $compare ),
			)
		);
	}

	public function wishlist_button() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'wishlist-enable', true ) ) {
			return;
		}
		$position = Opulentia_get_option( 'wishlist-button-position', 'after_add_to_cart' );
		if ( 'after_product_meta' === $position ) {
			return;
		}
		$this->render_wishlist_button();
	}

	public function compare_button() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'compare-enable', true ) ) {
			return;
		}
		$this->render_compare_button();
	}

	private function render_wishlist_button() {
		$product_id = get_the_ID();
		$wishlist   = $this->get_wishlist();
		$active     = in_array( $product_id, $wishlist, true );
		$icon_style = Opulentia_get_option( 'wishlist-icon-style', 'heart' );
		?>
		<button class="op-wishlist-btn op-wishlist-btn--<?php echo $active ? 'active' : 'inactive'; ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'op_wishlist_nonce' ) ); ?>" aria-label="<?php esc_attr_e( 'Toggle Wishlist', 'opulentia' ); ?>">
			<?php $this->svg_icon( $icon_style, $active ); ?>
			<span class="op-wishlist-btn__label"><?php echo $active ? esc_html__( 'Remove from Wishlist', 'opulentia' ) : esc_html__( 'Add to Wishlist', 'opulentia' ); ?></span>
		</button>
		<?php
	}

	private function render_compare_button() {
		$product_id = get_the_ID();
		$compare    = $this->get_compare();
		$active     = in_array( $product_id, $compare, true );
		?>
		<button class="op-compare-btn op-compare-btn--<?php echo $active ? 'active' : 'inactive'; ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'op_compare_nonce' ) ); ?>" aria-label="<?php esc_attr_e( 'Toggle Compare', 'opulentia' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M16 3h5v5M8 3H3v5"/><path d="M21 8v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8"/><path d="M7 12l2-2 2 2"/><path d="M17 12l-2-2-2 2"/><path d="M12 10v7"/></svg>
			<span class="op-compare-btn__label"><?php echo $active ? esc_html__( 'Remove from Compare', 'opulentia' ) : esc_html__( 'Add to Compare', 'opulentia' ); ?></span>
		</button>
		<?php
	}

	private function svg_icon( $style, $active ) {
		if ( 'bookmark' === $style ) {
			if ( $active ) {
				echo '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>';
			} else {
				echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>';
			}
		} elseif ( 'star' === $style ) {
			if ( $active ) {
				echo '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
			} else {
				echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
			}
		} elseif ( $active ) {
				echo '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg>';
		} else {
			echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg>';
		}
	}

	public function shortcode_wishlist( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}
		$atts = shortcode_atts(
			array(
				'columns' => 4,
			),
			$atts
		);
		$ids  = $this->get_wishlist();
		if ( empty( $ids ) ) {
			return '<div class="op-wishlist-empty"><p>' . esc_html__( 'Your wishlist is empty.', 'opulentia' ) . '</p></div>';
		}
		ob_start();
		?>
		<div class="op-wishlist-page">
			<ul class="products columns-<?php echo esc_attr( $atts['columns'] ); ?>">
				<?php
				$query = new WP_Query(
					array(
						'post_type'      => 'product',
						'post__in'       => $ids,
						'posts_per_page' => -1,
						'orderby'        => 'post__in',
					)
				);
				while ( $query->have_posts() ) {
					$query->the_post();
					wc_get_template_part( 'content', 'product' );
				}
				wp_reset_postdata();
				?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}

	public function shortcode_compare( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}
		$ids = $this->get_compare();
		if ( empty( $ids ) ) {
			return '<div class="op-compare-empty"><p>' . esc_html__( 'No products selected for comparison.', 'opulentia' ) . '</p></div>';
		}
		ob_start();
		?>
		<div class="op-compare-table-wrap">
			<table class="op-compare-table shop_table">
				<tbody>
					<tr class="op-compare-row-image">
						<th><?php esc_html_e( 'Image', 'opulentia' ); ?></th>
						<?php
						foreach ( $ids as $pid ) :
							$product = wc_get_product( $pid );
							?>
							<td><?php echo $product ? $product->get_image() : ''; ?></td>
						<?php endforeach; ?>
					</tr>
					<tr class="op-compare-row-title">
						<th><?php esc_html_e( 'Product', 'opulentia' ); ?></th>
						<?php
						foreach ( $ids as $pid ) :
							$product = wc_get_product( $pid );
							?>
							<td><?php echo $product ? '<a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $product->get_name() ) . '</a>' : ''; ?></td>
						<?php endforeach; ?>
					</tr>
					<tr class="op-compare-row-price">
						<th><?php esc_html_e( 'Price', 'opulentia' ); ?></th>
						<?php
						foreach ( $ids as $pid ) :
							$product = wc_get_product( $pid );
							?>
							<td><?php echo $product ? $product->get_price_html() : ''; ?></td>
						<?php endforeach; ?>
					</tr>
					<tr class="op-compare-row-desc">
						<th><?php esc_html_e( 'Description', 'opulentia' ); ?></th>
						<?php
						foreach ( $ids as $pid ) :
							$product = wc_get_product( $pid );
							?>
							<td><?php echo $product ? wp_kses_post( wp_trim_words( $product->get_short_description(), 20 ) ) : ''; ?></td>
						<?php endforeach; ?>
					</tr>
					<tr class="op-compare-row-cart">
						<th><?php esc_html_e( 'Action', 'opulentia' ); ?></th>
						<?php
						foreach ( $ids as $pid ) :
							$product = wc_get_product( $pid );
							?>
							<td><?php echo $product ? do_shortcode( '[add_to_cart id="' . $pid . '" style=""]' ) : ''; ?></td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_wishlist_compare',
			array(
				'title'    => __( 'Wishlist & Compare', 'opulentia' ),
				'panel'    => 'Opulentia_woocommerce',
				'priority' => 35,
			)
		);

		$wp_customize->add_setting(
			'wishlist-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wishlist-enable',
			array(
				'label'   => __( 'Enable Wishlist', 'opulentia' ),
				'section' => 'opulentia_wishlist_compare',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'compare-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'compare-enable',
			array(
				'label'   => __( 'Enable Compare', 'opulentia' ),
				'section' => 'opulentia_wishlist_compare',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'wishlist-button-position',
			array(
				'default'           => 'after_add_to_cart',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wishlist-button-position',
			array(
				'label'   => __( 'Wishlist Button Position', 'opulentia' ),
				'section' => 'opulentia_wishlist_compare',
				'type'    => 'select',
				'choices' => array(
					'after_add_to_cart'  => __( 'After Add to Cart', 'opulentia' ),
					'after_product_meta' => __( 'After Product Meta', 'opulentia' ),
					'both'               => __( 'Both', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'wishlist-icon-style',
			array(
				'default'           => 'heart',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wishlist-icon-style',
			array(
				'label'   => __( 'Wishlist Icon Style', 'opulentia' ),
				'section' => 'opulentia_wishlist_compare',
				'type'    => 'select',
				'choices' => array(
					'heart'    => __( 'Heart', 'opulentia' ),
					'bookmark' => __( 'Bookmark', 'opulentia' ),
					'star'     => __( 'Star', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'compare-max-products',
			array(
				'default'           => 4,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'compare-max-products',
			array(
				'label'       => __( 'Max Products for Compare', 'opulentia' ),
				'section'     => 'opulentia_wishlist_compare',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 2,
					'max'  => 10,
					'step' => 1,
				),
			)
		);
	}

	public function inline_css() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'wishlist-enable', true ) && ! Opulentia_get_option( 'compare-enable', true ) ) {
			return;
		}

		$css = '
        .op-wishlist-btn,
        .op-compare-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            margin-top: 12px;
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 6px;
            color: var(--color-text);
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
            line-height: 1.4;
        }
        .op-wishlist-btn:hover,
        .op-compare-btn:hover {
            border-color: var(--color-gold);
            color: var(--color-gold);
        }
        .op-wishlist-btn--active {
            border-color: #ef4444;
            color: #ef4444;
        }
        .op-wishlist-btn--active svg {
            color: #ef4444;
        }
        .op-compare-btn--active {
            border-color: var(--color-gold);
            color: var(--color-gold);
        }
        .op-wishlist-btn svg,
        .op-compare-btn svg {
            flex-shrink: 0;
        }
        .op-wishlist-empty,
        .op-compare-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--color-text-muted);
        }
        .op-compare-table-wrap {
            overflow-x: auto;
            margin: 30px 0;
        }
        .op-compare-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--color-border);
        }
        .op-compare-table th,
        .op-compare-table td {
            padding: 15px;
            border: 1px solid var(--color-border);
            text-align: center;
            vertical-align: middle;
        }
        .op-compare-table th {
            background: var(--color-secondary-dark);
            color: var(--color-gold);
            font-weight: 600;
            white-space: nowrap;
            width: 120px;
        }
        .op-compare-table tr:nth-child(even) td {
            background: rgba(255,255,255,0.02);
        }
        .op-compare-table td img {
            max-width: 120px;
            height: auto;
        }
        .op-compare-table .add_to_cart_button {
            display: inline-block;
            padding: 8px 16px;
            background: var(--color-accent);
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .op-compare-table .add_to_cart_button:hover {
            background: var(--color-accent-hover);
        }
        @media (max-width: 768px) {
            .op-compare-table th {
                width: 80px;
                font-size: 0.8rem;
                padding: 10px;
            }
            .op-compare-table td {
                padding: 10px;
                font-size: 0.8rem;
            }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );

		wp_add_inline_script(
			'opulentia-custom',
			'
        document.addEventListener("click", function(e) {
            var wishlistBtn = e.target.closest(".op-wishlist-btn");
            if (wishlistBtn) {
                e.preventDefault();
                var btn = wishlistBtn;
                var pid = btn.getAttribute("data-product-id");
                var nonce = btn.getAttribute("data-nonce");
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.success) {
                            if (r.data.active) {
                                btn.className = "op-wishlist-btn op-wishlist-btn--active";
                                btn.innerHTML = btn.innerHTML.replace(/<span[^>]*>.*?<\/span>/, "<span class=\"op-wishlist-btn__label\">' . esc_js( __( 'Remove from Wishlist', 'opulentia' ) ) . '</span>");
                            } else {
                                btn.className = "op-wishlist-btn op-wishlist-btn--inactive";
                                btn.innerHTML = btn.innerHTML.replace(/<span[^>]*>.*?<\/span>/, "<span class=\"op-wishlist-btn__label\">' . esc_js( __( 'Add to Wishlist', 'opulentia' ) ) . '</span>");
                            }
                        }
                    } catch(e) {}
                };
                xhr.send("action=op_wishlist_toggle&product_id=" + pid + "&nonce=" + nonce);
                return;
            }
            var compareBtn = e.target.closest(".op-compare-btn");
            if (compareBtn) {
                e.preventDefault();
                var btn = compareBtn;
                var pid = btn.getAttribute("data-product-id");
                var nonce = btn.getAttribute("data-nonce");
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.success) {
                            if (r.data.active) {
                                btn.className = "op-compare-btn op-compare-btn--active";
                                btn.innerHTML = btn.innerHTML.replace(/<span[^>]*>.*?<\/span>/, "<span class=\"op-compare-btn__label\">' . esc_js( __( 'Remove from Compare', 'opulentia' ) ) . '</span>");
                            } else {
                                btn.className = "op-compare-btn op-compare-btn--inactive";
                                btn.innerHTML = btn.innerHTML.replace(/<span[^>]*>.*?<\/span>/, "<span class=\"op-compare-btn__label\">' . esc_js( __( 'Add to Compare', 'opulentia' ) ) . '</span>");
                            }
                        }
                    } catch(e) {}
                };
                xhr.send("action=op_compare_toggle&product_id=" + pid + "&nonce=" + nonce);
            }
        });
        '
		);
	}
}

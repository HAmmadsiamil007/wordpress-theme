<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Ajax_Filtering {

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
		add_action( 'wp_ajax_op_filter_products', array( $this, 'ajax_filter_products' ) );
		add_action( 'wp_ajax_nopriv_op_filter_products', array( $this, 'ajax_filter_products' ) );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'render_filter_form' ), 5 );
		add_filter( 'parse_query', array( $this, 'apply_filters_to_query' ) );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_ajax_filtering',
			array(
				'title'    => __( 'AJAX Product Filtering', 'opulentia' ),
				'panel'    => 'Opulentia_woocommerce',
				'priority' => 36,
			)
		);

		$wp_customize->add_setting(
			'ajax-filter-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'ajax-filter-enable',
			array(
				'label'   => __( 'Enable AJAX Filtering', 'opulentia' ),
				'section' => 'opulentia_ajax_filtering',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'ajax-filter-position',
			array(
				'default'           => 'sidebar',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'ajax-filter-position',
			array(
				'label'   => __( 'Filter Position', 'opulentia' ),
				'section' => 'opulentia_ajax_filtering',
				'type'    => 'select',
				'choices' => array(
					'sidebar' => __( 'Sidebar', 'opulentia' ),
					'top'     => __( 'Above Products', 'opulentia' ),
					'both'    => __( 'Both', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'ajax-filter-price',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'ajax-filter-price',
			array(
				'label'   => __( 'Show Price Filter', 'opulentia' ),
				'section' => 'opulentia_ajax_filtering',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'ajax-filter-categories',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'ajax-filter-categories',
			array(
				'label'   => __( 'Show Category Filter', 'opulentia' ),
				'section' => 'opulentia_ajax_filtering',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'ajax-filter-attributes',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'ajax-filter-attributes',
			array(
				'label'   => __( 'Show Attribute Filter', 'opulentia' ),
				'section' => 'opulentia_ajax_filtering',
				'type'    => 'checkbox',
			)
		);
	}

	public function render_filter_form() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'ajax-filter-enable', true ) ) {
			return;
		}
		if ( ! is_shop() && ! is_product_category() && ! is_product_tag() && ! is_product_taxonomy() ) {
			return;
		}

		$position   = Opulentia_get_option( 'ajax-filter-position', 'sidebar' );
		$show_price = Opulentia_get_option( 'ajax-filter-price', true );
		$show_cats  = Opulentia_get_option( 'ajax-filter-categories', true );
		$show_attrs = Opulentia_get_option( 'ajax-filter-attributes', true );

		$min_price = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : 0;
		$max_price = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : '';

		global $wpdb;
		if ( ! $max_price ) {
			$max_price = $wpdb->get_var(
				"
                SELECT MAX(meta_value + 0)
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE p.post_type = 'product'
                AND p.post_status = 'publish'
                AND pm.meta_key = '_price'
            "
			);
			$max_price = $max_price ? ceil( floatval( $max_price ) ) : 500;
		}

		$selected_cats = isset( $_GET['product_cat'] ) ? array_map( 'sanitize_text_field', (array) $_GET['product_cat'] ) : array();

		$classes = 'op-filter-form';
		if ( 'sidebar' === $position ) {
			$classes .= ' op-filter-form--sidebar';
		} elseif ( 'top' === $position ) {
			$classes .= ' op-filter-form--top';
		} else {
			$classes .= ' op-filter-form--both';
		}
		?>
		<form class="<?php echo esc_attr( $classes ); ?>" method="GET" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
			<div class="op-filter-form__header">
				<span class="op-filter-form__title"><?php esc_html_e( 'Filter Products', 'opulentia' ); ?></span>
				<button type="button" class="op-filter-form__toggle"><?php esc_html_e( 'Filters', 'opulentia' ); ?></button>
			</div>
			<div class="op-filter-form__body">
				<div class="op-filter-form__active-tags"></div>

				<?php if ( $show_cats ) : ?>
				<div class="op-filter-field">
					<h4 class="op-filter-field__title"><?php esc_html_e( 'Categories', 'opulentia' ); ?></h4>
					<div class="op-filter-field__checklist">
						<?php
						$terms = get_terms(
							array(
								'taxonomy'   => 'product_cat',
								'hide_empty' => true,
								'parent'     => 0,
							)
						);
						foreach ( $terms as $term ) {
							$checked = in_array( $term->slug, $selected_cats, true ) ? 'checked' : '';
							echo '<label class="op-filter-checkbox"><input type="checkbox" name="product_cat[]" value="' . esc_attr( $term->slug ) . '" ' . $checked . '> <span>' . esc_html( $term->name ) . '</span></label>';
						}
						?>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $show_price ) : ?>
				<div class="op-filter-field">
					<h4 class="op-filter-field__title"><?php esc_html_e( 'Price', 'opulentia' ); ?></h4>
					<div class="op-filter-price">
						<div class="op-filter-price__inputs">
							<input type="number" name="min_price" class="op-filter-price__min" placeholder="<?php esc_attr_e( 'Min', 'opulentia' ); ?>" value="<?php echo esc_attr( $min_price ); ?>" min="0" step="1">
							<span class="op-filter-price__sep">-</span>
							<input type="number" name="max_price" class="op-filter-price__max" placeholder="<?php esc_attr_e( 'Max', 'opulentia' ); ?>" value="<?php echo esc_attr( $max_price ); ?>" min="0" step="1">
						</div>
						<div class="op-filter-price__slider">
							<div class="op-filter-price__track"></div>
							<input type="range" class="op-filter-price__range-min" min="0" max="<?php echo esc_attr( $max_price ); ?>" value="<?php echo esc_attr( $min_price ); ?>" step="1">
							<input type="range" class="op-filter-price__range-max" min="0" max="<?php echo esc_attr( $max_price ); ?>" value="<?php echo esc_attr( $max_price ); ?>" step="1">
						</div>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $show_attrs ) : ?>
					<?php
					$attribute_taxonomies = wc_get_attribute_taxonomies();
					foreach ( $attribute_taxonomies as $attr ) {
						$tax_name = 'pa_' . $attr->attribute_name;
						$terms    = get_terms(
							array(
								'taxonomy'   => $tax_name,
								'hide_empty' => true,
							)
						);
						if ( empty( $terms ) || is_wp_error( $terms ) ) {
							continue;
						}
						$selected_attr = isset( $_GET[ $tax_name ] ) ? array_map( 'sanitize_text_field', (array) $_GET[ $tax_name ] ) : array();
						?>
						<div class="op-filter-field op-filter-field--attr">
							<h4 class="op-filter-field__title"><?php echo esc_html( $attr->attribute_label ); ?></h4>
							<div class="op-filter-field__checklist">
								<?php
								foreach ( $terms as $term ) {
									$checked = in_array( $term->slug, $selected_attr, true ) ? 'checked' : '';
									echo '<label class="op-filter-checkbox"><input type="checkbox" name="' . esc_attr( $tax_name ) . '[]" value="' . esc_attr( $term->slug ) . '" ' . $checked . '> <span>' . esc_html( $term->name ) . '</span></label>';
								}
								?>
							</div>
						</div>
					<?php } ?>
				<?php endif; ?>
			</div>
		</form>
		<?php
	}

	public function ajax_filter_products() {
		check_ajax_referer( 'op_filter_nonce', 'nonce' );

		$filters = isset( $_POST['filters'] ) ? $_POST['filters'] : array();
		$paged   = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() ),
			'paged'          => $paged,
		);

		if ( ! empty( $filters['product_cat'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_text_field', (array) $filters['product_cat'] ),
			);
		}

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		foreach ( $attribute_taxonomies as $attr ) {
			$tax_name = 'pa_' . $attr->attribute_name;
			if ( ! empty( $filters[ $tax_name ] ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => $tax_name,
					'field'    => 'slug',
					'terms'    => array_map( 'sanitize_text_field', (array) $filters[ $tax_name ] ),
				);
			}
		}

		if ( ! empty( $filters['min_price'] ) || ! empty( $filters['max_price'] ) ) {
			$meta_query           = array(
				'key'     => '_price',
				'value'   => array( floatval( $filters['min_price'] ), floatval( $filters['max_price'] ) ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
			$args['meta_query'][] = $meta_query;
		}

		if ( isset( $filters['s'] ) && ! empty( $filters['s'] ) ) {
			$args['s'] = sanitize_text_field( $filters['s'] );
		}

		if ( isset( $filters['orderby'] ) ) {
			switch ( $filters['orderby'] ) {
				case 'price':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_price';
					$args['order']    = 'ASC';
					break;
				case 'price-desc':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_price';
					$args['order']    = 'DESC';
					break;
				case 'date':
					$args['orderby'] = 'date';
					$args['order']   = 'DESC';
					break;
				case 'rating':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_wc_average_rating';
					$args['order']    = 'DESC';
					break;
				default:
					$args['orderby'] = 'menu_order';
					$args['order']   = 'ASC';
			}
		}

		if ( isset( $filters['on_sale'] ) && $filters['on_sale'] ) {
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => '_sale_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => '_min_variation_sale_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
			);
		}

		if ( ! empty( $args['tax_query'] ) && count( $args['tax_query'] ) > 1 ) {
			$args['tax_query']['relation'] = 'AND';
		}
		if ( isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) && count( $args['meta_query'] ) > 1 ) {
			$args['meta_query']['relation'] = 'AND';
		}

		$query = new WP_Query( $args );

		ob_start();
		if ( $query->have_posts() ) {
			woocommerce_product_loop_start();
			while ( $query->have_posts() ) {
				$query->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();
		} else {
			echo '<p class="op-filter-no-results">' . esc_html__( 'No products found.', 'opulentia' ) . '</p>';
		}
		$products_html = ob_get_clean();

		ob_start();
		$total_pages = $query->max_num_pages;
		if ( $total_pages > 1 ) {
			echo '<nav class="woocommerce-pagination">';
			echo paginate_links(
				apply_filters(
					'woocommerce_pagination_args',
					array(
						'total'   => $total_pages,
						'current' => $paged,
						'type'    => 'list',
					)
				)
			);
			echo '</nav>';
		}
		$pagination_html = ob_get_clean();

		wp_reset_postdata();

		wp_send_json_success(
			array(
				'products'   => $products_html,
				'pagination' => $pagination_html,
				'count'      => $query->found_posts,
			)
		);
	}

	public function apply_filters_to_query( $query ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'ajax-filter-enable', true ) ) {
			return;
		}
		if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) ) {
			if ( ! empty( $_GET['product_cat'] ) && is_array( $_GET['product_cat'] ) ) {
				$tax_query = $query->get( 'tax_query' );
				if ( ! is_array( $tax_query ) ) {
					$tax_query = array();
				}
				$tax_query[] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => array_map( 'sanitize_text_field', $_GET['product_cat'] ),
				);
				$query->set( 'tax_query', $tax_query );
			}

			if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
				$meta_query = $query->get( 'meta_query' );
				if ( ! is_array( $meta_query ) ) {
					$meta_query = array();
				}
				$meta_query[] = array(
					'key'     => '_price',
					'value'   => array( floatval( $_GET['min_price'] ), floatval( $_GET['max_price'] ) ),
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN',
				);
				$query->set( 'meta_query', $meta_query );
			}
		}
	}

	public function inline_css() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'ajax-filter-enable', true ) ) {
			return;
		}

		$css = '
        .op-filter-form {
            margin-bottom: 24px;
        }
        .op-filter-form__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--color-border);
            margin-bottom: 16px;
        }
        .op-filter-form__title {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            color: var(--color-gold);
        }
        .op-filter-form__toggle {
            display: none;
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            color: var(--color-text);
            padding: 6px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.85rem;
        }
        .op-filter-form--sidebar .op-filter-form__body {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .op-filter-form--top .op-filter-form__body {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
        }
        .op-filter-form--both .op-filter-form__body {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .op-filter-field {
            border: 1px solid var(--color-border);
            border-radius: 6px;
            padding: 16px;
            background: var(--color-secondary-dark);
        }
        .op-filter-field__title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-gold);
            margin: 0 0 12px;
            font-family: var(--font-body);
        }
        .op-filter-field__checklist {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 240px;
            overflow-y: auto;
        }
        .op-filter-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--color-text);
            cursor: pointer;
            padding: 4px 0;
        }
        .op-filter-checkbox input[type="checkbox"] {
            accent-color: var(--color-gold);
            width: 16px;
            height: 16px;
        }
        .op-filter-price__inputs {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .op-filter-price__inputs input {
            width: 80px;
            padding: 6px 8px;
            background: var(--color-primary-dark);
            border: 1px solid var(--color-border);
            border-radius: 4px;
            color: var(--color-text);
            font-size: 0.85rem;
        }
        .op-filter-price__sep {
            color: var(--color-text-muted);
        }
        .op-filter-price__slider {
            position: relative;
            height: 24px;
        }
        .op-filter-price__track {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            height: 4px;
            background: var(--color-border);
            border-radius: 2px;
        }
        .op-filter-price__range-min,
        .op-filter-price__range-max {
            position: absolute;
            width: 100%;
            height: 24px;
            top: 0;
            background: transparent;
            -webkit-appearance: none;
            appearance: none;
            pointer-events: none;
        }
        .op-filter-price__range-min::-webkit-slider-thumb,
        .op-filter-price__range-max::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--color-gold);
            cursor: pointer;
            pointer-events: auto;
            border: 2px solid var(--color-primary-dark);
        }
        .op-filter-price__range-min::-moz-range-thumb,
        .op-filter-price__range-max::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--color-gold);
            cursor: pointer;
            pointer-events: auto;
            border: 2px solid var(--color-primary-dark);
        }
        .op-filter-form__active-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }
        .op-filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: var(--color-accent);
            color: #000;
            font-size: 0.8rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .op-filter-tag:hover {
            background: var(--color-accent-hover);
        }
        .op-filter-no-results {
            text-align: center;
            padding: 40px;
            color: var(--color-text-muted);
        }
        @media (max-width: 768px) {
            .op-filter-form__toggle {
                display: inline-flex;
            }
            .op-filter-form__body {
                display: none;
            }
            .op-filter-form__body.op-filter-form__body--open {
                display: flex;
            }
            .op-filter-form--top .op-filter-form__body {
                flex-direction: column;
            }
            .op-filter-field__checklist {
                max-height: 160px;
            }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );

		wp_add_inline_script(
			'opulentia-custom',
			'
        (function() {
            var filterForm = document.querySelector(".op-filter-form");
            if (!filterForm) return;

            var toggleBtn = filterForm.querySelector(".op-filter-form__toggle");
            if (toggleBtn) {
                toggleBtn.addEventListener("click", function() {
                    filterForm.querySelector(".op-filter-form__body").classList.toggle("op-filter-form__body--open");
                });
            }

            var xhr = null;

            function updateFilters() {
                var form = filterForm;
                var filters = {};
                var checkboxes = form.querySelectorAll("input[type=checkbox]");
                checkboxes.forEach(function(cb) {
                    if (cb.checked) {
                        var name = cb.name.replace(/\[\]$/, "");
                        if (!filters[name]) filters[name] = [];
                        filters[name].push(cb.value);
                    }
                });
                var minPrice = form.querySelector("input[name=min_price]");
                var maxPrice = form.querySelector("input[name=max_price]");
                if (minPrice && minPrice.value) filters.min_price = minPrice.value;
                if (maxPrice && maxPrice.value) filters.max_price = maxPrice.value;

                var params = new URLSearchParams(window.location.search);
                if (params.get("s")) filters.s = params.get("s");
                if (params.get("orderby")) filters.orderby = params.get("orderby");

                if (xhr) xhr.abort();
                xhr = new XMLHttpRequest();
                xhr.open("POST", "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.success) {
                            var productsWrap = document.querySelector(".products") || document.querySelector("ul.products");
                            if (productsWrap) {
                                var temp = document.createElement("div");
                                temp.innerHTML = r.data.products;
                                productsWrap.outerHTML = temp.querySelector(".products") ? temp.querySelector(".products").outerHTML : temp.innerHTML;
                            }
                            var paginationWrap = document.querySelector(".woocommerce-pagination");
                            if (paginationWrap) {
                                var temp2 = document.createElement("div");
                                temp2.innerHTML = r.data.pagination;
                                paginationWrap.outerHTML = temp2.querySelector(".woocommerce-pagination") ? temp2.querySelector(".woocommerce-pagination").outerHTML : r.data.pagination;
                            }
                            var url = new URL(window.location);
                            for (var key in filters) {
                                if (Array.isArray(filters[key])) {
                                    url.searchParams.delete(key);
                                    filters[key].forEach(function(v) { url.searchParams.append(key, v); });
                                } else {
                                    url.searchParams.set(key, filters[key]);
                                }
                            }
                            window.history.pushState({}, "", url.toString());
                        }
                    } catch(e) {}
                };
                var body = "action=op_filter_products&nonce=' . esc_js( wp_create_nonce( 'op_filter_nonce' ) ) . '&filters=" + encodeURIComponent(JSON.stringify(filters));
                xhr.send(body);
            }

            var debounceTimer;
            filterForm.addEventListener("change", function(e) {
                if (e.target.type === "checkbox") {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updateFilters, 300);
                }
            });

            var minRange = filterForm.querySelector(".op-filter-price__range-min");
            var maxRange = filterForm.querySelector(".op-filter-price__range-max");
            var minInput = filterForm.querySelector(".op-filter-price__min");
            var maxInput = filterForm.querySelector(".op-filter-price__max");

            if (minRange && maxRange && minInput && maxInput) {
                function syncRange() {
                    var minVal = parseFloat(minRange.value);
                    var maxVal = parseFloat(maxRange.value);
                    if (minVal > maxVal) {
                        var t = minVal; minVal = maxVal; maxVal = t;
                    }
                    minInput.value = minVal;
                    maxInput.value = maxVal;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updateFilters, 400);
                }
                minRange.addEventListener("input", syncRange);
                maxRange.addEventListener("input", syncRange);
                minInput.addEventListener("change", function() {
                    minRange.value = minInput.value;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updateFilters, 400);
                });
                maxInput.addEventListener("change", function() {
                    maxRange.value = maxInput.value;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updateFilters, 400);
                });
            }
        })();
        '
		);
	}
}

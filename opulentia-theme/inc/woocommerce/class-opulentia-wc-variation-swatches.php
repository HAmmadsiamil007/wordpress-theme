<?php
/**
 * Opulentia Variation Swatches
 *
 * Converts WooCommerce variation dropdowns into visual swatches:
 * - Color swatches (color picker per attribute term)
 * - Image swatches (product image per term)
 * - Label swatches (text-based buttons)
 *
 * @package Opulentia
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Opulentia_WC_Variation_Swatches' ) ) {

	/**
	 * Opulentia_WC_Variation_Swatches class.
	 */
	class Opulentia_WC_Variation_Swatches {

		/**
		 * Singleton instance.
		 *
		 * @var self|null
		 */
		private static $instance;

		/**
		 * Stores attribute term meta for swatches.
		 *
		 * @var array
		 */
		private $swatch_data = array();

		/**
		 * Returns the singleton instance.
		 *
		 * @return self
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor — registers hooks.
		 */
		private function __construct() {
			// Disable if module is not active.
			if ( ! Opulentia_get_option( 'wc-enable-swatches', true ) ) {
				return;
			}

			// Replace dropdown with swatches on variable products.
			add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'render_swatches' ), 10, 2 );

			// Enqueue swatches JS.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

			// Add custom swatch fields to attribute term edit screen.
			add_action( 'product_attribute_term_edit_form', array( $this, 'term_edit_fields' ), 10, 3 );
			add_action( 'created_term', array( $this, 'save_term_meta' ), 10, 3 );
			add_action( 'edited_term', array( $this, 'save_term_meta' ), 10, 3 );

			// Add swatch columns to term list.
			add_filter( 'manage_edit-pa_color_columns', array( $this, 'term_columns' ) );
			add_filter( 'manage_pa_color_custom_column', array( $this, 'term_column_content' ), 10, 3 );
		}

		/**
		 * Enqueue swatches scripts and styles.
		 */
		public function enqueue_scripts() {
			wp_add_inline_style( 'opulentia-style', $this->get_inline_css() );

			wp_add_inline_script(
				'wc-add-to-cart-variation',
				$this->get_swatches_js()
			);
		}

		/**
		 * Get the JavaScript for swatch interaction.
		 * Handles click-to-select, sync to hidden select, and variation trigger.
		 *
		 * @return string
		 */
		private function get_swatches_js() {
			return '
            jQuery(document).ready(function($) {
                /* Initialize swatches: sync selected state from hidden select values */
                $(".opulentia-swatches").each(function() {
                    var $swatches = $(this);
                    var attrName = $swatches.data("attribute");
                    var $select = $swatches.siblings("select[name=\"" + attrName + "\"]");

                    if (!$select.length) {
                        $select = $swatches.closest(".variations_form").find("select[name=\"" + attrName + "\"]");
                    }

                    /* Set initial selected state from select value */
                    var selectedVal = $select.val();
                    if (selectedVal) {
                        $swatches.find(".opulentia-swatch[data-value=\"" + selectedVal + "\"]").addClass("selected");
                    }

                    /* Click handler */
                    $swatches.on("click", ".opulentia-swatch", function(e) {
                        e.preventDefault();
                        var $swatch = $(this);

                        if ($swatch.hasClass("disabled")) {
                            return;
                        }

                        /* Toggle selection */
                        $swatch.closest(".opulentia-swatches").find(".opulentia-swatch").removeClass("selected");
                        $swatch.addClass("selected");

                        /* Sync value to hidden select */
                        var val = $swatch.data("value");
                        if ($select.length) {
                            $select.val(val).trigger("change");
                        }
                    });

                    /* Listen for WooCommerce variation updates to manage disabled states */
                    $swatches.closest(".variations_form").on("woocommerce_variation_select_change", function() {
                        $swatches.find(".opulentia-swatch.selected").removeClass("selected");
                        var currentVal = $select.val();
                        if (currentVal) {
                            $swatches.find(".opulentia-swatch[data-value=\"" + currentVal + "\"]").addClass("selected");
                        }
                    });
                });
            });
            ';
		}

		/**
		 * Get inline CSS for swatches.
		 *
		 * @return string
		 */
		private function get_inline_css() {
			$swatch_size  = (int) Opulentia_get_option( 'wc-swatch-size', 30 );
			$swatch_style = Opulentia_get_option( 'wc-swatch-style', 'rounded' );

			$border_radius = 'rounded' === $swatch_style ? '50%' : ( 'square' === $swatch_style ? '0' : '4px' );

			return '
            .opulentia-swatches {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin: 10px 0;
                padding: 0;
                list-style: none;
            }
            .opulentia-swatch {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: ' . $swatch_size . 'px;
                height: ' . $swatch_size . 'px;
                padding: 0 8px;
                border: 2px solid #333;
                border-radius: ' . $border_radius . ';
                cursor: pointer;
                font-size: 12px;
                font-weight: 500;
                color: #f5f5f5;
                background: #1a1a1a;
                transition: all 0.2s ease;
                position: relative;
                user-select: none;
            }
            .opulentia-swatch:hover {
                border-color: #c9a96e;
                transform: translateY(-2px);
            }
            .opulentia-swatch.selected {
                border-color: #c9a96e;
                box-shadow: 0 0 0 2px #c9a96e;
            }
            .opulentia-swatch.disabled {
                opacity: 0.3;
                cursor: not-allowed;
                pointer-events: none;
            }
            .opulentia-swatch--color {
                padding: 0;
                width: ' . ( $swatch_size + 4 ) . 'px;
                min-width: ' . ( $swatch_size + 4 ) . 'px;
                height: ' . ( $swatch_size + 4 ) . 'px;
            }
            .opulentia-swatch--color.selected {
                box-shadow: 0 0 0 3px #c9a96e, 0 0 0 5px #1a1a1a;
            }
            .opulentia-swatch--image {
                padding: 2px;
                overflow: hidden;
            }
            .opulentia-swatch--image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: inherit;
            }
            .opulentia-swatch__tooltip {
                display: none;
                position: absolute;
                bottom: calc(100% + 8px);
                left: 50%;
                transform: translateX(-50%);
                background: #111;
                color: #f5f5f5;
                padding: 4px 10px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 10;
                border: 1px solid #333;
            }
            .opulentia-swatch:hover .opulentia-swatch__tooltip {
                display: block;
            }
            .opulentia-swatches + select {
                display: none !important;
            }
            ';
		}

		/**
		 * Render swatch HTML replacing the select dropdown.
		 *
		 * @param  string $html      Default dropdown HTML.
		 * @param  array  $args      Dropdown args.
		 * @return string
		 */
		public function render_swatches( $html, $args ) {
			$options          = $args['options'];
			$product          = $args['product'];
			$attribute        = $args['attribute'];
			$name             = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
			$id               = $args['id'] ? $args['id'] : sanitize_title( $attribute );
			$class            = $args['class'];
			$show_option_none = $args['show_option_none'] ? true : false;
			$selected         = $args['selected'] ?? '';

			if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
				$attributes = $product->get_variation_attributes();
				$options    = $attributes[ $attribute ] ?? array();
			}

			if ( empty( $options ) ) {
				return $html;
			}

			// Determine swatch type based on taxonomy.
			$taxonomy    = wc_attribute_taxonomy_name( $attribute );
			$swatch_type = $this->get_swatch_type( $taxonomy );

			if ( 'none' === $swatch_type ) {
				return $html;
			}

			$swatches_html = '<div class="opulentia-swatches" data-attribute="' . esc_attr( $name ) . '" data-taxonomy="' . esc_attr( $taxonomy ) . '">';

			if ( $show_option_none ) {
				$swatches_html .= '<button class="opulentia-swatch opulentia-swatch--label" data-value="">' . esc_html( $show_option_none ) . '</button>';
			}

			foreach ( $options as $option ) {
				$swatches_html .= $this->get_swatch_item( $option, $taxonomy, $swatch_type, $selected );
			}

			$swatches_html .= '</div>';

			// Return swatches + hidden select for form submission.
			return $swatches_html . $html;
		}

		/**
		 * Get a single swatch item HTML.
		 *
		 * @param  string $option     Attribute term slug.
		 * @param  string $taxonomy   Attribute taxonomy name.
		 * @param  string $swatch_type Swatch type (color, image, label).
		 * @param  string $selected   Currently selected value.
		 * @return string
		 */
		private function get_swatch_item( $option, $taxonomy, $swatch_type, $selected ) {
			$term          = get_term_by( 'slug', $option, $taxonomy );
			$label         = $term ? $term->name : $option;
			$selected_attr = selected( $selected, $option, false );

			$item_class = 'opulentia-swatch opulentia-swatch--' . $swatch_type;
			if ( $selected_attr ) {
				$item_class .= ' selected';
			}

			$swatch_html = '<button class="' . esc_attr( $item_class ) . '" data-value="' . esc_attr( $option ) . '" title="' . esc_attr( $label ) . '">';

			switch ( $swatch_type ) {
				case 'color':
					$color        = $this->get_term_meta( $term->term_id ?? 0, 'swatch_color', '#ccc' );
					$swatch_html .= '<span class="opulentia-swatch__color" style="background-color:' . esc_attr( $color ) . '; width:100%; height:100%; border-radius:inherit;"></span>';
					break;

				case 'image':
					$image_id = $this->get_term_meta( $term->term_id ?? 0, 'swatch_image', 0 );
					if ( $image_id ) {
						$swatch_html .= wp_get_attachment_image(
							$image_id,
							array( 30, 30 ),
							true,
							array(
								'class' => 'opulentia-swatch__image',
								'alt'   => esc_attr( $label ),
							)
						);
					} else {
						$swatch_html .= esc_html( $label );
					}
					break;

				case 'label':
				default:
					$swatch_html .= '<span class="opulentia-swatch__label">' . esc_html( $label ) . '</span>';
					break;
			}

			// Tooltip on hover.
			$swatch_html .= '<span class="opulentia-swatch__tooltip">' . esc_html( $label ) . '</span>';

			$swatch_html .= '</button>';

			return $swatch_html;
		}

		/**
		 * Get the swatch type for an attribute taxonomy.
		 *
		 * @param  string $taxonomy Attribute taxonomy name.
		 * @return string           'color', 'image', 'label', or 'none'.
		 */
		private function get_swatch_type( $taxonomy ) {
			$type = get_option( 'Opulentia_swatch_type_' . $taxonomy, 'label' );

			// Validate type.
			if ( ! in_array( $type, array( 'color', 'image', 'label', 'none' ), true ) ) {
				$type = 'label';
			}

			return $type;
		}

		/**
		 * Get term meta for a swatch.
		 *
		 * @param  int    $term_id Term ID.
		 * @param  string $key     Meta key.
		 * @param  mixed  $default Default value.
		 * @return mixed
		 */
		private function get_term_meta( $term_id, $key, $default = '' ) {
			if ( ! $term_id ) {
				return $default;
			}
			$value = get_term_meta( $term_id, 'Opulentia_' . $key, true );
			return ! empty( $value ) ? $value : $default;
		}

		/**
		 * Save term meta for swatches.
		 *
		 * @param int    $term_id  Term ID.
		 * @param string $tt_id    Term taxonomy ID.
		 * @param string $taxonomy Taxonomy slug.
		 */
		public function save_term_meta( $term_id, $tt_id, $taxonomy ) {
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				return;
			}

			if ( isset( $_POST['Opulentia_swatch_type'] ) ) {
				update_option( 'Opulentia_swatch_type_' . $taxonomy, sanitize_text_field( $_POST['Opulentia_swatch_type'] ) );
			}

			if ( isset( $_POST['Opulentia_swatch_color'] ) ) {
				update_term_meta( $term_id, 'Opulentia_swatch_color', sanitize_hex_color( $_POST['Opulentia_swatch_color'] ) );
			}

			if ( isset( $_POST['Opulentia_swatch_image'] ) ) {
				update_term_meta( $term_id, 'Opulentia_swatch_image', absint( $_POST['Opulentia_swatch_image'] ) );
			}
		}

		/**
		 * Add custom columns to attribute term list.
		 *
		 * @param  array $columns Default columns.
		 * @return array
		 */
		public function term_columns( $columns ) {
			$columns['Opulentia_swatch'] = __( 'Swatch', 'opulentia' );
			return $columns;
		}

		/**
		 * Render swatch column content.
		 *
		 * @param  string $content   Default content.
		 * @param  string $column    Column name.
		 * @param  int    $term_id   Term ID.
		 * @return string
		 */
		public function term_column_content( $content, $column, $term_id ) {
			if ( 'Opulentia_swatch' !== $column ) {
				return $content;
			}

			// Swatch type is stored per-taxonomy as option, not per-term as meta.
			$taxonomy = get_term_field( 'taxonomy', $term_id );
			$type     = get_option( 'Opulentia_swatch_type_' . $taxonomy, 'label' );

			if ( 'color' === $type ) {
				$color = get_term_meta( $term_id, 'Opulentia_swatch_color', true );
				if ( $color ) {
					return '<span style="display:inline-block;width:30px;height:30px;background:' . esc_attr( $color ) . ';border:1px solid #ccc;"></span>';
				}
			}

			return '—';
		}
	}
}

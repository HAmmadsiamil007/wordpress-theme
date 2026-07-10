<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Faq_Cpt {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomy' ) );
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
        add_shortcode( 'op_faq', array( $this, 'shortcode_faq' ) );
    }

    public function register_post_type() {
        $labels = array(
            'name'               => __( 'FAQs', 'opulentia' ),
            'singular_name'      => __( 'FAQ', 'opulentia' ),
            'add_new'            => __( 'Add New', 'opulentia' ),
            'add_new_item'       => __( 'Add New FAQ', 'opulentia' ),
            'edit_item'          => __( 'Edit FAQ', 'opulentia' ),
            'new_item'           => __( 'New FAQ', 'opulentia' ),
            'view_item'          => __( 'View FAQ', 'opulentia' ),
            'search_items'       => __( 'Search FAQs', 'opulentia' ),
            'not_found'          => __( 'No FAQs found', 'opulentia' ),
            'not_found_in_trash' => __( 'No FAQs found in trash', 'opulentia' ),
            'all_items'          => __( 'All FAQs', 'opulentia' ),
            'menu_name'          => __( 'FAQs', 'opulentia' ),
        );

        register_post_type( 'faq', array(
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array( 'slug' => 'faq' ),
            'supports'     => array( 'title', 'editor' ),
            'menu_icon'    => 'dashicons-editor-help',
            'show_in_rest' => true,
        ) );
    }

    public function register_taxonomy() {
        register_taxonomy( 'faq_category', 'faq', array(
            'hierarchical' => true,
            'labels'       => array(
                'name'              => __( 'FAQ Categories', 'opulentia' ),
                'singular_name'     => __( 'FAQ Category', 'opulentia' ),
                'search_items'      => __( 'Search Categories', 'opulentia' ),
                'all_items'         => __( 'All Categories', 'opulentia' ),
                'parent_item'       => __( 'Parent Category', 'opulentia' ),
                'parent_item_colon' => __( 'Parent Category:', 'opulentia' ),
                'edit_item'         => __( 'Edit Category', 'opulentia' ),
                'update_item'       => __( 'Update Category', 'opulentia' ),
                'add_new_item'      => __( 'Add New Category', 'opulentia' ),
                'new_item_name'     => __( 'New Category Name', 'opulentia' ),
                'menu_name'         => __( 'Categories', 'opulentia' ),
            ),
            'rewrite'      => array( 'slug' => 'faq/category' ),
            'show_in_rest' => true,
        ) );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_faq', array(
            'title'    => __( 'FAQ', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 135,
        ) );

        $wp_customize->add_setting( 'faq-style', array(
            'default'           => 'accordion',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'faq-style', array(
            'label'   => __( 'FAQ Style', 'opulentia' ),
            'section' => 'opulentia_faq',
            'type'    => 'select',
            'choices' => array(
                'accordion' => __( 'Accordion', 'opulentia' ),
                'toggle'    => __( 'Toggle', 'opulentia' ),
                'grouped'   => __( 'Grouped by Category', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'faq-show-search', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'faq-show-search', array(
            'label'   => __( 'Show Search Input', 'opulentia' ),
            'section' => 'opulentia_faq',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'faq-open-first', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'faq-open-first', array(
            'label'   => __( 'Auto-open First Item', 'opulentia' ),
            'section' => 'opulentia_faq',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'faq-schema-markup', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'faq-schema-markup', array(
            'label'   => __( 'Output Schema.org Markup', 'opulentia' ),
            'section' => 'opulentia_faq',
            'type'    => 'checkbox',
        ) );
    }

    public function shortcode_faq( $atts ) {
        $atts = shortcode_atts( array(
            'category'   => '',
            'style'      => Opulentia_get_option( 'faq-style', 'accordion' ),
            'show_search' => Opulentia_get_option( 'faq-show-search', false ),
        ), $atts );

        $args = array(
            'post_type'      => 'faq',
            'posts_per_page'  => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        );

        if ( ! empty( $atts['category'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'faq_category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $atts['category'] ),
                ),
            );
        }

        $query = new WP_Query( $args );
        if ( ! $query->have_posts() ) {
            return '<p>' . __( 'No FAQs found.', 'opulentia' ) . '</p>';
        }

        $style       = sanitize_text_field( $atts['style'] );
        $show_search = filter_var( $atts['show_search'], FILTER_VALIDATE_BOOLEAN );
        $open_first  = Opulentia_get_option( 'faq-open-first', false );
        $use_schema  = Opulentia_get_option( 'faq-schema-markup', true );

        $faq_id = 'op-faq-' . uniqid();
        $output = '<div class="op-faq-wrapper op-faq-style--' . esc_attr( $style ) . '" id="' . esc_attr( $faq_id ) . '">';

        if ( $show_search ) {
            $output .= '<div class="op-faq-search">';
            $output .= '<input type="text" class="op-faq-search-input" placeholder="' . esc_attr__( 'Search FAQs...', 'opulentia' ) . '" data-faq="' . esc_attr( $faq_id ) . '">';
            $output .= '</div>';
        }

        $schema_items = array();

        if ( 'grouped' === $style ) {
            $categories = get_terms( array(
                'taxonomy' => 'faq_category',
                'hide_empty' => true,
            ) );

            if ( ! empty( $atts['category'] ) ) {
                $terms = get_terms( array(
                    'taxonomy' => 'faq_category',
                    'slug'     => sanitize_text_field( $atts['category'] ),
                    'hide_empty' => true,
                ) );
                if ( ! empty( $terms ) ) {
                    $categories = $terms;
                }
            }

            if ( ! empty( $categories ) ) {
                foreach ( $categories as $term ) {
                    $term_args = $args;
                    $term_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'faq_category',
                            'field'    => 'term_id',
                            'terms'    => $term->term_id,
                        ),
                    );
                    $term_query = new WP_Query( $term_args );

                    if ( $term_query->have_posts() ) {
                        $output .= '<div class="op-faq-group">';
                        $output .= '<h3 class="op-faq-group-title">' . esc_html( $term->name ) . '</h3>';
                        $output .= $this->render_faq_items( $term_query, $style, $open_first, $schema_items );
                        $output .= '</div>';
                    }
                    wp_reset_postdata();
                }
            } else {
                $output .= $this->render_faq_items( $query, $style, $open_first, $schema_items );
            }
        } else {
            $output .= $this->render_faq_items( $query, $style, $open_first, $schema_items );
        }

        if ( $use_schema && ! empty( $schema_items ) ) {
            $output .= '<script type="application/ld+json">';
            $output .= json_encode( array(
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $schema_items,
            ) );
            $output .= '</script>';
        }

        if ( $show_search ) {
            $output .= '<script>
            (function(){
                var inputs = document.querySelectorAll(".op-faq-search-input");
                inputs.forEach(function(input){
                    input.addEventListener("input", function(){
                        var container = document.getElementById(this.getAttribute("data-faq"));
                        if (!container) return;
                        var q = this.value.toLowerCase();
                        var items = container.querySelectorAll(".op-faq-item");
                        items.forEach(function(item){
                            var title = item.querySelector(".op-faq-question");
                            var text = item.querySelector(".op-faq-answer");
                            var match = (title && title.textContent.toLowerCase().indexOf(q) !== -1) || (text && text.textContent.toLowerCase().indexOf(q) !== -1);
                            item.style.display = match ? "" : "none";
                        });
                    });
                });
            })();
            </script>';
        }

        $output .= '</div>';

        wp_reset_postdata();

        return $output;
    }

    private function render_faq_items( $query, $style, $open_first, &$schema_items ) {
        $output = '';
        $index = 0;

        while ( $query->have_posts() ) {
            $query->the_post();
            $id       = get_the_ID();
            $question = get_the_title();
            $answer   = apply_filters( 'the_content', get_the_content() );

            $is_first = ( 0 === $index && $open_first );
            $expanded = $is_first ? 'open' : '';

            if ( 'accordion' === $style || 'toggle' === $style ) {
                $name_attr = 'accordion' === $style ? ' name="op-faq-group-' . esc_attr( $id ) . '"' : '';
                $output .= '<details class="op-faq-item op-faq-item--' . esc_attr( $style ) . '"' . $name_attr . ' ' . $expanded . '>';
                $output .= '<summary class="op-faq-question">' . esc_html( $question ) . '</summary>';
                $output .= '<div class="op-faq-answer">' . $answer . '</div>';
                $output .= '</details>';
            } elseif ( 'grouped' === $style ) {
                $output .= '<div class="op-faq-item">';
                $output .= '<div class="op-faq-question">' . esc_html( $question ) . '</div>';
                $output .= '<div class="op-faq-answer">' . $answer . '</div>';
                $output .= '</div>';
            }

            $schema_items[] = array(
                '@type'          => 'Question',
                'name'           => $question,
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( get_the_content() ),
                ),
            );

            $index++;
        }

        return $output;
    }

    public function inline_css() {
        global $post;
        if ( ! is_singular( 'faq' ) && ! is_post_type_archive( 'faq' ) ) {
            if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'op_faq' ) ) {
                return;
            }
        }

        $css = '
        .op-faq--accordion .op-faq-item,
        .op-faq--toggle .op-faq-item {
            border: 1px solid var(--color-border);
            border-radius: 8px;
            margin-bottom: 8px;
            overflow: hidden;
            background: var(--color-secondary-dark);
        }
        .op-faq-question {
            padding: 16px 20px;
            cursor: pointer;
            font-weight: 600;
            color: var(--color-text);
            font-family: var(--font-heading);
            position: relative;
            list-style: none;
            transition: color 0.2s ease;
        }
        .op-faq-question::-webkit-details-marker {
            display: none;
        }
        .op-faq-question:hover {
            color: var(--color-accent);
        }
        .op-faq-question::after {
            content: "+";
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--color-accent);
            transition: transform 0.2s ease;
        }
        details[open] .op-faq-question::after {
            content: "\u2212";
        }
        .op-faq-answer {
            padding: 0 20px 16px;
            color: var(--color-text-muted);
            line-height: 1.6;
        }
        .op-faq--accordion .op-faq-question::after {
            content: "+";
        }
        .op-faq--accordion details[open] .op-faq-question::after {
            content: "\u2212";
        }
        .op-faq--toggle .op-faq-question::after {
            content: "+";
        }
        .op-faq--toggle details[open] .op-faq-question::after {
            content: "\u2212";
        }
        .op-faq-group {
            margin-bottom: 32px;
        }
        .op-faq-group-title {
            font-family: var(--font-heading);
            color: var(--color-gold);
            margin-bottom: 16px;
            font-size: 1.3rem;
        }
        .op-faq-search {
            margin-bottom: 24px;
        }
        .op-faq-search-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            color: var(--color-text);
            font-family: var(--font-body);
            font-size: 0.95rem;
        }
        .op-faq-search-input:focus {
            outline: none;
            border-color: var(--color-accent);
        }
        .op-faq-search-input::placeholder {
            color: var(--color-text-muted);
        }
        @media (max-width: 768px) {
            .op-faq-question {
                font-size: 0.95rem;
                padding: 12px 16px;
            }
            .op-faq-answer {
                padding: 0 16px 12px;
                font-size: 0.9rem;
            }
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

add_action( 'init', array( 'Opulentia_Faq_Cpt', 'get_instance' ) );
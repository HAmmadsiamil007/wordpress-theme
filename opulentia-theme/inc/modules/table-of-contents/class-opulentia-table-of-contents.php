<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Table_Of_Contents {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_js' ), 110 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
        add_filter( 'the_content', array( $this, 'process_content' ), 5 );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_toc', array(
            'title'    => __( 'Table of Contents', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 115,
        ) );

        $wp_customize->add_setting( 'toc-enable', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'toc-enable', array(
            'label'   => __( 'Enable Table of Contents', 'opulentia' ),
            'section' => 'opulentia_toc',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'toc-heading-levels', array(
            'default'           => 'h2,h3',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'toc-heading-levels', array(
            'label'       => __( 'Heading Levels', 'opulentia' ),
            'description' => __( 'Comma-separated: h2, h3, h4', 'opulentia' ),
            'section'     => 'opulentia_toc',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'h2,h3' ),
        ) );

        $wp_customize->add_setting( 'toc-position', array(
            'default'           => 'inline',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'toc-position', array(
            'label'   => __( 'Position', 'opulentia' ),
            'section' => 'opulentia_toc',
            'type'    => 'select',
            'choices' => array(
                'inline'  => __( 'Inline (Before Content)', 'opulentia' ),
                'sidebar' => __( 'Sticky Sidebar', 'opulentia' ),
                'floating' => __( 'Floating Corner', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'toc-auto-insert', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'toc-auto-insert', array(
            'label'   => __( 'Auto-insert on Posts', 'opulentia' ),
            'section' => 'opulentia_toc',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'toc-title', array(
            'default'           => __( 'Table of Contents', 'opulentia' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'toc-title', array(
            'label'   => __( 'Title', 'opulentia' ),
            'section' => 'opulentia_toc',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'toc-accent', array(
            'default'           => 'var(--color-gold)',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'toc-accent', array(
            'label'       => __( 'Accent Color', 'opulentia' ),
            'section'     => 'opulentia_toc',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
        ) );
    }

    public function process_content( $content ) {
        if ( ! Opulentia_get_option( 'toc-enable', true ) ) {
            return $content;
        }
        if ( ! is_singular( 'post' ) ) {
            return $content;
        }
        if ( ! Opulentia_get_option( 'toc-auto-insert', true ) ) {
            return $content;
        }

        $levels = array_map( 'trim', explode( ',', Opulentia_get_option( 'toc-heading-levels', 'h2,h3' ) ) );
        $tags   = implode( '|', $levels );
        if ( empty( $tags ) ) {
            return $content;
        }

        $pattern = '/<(' . $tags . ')([^>]*)>(.*?)<\/\1>/i';
        if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
            return $content;
        }

        $toc_items = array();
        $heading_id_base = 'op-toc-';
        $index = 0;

        foreach ( $matches as $match ) {
            $tag     = $match[1];
            $attrs   = $match[2];
            $heading = strip_tags( $match[3] );
            $id      = $heading_id_base . $index;

            $has_id = preg_match( '/id=(["\'])(.*?)\1/i', $attrs, $id_match );
            if ( $has_id ) {
                $id = $id_match[2];
            } else {
                $new_attrs = preg_replace( '/\/?$/', ' id="' . esc_attr( $id ) . '"', trim( $attrs ) );
                $new_tag    = '<' . $tag . ' ' . $new_attrs . '>' . $match[3] . '</' . $tag . '>';
                $content    = str_replace( $match[0], $new_tag, $content );
            }

            $toc_items[] = array(
                'id'    => $id,
                'tag'   => $tag,
                'title' => $heading,
            );
            $index++;
        }

        if ( empty( $toc_items ) ) {
            return $content;
        }

        $toc_title = Opulentia_get_option( 'toc-title', __( 'Table of Contents', 'opulentia' ) );
        $position  = Opulentia_get_option( 'toc-position', 'inline' );
        $toc_html  = $this->build_toc_html( $toc_items, $toc_title, $position );

        if ( 'floating' === $position || 'sidebar' === $position ) {
            add_action( 'wp_footer', function() use ( $toc_html ) {
                echo $toc_html;
            }, 5 );
            return $content;
        }

        return $toc_html . $content;
    }

    private function build_toc_html( $items, $title, $position ) {
        $levels       = array_map( 'trim', explode( ',', Opulentia_get_option( 'toc-heading-levels', 'h2,h3' ) ) );
        $active_level = $levels[0];

        $html = '<nav class="op-toc op-toc--' . esc_attr( $position ) . '" role="navigation" aria-label="' . esc_attr( $title ) . '">';
        $html .= '<div class="op-toc__header">';
        $html .= '<span class="op-toc__title">' . esc_html( $title ) . '</span>';
        $html .= '<button class="op-toc__toggle" aria-label="' . esc_attr__( 'Toggle table of contents', 'opulentia' ) . '">';
        $html .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg>';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '<ul class="op-toc__list">';

        foreach ( $items as $item ) {
            $is_active = $item['tag'] === $active_level;
            $indent    = ! $is_active ? ' op-toc__item--sub' : '';
            $html .= '<li class="op-toc__item' . $indent . '">';
            $html .= '<a href="#' . esc_attr( $item['id'] ) . '" class="op-toc__link">' . esc_html( $item['title'] ) . '</a>';
            $html .= '</li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }

    public function inline_js() {
        if ( ! Opulentia_get_option( 'toc-enable', true ) ) {
            return;
        }

        wp_add_inline_script( 'opulentia-custom', '
        (function() {
            var toc = document.querySelector(".op-toc");
            if (toc) {
                var toggle = toc.querySelector(".op-toc__toggle");
                var list = toc.querySelector(".op-toc__list");
                if (toggle && list) {
                    toggle.addEventListener("click", function() {
                        list.classList.toggle("op-toc__list--collapsed");
                        toc.classList.toggle("op-toc--collapsed");
                    });
                }
                var links = toc.querySelectorAll(".op-toc__link");
                links.forEach(function(link) {
                    link.addEventListener("click", function(e) {
                        e.preventDefault();
                        var target = document.querySelector(this.getAttribute("href"));
                        if (target) {
                            var offset = 100;
                            var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                            window.scrollTo({ top: top, behavior: "smooth" });
                        }
                    });
                });
                if (toc.classList.contains("op-toc--floating")) {
                    var linksF = toc.querySelectorAll(".op-toc__link");
                    var headings = [];
                    linksF.forEach(function(l) {
                        var h = document.querySelector(l.getAttribute("href"));
                        if (h) headings.push(h);
                    });
                    if (headings.length) {
                        window.addEventListener("scroll", function() {
                            var current = "";
                            headings.forEach(function(h) {
                                var box = h.getBoundingClientRect();
                                if (box.top < 200) current = h.getAttribute("id");
                            });
                            linksF.forEach(function(l) {
                                l.parentElement.classList.remove("op-toc__item--current");
                                if (l.getAttribute("href") === "#" + current) {
                                    l.parentElement.classList.add("op-toc__item--current");
                                }
                            });
                        });
                    }
                }
            }
        })();
        ' );
    }

    public function inline_css() {
        if ( ! Opulentia_get_option( 'toc-enable', true ) ) {
            return;
        }

        $accent = Opulentia_get_option( 'toc-accent', 'var(--color-gold)' );

        $css = '
        .op-toc {
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        .op-toc__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .op-toc__title {
            font-family: var(--font-heading);
            font-size: 1rem;
            color: ' . $accent . ';
            font-weight: 600;
        }
        .op-toc__toggle {
            background: none;
            border: none;
            color: var(--color-text-muted);
            cursor: pointer;
            padding: 4px;
            transition: transform 0.2s ease;
        }
        .op-toc--collapsed .op-toc__toggle {
            transform: rotate(180deg);
        }
        .op-toc__list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .op-toc__list--collapsed {
            display: none;
        }
        .op-toc__item {
            margin-bottom: 6px;
        }
        .op-toc__item--sub {
            padding-left: 16px;
        }
        .op-toc__item--sub .op-toc__link {
            font-size: 0.85rem;
        }
        .op-toc__link {
            color: var(--color-text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            display: inline-block;
            padding: 2px 0;
        }
        .op-toc__link:hover,
        .op-toc__item--current .op-toc__link {
            color: ' . $accent . ';
        }
        .op-toc--floating {
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            z-index: 999;
            max-width: 260px;
            max-height: 60vh;
            overflow-y: auto;
            padding: 16px;
            margin: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .op-toc--floating .op-toc__link {
            font-size: 0.8rem;
        }
        .op-toc--sidebar {
            margin: 0;
        }
        @media (max-width: 768px) {
            .op-toc--floating {
                right: 10px;
                max-width: 200px;
                font-size: 0.8rem;
            }
            .op-toc--sidebar {
                position: static;
                margin: 24px 0;
            }
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

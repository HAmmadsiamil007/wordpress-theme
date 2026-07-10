<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Scroll_Reveal {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 100 );
        add_shortcode( 'op_reveal', array( $this, 'render_shortcode' ) );
    }

    private function is_enabled() {
        return (bool) get_theme_mod( 'op_scroll_reveal_enable', true );
    }

    public function customize_register( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_scroll_reveal', array(
            'title'    => __( 'Scroll Reveal', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 210,
        ) );

        $wp_customize->add_setting( 'op_scroll_reveal_enable', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_scroll_reveal_enable', array(
            'label'   => __( 'Enable Scroll Reveal', 'opulentia' ),
            'section' => 'opulentia_scroll_reveal',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'op_scroll_reveal_effect', array(
            'default'           => 'fade-up',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_scroll_reveal_effect', array(
            'label'   => __( 'Default Effect', 'opulentia' ),
            'section' => 'opulentia_scroll_reveal',
            'type'    => 'select',
            'choices' => array(
                'fade-up'    => __( 'Fade Up', 'opulentia' ),
                'fade-down'  => __( 'Fade Down', 'opulentia' ),
                'fade-left'  => __( 'Fade Left', 'opulentia' ),
                'fade-right' => __( 'Fade Right', 'opulentia' ),
                'zoom-in'    => __( 'Zoom In', 'opulentia' ),
                'flip-up'    => __( 'Flip Up', 'opulentia' ),
                'flip-x'     => __( 'Flip X', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'op_scroll_reveal_duration', array(
            'default'           => 600,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_scroll_reveal_duration', array(
            'label'       => __( 'Default Duration (ms)', 'opulentia' ),
            'section'     => 'opulentia_scroll_reveal',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 300,
                'max'  => 2000,
                'step' => 100,
            ),
        ) );

        $wp_customize->add_setting( 'op_scroll_reveal_auto_content', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_scroll_reveal_auto_content', array(
            'label'   => __( 'Auto-Reveal Post Content', 'opulentia' ),
            'description' => __( 'Automatically apply reveal to images and headings in post content.', 'opulentia' ),
            'section' => 'opulentia_scroll_reveal',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'op_scroll_reveal_mobile', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_scroll_reveal_mobile', array(
            'label'   => __( 'Enable on Mobile', 'opulentia' ),
            'section' => 'opulentia_scroll_reveal',
            'type'    => 'checkbox',
        ) );
    }

    public function enqueue_assets() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $default_effect  = get_theme_mod( 'op_scroll_reveal_effect', 'fade-up' );
        $default_duration = (int) get_theme_mod( 'op_scroll_reveal_duration', 600 );
        $auto_content     = (bool) get_theme_mod( 'op_scroll_reveal_auto_content', true );
        $enable_mobile    = (bool) get_theme_mod( 'op_scroll_reveal_mobile', true );

        $css = '
.op-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
    transition-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.op-reveal.op-revealed {
    opacity: 1;
    transform: translateY(0);
}
.op-reveal[data-effect="fade-up"] { transform: translateY(30px); }
.op-reveal[data-effect="fade-down"] { transform: translateY(-30px); }
.op-reveal[data-effect="fade-left"] { transform: translateX(-30px); }
.op-reveal[data-effect="fade-right"] { transform: translateX(30px); }
.op-reveal[data-effect="zoom-in"] { transform: scale(0.9); }
.op-reveal[data-effect="flip-up"] { transform: perspective(600px) rotateX(15deg); }
.op-reveal[data-effect="flip-x"] { transform: perspective(600px) rotateY(15deg); }
';

        if ( ! $enable_mobile ) {
            $css .= '
@media (max-width: 768px) {
    .op-reveal { opacity: 1; transform: none !important; }
}
';
        }

        $css .= '
@media (prefers-reduced-motion: reduce) {
    .op-reveal { opacity: 1; transform: none !important; transition: none !important; }
}
';

        wp_add_inline_style( 'opulentia-theme', $css );

        $js = '
document.addEventListener("DOMContentLoaded", function() {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    var mobileDisabled = ' . ( $enable_mobile ? 'false' : 'true' ) . ';
    if (mobileDisabled && window.innerWidth < 768) return;
    var defaultEffect = "' . esc_js( $default_effect ) . '";
    var defaultDuration = ' . (int) $default_duration . ';
    var autoContent = ' . ( $auto_content ? 'true' : 'false' ) . ';
    var targets = document.querySelectorAll(".op-reveal");
    if (autoContent) {
        var content = document.querySelector(".entry-content");
        if (content) {
            content.querySelectorAll("img, h2, h3").forEach(function(el) {
                el.classList.add("op-reveal");
            });
            content.querySelectorAll("img").forEach(function(el, i) {
                el.setAttribute("data-delay", i * 100);
            });
        }
    }
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var effect = el.getAttribute("data-effect") || defaultEffect;
            var delay = parseInt(el.getAttribute("data-delay")) || 0;
            var duration = parseInt(el.getAttribute("data-duration")) || defaultDuration;
            el.style.transitionDuration = duration + "ms";
            el.style.transitionDelay = delay + "ms";
            el.setAttribute("data-effect", effect);
            el.classList.add("op-revealed");
            observer.unobserve(el);
        });
    }, { rootMargin: "0px 0px -50px 0px", threshold: 0.1 });
    targets.forEach(function(el) {
        observer.observe(el);
    });
});
';

        wp_add_inline_script( 'opulentia-navigation', $js );
    }

    public function render_shortcode( $atts, $content = null ) {
        if ( ! $this->is_enabled() ) {
            return $content;
        }

        $atts = shortcode_atts( array(
            'effect'   => '',
            'delay'    => 0,
            'duration' => 0,
        ), $atts, 'op_reveal' );

        $effect   = sanitize_text_field( $atts['effect'] );
        $delay    = min( 2000, max( 0, (int) $atts['delay'] ) );
        $duration = min( 2000, max( 300, (int) $atts['duration'] ) );

        $data = '';
        if ( ! empty( $effect ) ) {
            $data .= ' data-effect="' . esc_attr( $effect ) . '"';
        }
        if ( $delay > 0 ) {
            $data .= ' data-delay="' . esc_attr( $delay ) . '"';
        }
        if ( $duration > 0 ) {
            $data .= ' data-duration="' . esc_attr( $duration ) . '"';
        }

        return '<div class="op-reveal"' . $data . '>' . do_shortcode( $content ) . '</div>';
    }
}

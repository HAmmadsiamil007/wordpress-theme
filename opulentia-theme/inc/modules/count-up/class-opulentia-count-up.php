<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Count_Up {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 100 );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_shortcode( 'op_countup', array( $this, 'render_shortcode' ) );
	}

	private function is_enabled() {
		return (bool) get_theme_mod( 'op_countup_enable', true );
	}

	public function enqueue_assets() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$default_duration = (int) get_theme_mod( 'op_countup_duration', 2000 );

		$css = '
.op-countup-wrap {
    text-align: center;
    padding: 30px 20px;
}
.op-countup-icon {
    margin-bottom: 16px;
}
.op-countup-icon svg {
    width: 48px;
    height: 48px;
    color: var(--color-gold, #c9a96e);
}
.op-countup-number {
    font-family: var(--font-heading, "Playfair Display");
    font-size: 3rem;
    font-weight: 700;
    color: var(--color-gold, #c9a96e);
    line-height: 1.2;
}
.op-countup {
    font-variant-numeric: tabular-nums;
}
.op-countup-prefix,
.op-countup-suffix {
    font-size: 1.5rem;
    color: var(--color-gold, #c9a96e);
}
.op-countup-label {
    font-family: var(--font-body, Inter);
    font-size: 0.9rem;
    color: var(--color-text-muted, #999);
    margin-top: 8px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
@media (prefers-reduced-motion: reduce) {
    .op-countup { transition: none; }
}
';

		wp_add_inline_style( 'opulentia-theme', $css );

		$js = '
(function() {
    var counters = document.querySelectorAll(".op-countup");
    if (!counters.length) return;
    var defaultDuration = ' . (int) $default_duration . ';
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                observer.unobserve(el);
                var target = parseFloat(el.getAttribute("data-target")) || 0;
                var duration = parseInt(el.getAttribute("data-duration")) || defaultDuration;
                var decimals = parseInt(el.getAttribute("data-decimals")) || 0;
                var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
                if (reduced) {
                    el.textContent = formatNumber(target, decimals);
                    return;
                }
                var startTime = null;
                function step(timestamp) {
                    if (!startTime) startTime = timestamp;
                    var progress = Math.min((timestamp - startTime) / duration, 1);
                    var eased = 1 - (1 - progress) * (1 - progress);
                    var current = target * eased;
                    el.textContent = formatNumber(current, decimals);
                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                }
                requestAnimationFrame(step);
            }
        });
    }, { threshold: 0.3 });
    counters.forEach(function(el) { observer.observe(el); });
    function formatNumber(num, decimals) {
        return num.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
})();
';

		wp_add_inline_script( 'opulentia-navigation', $js );
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_count_up',
			array(
				'title'    => __( 'Count-Up Numbers', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 215,
			)
		);

		$wp_customize->add_setting(
			'op_countup_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_countup_enable',
			array(
				'label'   => __( 'Enable Count-Up Numbers', 'opulentia' ),
				'section' => 'opulentia_count_up',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_countup_duration',
			array(
				'default'           => 2000,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_countup_duration',
			array(
				'label'       => __( 'Default Animation Duration (ms)', 'opulentia' ),
				'section'     => 'opulentia_count_up',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 500,
					'max'  => 5000,
					'step' => 100,
				),
			)
		);
	}

	public function render_shortcode( $atts ) {
		if ( ! $this->is_enabled() ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'number'   => 0,
				'prefix'   => '',
				'suffix'   => '',
				'label'    => '',
				'duration' => 0,
				'decimals' => 0,
				'icon'     => '',
			),
			$atts,
			'op_countup'
		);

		$number   = floatval( $atts['number'] );
		$prefix   = sanitize_text_field( $atts['prefix'] );
		$suffix   = sanitize_text_field( $atts['suffix'] );
		$label    = sanitize_text_field( $atts['label'] );
		$duration = min( 5000, max( 500, (int) $atts['duration'] ) );
		$decimals = min( 10, max( 0, (int) $atts['decimals'] ) );
		$icon     = sanitize_text_field( $atts['icon'] );

		$output = '<div class="op-countup-wrap">';

		if ( ! empty( $icon ) ) {
			$output .= '<div class="op-countup-icon">';

			$icons = array(
				'users' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
				'star'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
				'cup'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>',
				'globe' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
				'check' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
			);

			if ( isset( $icons[ $icon ] ) ) {
				$output .= $icons[ $icon ];
			} else {
				$output .= $icons['check'];
			}

			$output .= '</div>';
		}

		$output .= '<div class="op-countup-number">';

		if ( ! empty( $prefix ) ) {
			$output .= '<span class="op-countup-prefix">' . esc_html( $prefix ) . '</span>';
		}

		$output .= '<span class="op-countup" data-target="' . esc_attr( $number ) . '" data-duration="' . esc_attr( $duration ) . '" data-decimals="' . esc_attr( $decimals ) . '">0</span>';

		if ( ! empty( $suffix ) ) {
			$output .= '<span class="op-countup-suffix">' . esc_html( $suffix ) . '</span>';
		}

		$output .= '</div>';

		if ( ! empty( $label ) ) {
			$output .= '<div class="op-countup-label">' . esc_html( $label ) . '</div>';
		}

		$output .= '</div>';

		return $output;
	}
}

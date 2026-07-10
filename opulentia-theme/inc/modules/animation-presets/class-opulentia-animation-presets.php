<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Animation_Presets {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	private function is_enabled() {
		return (bool) get_theme_mod( 'op_animations_enable', true );
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_panel(
			'opulentia_animations',
			array(
				'title'       => __( 'Animations', 'opulentia' ),
				'description' => __( 'GSAP animation presets for scroll reveals, parallax, counters, and text effects.', 'opulentia' ),
				'priority'    => 35,
			)
		);

		// ── Global Toggle ──
		$wp_customize->add_section(
			'op_anim_global',
			array(
				'title'    => __( 'Global Settings', 'opulentia' ),
				'panel'    => 'opulentia_animations',
				'priority' => 1,
			)
		);

		$wp_customize->add_setting(
			'op_animations_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_animations_enable',
			array(
				'label'       => __( 'Enable Animations', 'opulentia' ),
				'description' => __( 'Globally enable or disable all GSAP animations.', 'opulentia' ),
				'section'     => 'op_anim_global',
				'type'        => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_anim_ease',
			array(
				'default'           => 'power3.out',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_ease',
			array(
				'label'   => __( 'Default Easing', 'opulentia' ),
				'section' => 'op_anim_global',
				'type'    => 'select',
				'choices' => array(
					'power1.out'  => __( 'Power 1 Out', 'opulentia' ),
					'power2.out'  => __( 'Power 2 Out', 'opulentia' ),
					'power3.out'  => __( 'Power 3 Out', 'opulentia' ),
					'power4.out'  => __( 'Power 4 Out', 'opulentia' ),
					'expo.out'    => __( 'Expo Out', 'opulentia' ),
					'back.out'    => __( 'Back Out', 'opulentia' ),
					'bounce.out'  => __( 'Bounce Out', 'opulentia' ),
					'elastic.out' => __( 'Elastic Out', 'opulentia' ),
					'none'        => __( 'Linear', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_duration',
			array(
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_duration',
			array(
				'label'       => __( 'Default Duration (seconds)', 'opulentia' ),
				'section'     => 'op_anim_global',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0.3,
					'max'  => 3,
					'step' => 0.1,
				),
			)
		);

		// ── Scroll Reveal ──
		$wp_customize->add_section(
			'op_anim_scroll_reveal',
			array(
				'title'    => __( 'Scroll Reveal', 'opulentia' ),
				'panel'    => 'opulentia_animations',
				'priority' => 10,
			)
		);

		$wp_customize->add_setting(
			'op_anim_reveal_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_reveal_enable',
			array(
				'label'   => __( 'Enable Scroll Reveal', 'opulentia' ),
				'section' => 'op_anim_scroll_reveal',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_anim_reveal_effect',
			array(
				'default'           => 'fade',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_reveal_effect',
			array(
				'label'   => __( 'Default Effect', 'opulentia' ),
				'section' => 'op_anim_scroll_reveal',
				'type'    => 'select',
				'choices' => array(
					'fade'   => __( 'Fade In', 'opulentia' ),
					'slide'  => __( 'Slide In', 'opulentia' ),
					'zoom'   => __( 'Zoom In', 'opulentia' ),
					'rotate' => __( 'Rotate In', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_reveal_direction',
			array(
				'default'           => 'up',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_reveal_direction',
			array(
				'label'   => __( 'Default Direction', 'opulentia' ),
				'section' => 'op_anim_scroll_reveal',
				'type'    => 'select',
				'choices' => array(
					'up'    => __( 'Up', 'opulentia' ),
					'down'  => __( 'Down', 'opulentia' ),
					'left'  => __( 'Left', 'opulentia' ),
					'right' => __( 'Right', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_reveal_distance',
			array(
				'default'           => 50,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_reveal_distance',
			array(
				'label'       => __( 'Distance (px)', 'opulentia' ),
				'section'     => 'op_anim_scroll_reveal',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 500,
					'step' => 5,
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_reveal_duration',
			array(
				'default'           => 0.8,
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_reveal_duration',
			array(
				'label'       => __( 'Duration (seconds)', 'opulentia' ),
				'section'     => 'op_anim_scroll_reveal',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0.2,
					'max'  => 3,
					'step' => 0.1,
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_reveal_delay',
			array(
				'default'           => 0,
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_reveal_delay',
			array(
				'label'       => __( 'Delay (seconds)', 'opulentia' ),
				'section'     => 'op_anim_scroll_reveal',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 3,
					'step' => 0.1,
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_reveal_trigger',
			array(
				'default'           => 'top 85%',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_reveal_trigger',
			array(
				'label'   => __( 'Trigger Point', 'opulentia' ),
				'section' => 'op_anim_scroll_reveal',
				'type'    => 'select',
				'choices' => array(
					'top 90%'       => __( 'Early (top 90%)', 'opulentia' ),
					'top 85%'       => __( 'Normal (top 85%)', 'opulentia' ),
					'top 80%'       => __( 'Late (top 80%)', 'opulentia' ),
					'center center' => __( 'Center', 'opulentia' ),
				),
			)
		);

		// ── Parallax ──
		$wp_customize->add_section(
			'op_anim_parallax',
			array(
				'title'    => __( 'Parallax', 'opulentia' ),
				'panel'    => 'opulentia_animations',
				'priority' => 20,
			)
		);

		$wp_customize->add_setting(
			'op_anim_parallax_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_parallax_enable',
			array(
				'label'   => __( 'Enable Parallax', 'opulentia' ),
				'section' => 'op_anim_parallax',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_anim_parallax_speed',
			array(
				'default'           => 0.5,
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_parallax_speed',
			array(
				'label'       => __( 'Speed (0.1–1.0)', 'opulentia' ),
				'section'     => 'op_anim_parallax',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0.1,
					'max'  => 1,
					'step' => 0.1,
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_parallax_direction',
			array(
				'default'           => 'up',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_parallax_direction',
			array(
				'label'   => __( 'Direction', 'opulentia' ),
				'section' => 'op_anim_parallax',
				'type'    => 'select',
				'choices' => array(
					'up'   => __( 'Up', 'opulentia' ),
					'down' => __( 'Down', 'opulentia' ),
				),
			)
		);

		// ── Stagger ──
		$wp_customize->add_section(
			'op_anim_stagger',
			array(
				'title'    => __( 'Stagger', 'opulentia' ),
				'panel'    => 'opulentia_animations',
				'priority' => 30,
			)
		);

		$wp_customize->add_setting(
			'op_anim_stagger_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_stagger_enable',
			array(
				'label'   => __( 'Enable Stagger', 'opulentia' ),
				'section' => 'op_anim_stagger',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_anim_stagger_delay',
			array(
				'default'           => 0.1,
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_stagger_delay',
			array(
				'label'       => __( 'Stagger Delay (seconds)', 'opulentia' ),
				'section'     => 'op_anim_stagger',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0.02,
					'max'  => 0.3,
					'step' => 0.01,
				),
			)
		);

		$wp_customize->add_setting(
			'op_anim_stagger_axis',
			array(
				'default'           => 'y',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_stagger_axis',
			array(
				'label'   => __( 'Axis', 'opulentia' ),
				'section' => 'op_anim_stagger',
				'type'    => 'select',
				'choices' => array(
					'y' => __( 'Vertical (Y)', 'opulentia' ),
					'x' => __( 'Horizontal (X)', 'opulentia' ),
				),
			)
		);

		// ── Counter ──
		$wp_customize->add_section(
			'op_anim_counter',
			array(
				'title'    => __( 'Counter Animation', 'opulentia' ),
				'panel'    => 'opulentia_animations',
				'priority' => 40,
			)
		);

		$wp_customize->add_setting(
			'op_anim_counter_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_counter_enable',
			array(
				'label'   => __( 'Enable Counter Animation', 'opulentia' ),
				'section' => 'op_anim_counter',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_anim_counter_duration',
			array(
				'default'           => 2000,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_counter_duration',
			array(
				'label'       => __( 'Duration (ms)', 'opulentia' ),
				'section'     => 'op_anim_counter',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 500,
					'max'  => 6000,
					'step' => 100,
				),
			)
		);

		// ── Text Split ──
		$wp_customize->add_section(
			'op_anim_text',
			array(
				'title'    => __( 'Text Split', 'opulentia' ),
				'panel'    => 'opulentia_animations',
				'priority' => 50,
			)
		);

		$wp_customize->add_setting(
			'op_anim_text_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_text_enable',
			array(
				'label'   => __( 'Enable Text Split', 'opulentia' ),
				'section' => 'op_anim_text',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_anim_text_type',
			array(
				'default'           => 'words',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_anim_text_type',
			array(
				'label'   => __( 'Split Type', 'opulentia' ),
				'section' => 'op_anim_text',
				'type'    => 'select',
				'choices' => array(
					'words' => __( 'Words', 'opulentia' ),
					'chars' => __( 'Characters', 'opulentia' ),
				),
			)
		);
	}

	public function enqueue_scripts() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$deps = array( 'gsap-core', 'gsap-scrolltrigger' );

		wp_enqueue_script(
			'opulentia-animations',
			Opulentia_URI . '/js/animations.js',
			$deps,
			Opulentia_VERSION,
			true
		);

		wp_localize_script(
			'opulentia-animations',
			'OpulentiaAnim',
			array(
				'reveal'   => array(
					'enable'    => (bool) get_theme_mod( 'op_anim_reveal_enable', true ),
					'effect'    => get_theme_mod( 'op_anim_reveal_effect', 'fade' ),
					'direction' => get_theme_mod( 'op_anim_reveal_direction', 'up' ),
					'distance'  => (int) get_theme_mod( 'op_anim_reveal_distance', 50 ),
					'duration'  => (float) get_theme_mod( 'op_anim_reveal_duration', 0.8 ),
					'delay'     => (float) get_theme_mod( 'op_anim_reveal_delay', 0 ),
					'trigger'   => get_theme_mod( 'op_anim_reveal_trigger', 'top 85%' ),
				),
				'parallax' => array(
					'enable'    => (bool) get_theme_mod( 'op_anim_parallax_enable', true ),
					'speed'     => (float) get_theme_mod( 'op_anim_parallax_speed', 0.5 ),
					'direction' => get_theme_mod( 'op_anim_parallax_direction', 'up' ),
				),
				'stagger'  => array(
					'enable' => (bool) get_theme_mod( 'op_anim_stagger_enable', true ),
					'delay'  => (float) get_theme_mod( 'op_anim_stagger_delay', 0.1 ),
					'axis'   => get_theme_mod( 'op_anim_stagger_axis', 'y' ),
				),
				'counter'  => array(
					'enable'   => (bool) get_theme_mod( 'op_anim_counter_enable', true ),
					'duration' => (int) get_theme_mod( 'op_anim_counter_duration', 2000 ),
				),
				'text'     => array(
					'enable' => (bool) get_theme_mod( 'op_anim_text_enable', true ),
					'type'   => get_theme_mod( 'op_anim_text_type', 'words' ),
				),
				'ease'     => get_theme_mod( 'op_anim_ease', 'power3.out' ),
				'duration' => (float) get_theme_mod( 'op_anim_duration', 1 ),
			)
		);
	}

	public function dynamic_css( $css ) {
		if ( ! $this->is_enabled() ) {
			return $css;
		}

		$reveal_distance = (int) get_theme_mod( 'op_anim_reveal_distance', 50 );

		$css .= '
[data-op-reveal] {
    opacity: 0;
}
[data-op-reveal].op-reveal-visible {
    opacity: 1;
}
[data-op-parallax] {
    will-change: transform;
}
.op-counter__value {
    display: inline-block;
}
.op-text-split__word,
.op-text-split__char {
    display: inline-block;
    opacity: 0;
}
';

		return $css;
	}

	public function add_meta_box() {
		$post_types = apply_filters( 'opulentia_animation_meta_post_types', array( 'post', 'page' ) );

		foreach ( $post_types as $pt ) {
			add_meta_box(
				'opulentia_animation_meta',
				__( 'Animation Override', 'opulentia' ),
				array( $this, 'render_meta_box' ),
				$pt,
				'side',
				'default'
			);
		}
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'opulentia_animation_meta', 'opulentia_animation_meta_nonce' );

		$disable = get_post_meta( $post->ID, '_op_disable_animations', true );
		$reveal  = get_post_meta( $post->ID, '_op_reveal_override', true );
		$effect  = get_post_meta( $post->ID, '_op_reveal_effect', true );
		?>
		<p>
			<label>
				<input type="checkbox" name="_op_disable_animations" value="1" <?php checked( $disable, '1' ); ?>>
				<?php esc_html_e( 'Disable animations on this page', 'opulentia' ); ?>
			</label>
		</p>
		<p>
			<label for="_op_reveal_override"><?php esc_html_e( 'Reveal Override', 'opulentia' ); ?></label>
			<select name="_op_reveal_override" id="_op_reveal_override" style="width:100%">
				<option value="" <?php selected( $reveal, '' ); ?>><?php esc_html_e( 'Default', 'opulentia' ); ?></option>
				<option value="none" <?php selected( $reveal, 'none' ); ?>><?php esc_html_e( 'None', 'opulentia' ); ?></option>
				<option value="fade" <?php selected( $reveal, 'fade' ); ?>><?php esc_html_e( 'Fade In', 'opulentia' ); ?></option>
				<option value="slide" <?php selected( $reveal, 'slide' ); ?>><?php esc_html_e( 'Slide In', 'opulentia' ); ?></option>
				<option value="zoom" <?php selected( $reveal, 'zoom' ); ?>><?php esc_html_e( 'Zoom In', 'opulentia' ); ?></option>
				<option value="rotate" <?php selected( $reveal, 'rotate' ); ?>><?php esc_html_e( 'Rotate In', 'opulentia' ); ?></option>
			</select>
		</p>
		<p>
			<label for="_op_reveal_effect"><?php esc_html_e( 'Effect Type', 'opulentia' ); ?></label>
			<select name="_op_reveal_effect" id="_op_reveal_effect" style="width:100%">
				<option value="" <?php selected( $effect, '' ); ?>><?php esc_html_e( 'Default', 'opulentia' ); ?></option>
				<option value="fade" <?php selected( $effect, 'fade' ); ?>><?php esc_html_e( 'Fade In', 'opulentia' ); ?></option>
				<option value="slide" <?php selected( $effect, 'slide' ); ?>><?php esc_html_e( 'Slide In', 'opulentia' ); ?></option>
				<option value="zoom" <?php selected( $effect, 'zoom' ); ?>><?php esc_html_e( 'Zoom In', 'opulentia' ); ?></option>
				<option value="rotate" <?php selected( $effect, 'rotate' ); ?>><?php esc_html_e( 'Rotate In', 'opulentia' ); ?></option>
			</select>
		</p>
		<?php
	}

	public function save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['opulentia_animation_meta_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['opulentia_animation_meta_nonce'] ) ), 'opulentia_animation_meta' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$disable = isset( $_POST['_op_disable_animations'] ) ? '1' : '';
		update_post_meta( $post_id, '_op_disable_animations', $disable );

		$reveal = isset( $_POST['_op_reveal_override'] ) ? sanitize_text_field( wp_unslash( $_POST['_op_reveal_override'] ) ) : '';
		update_post_meta( $post_id, '_op_reveal_override', $reveal );

		$effect = isset( $_POST['_op_reveal_effect'] ) ? sanitize_text_field( wp_unslash( $_POST['_op_reveal_effect'] ) ) : '';
		update_post_meta( $post_id, '_op_reveal_effect', $effect );
	}

	public function body_class( $classes ) {
		if ( is_singular() ) {
			$disable = get_post_meta( get_the_ID(), '_op_disable_animations', true );
			if ( $disable ) {
				$classes[] = 'op-animations-disabled';
			}
		}
		return $classes;
	}
}

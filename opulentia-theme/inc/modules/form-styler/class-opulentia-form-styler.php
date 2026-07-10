<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Form_Styler {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_panel(
			'opulentia_forms',
			array(
				'title'       => __( 'Forms', 'opulentia' ),
				'description' => __( 'Style inputs, labels, buttons, and messages for Contact Form 7, Gravity Forms, WPForms, and Elementor Forms.', 'opulentia' ),
				'priority'    => 155,
			)
		);

		// ── Input Fields ──
		$wp_customize->add_section(
			'op_forms_inputs',
			array(
				'title' => __( 'Input Fields', 'opulentia' ),
				'panel' => 'opulentia_forms',
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_bg',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_input_bg',
				array(
					'label'   => __( 'Background', 'opulentia' ),
					'section' => 'op_forms_inputs',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_border',
			array(
				'default'           => '#333',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_input_border',
				array(
					'label'   => __( 'Border Color', 'opulentia' ),
					'section' => 'op_forms_inputs',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_border_focus',
			array(
				'default'           => '#c9a96e',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_input_border_focus',
				array(
					'label'   => __( 'Border Color (Focus)', 'opulentia' ),
					'section' => 'op_forms_inputs',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_radius',
			array(
				'default'           => '4px',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_input_radius',
			array(
				'label'       => __( 'Border Radius', 'opulentia' ),
				'section'     => 'op_forms_inputs',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => '4px' ),
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_padding',
			array(
				'default'           => '12px 16px',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_input_padding',
			array(
				'label'       => __( 'Padding', 'opulentia' ),
				'section'     => 'op_forms_inputs',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => '12px 16px' ),
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_text',
			array(
				'default'           => '#f5f5f5',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_input_text',
				array(
					'label'   => __( 'Text Color', 'opulentia' ),
					'section' => 'op_forms_inputs',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_size',
			array(
				'default'           => '16px',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_input_size',
			array(
				'label'       => __( 'Font Size', 'opulentia' ),
				'section'     => 'op_forms_inputs',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => '16px' ),
			)
		);

		$wp_customize->add_setting(
			'op_forms_input_placeholder',
			array(
				'default'           => '#666',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_input_placeholder',
				array(
					'label'   => __( 'Placeholder Color', 'opulentia' ),
					'section' => 'op_forms_inputs',
				)
			)
		);

		// ── Labels ──
		$wp_customize->add_section(
			'op_forms_labels',
			array(
				'title' => __( 'Labels', 'opulentia' ),
				'panel' => 'opulentia_forms',
			)
		);

		$wp_customize->add_setting(
			'op_forms_label_color',
			array(
				'default'           => '#c9a96e',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_label_color',
				array(
					'label'   => __( 'Label Color', 'opulentia' ),
					'section' => 'op_forms_labels',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_label_size',
			array(
				'default'           => '14px',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_label_size',
			array(
				'label'       => __( 'Label Font Size', 'opulentia' ),
				'section'     => 'op_forms_labels',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => '14px' ),
			)
		);

		$wp_customize->add_setting(
			'op_forms_label_weight',
			array(
				'default'           => '600',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_label_weight',
			array(
				'label'   => __( 'Label Font Weight', 'opulentia' ),
				'section' => 'op_forms_labels',
				'type'    => 'select',
				'choices' => array(
					'400' => 'Normal',
					'500' => '500',
					'600' => 'Semi Bold',
					'700' => 'Bold',
				),
			)
		);

		// ── Submit Button ──
		$wp_customize->add_section(
			'op_forms_button',
			array(
				'title' => __( 'Submit Button', 'opulentia' ),
				'panel' => 'opulentia_forms',
			)
		);

		$wp_customize->add_setting(
			'op_forms_btn_bg',
			array(
				'default'           => '#b8860b',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_btn_bg',
				array(
					'label'   => __( 'Background', 'opulentia' ),
					'section' => 'op_forms_button',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_btn_bg_hover',
			array(
				'default'           => '#d4a843',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_btn_bg_hover',
				array(
					'label'   => __( 'Background (Hover)', 'opulentia' ),
					'section' => 'op_forms_button',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_btn_text',
			array(
				'default'           => '#fff',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_btn_text',
				array(
					'label'   => __( 'Text Color', 'opulentia' ),
					'section' => 'op_forms_button',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_btn_radius',
			array(
				'default'           => '4px',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_btn_radius',
			array(
				'label'       => __( 'Border Radius', 'opulentia' ),
				'section'     => 'op_forms_button',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => '4px' ),
			)
		);

		$wp_customize->add_setting(
			'op_forms_btn_padding',
			array(
				'default'           => '14px 32px',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_btn_padding',
			array(
				'label'       => __( 'Padding', 'opulentia' ),
				'section'     => 'op_forms_button',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => '14px 32px' ),
			)
		);

		$wp_customize->add_setting(
			'op_forms_btn_size',
			array(
				'default'           => '16px',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'op_forms_btn_size',
			array(
				'label'       => __( 'Font Size', 'opulentia' ),
				'section'     => 'op_forms_button',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => '16px' ),
			)
		);

		// ── Messages ──
		$wp_customize->add_section(
			'op_forms_messages',
			array(
				'title' => __( 'Messages', 'opulentia' ),
				'panel' => 'opulentia_forms',
			)
		);

		$wp_customize->add_setting(
			'op_forms_error_bg',
			array(
				'default'           => '#2d1a1a',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_error_bg',
				array(
					'label'   => __( 'Error Message Background', 'opulentia' ),
					'section' => 'op_forms_messages',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_error_text',
			array(
				'default'           => '#ff6b6b',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_error_text',
				array(
					'label'   => __( 'Error Message Text', 'opulentia' ),
					'section' => 'op_forms_messages',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_success_bg',
			array(
				'default'           => '#1a2d1a',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_success_bg',
				array(
					'label'   => __( 'Success Message Background', 'opulentia' ),
					'section' => 'op_forms_messages',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_success_text',
			array(
				'default'           => '#4caf50',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_success_text',
				array(
					'label'   => __( 'Success Message Text', 'opulentia' ),
					'section' => 'op_forms_messages',
				)
			)
		);

		// ── Checkbox / Radio ──
		$wp_customize->add_section(
			'op_forms_checks',
			array(
				'title' => __( 'Checkbox & Radio', 'opulentia' ),
				'panel' => 'opulentia_forms',
			)
		);

		$wp_customize->add_setting(
			'op_forms_check_accent',
			array(
				'default'           => '#c9a96e',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_check_accent',
				array(
					'label'   => __( 'Accent Color', 'opulentia' ),
					'section' => 'op_forms_checks',
				)
			)
		);

		$wp_customize->add_setting(
			'op_forms_check_bg',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_forms_check_bg',
				array(
					'label'   => __( 'Background', 'opulentia' ),
					'section' => 'op_forms_checks',
				)
			)
		);
	}

	public function dynamic_css( $css ) {
		$css .= '
input[type="text"],input[type="email"],input[type="url"],input[type="password"],input[type="search"],input[type="number"],input[type="tel"],input[type="date"],input[type="month"],input[type="week"],input[type="time"],input[type="datetime"],input[type="datetime-local"],input[type="color"],textarea,select,
.wpcf7-form-control.wpcf7-text,.wpcf7-form-control.wpcf7-textarea,.wpcf7-form-control.wpcf7-select,
.gform_wrapper input:not([type=submit]):not([type=button]):not([type=image]),.gform_wrapper textarea,.gform_wrapper select,
.wpforms-form input:not([type=submit]):not([type=button]):not([type=radio]):not([type=checkbox]),.wpforms-form textarea,.wpforms-form select,
.elementor-field-group input,.elementor-field-group textarea,.elementor-field-group select{
background:' . get_theme_mod( 'op_forms_input_bg', '#222' ) . ';
border:1px solid ' . get_theme_mod( 'op_forms_input_border', '#333' ) . ';
border-radius:' . get_theme_mod( 'op_forms_input_radius', '4px' ) . ';
padding:' . get_theme_mod( 'op_forms_input_padding', '12px 16px' ) . ';
color:' . get_theme_mod( 'op_forms_input_text', '#f5f5f5' ) . ';
font-size:' . get_theme_mod( 'op_forms_input_size', '16px' ) . ';
width:100%;transition:border-color 0.3s,box-shadow 0.3s
}
input:focus,textarea:focus,select:focus,
.wpcf7-form-control.wpcf7-text:focus,.wpcf7-form-control.wpcf7-textarea:focus,
.gform_wrapper input:focus,.gform_wrapper textarea:focus,.gform_wrapper select:focus,
.wpforms-form input:focus,.wpforms-form textarea:focus,.wpforms-form select:focus,
.elementor-field-group input:focus,.elementor-field-group textarea:focus,.elementor-field-group select:focus{
border-color:' . get_theme_mod( 'op_forms_input_border_focus', '#c9a96e' ) . ';
outline:none;box-shadow:0 0 0 2px rgba(201,169,110,0.15)
}
input::placeholder,textarea::placeholder,.wpcf7-form-control::placeholder{color:' . get_theme_mod( 'op_forms_input_placeholder', '#666' ) . ';opacity:1}
';

		// Labels
		$css .= '
label,.wpcf7-form-control-wrap .wpcf7-not-valid-tip,.gfield_label,.wpforms-field-label,.elementor-field-group label{
color:' . get_theme_mod( 'op_forms_label_color', '#c9a96e' ) . '!important;
font-size:' . get_theme_mod( 'op_forms_label_size', '14px' ) . ';
font-weight:' . get_theme_mod( 'op_forms_label_weight', '600' ) . ';
margin-bottom:6px;display:block
}
';

		// Submit
		$css .= '
input[type="submit"],input[type="button"],button[type="submit"],
.wpcf7-form-control.wpcf7-submit,
.gform_wrapper .gform_footer input[type=submit],
.wpforms-form button[type=submit],
.elementor-field-group .elementor-field-type-submit button{
background:' . get_theme_mod( 'op_forms_btn_bg', '#b8860b' ) . '!important;
color:' . get_theme_mod( 'op_forms_btn_text', '#fff' ) . '!important;
border:none!important;
border-radius:' . get_theme_mod( 'op_forms_btn_radius', '4px' ) . '!important;
padding:' . get_theme_mod( 'op_forms_btn_padding', '14px 32px' ) . '!important;
font-size:' . get_theme_mod( 'op_forms_btn_size', '16px' ) . '!important;
font-weight:600;cursor:pointer;transition:background 0.3s,transform 0.2s;display:inline-block;line-height:1.4
}
input[type="submit"]:hover,input[type="button"]:hover,button[type="submit"]:hover,
.wpcf7-form-control.wpcf7-submit:hover,
.gform_wrapper .gform_footer input[type=submit]:hover,
.wpforms-form button[type="submit"]:hover,
.elementor-field-group .elementor-field-type-submit button:hover{
background:' . get_theme_mod( 'op_forms_btn_bg_hover', '#d4a843' ) . '!important;
transform:translateY(-1px)
}
';

		// Messages
		$css .= '
.wpcf7-response-output,.wpcf7 .wpcf7-response-output,.gform_confirmation_message,.wpforms-confirmation-container,.elementor-message{
background:' . get_theme_mod( 'op_forms_success_bg', '#1a2d1a' ) . '!important;
color:' . get_theme_mod( 'op_forms_success_text', '#4caf50' ) . '!important;
border:1px solid ' . get_theme_mod( 'op_forms_success_text', '#4caf50' ) . '!important;
border-radius:' . get_theme_mod( 'op_forms_input_radius', '4px' ) . ';
padding:12px 16px;margin-top:10px
}
.wpcf7-not-valid-tip,.gfield_error .gfield_label,.wpforms-error,.elementor-message-danger{
color:' . get_theme_mod( 'op_forms_error_text', '#ff6b6b' ) . '!important;
font-size:13px;margin-top:4px
}
.wpcf7-not-valid,.gform_wrapper .gfield_error input,.wpforms-form .wpforms-error-container,.elementor-field-group.elementor-field-error input{
border-color:' . get_theme_mod( 'op_forms_error_text', '#ff6b6b' ) . '!important
}
';

		// Checkboxes / Radios
		$css .= '
input[type="checkbox"],input[type="radio"]{
accent-color:' . get_theme_mod( 'op_forms_check_accent', '#c9a96e' ) . ';
width:18px;height:18px;margin-right:6px;vertical-align:middle
}
.wpcf7-form-control.wpcf7-acceptance .wpcf7-list-item-label,.gform_wrapper .gfield_checkbox label,.gform_wrapper .gfield_radio label,.wpforms-field-checkbox label,.wpforms-field-radio label,.elementor-field-group .elementor-field-subgroup label{
display:inline!important;vertical-align:middle
}
';

		return $css;
	}
}

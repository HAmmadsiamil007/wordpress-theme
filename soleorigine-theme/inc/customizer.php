<?php
/**
 * Customizer Settings for SoleOrigine Theme
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Guard: if the config-driven class is loaded, skip this flat file.
 */
if ( class_exists( 'SoleOrigine_Customizer_Config' ) ) {
    return;
}

/**
 * Add Customizer Settings
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function soleorigine_customize_register( $wp_customize ) {

    /**
     * Hero Section
     */
    $wp_customize->add_section( 'soleorigine_hero', array(
        'title'    => __( 'Hero Section', 'soleorigine' ),
        'priority' => 30,
    ) );

    // Hero Title
    $wp_customize->add_setting( 'hero_title', array(
        'default'           => __( 'SoleOrigine', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'hero_title', array(
        'label'   => __( 'Hero Title', 'soleorigine' ),
        'section' => 'soleorigine_hero',
        'type'    => 'text',
    ) );

    // Hero Subtitle
    $wp_customize->add_setting( 'hero_subtitle', array(
        'default'           => __( 'Premium Italian Footwear', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'hero_subtitle', array(
        'label'   => __( 'Hero Subtitle', 'soleorigine' ),
        'section' => 'soleorigine_hero',
        'type'    => 'text',
    ) );

    // Hero Button 1 Text
    $wp_customize->add_setting( 'hero_button_1_text', array(
        'default'           => __( 'Explore Collection', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'hero_button_1_text', array(
        'label'   => __( 'Button 1 Text', 'soleorigine' ),
        'section' => 'soleorigine_hero',
        'type'    => 'text',
    ) );

    // Hero Button 1 URL
    $wp_customize->add_setting( 'hero_button_1_url', array(
        'default'           => '/collection',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( 'hero_button_1_url', array(
        'label'   => __( 'Button 1 URL', 'soleorigine' ),
        'section' => 'soleorigine_hero',
        'type'    => 'url',
    ) );

    // Hero Button 2 Text
    $wp_customize->add_setting( 'hero_button_2_text', array(
        'default'           => __( 'View Styles', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'hero_button_2_text', array(
        'label'   => __( 'Button 2 Text', 'soleorigine' ),
        'section' => 'soleorigine_hero',
        'type'    => 'text',
    ) );

    // Hero Button 2 URL
    $wp_customize->add_setting( 'hero_button_2_url', array(
        'default'           => '/styles',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( 'hero_button_2_url', array(
        'label'   => __( 'Button 2 URL', 'soleorigine' ),
        'section' => 'soleorigine_hero',
        'type'    => 'url',
    ) );

    // Hero Background Image
    $wp_customize->add_setting( 'hero_background', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_background', array(
        'label'   => __( 'Hero Background Image', 'soleorigine' ),
        'section' => 'soleorigine_hero',
    ) ) );

    /**
     * About Section
     */
    $wp_customize->add_section( 'soleorigine_about', array(
        'title'    => __( 'About Section', 'soleorigine' ),
        'priority' => 35,
    ) );

    // About Title
    $wp_customize->add_setting( 'about_title', array(
        'default'           => __( 'Our Heritage', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'about_title', array(
        'label'   => __( 'About Title', 'soleorigine' ),
        'section' => 'soleorigine_about',
        'type'    => 'text',
    ) );

    // About Subtitle
    $wp_customize->add_setting( 'about_subtitle', array(
        'default'           => __( 'A Legacy of Excellence', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'about_subtitle', array(
        'label'   => __( 'About Subtitle', 'soleorigine' ),
        'section' => 'soleorigine_about',
        'type'    => 'text',
    ) );

    // About Text
    $wp_customize->add_setting( 'about_text', array(
        'default'           => __( 'Born from a passion for exceptional footwear, SoleOrigine represents the pinnacle of Italian craftsmanship. Each pair is meticulously handcrafted using time-honored techniques passed down through generations.', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );

    $wp_customize->add_control( 'about_text', array(
        'label'   => __( 'About Text', 'soleorigine' ),
        'section' => 'soleorigine_about',
        'type'    => 'textarea',
    ) );

    // About Image
    $wp_customize->add_setting( 'about_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'about_image', array(
        'label'   => __( 'About Image', 'soleorigine' ),
        'section' => 'soleorigine_about',
    ) ) );

    /**
     * Collection Section
     */
    $wp_customize->add_section( 'soleorigine_collection', array(
        'title'    => __( 'Collection Section', 'soleorigine' ),
        'priority' => 40,
    ) );

    // Collection Title
    $wp_customize->add_setting( 'collection_title', array(
        'default'           => __( 'Featured Collection', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'collection_title', array(
        'label'   => __( 'Collection Title', 'soleorigine' ),
        'section' => 'soleorigine_collection',
        'type'    => 'text',
    ) );

    // Collection Subtitle
    $wp_customize->add_setting( 'collection_subtitle', array(
        'default'           => __( 'Discover Our Finest Creations', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'collection_subtitle', array(
        'label'   => __( 'Collection Subtitle', 'soleorigine' ),
        'section' => 'soleorigine_collection',
        'type'    => 'text',
    ) );

    // Number of Products
    $wp_customize->add_setting( 'collection_products_count', array(
        'default'           => 8,
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'collection_products_count', array(
        'label'       => __( 'Number of Products', 'soleorigine' ),
        'description' => __( 'Number of products to display in the collection section.', 'soleorigine' ),
        'section'     => 'soleorigine_collection',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 12,
            'step' => 1,
        ),
    ) );

    /**
     * Footer Section
     */
    $wp_customize->add_section( 'soleorigine_footer', array(
        'title'    => __( 'Footer Settings', 'soleorigine' ),
        'priority' => 45,
    ) );

    // Footer Copyright
    $wp_customize->add_setting( 'footer_copyright', array(
        'default'           => __( '&copy; 2026 SoleOrigine. All Rights Reserved.', 'soleorigine' ),
        'sanitize_callback' => 'wp_kses_post',
    ) );

    $wp_customize->add_control( 'footer_copyright', array(
        'label'       => __( 'Footer Copyright Text', 'soleorigine' ),
        'description' => __( 'HTML is allowed.', 'soleorigine' ),
        'section'     => 'soleorigine_footer',
        'type'        => 'textarea',
    ) );

    // Social Links
    $social_links = array(
        'facebook'  => __( 'Facebook URL', 'soleorigine' ),
        'instagram' => __( 'Instagram URL', 'soleorigine' ),
        'twitter'   => __( 'Twitter URL', 'soleorigine' ),
        'youtube'   => __( 'YouTube URL', 'soleorigine' ),
        'pinterest' => __( 'Pinterest URL', 'soleorigine' ),
    );

    foreach ( $social_links as $social => $label ) {
        $wp_customize->add_setting( 'social_' . $social, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );

        $wp_customize->add_control( 'social_' . $social, array(
            'label'   => $label,
            'section' => 'soleorigine_footer',
            'type'    => 'url',
        ) );
    }

    /**
     * Blog Settings
     */
    $wp_customize->add_section( 'soleorigine_blog', array(
        'title'    => __( 'Blog Settings', 'soleorigine' ),
        'priority' => 50,
    ) );

    // Blog Title
    $wp_customize->add_setting( 'blog_title', array(
        'default'           => __( 'The Journal', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'blog_title', array(
        'label'   => __( 'Blog Section Title', 'soleorigine' ),
        'section' => 'soleorigine_blog',
        'type'    => 'text',
    ) );

    // Blog Subtitle
    $wp_customize->add_setting( 'blog_subtitle', array(
        'default'           => __( 'Stories from the World of SoleOrigine', 'soleorigine' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'blog_subtitle', array(
        'label'   => __( 'Blog Section Subtitle', 'soleorigine' ),
        'section' => 'soleorigine_blog',
        'type'    => 'text',
    ) );

    // Posts Per Page
    $wp_customize->add_setting( 'blog_posts_per_page', array(
        'default'           => 6,
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'blog_posts_per_page', array(
        'label'       => __( 'Posts Per Page', 'soleorigine' ),
        'section'     => 'soleorigine_blog',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 12,
            'step' => 1,
        ),
    ) );
}
add_action( 'customize_register', 'soleorigine_customize_register' );

/**
 * Customizer Live Preview
 */
function soleorigine_customize_preview_js() {
    wp_enqueue_script(
        'soleorigine-customizer',
        get_template_directory_uri() . '/js/customizer.js',
        array( 'customize-preview' ),
        '1.0.0',
        true
    );
}
add_action( 'customize_preview_init', 'soleorigine_customize_preview_js' );

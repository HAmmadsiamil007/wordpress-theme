<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Color_Palette {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        add_action( 'wp_ajax_opulentia_generate_palette', array( $this, 'ajax_generate_palette' ) );
        add_action( 'wp_ajax_opulentia_extract_image_colors', array( $this, 'ajax_extract_colors' ) );
        add_action( 'wp_ajax_opulentia_check_contrast', array( $this, 'ajax_check_contrast' ) );
        add_action( 'wp_ajax_opulentia_apply_palette', array( $this, 'ajax_apply_palette' ) );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'controls_enqueue' ) );
    }

    public function controls_enqueue() {
        wp_enqueue_script(
            'opulentia-color-palette',
            Opulentia_URI . '/js/color-palette.js',
            array( 'jquery', 'customize-controls' ),
            Opulentia_VERSION,
            true
        );

        wp_localize_script( 'opulentia-color-palette', 'OpulentiaColorPalette', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'opulentia_palette_nonce' ),
        ) );
    }

    public function customize_register( $wp_customize ) {
        $wp_customize->add_section( 'op_color_palette', array(
            'title'       => __( 'Color Palette Generator', 'opulentia' ),
            'panel'       => 'opulentia_global',
            'priority'    => 2,
        ) );

        $wp_customize->add_setting( 'op_palette_base', array(
            'default'           => '#b8860b',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'op_palette_base', array(
            'label'       => __( 'Base Color', 'opulentia' ),
            'description' => __( 'Choose a base color then click "Generate Palette" below.', 'opulentia' ),
            'section'     => 'op_color_palette',
        ) ) );

        $wp_customize->add_setting( 'op_palette_harmony', array(
            'default'           => 'monochromatic',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( 'op_palette_harmony', array(
            'label'   => __( 'Harmony Rule', 'opulentia' ),
            'section' => 'op_color_palette',
            'type'    => 'select',
            'choices' => array(
                'monochromatic' => __( 'Monochromatic', 'opulentia' ),
                'complementary' => __( 'Complementary', 'opulentia' ),
                'analogous'     => __( 'Analogous', 'opulentia' ),
                'triadic'       => __( 'Triadic', 'opulentia' ),
                'tetradic'      => __( 'Tetradic (Double Complementary)', 'opulentia' ),
            ),
        ) );
    }

    public function ajax_generate_palette() {
        check_ajax_referer( 'opulentia_palette_nonce', 'nonce' );

        $hex     = sanitize_hex_color( wp_unslash( $_POST['base_color'] ) );
        $harmony = sanitize_text_field( wp_unslash( $_POST['harmony'] ) );

        $palette = $this->generate_palette( $hex, $harmony );

        wp_send_json_success( array( 'palette' => $palette ) );
    }

    public function ajax_extract_colors() {
        check_ajax_referer( 'opulentia_palette_nonce', 'nonce' );

        if ( ! isset( $_FILES['image'] ) ) {
            wp_send_json_error( array( 'message' => __( 'No image uploaded.', 'opulentia' ) ) );
        }

        $file = $_FILES['image'];
        $tmp = $file['tmp_name'];
        $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

        if ( ! in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ), true ) ) {
            wp_send_json_error( array( 'message' => __( 'Unsupported image format.', 'opulentia' ) ) );
        }

        $colors = $this->extract_colors_from_image( $tmp );

        wp_send_json_success( array( 'colors' => $colors ) );
    }

    public function ajax_check_contrast() {
        check_ajax_referer( 'opulentia_palette_nonce', 'nonce' );

        $fg = sanitize_hex_color( wp_unslash( $_POST['foreground'] ) );
        $bg = sanitize_hex_color( wp_unslash( $_POST['background'] ) );

        $ratio = $this->contrast_ratio( $fg, $bg );

        wp_send_json_success( array(
            'ratio' => round( $ratio, 2 ),
            'aa'    => $ratio >= 4.5,
            'aaa'   => $ratio >= 7,
            'a18'   => $ratio >= 3,
        ) );
    }

    public function ajax_apply_palette() {
        check_ajax_referer( 'opulentia_palette_nonce', 'nonce' );

        $colors = isset( $_POST['colors'] ) ? json_decode( wp_unslash( $_POST['colors'] ), true ) : array();

        if ( ! is_array( $colors ) || count( $colors ) < 3 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid palette.', 'opulentia' ) ) );
        }

        $mapping = array(
            0 => 'op_accent_color',
            1 => 'op_accent_hover_color',
            2 => 'op_headings_color',
            3 => 'op_headings_hover_color',
            4 => 'op_site_bg_color',
        );

        foreach ( $colors as $i => $color ) {
            if ( isset( $mapping[ $i ] ) ) {
                set_theme_mod( $mapping[ $i ], sanitize_hex_color( $color ) );
            }
        }

        wp_send_json_success( array( 'message' => __( 'Palette applied! Please save & publish.', 'opulentia' ) ) );
    }

    private function generate_palette( $hex, $harmony ) {
        $hsl = $this->hex_to_hsl( $hex );
        $h   = $hsl[0];
        $s   = $hsl[1];
        $l   = $hsl[2];

        switch ( $harmony ) {
            case 'complementary':
                $hues = array( $h, $h, ( $h + 180 ) % 360, ( $h + 180 ) % 360, ( $h + 180 ) % 360 );
                break;
            case 'analogous':
                $hues = array( $h, ( $h + 30 ) % 360, ( $h + 60 ) % 360, ( $h + 90 ) % 360, $h );
                break;
            case 'triadic':
                $hues = array( $h, $h, ( $h + 120 ) % 360, ( $h + 240 ) % 360, ( $h + 240 ) % 360 );
                break;
            case 'tetradic':
                $hues = array( $h, ( $h + 60 ) % 360, ( $h + 180 ) % 360, ( $h + 240 ) % 360, $h );
                break;
            default:
                $hues = array( $h, $h, $h, $h, $h );
                break;
        }

        $luminances = array( $l, $l + 15, $l - 15, $l + 25, $l - 25 );
        $saturations = array( $s, max( $s - 10, 0 ), min( $s + 10, 100 ), max( $s - 20, 0 ), min( $s + 20, 100 ) );

        $palette = array();
        for ( $i = 0; $i < 5; $i++ ) {
            $cl = max( 0, min( 100, $luminances[ $i ] ) );
            $cs = max( 0, min( 100, $saturations[ $i ] ) );
            $palette[] = $this->hsl_to_hex( $hues[ $i ], $cs, $cl );
        }

        return $palette;
    }

    private function hex_to_hsl( $hex ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
        $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
        $b = hexdec( substr( $hex, 4, 2 ) ) / 255;

        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $l = ( $max + $min ) / 2;

        if ( $max === $min ) {
            return array( 0, 0, round( $l * 100 ) );
        }

        $d = $max - $min;
        $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );

        switch ( $max ) {
            case $r:
                $h = ( ( $g - $b ) / $d + ( $g < $b ? 6 : 0 ) ) / 6;
                break;
            case $g:
                $h = ( ( $b - $r ) / $d + 2 ) / 6;
                break;
            default:
                $h = ( ( $r - $g ) / $d + 4 ) / 6;
                break;
        }

        return array( round( $h * 360 ), round( $s * 100 ), round( $l * 100 ) );
    }

    private function hsl_to_hex( $h, $s, $l ) {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ( 0 === $s ) {
            $val = round( $l * 255 );
            return sprintf( '#%02x%02x%02x', $val, $val, $val );
        }

        $hue2rgb = function ( $p, $q, $t ) {
            if ( $t < 0 ) { $t += 1; }
            if ( $t > 1 ) { $t -= 1; }
            if ( $t < 1 / 6 ) { return $p + ( $q - $p ) * 6 * $t; }
            if ( $t < 1 / 2 ) { return $q; }
            if ( $t < 2 / 3 ) { return $p + ( $q - $p ) * ( 2 / 3 - $t ) * 6; }
            return $p;
        };

        $q = $l < 0.5 ? $l * ( 1 + $s ) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        return sprintf( '#%02x%02x%02x',
            round( $hue2rgb( $p, $q, $h + 1 / 3 ) * 255 ),
            round( $hue2rgb( $p, $q, $h ) * 255 ),
            round( $hue2rgb( $p, $q, $h - 1 / 3 ) * 255 )
        );
    }

    private function extract_colors_from_image( $path ) {
        $image = false;
        $info = getimagesize( $path );

        if ( ! $info ) {
            return array( '#333333', '#666666', '#999999', '#bbbbbb', '#dddddd' );
        }

        switch ( $info[2] ) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg( $path );
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng( $path );
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif( $path );
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp( $path );
                break;
        }

        if ( ! $image ) {
            return array( '#333333', '#666666', '#999999', '#bbbbbb', '#dddddd' );
        }

        $width = imagesx( $image );
        $height = imagesy( $image );
        $color_counts = array();

        $step = max( 1, (int) ( $width * $height / 400 ) );

        for ( $x = 0; $x < $width; $x += $step ) {
            for ( $y = 0; $y < $height; $y += $step ) {
                $rgb = imagecolorat( $image, $x, $y );
                $r = ( $rgb >> 16 ) & 0xFF;
                $g = ( $rgb >> 8 ) & 0xFF;
                $b = $rgb & 0xFF;

                $quantized = round( $r / 32 ) * 32 . ',' . round( $g / 32 ) * 32 . ',' . round( $b / 32 ) * 32;
                if ( ! isset( $color_counts[ $quantized ] ) ) {
                    $color_counts[ $quantized ] = 0;
                }
                $color_counts[ $quantized ]++;
            }
        }

        imagedestroy( $image );

        arsort( $color_counts );
        $top_colors = array_slice( array_keys( $color_counts ), 0, 5 );

        $palette = array();
        foreach ( $top_colors as $c ) {
            $parts = explode( ',', $c );
            $palette[] = sprintf( '#%02x%02x%02x', (int) $parts[0], (int) $parts[1], (int) $parts[2] );
        }

        return $palette;
    }

    private function contrast_ratio( $hex1, $hex2 ) {
        $lum1 = $this->relative_luminance( $hex1 );
        $lum2 = $this->relative_luminance( $hex2 );

        $lighter = max( $lum1, $lum2 );
        $darker  = min( $lum1, $lum2 );

        return ( $lighter + 0.05 ) / ( $darker + 0.05 );
    }

    private function relative_luminance( $hex ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
        $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
        $b = hexdec( substr( $hex, 4, 2 ) ) / 255;

        $linearize = function ( $c ) {
            if ( $c <= 0.03928 ) {
                return $c / 12.92;
            }
            return pow( ( $c + 0.055 ) / 1.055, 2.4 );
        };

        return 0.2126 * $linearize( $r ) + 0.7152 * $linearize( $g ) + 0.0722 * $linearize( $b );
    }
}

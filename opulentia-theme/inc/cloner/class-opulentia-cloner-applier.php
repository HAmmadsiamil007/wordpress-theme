<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Opulentia_Cloner_Applier {
    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function apply( $analysis, $overrides = array() ) {
        $tokens  = Opulentia_Cloner_Tokens::get_instance();
        $mods    = $tokens->analysis_to_theme_mods( $analysis );

        if ( ! empty( $overrides ) ) {
            $mods = array_merge( $mods, $overrides );
        }

        $applied = array();
        foreach ( $mods as $key => $value ) {
            set_theme_mod( $key, $value );
            $applied[ $key ] = $value;
        }

        $images = $analysis['images'] ?? array();
        if ( ! empty( $images['logo'] ) ) {
            $logo_id = $this->import_image( $images['logo'] );
            if ( $logo_id && ! is_wp_error( $logo_id ) ) {
                set_theme_mod( 'opulentia-custom-logo', $logo_id );
                $applied['opulentia-custom-logo'] = $logo_id;
            }
        }

        if ( ! empty( $images['favicon'] ) ) {
            $favicon_id = $this->import_image( $images['favicon'] );
            if ( $favicon_id && ! is_wp_error( $favicon_id ) ) {
                update_option( 'site_icon', $favicon_id );
                $applied['site_icon'] = $favicon_id;
            }
        }

        do_action( 'opulentia_cloner_applied', $analysis, $applied );

        return $applied;
    }

    public function generate_preview_css( $analysis ) {
        $tokens = Opulentia_Cloner_Tokens::get_instance();
        $mods   = $tokens->analysis_to_theme_mods( $analysis );

        $css  = ':root {';
        foreach ( $mods as $key => $value ) {
            if ( strpos( $key, 'opulentia-global-color-' ) === 0 ) {
                $var  = '--' . str_replace( '_', '-', $key );
                $css .= "{$var}: {$value};";
            }
        }
        $css .= '}';

        if ( ! empty( $mods['opulentia-body-font-family'] ) ) {
            $css .= "body{font-family:'{$mods['opulentia-body-font-family']}',sans-serif;}";
        }
        if ( ! empty( $mods['opulentia-heading-font-family'] ) ) {
            $css .= "h1,h2,h3,h4,h5,h6{font-family:'{$mods['opulentia-heading-font-family']}',serif;}";
        }

        return $css;
    }

    private function import_image( $url ) {
        if ( ! function_exists( 'media_sideload_image' ) ) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        $tmp = download_url( $url );
        if ( is_wp_error( $tmp ) ) { return $tmp; }

        $file_name = basename( parse_url( $url, PHP_URL_PATH ) );
        $file      = array(
            'name'     => $file_name,
            'tmp_name' => $tmp,
        );

        $id = media_handle_sideload( $file, 0 );
        if ( is_wp_error( $id ) ) {
            @unlink( $tmp );
        }

        return $id;
    }
}

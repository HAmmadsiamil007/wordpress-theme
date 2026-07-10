<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Opulentia_Cloner_Analyzer {
    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function analyze( $session ) {
        $data = $session['data'] ?? array();
        if ( empty( $data ) ) {
            return new WP_Error( 'no_data', __( 'No capture data to analyze.', 'opulentia' ) );
        }

        $analysis = array(
            'color_palette' => $this->extract_colors( $data ),
            'typography'    => $this->extract_typography( $data ),
            'layout'        => $this->extract_layout( $data ),
            'components'    => $this->extract_components( $data ),
            'images'        => $this->extract_images( $data ),
            'metadata'      => $this->extract_metadata( $data ),
        );

        $dir = Opulentia_Site_Cloner::get_instance()->prepare_upload_dir() . '/' . $session['session_id'];
        file_put_contents(
            $dir . '/analysis.json',
            wp_json_encode( $analysis, JSON_PRETTY_PRINT )
        );

        return $analysis;
    }

    private function extract_colors( $data ) {
        $colors = array(
            'primary'   => '#1a1a1a',
            'secondary' => '#111111',
            'accent'    => '#b8860b',
            'text'      => '#f5f5f5',
            'background' => '#1a1a1a',
        );

        $html = $data['html'] ?? '';
        if ( empty( $html ) ) { return $colors; }

        preg_match_all( '/#[0-9a-fA-F]{3,6}\b/', $html, $matches );
        $found = array_count_values( $matches[0] ?? array() );
        arsort( $found );
        $top_colors = array_keys( array_slice( $found, 0, 10, true ) );

        if ( ! empty( $top_colors ) ) {
            $colors['primary']   = $top_colors[0] ?? $colors['primary'];
            $colors['accent']    = $top_colors[1] ?? $colors['accent'];
            $colors['text']      = $top_colors[2] ?? $colors['text'];
            $colors['background'] = $top_colors[0] ?? $colors['background'];
        }

        foreach ( $data['styles']['colors'] ?? array() as $css_var ) {
            if ( preg_match( '/--primary[^:]*:\s*(#[^;]+)/i', $css_var, $m ) ) {
                $colors['primary'] = $m[1];
            }
            if ( preg_match( '/--accent[^:]*:\s*(#[^;]+)/i', $css_var, $m ) ) {
                $colors['accent'] = $m[1];
            }
        }

        return $colors;
    }

    private function extract_typography( $data ) {
        $html = $data['html'] ?? '';

        $typography = array(
            'body_font'    => 'Inter',
            'heading_font' => 'Playfair Display',
            'base_size'    => '16px',
            'line_height'  => '1.6',
        );

        preg_match_all( '/fonts\.googleapis\.com\/css2\?family=([^&"\']+)/i', $html, $font_matches );
        if ( ! empty( $font_matches[1] ) ) {
            foreach ( $font_matches[1] as $family ) {
                $family = urldecode( $family );
                $family = preg_replace( '/:.*/', '', $family );
                $family = str_replace( '+', ' ', $family );
                if ( empty( $typography['heading_font'] ) || $typography['heading_font'] === 'Playfair Display' ) {
                    $typography['heading_font'] = $family;
                } elseif ( empty( $typography['body_font'] ) || $typography['body_font'] === 'Inter' ) {
                    $typography['body_font'] = $family;
                }
            }
        }

        preg_match_all( '/font-family[^:]*:\s*[\'"]([^\'"]+)/i', $html, $font_family_matches );
        if ( ! empty( $font_family_matches[1] ) ) {
            foreach ( $font_family_matches[1] as $ff ) {
                $ff = trim( $ff );
                if ( stripos( $ff, 'serif' ) !== false || stripos( $ff, 'playfair' ) !== false ) {
                    $typography['heading_font'] = $ff;
                } elseif ( stripos( $ff, 'sans-serif' ) !== false || stripos( $ff, 'inter' ) !== false ) {
                    $typography['body_font'] = $ff;
                }
            }
        }

        return $typography;
    }

    private function extract_layout( $data ) {
        $html = $data['html'] ?? '';

        $layout = array(
            'container_width' => '1200px',
            'content_width'   => '66.67%',
            'sidebar_width'   => '33.33%',
            'grid_columns'    => 3,
        );

        preg_match( '/max-width[^:]*:\s*(\d+)/i', $html, $mw );
        if ( ! empty( $mw[1] ) ) {
            $layout['container_width'] = $mw[1] . 'px';
        }

        preg_match_all( '/class=["\'][^"\']*(?:container|wrapper|site-inner)[^"\']*["\']/i', $html, $container_classes );

        return $layout;
    }

    private function extract_components( $data ) {
        $html = $data['html'] ?? '';

        $components = array(
            'header_style'    => 'default',
            'footer_style'    => 'default',
            'button_style'    => 'solid',
            'button_radius'   => '4px',
            'navigation_style' => 'horizontal',
        );

        if ( preg_match( '/header/i', $html ) ) {
            $components['header_style'] = 'custom';
        }
        if ( preg_match( '/sticky|fixed/i', $html ) ) {
            $components['header_style'] = 'sticky';
        }

        preg_match( '/border-radius[^:]*:\s*(\d+)/i', $html, $br );
        if ( ! empty( $br[1] ) ) {
            $components['button_radius'] = $br[1] . 'px';
        }

        return $components;
    }

    private function extract_images( $data ) {
        $html = $data['html'] ?? '';

        $images = array(
            'logo'    => '',
            'hero'    => '',
            'favicon' => '',
        );

        preg_match( '/<img[^>]+class=["\'][^"\']*logo[^"\']*["\'][^>]*src=["\']([^"\']+)/i', $html, $logo );
        if ( ! empty( $logo[1] ) ) {
            $images['logo'] = $logo[1];
        }

        preg_match( '/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*href=["\']([^"\']+)/i', $html, $favicon );
        if ( ! empty( $favicon[1] ) ) {
            $images['favicon'] = $favicon[1];
        }

        preg_match( '/<img[^>]+class=["\'][^"\']*hero[^"\']*["\'][^>]*src=["\']([^"\']+)/i', $html, $hero );
        if ( ! empty( $hero[1] ) ) {
            $images['hero'] = $hero[1];
        }

        return $images;
    }

    private function extract_metadata( $data ) {
        $html = $data['html'] ?? '';

        $metadata = array(
            'title'       => '',
            'description' => '',
            'lang'        => 'en-US',
            'charset'     => 'UTF-8',
        );

        preg_match( '/<title>([^<]+)<\/title>/i', $html, $title );
        if ( ! empty( $title[1] ) ) {
            $metadata['title'] = trim( $title[1] );
        }

        preg_match( '/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)/i', $html, $desc );
        if ( ! empty( $desc[1] ) ) {
            $metadata['description'] = $desc[1];
        }

        preg_match( '/<html[^>]+lang=["\']([^"\']+)/i', $html, $lang );
        if ( ! empty( $lang[1] ) ) {
            $metadata['lang'] = $lang[1];
        }

        return $metadata;
    }
}

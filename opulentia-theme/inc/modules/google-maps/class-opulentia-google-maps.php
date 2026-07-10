<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Google_Maps {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
        add_shortcode( 'op_map', array( $this, 'render_shortcode' ) );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_maps', array(
            'title'    => __( 'Google Maps', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 200,
        ) );

        $wp_customize->add_setting( 'map-default-address', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'map-default-address', array(
            'label'   => __( 'Default Address', 'opulentia' ),
            'section' => 'opulentia_maps',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'map-default-lat', array(
            'default'           => '51.5',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'map-default-lat', array(
            'label'   => __( 'Default Latitude', 'opulentia' ),
            'section' => 'opulentia_maps',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'map-default-lng', array(
            'default'           => '-0.09',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'map-default-lng', array(
            'label'   => __( 'Default Longitude', 'opulentia' ),
            'section' => 'opulentia_maps',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'map-default-zoom', array(
            'default'           => 14,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'map-default-zoom', array(
            'label'   => __( 'Default Zoom', 'opulentia' ),
            'section' => 'opulentia_maps',
            'type'    => 'number',
            'input_attrs' => array( 'min' => 1, 'max' => 20 ),
        ) );

        $wp_customize->add_setting( 'map-height', array(
            'default'           => 400,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'map-height', array(
            'label'   => __( 'Map Height (px)', 'opulentia' ),
            'section' => 'opulentia_maps',
            'type'    => 'number',
            'input_attrs' => array( 'min' => 100, 'max' => 1200 ),
        ) );

        $wp_customize->add_setting( 'map-marker-icon', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'map-marker-icon', array(
            'label'   => __( 'Custom Marker Icon URL', 'opulentia' ),
            'section' => 'opulentia_maps',
            'type'    => 'text',
        ) );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'address' => Opulentia_get_option( 'map-default-address', '' ),
            'lat'     => Opulentia_get_option( 'map-default-lat', '51.5' ),
            'lng'     => Opulentia_get_option( 'map-default-lng', '-0.09' ),
            'zoom'    => Opulentia_get_option( 'map-default-zoom', 14 ),
            'height'  => Opulentia_get_option( 'map-height', 400 ),
            'width'   => '100%',
            'title'   => '',
        ), $atts, 'op_map' );

        $map_id = 'op-map-' . uniqid();

        $this->enqueue_leaflet();

        $height = absint( $atts['height'] );
        $zoom   = absint( $atts['zoom'] );
        $lat    = floatval( $atts['lat'] );
        $lng    = floatval( $atts['lng'] );
        $title  = esc_attr( $atts['title'] );
        $marker_icon = Opulentia_get_option( 'map-marker-icon', '' );

        $output = '<div id="' . esc_attr( $map_id ) . '" class="op-map-container" style="height:' . $height . 'px;width:' . esc_attr( $atts['width'] ) . ';" data-lat="' . $lat . '" data-lng="' . $lng . '" data-zoom="' . $zoom . '" data-marker-icon="' . esc_attr( $marker_icon ) . '" data-title="' . $title . '" data-address="' . esc_attr( $atts['address'] ) . '"></div>';

        $output .= '<script>
        (function() {
            var container = document.getElementById("' . $map_id . '");
            if (!container) return;
            var lat = parseFloat(container.getAttribute("data-lat")) || 51.5;
            var lng = parseFloat(container.getAttribute("data-lng")) || -0.09;
            var zoom = parseInt(container.getAttribute("data-zoom")) || 14;
            var markerIcon = container.getAttribute("data-marker-icon") || "";
            var title = container.getAttribute("data-title") || "";
            var address = container.getAttribute("data-address") || "";
            function initMap() {
                if (typeof L === "undefined") { setTimeout(initMap, 200); return; }
                var map = L.map(container).setView([lat, lng], zoom);
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "&copy; <a href=\"https://openstreetmap.org/copyright\">OpenStreetMap</a>",
                    maxZoom: 19
                }).addTo(map);
                var markerOpts = {};
                if (markerIcon) {
                    markerOpts.icon = L.icon({ iconUrl: markerIcon, iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -32] });
                }
                var marker = L.marker([lat, lng], markerOpts).addTo(map);
                if (title || address) {
                    marker.bindPopup(title ? "<strong>" + title + "</strong>" + (address ? "<br>" + address : "") : address);
                }
                setTimeout(function() { map.invalidateSize(); }, 500);
            }
            if (document.readyState === "complete" || document.readyState === "interactive") {
                initMap();
            } else {
                document.addEventListener("DOMContentLoaded", initMap);
            }
        })();
        </script>';

        return $output;
    }

    private function enqueue_leaflet() {
        wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
        wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
    }

    public function inline_css() {
        $height = (int) Opulentia_get_option( 'map-height', 400 );

        $css = '
        .op-map-container {
            position: relative;
            width: 100%;
            background: var(--color-secondary-dark, #111);
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
        }
        .op-map-container .leaflet-container {
            border-radius: 8px;
        }
        @media (min-width: 993px) {
            .op-map-container {
                aspect-ratio: 16 / 9;
                height: auto !important;
            }
        }
        @media (max-width: 992px) {
            .op-map-container {
                aspect-ratio: 4 / 3;
                height: auto !important;
            }
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

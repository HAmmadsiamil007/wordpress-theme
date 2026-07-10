<?php
/**
 * AMP Compatibility — Singleton
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_AMP class.
 */
class Opulentia_AMP {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Returns the singleton instance.
     *
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor — registers hooks.
     */
    private function __construct() {
        add_action( 'wp', array( $this, 'init' ) );
    }

    /**
     * Initialize AMP compatibility.
     */
    public function init() {
        if ( ! function_exists( 'is_amp_endpoint' ) || ! is_amp_endpoint() ) {
            return;
        }

        add_filter( 'Opulentia_is_svg_icons', '__return_false' );
        remove_action( 'wp_head', array( Opulentia_Fonts::get_instance(), 'preload_fonts' ), 2 );
    }
}

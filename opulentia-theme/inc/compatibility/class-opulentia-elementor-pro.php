<?php
/**
 * Elementor Pro Compatibility — Singleton
 *
 * Integrates Opulentia with Elementor Pro's Theme Builder:
 * - Registers theme locations (header, footer, single, archive, 404, search)
 * - Syncs Opulentia global palette colors to Elementor's global colors
 * - Replaces theme header/footer with Elementor Pro templates when assigned
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Elementor_Pro class.
 */
class Opulentia_Elementor_Pro {

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
        add_action( 'after_setup_theme', array( $this, 'init' ), 20 );
    }

    /**
     * Initialize Elementor Pro integration.
     */
    public function init() {
        if ( ! $this->has_elementor_pro() ) {
            return;
        }

        // Register theme builder locations.
        add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

        // Sync Opulentia global palette to Elementor colors.
        add_action( 'elementor/editor/init', array( $this, 'sync_global_colors' ) );

        // Replace theme header/footer with Elementor Pro templates on frontend.
        add_action( 'template_redirect', array( $this, 'handle_header_footer' ), 5 );
    }

    /**
     * Check if Elementor Pro is active.
     *
     * @return bool
     */
    private function has_elementor_pro() {
        return function_exists( 'elementor_pro_load_plugin' )
            || defined( 'ELEMENTOR_PRO_VERSION' );
    }

    /**
     * Register all core theme builder locations.
     *
     * @param \ElementorPro\Modules\ThemeBuilder\Module $elementor_theme_manager Theme builder manager.
     */
    public function register_locations( $elementor_theme_manager ) {
        if ( ! is_object( $elementor_theme_manager ) || ! method_exists( $elementor_theme_manager, 'register_all_core_location' ) ) {
            return;
        }

        $elementor_theme_manager->register_all_core_location();
    }

    /**
     * Sync Opulentia's global palette to Elementor's global colors.
     *
     * Maps the 9 Opulentia global palette colors to Elementor's
     * system colors so theme color changes propagate to Elementor widgets.
     */
    public function sync_global_colors() {
        if ( ! class_exists( '\Elementor\Plugin' ) ) {
            return;
        }

        $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
        if ( ! $kit ) {
            return;
        }

        $settings = $kit->get_settings();

        $palette_map = array(
            0 => array( 'id' => 'opulentia-palette-0', 'title' => __( 'Page Background', 'opulentia' ) ),
            1 => array( 'id' => 'opulentia-palette-1', 'title' => __( 'Section Background', 'opulentia' ) ),
            2 => array( 'id' => 'opulentia-palette-2', 'title' => __( 'Accent', 'opulentia' ) ),
            3 => array( 'id' => 'opulentia-palette-3', 'title' => __( 'Gold / Headings', 'opulentia' ) ),
            4 => array( 'id' => 'opulentia-palette-4', 'title' => __( 'Light Gold', 'opulentia' ) ),
            5 => array( 'id' => 'opulentia-palette-5', 'title' => __( 'Body Text', 'opulentia' ) ),
            6 => array( 'id' => 'opulentia-palette-6', 'title' => __( 'Muted Text', 'opulentia' ) ),
            7 => array( 'id' => 'opulentia-palette-7', 'title' => __( 'Border', 'opulentia' ) ),
            8 => array( 'id' => 'opulentia-palette-8', 'title' => __( 'White / Bright', 'opulentia' ) ),
        );

        foreach ( $palette_map as $i => $info ) {
            $color_value = get_theme_mod( 'global-color-' . $i, '' );

            if ( empty( $color_value ) ) {
                $scheme     = get_theme_mod( 'color_scheme_preset', 'dark-luxury' );
                $defaults   = function_exists( 'Opulentia_get_global_palette_by_preset' )
                    ? Opulentia_get_global_palette_by_preset( $scheme )
                    : array();
                $color_value = isset( $defaults[ $i ] ) ? $defaults[ $i ] : '#1a1a1a';
            }

            if ( isset( $settings['system_colors'] ) && is_array( $settings['system_colors'] ) ) {
                $settings['system_colors'][ $i ] = array(
                    '_id'   => $info['id'],
                    'title' => $info['title'],
                    'color' => $color_value,
                );
            }
        }

        $kit->save( array( 'settings' => $settings ) );
    }

    /**
     * Handle header/footer replacement on frontend.
     *
     * If Elementor Pro has header/footer templates that match the
     * current page conditions, renders them and suppresses the
     * theme's own header/footer.
     */
    public function handle_header_footer() {
        if ( is_admin() || ! class_exists( '\ElementorPro\Plugin' ) ) {
            return;
        }

        try {
            $theme_builder = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
            if ( ! $theme_builder ) {
                return;
            }

            $conditions_manager = $theme_builder->get_conditions_manager();
            if ( ! $conditions_manager ) {
                return;
            }

            // Replace header if Elementor has a matching template.
            if ( $this->has_location_template( 'header', $conditions_manager ) ) {
                remove_all_actions( 'opulentia_header_before' );
                remove_all_actions( 'opulentia_header_after' );
                add_action( 'opulentia_header_before', array( $this, 'render_elementor_header' ) );
                add_action( 'opulentia_header_after', '__return_empty_string' );
            }

            // Replace footer if Elementor has a matching template.
            if ( $this->has_location_template( 'footer', $conditions_manager ) ) {
                remove_all_actions( 'opulentia_footer_before' );
                remove_all_actions( 'opulentia_footer_after' );
                add_action( 'opulentia_footer_before', array( $this, 'render_elementor_footer' ) );
                add_action( 'opulentia_footer_after', '__return_empty_string' );
            }
        } catch ( \Exception $e ) {
            // Silently fail if Elementor Pro APIs are unavailable.
            return;
        }
    }

    /**
     * Check if Elementor Pro has a template assigned for a location.
     *
     * Uses Elementor Pro's own condition manager to check both
     * assignment AND display condition matching.
     *
     * @param string                                                            $location          Location key (header, footer, etc.).
     * @param \ElementorPro\Modules\ThemeBuilder\Classes\ConditionsManager|null $conditions_manager Optional pre-fetched conditions manager.
     * @return bool
     */
    private function has_location_template( $location, $conditions_manager = null ) {
        if ( ! class_exists( '\ElementorPro\Plugin' ) ) {
            return false;
        }

        try {
            if ( ! $conditions_manager ) {
                $theme_builder = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
                if ( ! $theme_builder ) {
                    return false;
                }
                $conditions_manager = $theme_builder->get_conditions_manager();
            }

            if ( ! $conditions_manager || ! method_exists( $conditions_manager, 'get_documents_for_location' ) ) {
                return false;
            }

            $documents = $conditions_manager->get_documents_for_location( $location );
            return ! empty( $documents );
        } catch ( \Exception $e ) {
            return false;
        }
    }

    /**
     * Render Elementor Pro header template.
     */
    public function render_elementor_header() {
        if ( function_exists( 'elementor_theme_do_location' ) && $this->has_location_template( 'header' ) ) {
            ?>
            <header id="opulentia-elementor-header" class="opulentia-elementor-header">
                <?php elementor_theme_do_location( 'header' ); ?>
            </header>
            <?php
        }
    }

    /**
     * Render Elementor Pro footer template.
     */
    public function render_elementor_footer() {
        if ( function_exists( 'elementor_theme_do_location' ) && $this->has_location_template( 'footer' ) ) {
            ?>
            <footer id="opulentia-elementor-footer" class="opulentia-elementor-footer">
                <?php elementor_theme_do_location( 'footer' ); ?>
            </footer>
            <?php
        }
    }
}

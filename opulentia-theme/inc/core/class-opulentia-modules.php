<?php
/**
 * Opulentia Module Manager
 *
 * Registers, activates, and deactivates theme modules.
 * Each module is a self-contained feature (e.g., header-builder,
 * woocommerce-enhancements, blog-pro) that can be enabled or
 * disabled via a filter or customizer setting.
 *
 * Provides dependency tracking to ensure modules load in
 * the correct order and parent modules are active before
 * their dependents.
 *
 * @package Opulentia
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Opulentia_Modules' ) ) {

    /**
     * Opulentia_Modules class.
     */
    class Opulentia_Modules {

        /**
         * Singleton instance.
         *
         * @var self|null
         */
        private static $instance;

        /**
         * Registered modules.
         *
         * @var array
         */
        private $modules = array();

        /**
         * Module activation callbacks.
         *
         * @var array
         */
        private $activation_hooks = array();

        /**
         * Module deactivation callbacks.
         *
         * @var array
         */
        private $deactivation_hooks = array();

        /**
         * Returns the singleton instance.
         *
         * @return self
         */
        public static function get_instance() {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Private constructor.
         */
        private function __construct() {
            add_action( 'after_setup_theme', array( $this, 'init_modules' ), 20 );
        }

        // ---------------------------------------------------------------------
        // Module Registration
        // ---------------------------------------------------------------------

        /**
         * Register a module.
         *
         * @param  string $slug       Unique module identifier (e.g., 'header-builder').
         * @param  array  $args {
         *     Optional. Module arguments.
         *
         *     @type string $name        Human-readable name.
         *     @type string $description Module description.
         *     @type array  $dependencies Array of module slugs this module depends on.
         *     @type string $file        File path to load when active (relative to theme root).
         *     @type string $class       Class to instantiate when active.
         *     @type string $setting_id  Customizer setting ID that controls activation.
         *     @type bool   $default     Default status (true = active by default).
         *     @type int    $priority    Load order priority (lower = earlier, default 10).
         *     @type string $category    Category for UI grouping (e.g., 'builder', 'ecommerce', 'blog').
         * }
         * @return void
         */
        public function register( $slug, $args = array() ) {
            $defaults = array(
                'name'         => ucwords( str_replace( '-', ' ', $slug ) ),
                'description'  => '',
                'dependencies' => array(),
                'file'         => '',
                'class'        => '',
                'setting_id'   => 'module_' . $slug,
                'default'      => true,
                'priority'     => 10,
                'category'     => 'general',
            );

            $this->modules[ $slug ] = wp_parse_args( $args, $defaults );
        }

        /**
         * Unregister a module.
         *
         * @param  string $slug Module slug.
         * @return void
         */
        public function unregister( $slug ) {
            unset( $this->modules[ $slug ] );
        }

        /**
         * Check if a module is registered.
         *
         * @param  string $slug Module slug.
         * @return bool
         */
        public function is_registered( $slug ) {
            return isset( $this->modules[ $slug ] );
        }

        /**
         * Get all registered modules.
         *
         * @return array
         */
        public function get_all() {
            return $this->modules;
        }

        /**
         * Get modules by category.
         *
         * @param  string $category Category slug.
         * @return array
         */
        public function get_by_category( $category ) {
            return array_filter( $this->modules, function( $module ) use ( $category ) {
                return $module['category'] === $category;
            } );
        }

        // ---------------------------------------------------------------------
        // Activation Status
        // ---------------------------------------------------------------------

        /**
         * Check if a module is active.
         *
         * @param  string $slug Module slug.
         * @return bool
         */
        public function is_active( $slug ) {
            if ( ! isset( $this->modules[ $slug ] ) ) {
                return false;
            }

            $module = $this->modules[ $slug ];

            // Check dependencies first.
            foreach ( $module['dependencies'] as $dep ) {
                if ( ! $this->is_active( $dep ) ) {
                    return false;
                }
            }

            // Check customizer setting or default.
            $active = $module['default'];
            if ( ! empty( $module['setting_id'] ) ) {
                $active = Opulentia_get_option( $module['setting_id'], $module['default'] );
            }

            /**
             * Filter module activation status.
             *
             * @param bool   $active Whether the module is active.
             * @param string $slug   Module slug.
             */
            return (bool) apply_filters( "Opulentia_module_active_{$slug}", $active, $slug );
        }

        /**
         * Activate a module programmatically.
         *
         * @param  string $slug Module slug.
         * @return void
         */
        public function activate( $slug ) {
            if ( ! isset( $this->modules[ $slug ] ) ) {
                return;
            }

            Opulentia_update_option( $this->modules[ $slug ]['setting_id'], true );

            if ( isset( $this->activation_hooks[ $slug ] ) ) {
                call_user_func( $this->activation_hooks[ $slug ] );
            }

            do_action( "Opulentia_module_activated_{$slug}" );
        }

        /**
         * Deactivate a module programmatically.
         *
         * @param  string $slug Module slug.
         * @return void
         */
        public function deactivate( $slug ) {
            if ( ! isset( $this->modules[ $slug ] ) ) {
                return;
            }

            Opulentia_update_option( $this->modules[ $slug ]['setting_id'], false );

            if ( isset( $this->deactivation_hooks[ $slug ] ) ) {
                call_user_func( $this->deactivation_hooks[ $slug ] );
            }

            do_action( "Opulentia_module_deactivated_{$slug}" );
        }

        /**
         * Register activation hook for a module.
         *
         * @param  string   $slug     Module slug.
         * @param  callable $callback Callback on activation.
         * @return void
         */
        public function on_activate( $slug, $callback ) {
            $this->activation_hooks[ $slug ] = $callback;
        }

        /**
         * Register deactivation hook for a module.
         *
         * @param  string   $slug     Module slug.
         * @param  callable $callback Callback on deactivation.
         * @return void
         */
        public function on_deactivate( $slug, $callback ) {
            $this->deactivation_hooks[ $slug ] = $callback;
        }

        // ---------------------------------------------------------------------
        // Initialization
        // ---------------------------------------------------------------------

        /**
         * Initialize all active modules.
         *
         * Loads module files and instantiates module classes
         * sorted by priority and dependency order.
         */
        public function init_modules() {
            $sorted = $this->get_sorted_active_modules();

            foreach ( $sorted as $slug => $module ) {
                if ( ! $this->is_active( $slug ) ) {
                    continue;
                }

                // Load module file.
                if ( ! empty( $module['file'] ) ) {
                    $file_path = Opulentia_DIR . '/' . ltrim( $module['file'], '/' );
                    if ( file_exists( $file_path ) ) {
                        require_once $file_path;
                    }
                }

                // Instantiate module class.
                if ( ! empty( $module['class'] ) && class_exists( $module['class'] ) ) {
                    if ( method_exists( $module['class'], 'get_instance' ) ) {
                        call_user_func( array( $module['class'], 'get_instance' ) );
                    } else {
                        new $module['class']();
                    }
                }

                do_action( "Opulentia_module_loaded_{$slug}", $slug );
            }

            do_action( 'Opulentia_modules_loaded' );
        }

        // ---------------------------------------------------------------------
        // Sorting & Ordering
        // ---------------------------------------------------------------------

        /**
         * Get active modules sorted by priority and dependencies.
         *
         * @return array Sorted array of active module configs.
         */
        private function get_sorted_active_modules() {
            $sorted = array();

            // First pass: collect modules sorted by priority.
            $by_priority = $this->modules;
            uasort( $by_priority, function( $a, $b ) {
                return $a['priority'] - $b['priority'];
            } );

            // Second pass: build dependency-ordered array.
            $visited = array();
            foreach ( $by_priority as $slug => $module ) {
                $this->resolve_dependencies( $slug, $by_priority, $sorted, $visited );
            }

            return $sorted;
        }

        /**
         * Resolve module dependencies recursively (topological sort).
         *
         * @param  string $slug     Module slug.
         * @param  array  $modules  All registered modules.
         * @param  array  &$sorted  Sorted output array (by reference).
         * @param  array  &$visited Visited set (by reference).
         * @return void
         */
        private function resolve_dependencies( $slug, $modules, &$sorted, &$visited ) {
            if ( isset( $visited[ $slug ] ) ) {
                return;
            }

            $visited[ $slug ] = true;

            if ( ! isset( $modules[ $slug ] ) ) {
                return;
            }

            foreach ( $modules[ $slug ]['dependencies'] as $dep ) {
                if ( ! isset( $visited[ $dep ] ) && isset( $modules[ $dep ] ) ) {
                    $this->resolve_dependencies( $dep, $modules, $sorted, $visited );
                }
            }

            $sorted[ $slug ] = $modules[ $slug ];
        }

        /**
         * Get the list of module slugs that can't activate due to missing dependencies.
         *
         * @return array Array of [ slug => [ missing_dep, ... ] ].
         */
        public function get_missing_dependencies() {
            $missing = array();

            foreach ( $this->modules as $slug => $module ) {
                foreach ( $module['dependencies'] as $dep ) {
                    if ( ! isset( $this->modules[ $dep ] ) ) {
                        if ( ! isset( $missing[ $slug ] ) ) {
                            $missing[ $slug ] = array();
                        }
                        $missing[ $slug ][] = $dep;
                    }
                }
            }

            return $missing;
        }
    }

    // Kick off the singleton.
    Opulentia_Modules::get_instance();
}

// -----------------------------------------------------------------------------
// Convenience Functions
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_register_module' ) ) {
    /**
     * Register a module with the Module Manager.
     *
     * @param  string $slug Module slug.
     * @param  array  $args Module arguments.
     * @return void
     */
    function Opulentia_register_module( $slug, $args = array() ) {
        Opulentia_Modules::get_instance()->register( $slug, $args );
    }
}

if ( ! function_exists( 'Opulentia_is_module_active' ) ) {
    /**
     * Check if a module is active.
     *
     * @param  string $slug Module slug.
     * @return bool
     */
    function Opulentia_is_module_active( $slug ) {
        return Opulentia_Modules::get_instance()->is_active( $slug );
    }
}

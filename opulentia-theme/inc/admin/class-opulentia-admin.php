<?php
/**
 * Opulentia Admin Dashboard
 *
 * Provides an admin dashboard under Appearance > Opulentia with:
 * - Theme information panel
 * - Module Manager UI with enable/disable toggles grouped by category
 * - Import/Export tool for theme settings
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Admin class.
 */
class Opulentia_Admin {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Available tabs.
     *
     * @var array
     */
    private $tabs = array();

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
        add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_Opulentia_toggle_module', array( $this, 'ajax_toggle_module' ) );
        add_action( 'wp_ajax_Opulentia_export_settings', array( $this, 'ajax_export_settings' ) );
        add_action( 'wp_ajax_Opulentia_import_settings', array( $this, 'ajax_import_settings' ) );
        add_action( 'wp_ajax_Opulentia_reset_settings', array( $this, 'ajax_reset_settings' ) );
        add_filter( 'theme_action_links', array( $this, 'add_action_links' ) );

        $this->tabs = array(
            'info'     => __( 'Theme Info', 'opulentia' ),
            'modules'  => __( 'Modules', 'opulentia' ),
            'import-export' => __( 'Import / Export', 'opulentia' ),
            'cloner'   => __( 'Site Cloner', 'opulentia' ),
        );
    }

    /**
     * Add admin menu pages.
     */
    public function add_admin_pages() {
        $hook = add_theme_page(
            __( 'Opulentia Dashboard', 'opulentia' ),
            __( 'opulentia', 'opulentia' ),
            'manage_options',
            'opulentia',
            array( $this, 'render_dashboard' )
        );
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets( $hook ) {
        if ( 'appearance_page_opulentia' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'opulentia-admin' );

        wp_add_inline_script(
            'jquery',
            'var OpulentiaAdmin = ' . wp_json_encode( array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'Opulentia_admin_nonce' ),
                'i18n'    => array(
                    'saving'   => __( 'Saving...', 'opulentia' ),
                    'saved'    => __( 'Saved', 'opulentia' ),
                    'error'    => __( 'Error saving. Please try again.', 'opulentia' ),
                    'exporting' => __( 'Exporting...', 'opulentia' ),
                    'importing' => __( 'Importing...', 'opulentia' ),
                    'importSuccess' => __( 'Settings imported successfully!', 'opulentia' ),
                    'importError'   => __( 'Error importing settings.', 'opulentia' ),
                    'confirmImport' => __( 'This will overwrite all current theme settings. Continue?', 'opulentia' ),
                ),
            ) ) . ';'
        );

        wp_add_inline_script( 'jquery', $this->get_admin_js() );
    }

    /**
     * Render the dashboard page.
     */
    public function render_dashboard() {
        $current_tab = isset( $_GET['tab'] ) && isset( $this->tabs[ $_GET['tab'] ] )
            ? sanitize_key( $_GET['tab'] )
            : 'info';
        ?>
        <div class="wrap opulentia-dashboard">
            <h1><?php esc_html_e( 'Opulentia Theme Dashboard', 'opulentia' ); ?></h1>

            <nav class="nav-tab-wrapper">
                <?php foreach ( $this->tabs as $tab_id => $tab_label ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'themes.php?page=Opulentia&tab=' . $tab_id ) ); ?>"
                       class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html( $tab_label ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="opulentia-dashboard__content">
                <?php
                switch ( $current_tab ) {
                    case 'modules':
                        $this->render_modules_tab();
                        break;
                    case 'import-export':
                        $this->render_import_export_tab();
                        break;
                    case 'cloner':
                        $this->render_cloner_tab();
                        break;
                    case 'info':
                    default:
                        $this->render_info_tab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render the Theme Info tab.
     */
    private function render_info_tab() {
        $theme = wp_get_theme();
        ?>
        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'Theme Information', 'opulentia' ); ?></h2>
            <table class="wp-list-table widefat fixed striped opulentia-dashboard__table">
                <tbody>
                    <tr><td><strong><?php esc_html_e( 'Theme Name', 'opulentia' ); ?></strong></td><td><?php echo esc_html( $theme->get( 'Name' ) ); ?></td></tr>
                    <tr><td><strong><?php esc_html_e( 'Version', 'opulentia' ); ?></strong></td><td><?php echo esc_html( $theme->get( 'Version' ) ); ?></td></tr>
                    <tr><td><strong><?php esc_html_e( 'Author', 'opulentia' ); ?></strong></td><td><?php echo esc_html( $theme->get( 'Author' ) ); ?></td></tr>
                    <tr><td><strong><?php esc_html_e( 'Description', 'opulentia' ); ?></strong></td><td><?php echo esc_html( $theme->get( 'Description' ) ); ?></td></tr>
                    <tr><td><strong><?php esc_html_e( 'Text Domain', 'opulentia' ); ?></strong></td><td><?php echo esc_html( $theme->get( 'TextDomain' ) ); ?></td></tr>
                    <tr><td><strong><?php esc_html_e( 'Active Modules', 'opulentia' ); ?></strong></td><td><?php echo count( $this->get_active_modules() ); ?> / <?php echo count( $this->get_all_modules() ); ?></td></tr>
                </tbody>
            </table>
        </div>

        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'Quick Links', 'opulentia' ); ?></h2>
            <ul>
                <li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Customize Theme', 'opulentia' ); ?></a></li>
                <li><a href="<?php echo esc_url( admin_url( 'themes.php?page=Opulentia&tab=modules' ) ); ?>"><?php esc_html_e( 'Manage Modules', 'opulentia' ); ?></a></li>
                <li><a href="<?php echo esc_url( admin_url( 'themes.php?page=Opulentia&tab=import-export' ) ); ?>"><?php esc_html_e( 'Import / Export Settings', 'opulentia' ); ?></a></li>
            </ul>
        </div>

        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'System Status', 'opulentia' ); ?></h2>
            <table class="wp-list-table widefat fixed striped opulentia-dashboard__table">
                <tbody>
                    <tr>
                        <td><strong><?php esc_html_e( 'WordPress Version', 'opulentia' ); ?></strong></td>
                        <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'PHP Version', 'opulentia' ); ?></strong></td>
                        <td><?php echo esc_html( phpversion() ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'PHP Memory Limit', 'opulentia' ); ?></strong></td>
                        <td><?php echo esc_html( WP_MEMORY_LIMIT ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Max Upload Size', 'opulentia' ); ?></strong></td>
                        <td><?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Max Execution Time', 'opulentia' ); ?></strong></td>
                        <td><?php echo esc_html( ini_get( 'max_execution_time' ) . 's' ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'MySQL Version', 'opulentia' ); ?></strong></td>
                        <td><?php
                            global $wpdb;
                            echo esc_html( $wpdb->db_version() );
                        ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'WooCommerce', 'opulentia' ); ?></strong></td>
                        <td><?php echo class_exists( 'WooCommerce' ) ? esc_html__( 'Active', 'opulentia' ) : esc_html__( 'Not Active', 'opulentia' ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Elementor', 'opulentia' ); ?></strong></td>
                        <td><?php echo did_action( 'elementor/loaded' ) ? esc_html__( 'Active', 'opulentia' ) : esc_html__( 'Not Active', 'opulentia' ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Yoast SEO', 'opulentia' ); ?></strong></td>
                        <td><?php echo defined( 'WPSEO_VERSION' ) ? esc_html__( 'Active', 'opulentia' ) : esc_html__( 'Not Active', 'opulentia' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'Active Plugins', 'opulentia' ); ?></h2>
            <?php
            $all_plugins   = get_plugins();
            $active_slugs  = get_option( 'active_plugins', array() );
            $active_plugins = array();

            foreach ( $active_slugs as $slug ) {
                if ( isset( $all_plugins[ $slug ] ) ) {
                    $active_plugins[ $slug ] = $all_plugins[ $slug ];
                }
            }

            if ( is_multisite() ) {
                $network_slugs = get_site_option( 'active_sitewide_plugins', array() );
                foreach ( $network_slugs as $slug => $time ) {
                    if ( isset( $all_plugins[ $slug ] ) && ! isset( $active_plugins[ $slug ] ) ) {
                        $active_plugins[ $slug ] = $all_plugins[ $slug ];
                    }
                }
            }
            ?>
            <table class="wp-list-table widefat fixed striped opulentia-dashboard__table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Plugin', 'opulentia' ); ?></th>
                        <th><?php esc_html_e( 'Version', 'opulentia' ); ?></th>
                        <th><?php esc_html_e( 'Author', 'opulentia' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $active_plugins as $slug => $plugin ) : ?>
                        <tr>
                            <td><?php echo esc_html( $plugin['Name'] ); ?></td>
                            <td><?php echo esc_html( $plugin['Version'] ); ?></td>
                            <td><?php echo esc_html( $plugin['Author'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><em><?php esc_html_e( 'Total active plugins:', 'opulentia' ); ?> <?php echo count( $active_plugins ); ?></em></p>
        </div>
        <?php
    }

    /**
     * Render the Modules tab.
     */
    private function render_modules_tab() {
        $modules     = $this->get_all_modules();
        $categories  = $this->get_module_categories();
        $active_ids  = $this->get_active_module_ids();
        ?>
        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'Module Manager', 'opulentia' ); ?></h2>
            <p><?php esc_html_e( 'Enable or disable theme modules. Disabling a module removes its frontend output and scripts, but preserves its settings.', 'opulentia' ); ?></p>
            <p><?php esc_html_e( 'Total:', 'opulentia' ); ?> <?php echo count( $modules ); ?> | <?php esc_html_e( 'Active:', 'opulentia' ); ?> <span id="opulentia-active-count"><?php echo count( $active_ids ); ?></span></p>

            <div id="opulentia-module-message" class="notice inline" style="display:none;"></div>

            <?php foreach ( $categories as $cat_key => $cat_label ) : ?>
                <?php
                $cat_modules = array_filter( $modules, function( $m ) use ( $cat_key ) {
                    return ( $m['category'] ?? 'general' ) === $cat_key;
                } );
                if ( empty( $cat_modules ) ) {
                    continue;
                }
                ?>
                <div class="opulentia-module-category">
                    <h3 class="opulentia-module-category__title"><?php echo esc_html( $cat_label ); ?></h3>
                    <table class="wp-list-table widefat fixed striped opulentia-module-table">
                        <thead>
                            <tr>
                                <th class="opulentia-module-table__toggle"><?php esc_html_e( 'Status', 'opulentia' ); ?></th>
                                <th class="opulentia-module-table__name"><?php esc_html_e( 'Module', 'opulentia' ); ?></th>
                                <th class="opulentia-module-table__desc"><?php esc_html_e( 'Description', 'opulentia' ); ?></th>
                                <th class="opulentia-module-table__deps"><?php esc_html_e( 'Dependencies', 'opulentia' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $cat_modules as $slug => $module ) : ?>
                                <?php
                                $is_active  = in_array( $slug, $active_ids, true );
                                $has_deps   = ! empty( $module['dependencies'] );
                                $deps_met   = $this->check_dependencies( $slug, $active_ids );
                                $can_toggle = $has_deps ? $deps_met : true;
                                ?>
                                <tr class="opulentia-module-row <?php echo $is_active ? 'is-active' : 'is-inactive'; ?>"
                                    data-slug="<?php echo esc_attr( $slug ); ?>">
                                    <td class="opulentia-module-table__toggle">
                                        <label class="opulentia-toggle">
                                            <input type="checkbox"
                                                   class="opulentia-toggle__input js-opulentia-toggle-module"
                                                   <?php checked( $is_active ); ?>
                                                   <?php disabled( ! $can_toggle && ! $is_active ); ?>
                                                   data-slug="<?php echo esc_attr( $slug ); ?>">
                                            <span class="opulentia-toggle__slider"></span>
                                        </label>
                                        <span class="opulentia-module-status <?php echo $is_active ? 'is-active' : 'is-inactive'; ?>">
                                            <?php echo $is_active ? esc_html__( 'ON', 'opulentia' ) : esc_html__( 'OFF', 'opulentia' ); ?>
                                        </span>
                                    </td>
                                    <td class="opulentia-module-table__name">
                                        <strong><?php echo esc_html( $module['name'] ); ?></strong>
                                    </td>
                                    <td class="opulentia-module-table__desc">
                                        <?php echo esc_html( $module['description'] ); ?>
                                        <?php if ( ! $can_toggle && $has_deps ) : ?>
                                            <br><span class="opulentia-module-deps-warning">
                                                <?php esc_html_e( 'Missing dependencies:', 'opulentia' ); ?>
                                                <?php echo esc_html( implode( ', ', $this->get_missing_dep_names( $slug ) ) ); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="opulentia-module-table__deps">
                                        <?php if ( $has_deps ) : ?>
                                            <?php echo esc_html( implode( ', ', array_map( 'ucwords', str_replace( '-', ' ', $module['dependencies'] ) ) ) ); ?>
                                        <?php else : ?>
                                            <span class="opulentia-module-no-deps">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render the Import/Export tab.
     */
    private function render_import_export_tab() {
        ?>
        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'Export Settings', 'opulentia' ); ?></h2>
            <p><?php esc_html_e( 'Download a JSON file containing all Opulentia theme settings. This includes customizer values, module toggles, and all theme options.', 'opulentia' ); ?></p>
            <button type="button" class="button button-primary" id="opulentia-export-btn">
                <span class="opulentia-btn-text"><?php esc_html_e( 'Download Export File', 'opulentia' ); ?></span>
                <span class="spinner" style="float:none;margin:0 4px 0 8px;"></span>
            </button>
        </div>

        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'Import Settings', 'opulentia' ); ?></h2>
            <p><?php esc_html_e( 'Import a previously exported JSON settings file. This will overwrite all current theme settings.', 'opulentia' ); ?></p>
            <form id="opulentia-import-form" method="post" enctype="multipart/form-data">
                <p>
                    <input type="file" name="Opulentia_import_file" id="opulentia-import-file" accept=".json">
                </p>
                <p>
                    <button type="button" class="button button-primary" id="opulentia-import-btn" disabled>
                        <span class="opulentia-btn-text"><?php esc_html_e( 'Import Settings', 'opulentia' ); ?></span>
                        <span class="spinner" style="float:none;margin:0 4px 0 8px;"></span>
                    </button>
                </p>
                <div id="opulentia-import-result" style="display:none;"></div>
            </form>
        </div>

        <div class="opulentia-dashboard__card">
            <h2><?php esc_html_e( 'Reset Settings', 'opulentia' ); ?></h2>
            <p><?php esc_html_e( 'Reset all theme settings to their default values. This cannot be undone.', 'opulentia' ); ?></p>
            <button type="button" class="button button-secondary" id="opulentia-reset-btn">
                <?php esc_html_e( 'Reset to Defaults', 'opulentia' ); ?>
            </button>
        </div>
        <?php
    }

    // -------------------------------------------------------------------------
    // Module Data
    // -------------------------------------------------------------------------

    /**
     * Get all registered modules from the Module Manager.
     *
     * @return array
     */
    private function get_all_modules() {
        if ( ! class_exists( 'Opulentia_Modules' ) ) {
            return array();
        }

        $manager = Opulentia_Modules::get_instance();
        return $manager->get_all();
    }

    /**
     * Get module categories with labels.
     *
     * @return array
     */
    private function get_module_categories() {
        return array(
            'core'          => __( 'Core', 'opulentia' ),
            'builder'       => __( 'Builder', 'opulentia' ),
            'content'       => __( 'Content', 'opulentia' ),
            'ecommerce'     => __( 'E-Commerce', 'opulentia' ),
            'seo'           => __( 'SEO', 'opulentia' ),
            'ui'            => __( 'User Interface', 'opulentia' ),
            'optimization'  => __( 'Performance & Security', 'opulentia' ),
            'compatibility' => __( 'Compatibility', 'opulentia' ),
            'general'       => __( 'General', 'opulentia' ),
        );
    }

    /**
     * Get IDs of active modules.
     *
     * @return array
     */
    private function get_active_module_ids() {
        $modules = $this->get_all_modules();
        $active  = array();

        foreach ( $modules as $slug => $module ) {
            $setting_id = ! empty( $module['setting_id'] ) ? $module['setting_id'] : 'module_' . $slug;
            $active_setting = Opulentia_get_option( $setting_id, null );
            if ( null === $active_setting ) {
                $default = isset( $module['default'] ) ? $module['default'] : true;
                if ( $default ) {
                    $active[] = $slug;
                }
            } elseif ( $active_setting ) {
                $active[] = $slug;
            }
        }

        return $active;
    }

    /**
     * Get active modules count.
     *
     * @return array Module slugs that are active.
     */
    private function get_active_modules() {
        return $this->get_active_module_ids();
    }

    /**
     * Check if a module's dependencies are met.
     *
     * @param  string $slug       Module slug.
     * @param  array  $active_ids Currently active module slugs.
     * @return bool
     */
    private function check_dependencies( $slug, $active_ids ) {
        $modules = $this->get_all_modules();
        if ( ! isset( $modules[ $slug ] ) || empty( $modules[ $slug ]['dependencies'] ) ) {
            return true;
        }

        foreach ( $modules[ $slug ]['dependencies'] as $dep ) {
            if ( ! in_array( $dep, $active_ids, true ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get names of missing dependencies for a module.
     *
     * @param  string $slug Module slug.
     * @return array
     */
    private function get_missing_dep_names( $slug ) {
        $modules   = $this->get_all_modules();
        $active    = $this->get_active_module_ids();
        $missing   = array();

        if ( ! isset( $modules[ $slug ] ) || empty( $modules[ $slug ]['dependencies'] ) ) {
            return $missing;
        }

        foreach ( $modules[ $slug ]['dependencies'] as $dep ) {
            if ( ! in_array( $dep, $active, true ) && isset( $modules[ $dep ] ) ) {
                $missing[] = $modules[ $dep ]['name'];
            }
        }

        return $missing;
    }

    /**
     * Add action links to the themes page.
     *
     * @param  array $links Existing action links.
     * @return array
     */
    public function add_action_links( $links ) {
        $dashboard_link = '<a href="' . esc_url( admin_url( 'themes.php?page=Opulentia' ) ) . '">' .
            esc_html__( 'Dashboard', 'opulentia' ) . '</a>';
        array_unshift( $links, $dashboard_link );

        $customizer_link = '<a href="' . esc_url( admin_url( 'customize.php' ) ) . '">' .
            esc_html__( 'Customize', 'opulentia' ) . '</a>';
        array_unshift( $links, $customizer_link );

        return $links;
    }

    // -------------------------------------------------------------------------
    // AJAX Handlers
    // -------------------------------------------------------------------------

    /**
     * AJAX handler for toggling a module on/off.
     */
    public function ajax_toggle_module() {
        check_ajax_referer( 'Opulentia_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'opulentia' ) ) );
        }

        $slug   = isset( $_POST['slug'] ) ? sanitize_key( $_POST['slug'] ) : '';
        $active = isset( $_POST['active'] ) ? 'true' === $_POST['active'] : false;

        if ( empty( $slug ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid module slug.', 'opulentia' ) ) );
        }

        $manager = Opulentia_Modules::get_instance();
        if ( ! $manager->is_registered( $slug ) ) {
            wp_send_json_error( array( 'message' => __( 'Module not found.', 'opulentia' ) ) );
        }

        $modules = $manager->get_all();
        $module  = $modules[ $slug ];
        $setting_id = ! empty( $module['setting_id'] ) ? $module['setting_id'] : 'module_' . $slug;

        if ( $active ) {
            $manager->activate( $slug );
            $message = sprintf(
                /* translators: %s = module name */
                __( '"%s" module activated.', 'opulentia' ),
                $module['name']
            );
        } else {
            $manager->deactivate( $slug );
            $message = sprintf(
                /* translators: %s = module name */
                __( '"%s" module deactivated.', 'opulentia' ),
                $module['name']
            );
        }

        $active_ids = $this->get_active_module_ids();

        wp_send_json_success( array(
            'message'     => $message,
            'slug'        => $slug,
            'active'      => $active,
            'activeCount' => count( $active_ids ),
        ) );
    }

    /**
     * AJAX handler for exporting settings as JSON.
     */
    public function ajax_export_settings() {
        check_ajax_referer( 'Opulentia_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'opulentia' ) ) );
        }

        $settings = get_option( Opulentia_SETTINGS, array() );

        $export = array(
            'version'   => Opulentia_VERSION,
            'date'      => current_time( 'mysql' ),
            'site_url'  => home_url(),
            'theme'     => wp_get_theme()->get( 'Name' ),
            'settings'  => $settings,
        );

        $json = wp_json_encode( $export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

        // Set headers for file download via AJAX.
        $filename = 'opulentia-settings-' . date( 'Y-m-d' ) . '.json';

        wp_send_json_success( array(
            'filename' => $filename,
            'content'  => $json,
        ) );
    }

    /**
     * AJAX handler for importing settings from JSON.
     */
    public function ajax_import_settings() {
        check_ajax_referer( 'Opulentia_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'opulentia' ) ) );
        }

        if ( ! isset( $_FILES['file'] ) || UPLOAD_ERR_OK !== $_FILES['file']['error'] ) {
            wp_send_json_error( array( 'message' => __( 'No file uploaded or upload error.', 'opulentia' ) ) );
        }

        $file_content = file_get_contents( $_FILES['file']['tmp_name'] );
        if ( false === $file_content ) {
            wp_send_json_error( array( 'message' => __( 'Could not read uploaded file.', 'opulentia' ) ) );
        }

        $data = json_decode( $file_content, true );
        if ( null === $data || ! isset( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid export file format.', 'opulentia' ) ) );
        }

        // Validate theme name.
        $expected_theme = wp_get_theme()->get( 'Name' );
        if ( isset( $data['theme'] ) && $data['theme'] !== $expected_theme ) {
            wp_send_json_error( array(
                'message' => sprintf(
                    /* translators: %s = theme name */
                    __( 'This export file was created for "%s" theme. Import may cause unexpected behavior.', 'opulentia' ),
                    $data['theme']
                ),
            ) );
        }

        update_option( Opulentia_SETTINGS, $data['settings'] );

        // Clear any cached data.
        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }

        do_action( 'Opulentia_settings_imported', $data['settings'] );

        wp_send_json_success( array(
            'message' => sprintf(
                /* translators: %d = number of settings imported */
                __( 'Successfully imported %d settings.', 'opulentia' ),
                count( $data['settings'] )
            ),
        ) );
    }

    /**
     * AJAX handler for resetting settings to defaults.
     */
    public function ajax_reset_settings() {
        check_ajax_referer( 'Opulentia_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'opulentia' ) ) );
        }

        delete_option( Opulentia_SETTINGS );

        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }

        do_action( 'Opulentia_settings_reset' );

        wp_send_json_success( array(
            'message' => __( 'All theme settings have been reset to defaults. Refreshing page...', 'opulentia' ),
        ) );
    }

    // -------------------------------------------------------------------------
    // Inline JavaScript
    // -------------------------------------------------------------------------

    /**
     * Get inline admin JavaScript.
     *
     * @return string
     */
    private function get_admin_js() {
        return '
        (function($) {
            var admin = OpulentiaAdmin;

            // --- Module Toggle ---
            $(document).on("change", ".js-opulentia-toggle-module", function() {
                var $checkbox = $(this);
                var $row      = $checkbox.closest(".opulentia-module-row");
                var slug      = $checkbox.data("slug");
                var active    = $checkbox.prop("checked");
                var $msg      = $("#opulentia-module-message");
                var $count    = $("#opulentia-active-count");

                $checkbox.prop("disabled", true);

                $.post(admin.ajaxUrl, {
                    action: "Opulentia_toggle_module",
                    nonce: admin.nonce,
                    slug: slug,
                    active: active ? "true" : "false"
                }, function(response) {
                    if (response.success) {
                        $msg.removeClass("notice-error").addClass("notice-success")
                            .html("<p>" + response.data.message + "</p>").show();
                        $row.toggleClass("is-active", response.data.active)
                            .toggleClass("is-inactive", !response.data.active);
                        $row.find(".opulentia-module-status")
                            .text(response.data.active ? "ON" : "OFF")
                            .toggleClass("is-active", response.data.active)
                            .toggleClass("is-inactive", !response.data.active);
                        if ($count.length) {
                            $count.text(response.data.activeCount);
                        }
                        setTimeout(function() { $msg.fadeOut(); }, 3000);
                    } else {
                        $msg.removeClass("notice-success").addClass("notice-error")
                            .html("<p>" + (response.data && response.data.message ? response.data.message : admin.i18n.error) + "</p>").show();
                        $checkbox.prop("checked", !active);
                    }
                }).always(function() {
                    $checkbox.prop("disabled", false);
                });
            });

            // --- Export ---
            $("#opulentia-export-btn").on("click", function() {
                var $btn = $(this);
                $btn.addClass("is-busy").prop("disabled", true);
                $btn.find(".opulentia-btn-text").text(admin.i18n.exporting);

                $.post(admin.ajaxUrl, {
                    action: "Opulentia_export_settings",
                    nonce: admin.nonce
                }, function(response) {
                    if (response.success && response.data.content) {
                        var blob = new Blob([response.data.content], { type: "application/json" });
                        var url  = URL.createObjectURL(blob);
                        var a    = document.createElement("a");
                        a.href   = url;
                        a.download = response.data.filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    } else {
                        alert(admin.i18n.error);
                    }
                }).always(function() {
                    $btn.removeClass("is-busy").prop("disabled", false);
                    $btn.find(".opulentia-btn-text").text("' . esc_js( __( 'Download Export File', 'opulentia' ) ) . '");
                });
            });

            // --- Import: enable button when file selected ---
            $("#opulentia-import-file").on("change", function() {
                $("#opulentia-import-btn").prop("disabled", !this.files.length);
            });

            // --- Import ---
            $("#opulentia-import-btn").on("click", function() {
                var $btn  = $(this);
                var $file = $("#opulentia-import-file");
                var $result = $("#opulentia-import-result");

                if (!$file[0].files.length) return;
                if (!confirm(admin.i18n.confirmImport)) return;

                $btn.addClass("is-busy").prop("disabled", true);
                $btn.find(".opulentia-btn-text").text(admin.i18n.importing);
                $result.hide();

                var fd = new FormData();
                fd.append("action", "Opulentia_import_settings");
                fd.append("nonce", admin.nonce);
                fd.append("file", $file[0].files[0]);

                $.ajax({
                    url: admin.ajaxUrl,
                    type: "POST",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            $result.removeClass("notice-error").addClass("notice-success")
                                .html("<p>" + response.data.message + "</p>").show();
                            $file.val("");
                            $btn.prop("disabled", true);
                        } else {
                            $result.removeClass("notice-success").addClass("notice-error")
                                .html("<p>" + (response.data && response.data.message ? response.data.message : admin.i18n.importError) + "</p>").show();
                        }
                    },
                    error: function() {
                        $result.removeClass("notice-success").addClass("notice-error")
                            .html("<p>" + admin.i18n.importError + "</p>").show();
                    }
                }).always(function() {
                    $btn.removeClass("is-busy").prop("disabled", false);
                    $btn.find(".opulentia-btn-text").text("' . esc_js( __( 'Import Settings', 'opulentia' ) ) . '");
                });
            });

            // --- Reset ---
            $("#opulentia-reset-btn").on("click", function() {
                if (!confirm("' . esc_js( __( 'Are you sure you want to reset all theme settings to defaults? This cannot be undone.', 'opulentia' ) ) . '")) return;
                if (!confirm("' . esc_js( __( 'FINAL WARNING: This will permanently delete all customizations. Proceed?', 'opulentia' ) ) . '")) return;

                var $btn = $(this);
                $btn.prop("disabled", true).text("' . esc_js( __( 'Resetting...', 'opulentia' ) ) . '");

                $.post(admin.ajaxUrl, {
                    action: "Opulentia_reset_settings",
                    nonce: admin.nonce
                }, function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(admin.i18n.error);
                        $btn.prop("disabled", false).text("' . esc_js( __( 'Reset to Defaults', 'opulentia' ) ) . '");
                    }
                });
            });
        })(jQuery);
        ';
    }

    private function render_cloner_tab() {
        $nonce = wp_create_nonce( 'opulentia_cloner_nonce' );
        ?>
        <div class="opulentia-dashboard__card">
            <?php include Opulentia_DIR . '/admin/cloner-page.php'; ?>
            <script>
            jQuery( function() {
                if ( typeof opulentiaCloner === 'undefined' ) {
                    window.opulentiaCloner = { nonce: '<?php echo esc_js( $nonce ); ?>' };
                }
            });
            </script>
        </div>
        <?php
    }
}

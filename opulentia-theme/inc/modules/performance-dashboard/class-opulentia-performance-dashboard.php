<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Performance_Dashboard {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
        add_action( 'wp_ajax_opulentia_performance_scan', array( $this, 'ajax_performance_scan' ) );
        add_action( 'wp_ajax_opulentia_pageSpeed_check', array( $this, 'ajax_pageSpeed_check' ) );

        if ( get_theme_mod( 'op_perf_lazy_load', true ) ) {
            add_filter( 'wp_lazy_loading_enabled', '__return_true' );
        }

        if ( get_theme_mod( 'op_perf_minify_css', false ) ) {
            add_filter( 'opulentia_minify_css', '__return_true' );
        }
    }

    public function admin_menu() {
        $hook = add_theme_page(
            __( 'Performance', 'opulentia' ),
            __( 'Performance', 'opulentia' ),
            'manage_options',
            'opulentia-performance',
            array( $this, 'render_admin_page' )
        );
    }

    public function admin_enqueue( $hook ) {
        if ( 'appearance_page_opulentia-performance' !== $hook ) {
            return;
        }
        wp_enqueue_style( 'wp-admin' );
        wp_enqueue_script(
            'opulentia-performance',
            Opulentia_URI . '/js/performance-dashboard.js',
            array( 'jquery' ),
            Opulentia_VERSION,
            true
        );
        wp_localize_script( 'opulentia-performance', 'OpulentiaPerf', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'opulentia_perf_nonce' ),
        ) );
    }

    public function customize_register( $wp_customize ) {
        $wp_customize->add_section( 'op_performance', array(
            'title'       => __( 'Performance', 'opulentia' ),
            'description' => __( 'Lazy loading, minification, preload, and font-display settings.', 'opulentia' ),
            'priority'    => 130,
        ) );

        $wp_customize->add_setting( 'op_perf_lazy_load', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_perf_lazy_load', array(
            'label'   => __( 'Lazy Load Images', 'opulentia' ),
            'section' => 'op_performance',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'op_perf_minify_css', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_perf_minify_css', array(
            'label'   => __( 'Minify CSS', 'opulentia' ),
            'section' => 'op_performance',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'op_perf_minify_js', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_perf_minify_js', array(
            'label'   => __( 'Minify JavaScript', 'opulentia' ),
            'section' => 'op_performance',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'op_perf_preconnect', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_perf_preconnect', array(
            'label'       => __( 'Preconnect URLs', 'opulentia' ),
            'description' => __( 'Comma-separated URLs for preconnect hints.', 'opulentia' ),
            'section'     => 'op_performance',
            'type'        => 'text',
        ) );

        $wp_customize->add_setting( 'op_perf_preload', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_perf_preload', array(
            'label'       => __( 'Preload Font URLs', 'opulentia' ),
            'description' => __( 'Comma-separated font URLs to preload.', 'opulentia' ),
            'section'     => 'op_performance',
            'type'        => 'text',
        ) );

        $wp_customize->add_setting( 'op_perf_font_display', array(
            'default'           => 'swap',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_perf_font_display', array(
            'label'   => __( 'Font Display', 'opulentia' ),
            'section' => 'op_performance',
            'type'    => 'select',
            'choices' => array(
                'swap'     => __( 'Swap', 'opulentia' ),
                'optional' => __( 'Optional', 'opulentia' ),
                'block'    => __( 'Block', 'opulentia' ),
                'fallback' => __( 'Fallback', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'op_perf_pageSpeed_api', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_perf_pageSpeed_api', array(
            'label'       => __( 'PageSpeed Insights API Key', 'opulentia' ),
            'description' => __( 'Optional. Get a free key at https://developers.google.com/speed/docs/insights/v5/get-started', 'opulentia' ),
            'section'     => 'op_performance',
            'type'        => 'text',
        ) );
    }

    public function render_admin_page() {
        $modules = $this->get_module_sizes();
        $total_size = array_sum( array_column( $modules, 'size' ) );
        $total_count = count( $modules );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Performance Dashboard', 'opulentia' ); ?></h1>

            <div id="op-perf-message" style="display:none"></div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin:20px 0">
                <div class="card" style="padding:20px;text-align:center">
                    <h2 style="margin:0;font-size:36px"><?php echo count( $this->get_module_sizes() ); ?></h2>
                    <p style="margin:5px 0 0"><?php esc_html_e( 'Active Modules', 'opulentia' ); ?></p>
                </div>
                <div class="card" style="padding:20px;text-align:center">
                    <h2 style="margin:0;font-size:36px"><?php echo esc_html( size_format( $total_size ) ); ?></h2>
                    <p style="margin:5px 0 0"><?php esc_html_e( 'Estimated CSS+JS Weight', 'opulentia' ); ?></p>
                </div>
                <div class="card" style="padding:20px;text-align:center">
                    <h2 style="margin:0;font-size:36px" id="op-perf-lighthouse">--</h2>
                    <p style="margin:5px 0 0"><?php esc_html_e( 'PageSpeed Score', 'opulentia' ); ?></p>
                </div>
            </div>

            <div class="card" style="padding:20px;">
                <h2><?php esc_html_e( 'Module Impact Report', 'opulentia' ); ?></h2>
                <p><?php esc_html_e( 'Estimated asset weight contributed by each module:', 'opulentia' ); ?></p>
                <table class="wp-list-table widefat striped" style="margin-top:10px">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Module', 'opulentia' ); ?></th>
                            <th><?php esc_html_e( 'CSS', 'opulentia' ); ?></th>
                            <th><?php esc_html_e( 'JS', 'opulentia' ); ?></th>
                            <th><?php esc_html_e( 'Total', 'opulentia' ); ?></th>
                            <th><?php esc_html_e( 'Impact', 'opulentia' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $modules as $slug => $mod ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $mod['label'] ); ?></strong></td>
                            <td><?php echo $mod['css'] ? esc_html( size_format( $mod['css'] ) ) : '—'; ?></td>
                            <td><?php echo $mod['js'] ? esc_html( size_format( $mod['js'] ) ) : '—'; ?></td>
                            <td><?php echo esc_html( size_format( $mod['size'] ) ); ?></td>
                            <td>
                                <?php if ( $total_size > 0 ) : ?>
                                    <span class="op-perf-impact" style="display:inline-block;height:16px;width:<?php echo esc_attr( round( ( $mod['size'] / max( $total_size, 1 ) ) * 100 ) ); ?>px;background:<?php echo $mod['size'] > 50000 ? '#d63638' : ( $mod['size'] > 10000 ? '#f0b849' : '#46b450' ); ?>;border-radius:2px;vertical-align:middle"></span>
                                    <?php echo round( ( $mod['size'] / max( $total_size, 1 ) ) * 100, 1 ); ?>%
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card" style="padding:20px;margin-top:20px">
                <h2><?php esc_html_e( 'Recommendations', 'opulentia' ); ?></h2>
                <ul style="list-style:disc;padding-left:20px">
                    <?php foreach ( $this->get_recommendations() as $rec ) : ?>
                        <li><strong><?php echo esc_html( $rec['title'] ); ?>:</strong> <?php echo esc_html( $rec['description'] ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card" style="padding:20px;margin-top:20px">
                <h2><?php esc_html_e( 'Actions', 'opulentia' ); ?></h2>
                <p>
                    <button class="button button-secondary" id="op-perf-scan-btn"><?php esc_html_e( 'Run Performance Scan', 'opulentia' ); ?></button>
                    <button class="button button-secondary" id="op-perf-pageSpeed-btn"><?php esc_html_e( 'Check PageSpeed Score', 'opulentia' ); ?></button>
                </p>
                <div id="op-perf-scan-results" style="margin-top:10px"></div>
            </div>
        </div>
        <?php
    }

    private function get_module_sizes() {
        $modules_dir = Opulentia_DIR . '/inc/modules';
        $modules = array();

        if ( ! is_dir( $modules_dir ) ) {
            return $modules;
        }

        $dirs = glob( $modules_dir . '/*', GLOB_ONLYDIR );
        foreach ( $dirs as $dir ) {
            $slug = basename( $dir );
            $php_files = glob( $dir . '/*.php' );
            $js_size = 0;
            $css_size = 0;

            $js_files = glob( $dir . '/*.js' );
            foreach ( $js_files as $js ) {
                $js_size += filesize( $js );
            }

            $css_files = glob( $dir . '/*.css' );
            foreach ( $css_files as $css ) {
                $css_size += filesize( $css );
            }

            $modules[ $slug ] = array(
                'label' => ucwords( str_replace( array( '-', '_' ), ' ', $slug ) ),
                'css'   => $css_size,
                'js'    => $js_size,
                'size'  => $css_size + $js_size,
            );
        }

        uasort( $modules, function ( $a, $b ) {
            return $b['size'] - $a['size'];
        } );

        return $modules;
    }

    private function get_recommendations() {
        $recs = array();

        if ( ! get_theme_mod( 'op_perf_lazy_load', true ) ) {
            $recs[] = array(
                'title'       => __( 'Enable Lazy Loading', 'opulentia' ),
                'description' => __( 'Lazy loading defers off-screen images, reducing initial page weight.', 'opulentia' ),
            );
        }

        if ( ! get_theme_mod( 'op_perf_minify_css', false ) ) {
            $recs[] = array(
                'title'       => __( 'Enable CSS Minification', 'opulentia' ),
                'description' => __( 'Minifying CSS can reduce file sizes by 20-30%.', 'opulentia' ),
            );
        }

        if ( ! get_theme_mod( 'op_perf_minify_js', false ) ) {
            $recs[] = array(
                'title'       => __( 'Enable JS Minification', 'opulentia' ),
                'description' => __( 'Minifying JavaScript reduces download time and parsing cost.', 'opulentia' ),
            );
        }

        if ( ! get_theme_mod( 'op_perf_pageSpeed_api', '' ) ) {
            $recs[] = array(
                'title'       => __( 'Add PageSpeed API Key', 'opulentia' ),
                'description' => __( 'A free Google API key enables real-time performance scoring from the dashboard.', 'opulentia' ),
            );
        }

        if ( 'swap' !== get_theme_mod( 'op_perf_font_display', 'swap' ) ) {
            $recs[] = array(
                'title'       => __( 'Use Font Display Swap', 'opulentia' ),
                'description' => __( 'font-display: swap ensures text remains visible during web font load.', 'opulentia' ),
            );
        }

        if ( ! $recs ) {
            $recs[] = array(
                'title'       => __( 'Great Job!', 'opulentia' ),
                'description' => __( 'All performance recommendations are addressed.', 'opulentia' ),
            );
        }

        return $recs;
    }

    public function ajax_performance_scan() {
        check_ajax_referer( 'opulentia_perf_nonce', 'nonce' );

        $modules = $this->get_module_sizes();
        $total   = array_sum( array_column( $modules, 'size' ) );

        $data = array(
            'total_size'  => size_format( $total ),
            'module_count' => count( $modules ),
            'largest'     => ! empty( $modules ) ? reset( $modules )['label'] : '—',
        );

        wp_send_json_success( $data );
    }

    public function ajax_pageSpeed_check() {
        check_ajax_referer( 'opulentia_perf_nonce', 'nonce' );

        $api_key = get_theme_mod( 'op_perf_pageSpeed_api', '' );
        if ( empty( $api_key ) ) {
            wp_send_json_error( array(
                'message' => __( 'No API key configured. Add it in Customizer > Performance.', 'opulentia' ),
            ) );
        }

        $url = home_url();
        $api_url = add_query_arg( array(
            'url'     => rawurlencode( $url ),
            'key'     => $api_key,
            'strategy' => 'mobile',
        ), 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed' );

        $response = wp_remote_get( $api_url, array( 'timeout' => 30 ) );
        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! isset( $data['lighthouseResult']['categories']['performance']['score'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'Could not retrieve PageSpeed score. Check your API key.', 'opulentia' ),
            ) );
        }

        $score = (int) round( $data['lighthouseResult']['categories']['performance']['score'] * 100 );

        wp_send_json_success( array(
            'score'       => $score,
            'score_label' => $score >= 90 ? __( 'Good', 'opulentia' ) : ( $score >= 50 ? __( 'Needs Improvement', 'opulentia' ) : __( 'Poor', 'opulentia' ) ),
            'url'         => $url,
        ) );
    }
}

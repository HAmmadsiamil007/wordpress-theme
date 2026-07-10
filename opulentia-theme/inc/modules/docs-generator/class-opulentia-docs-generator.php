<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Docs_Generator {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function admin_menu() {
        add_submenu_page(
            'opulentia-theme',
            __( 'Documentation Generator', 'opulentia' ),
            __( 'Docs Generator', 'opulentia' ),
            'manage_options',
            'opulentia-docs',
            array( $this, 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( 'opulentia_page_opulentia-docs' !== $hook ) {
            return;
        }
        wp_add_inline_style( 'wp-admin', '
.op-docs-wrap{max-width:1000px;margin:20px 0}
.op-docs-scan{background:#1d2327;color:#f0f0f1;padding:12px 16px;border-radius:4px;font-family:monospace;font-size:13px;line-height:1.6;max-height:400px;overflow:auto;white-space:pre-wrap;word-break:break-all;margin:10px 0}
.op-docs-section{background:#fff;border:1px solid #c3c4c7;padding:20px;margin:16px 0;border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.04)}
.op-docs-section h2{margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px}
.op-docs-table{width:100%;border-collapse:collapse}
.op-docs-table th,.op-docs-table td{text-align:left;padding:8px 10px;border-bottom:1px solid #f0f0f1;font-size:13px}
.op-docs-table th{background:#f6f7f7;font-weight:600}
.op-docs-badge{display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:600;text-transform:uppercase}
.op-docs-badge--module{background:#e6f0fa;color:#1d5aa3}
.op-docs-badge--filter{background:#f0e6fa;color:#7b1da3}
.op-docs-badge--hook{background:#e6fae6;color:#1d7a1d}
.op-docs-badge--shortcode{background:#faf0e6;color:#a35b1d}
');
    }

    public function render_page() {
        if ( isset( $_GET['generate'] ) && check_admin_referer( 'op_docs_generate' ) ) {
            $docs = $this->generate_docs();
            $html = $this->render_html( $docs );
            echo '<div class="wrap op-docs-wrap">';
            echo '<h1>' . esc_html__( 'Generated Documentation', 'opulentia' ) . '</h1>';
            echo '<div class="op-docs-scan">' . esc_html( $html ) . '</div>';
            echo '<form method="post">';
            wp_nonce_field( 'op_docs_download' );
            echo '<input type="hidden" name="op_docs_html" value="' . esc_attr( $html ) . '">';
            submit_button( __( 'Download HTML', 'opulentia' ), 'primary', 'op_docs_download', false );
            echo '</form>';
            echo '</div>';
            return;
        }

        if ( isset( $_POST['op_docs_download'] ) && check_admin_referer( 'op_docs_download' ) ) {
            $html = wp_unslash( $_POST['op_docs_html'] );
            header( 'Content-Type: text/html; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=opulentia-documentation.html' );
            echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . esc_html__( 'Opulentia Documentation', 'opulentia' ) . '</title>';
            echo '<style>body{font-family:Inter,sans-serif;max-width:900px;margin:40px auto;padding:0 20px;color:#1a1a1a;line-height:1.6}h1{border-bottom:3px solid #c9a96e;padding-bottom:10px}table{width:100%;border-collapse:collapse;margin:16px 0}th,td{text-align:left;padding:8px 12px;border-bottom:1px solid #ddd}th{background:#f5f5f5;font-weight:600}code{background:#f0f0f0;padding:2px 6px;border-radius:3px;font-size:13px}.badge{display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:600;text-transform:uppercase;margin-right:4px}.badge-module{background:#e6f0fa;color:#1d5aa3}.badge-filter{background:#f0e6fa;color:#7b1da3}.badge-hook{background:#e6fae6;color:#1d7a1d}.badge-shortcode{background:#faf0e6;color:#a35b1d}</style></head><body>';
            echo $html;
            echo '</body></html>';
            exit;
        }

        echo '<div class="wrap op-docs-wrap">';
        echo '<h1>' . esc_html__( 'Documentation Generator', 'opulentia' ) . '</h1>';
        echo '<p>' . esc_html__( 'Scan all theme files to auto-generate documentation including module list, shortcodes, filters, actions, and template hierarchy.', 'opulentia' ) . '</p>';
        echo '<div class="op-docs-section"><h2>' . esc_html__( 'What will be generated:', 'opulentia' ) . '</h2>';
        echo '<ul style="list-style:disc;padding-left:20px">';
        echo '<li>' . esc_html__( 'Module inventory with descriptions and dependencies', 'opulentia' ) . '</li>';
        echo '<li>' . esc_html__( 'Shortcode reference', 'opulentia' ) . '</li>';
        echo '<li>' . esc_html__( 'Filter and action hook reference', 'opulentia' ) . '</li>';
        echo '<li>' . esc_html__( 'Template hierarchy diagram', 'opulentia' ) . '</li>';
        echo '<li>' . esc_html__( 'Customizer panel/section list', 'opulentia' ) . '</li>';
        echo '</ul></div>';
        echo '<form method="get">';
        wp_nonce_field( 'op_docs_generate' );
        echo '<input type="hidden" name="page" value="opulentia-docs">';
        echo '<input type="hidden" name="generate" value="1">';
        submit_button( __( 'Generate Documentation', 'opulentia' ), 'primary' );
        echo '</form>';
        echo '</div>';
    }

    private function generate_docs() {
        $theme = wp_get_theme();
        return array(
            'theme_name'    => $theme->get( 'Name' ),
            'theme_version' => $theme->get( 'Version' ),
            'generated'     => current_time( 'mysql' ),
            'modules'       => $this->scan_modules(),
            'shortcodes'    => $this->scan_shortcodes(),
            'filters'       => $this->scan_filters(),
            'actions'       => $this->scan_actions(),
            'templates'     => $this->scan_templates(),
            'customizer'    => $this->scan_customizer(),
        );
    }

    private function scan_modules() {
        $modules_dir = Opulentia_DIR . '/inc/modules';
        if ( ! is_dir( $modules_dir ) ) {
            return array();
        }

        $modules = array();
        $items   = scandir( $modules_dir );

        foreach ( $items as $item ) {
            if ( '.' === $item || '..' === $item || ! is_dir( $modules_dir . '/' . $item ) ) {
                continue;
            }
            $files = glob( $modules_dir . '/' . $item . '/class-*.php' );
            if ( ! empty( $files ) ) {
                $content  = file_get_contents( $files[0] );
                $name     = $this->extract_phpdoc_tag( $content, 'package' ) ?: $item;
                $desc     = $this->extract_class_description( $content );
                $filters  = substr_count( $content, 'apply_filters' );
                $actions  = substr_count( $content, 'do_action' );
                $modules[] = array(
                    'slug'    => $item,
                    'name'    => ucwords( str_replace( '-', ' ', $item ) ),
                    'desc'    => $desc,
                    'filters' => $filters,
                    'actions' => $actions,
                    'file'    => basename( $files[0] ),
                );
            }
        }

        return $modules;
    }

    private function scan_shortcodes() {
        $shortcodes = array();
        $files      = $this->get_php_files( Opulentia_DIR . '/inc' );

        foreach ( $files as $file ) {
            $content = file_get_contents( $file );
            if ( preg_match_all( "/add_shortcut\s*\(\s*'([^']+)'/", $content, $matches ) ) {
                foreach ( $matches[1] as $tag ) {
                    $shortcodes[ $tag ] = array(
                        'tag'  => $tag,
                        'file' => str_replace( Opulentia_DIR . '/', '', $file ),
                    );
                }
            }
            if ( preg_match_all( "/add_shortcode\s*\(\s*'([^']+)'/", $content, $matches ) ) {
                foreach ( $matches[1] as $tag ) {
                    $shortcodes[ $tag ] = array(
                        'tag'  => $tag,
                        'file' => str_replace( Opulentia_DIR . '/', '', $file ),
                    );
                }
            }
        }

        return array_values( $shortcodes );
    }

    private function scan_filters() {
        $filters = array();
        $files   = $this->get_php_files( Opulentia_DIR . '/inc' );

        foreach ( $files as $file ) {
            $content = file_get_contents( $file );
            if ( preg_match_all( "/apply_filters\s*\(\s*'([^']+)'/", $content, $matches ) ) {
                foreach ( $matches[1] as $tag ) {
                    $filters[ $tag ] = array(
                        'tag'  => $tag,
                        'file' => str_replace( Opulentia_DIR . '/', '', $file ),
                    );
                }
            }
        }

        ksort( $filters );
        return array_values( $filters );
    }

    private function scan_actions() {
        $actions = array();
        $files   = $this->get_php_files( Opulentia_DIR . '/inc' );

        foreach ( $files as $file ) {
            $content = file_get_contents( $file );
            if ( preg_match_all( "/do_action\s*\(\s*'([^']+)'/", $content, $matches ) ) {
                foreach ( $matches[1] as $tag ) {
                    $actions[ $tag ] = array(
                        'tag'  => $tag,
                        'file' => str_replace( Opulentia_DIR . '/', '', $file ),
                    );
                }
            }
        }

        ksort( $actions );
        return array_values( $actions );
    }

    private function scan_templates() {
        $root     = Opulentia_DIR;
        $files    = array( 'index.php', 'front-page.php', 'home.php', 'page.php', 'single.php', 'archive.php', 'search.php', '404.php', 'header.php', 'footer.php', 'sidebar.php', 'comments.php' );
        $found    = array();
        $page_tpl = array();

        if ( is_dir( $root . '/page-templates' ) ) {
            foreach ( scandir( $root . '/page-templates' ) as $f ) {
                if ( '.php' === substr( $f, -4 ) ) {
                    $page_tpl[] = $f;
                }
            }
        }

        foreach ( $files as $f ) {
            $found[ $f ] = file_exists( $root . '/' . $f );
        }

        return array(
            'core'           => $found,
            'page_templates' => $page_tpl,
        );
    }

    private function scan_customizer() {
        $panels = array();
        $files  = $this->get_php_files( Opulentia_DIR . '/inc' );

        foreach ( $files as $file ) {
            $content = file_get_contents( $file );
            if ( preg_match_all( "/\\\$wp_customize->add_panel\s*\(\s*'([^']+)'[^;]+'title'\s*=>\s*__\s*\(\s*'([^']+)'/s", $content, $matches, PREG_SET_ORDER ) ) {
                foreach ( $matches as $m ) {
                    $panels[ $m[1] ] = $m[2];
                }
            }
            if ( preg_match_all( "/\\\$wp_customize->add_section\s*\(\s*'([^']+)'[^;]+'title'\s*=>\s*__\s*\(\s*'([^']+)'/s", $content, $matches, PREG_SET_ORDER ) ) {
                foreach ( $matches as $m ) {
                    if ( ! isset( $panels[ $m[1] ] ) ) {
                        $panels[ $m[1] ] = $m[2];
                    }
                }
            }
        }

        return $panels;
    }

    private function render_html( $docs ) {
        $html = '<h1>' . esc_html( $docs['theme_name'] ) . ' v' . esc_html( $docs['theme_version'] ) . ' — Documentation</h1>';
        $html .= '<p><em>Generated: ' . esc_html( $docs['generated'] ) . '</em></p>';

        // Modules
        $html .= '<h2>Module Inventory</h2>';
        if ( ! empty( $docs['modules'] ) ) {
            $html .= '<table><thead><tr><th>Module</th><th>Description</th><th>Filters</th><th>Actions</th></tr></thead><tbody>';
            foreach ( $docs['modules'] as $m ) {
                $html .= '<tr><td><span class="badge badge-module">' . esc_html( $m['slug'] ) . '</span></td><td>' . esc_html( $m['desc'] ) . '</td><td>' . intval( $m['filters'] ) . '</td><td>' . intval( $m['actions'] ) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No modules found.</p>';
        }

        // Shortcodes
        $html .= '<h2>Shortcodes</h2>';
        if ( ! empty( $docs['shortcodes'] ) ) {
            $html .= '<table><thead><tr><th>Tag</th><th>File</th></tr></thead><tbody>';
            foreach ( $docs['shortcodes'] as $s ) {
                $html .= '<tr><td><code>[' . esc_html( $s['tag'] ) . ']</code></td><td>' . esc_html( $s['file'] ) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No shortcodes found.</p>';
        }

        // Filters
        $html .= '<h2>Filters (' . count( $docs['filters'] ) . ')</h2>';
        if ( ! empty( $docs['filters'] ) ) {
            $html .= '<table><thead><tr><th>Tag</th><th>File</th></tr></thead><tbody>';
            foreach ( $docs['filters'] as $f ) {
                $html .= '<tr><td><code>' . esc_html( $f['tag'] ) . '</code></td><td>' . esc_html( $f['file'] ) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        }

        // Actions
        $html .= '<h2>Actions (' . count( $docs['actions'] ) . ')</h2>';
        if ( ! empty( $docs['actions'] ) ) {
            $html .= '<table><thead><tr><th>Tag</th><th>File</th></tr></thead><tbody>';
            foreach ( $docs['actions'] as $a ) {
                $html .= '<tr><td><code>' . esc_html( $a['tag'] ) . '</code></td><td>' . esc_html( $a['file'] ) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        }

        // Templates
        $html .= '<h2>Template Hierarchy</h2>';
        $html .= '<table><thead><tr><th>Template</th><th>Exists</th></tr></thead><tbody>';
        foreach ( $docs['templates']['core'] as $tpl => $exists ) {
            $html .= '<tr><td><code>' . esc_html( $tpl ) . '</code></td><td>' . ( $exists ? '✓' : '✗' ) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        if ( ! empty( $docs['templates']['page_templates'] ) ) {
            $html .= '<h3>Page Templates</h3><ul>';
            foreach ( $docs['templates']['page_templates'] as $pt ) {
                $html .= '<li><code>' . esc_html( $pt ) . '</code></li>';
            }
            $html .= '</ul>';
        }

        // Customizer
        $html .= '<h2>Customizer Panels & Sections</h2>';
        if ( ! empty( $docs['customizer'] ) ) {
            $html .= '<table><thead><tr><th>ID</th><th>Title</th></tr></thead><tbody>';
            foreach ( $docs['customizer'] as $id => $title ) {
                $html .= '<tr><td><code>' . esc_html( $id ) . '</code></td><td>' . esc_html( $title ) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        }

        return $html;
    }

    private function get_php_files( $dir ) {
        $files = array();
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS )
        );
        foreach ( $items as $item ) {
            if ( $item->isFile() && 'php' === $item->getExtension() ) {
                $files[] = $item->getPathname();
            }
        }
        return $files;
    }

    private function extract_phpdoc_tag( $content, $tag ) {
        if ( preg_match( '/@' . $tag . '\s+(.+)/i', $content, $m ) ) {
            return trim( $m[1] );
        }
        return '';
    }

    private function extract_class_description( $content ) {
        if ( preg_match( '/\/\*\*\s*\n\s+\*\s(.+)/', $content, $m ) ) {
            return trim( $m[1] );
        }
        return '';
    }
}

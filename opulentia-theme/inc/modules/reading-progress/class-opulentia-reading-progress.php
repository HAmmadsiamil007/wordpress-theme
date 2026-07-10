<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Reading_Progress {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_progress_styles' ), 110 );
        add_action( 'wp_body_open', array( $this, 'render_progress_bar' ), 1 );
    }

    private function is_enabled() {
        return (bool) Opulentia_get_option( 'reading-progress-enable', false );
    }

    private function should_render() {
        if ( is_admin() || is_customize_preview() ) {
            return false;
        }

        if ( ! $this->is_enabled() ) {
            return false;
        }

        $show_on = Opulentia_get_option( 'reading-progress-show-on', 'posts' );

        switch ( $show_on ) {
            case 'posts':
                return is_single();
            case 'pages':
                return is_page();
            case 'both':
                return is_singular();
            case 'all':
                return true;
            default:
                return false;
        }
    }

    public function render_progress_bar() {
        if ( ! $this->should_render() ) {
            return;
        }

        $position = Opulentia_get_option( 'reading-progress-position', 'top' );
        $height   = (int) Opulentia_get_option( 'reading-progress-height', 3 );
        $bg_color = Opulentia_get_option( 'reading-progress-bg-color', 'transparent' );
        $use_gradient = (bool) Opulentia_get_option( 'reading-progress-gradient', true );
        $bar_color = Opulentia_get_option( 'reading-progress-color', '#c9a96e' );

        if ( $use_gradient ) {
            $bar_style = 'background: linear-gradient(90deg, ' . esc_attr( $bar_color ) . ', var(--color-accent, #b8860b));';
        } else {
            $bar_style = 'background: ' . esc_attr( $bar_color ) . ';';
        }

        $wrap_style  = 'position: fixed; ';
        $wrap_style .= 'top' === $position ? 'top: 0; bottom: auto;' : 'bottom: 0; top: auto;';
        $wrap_style .= ' left: 0; width: 100%; height: ' . (int) $height . 'px; ';
        $wrap_style .= 'z-index: 999999; background: ' . esc_attr( $bg_color ) . ';';
        ?>
        <div id="op-reading-progress-wrap" style="<?php echo esc_attr( $wrap_style ); ?>">
            <span id="op-reading-progress-bar" style="<?php echo esc_attr( $bar_style ); ?> display: block; height: 100%; width: 0%;"></span>
        </div>
        <?php
    }

    public function enqueue_progress_styles() {
        if ( ! $this->should_render() ) {
            return;
        }

        $disable_transition = (bool) Opulentia_get_option( 'reading-progress-reduced-motion', false );
        $transition = $disable_transition ? 'none' : 'width 0.1s linear';

        $css = '
        #op-reading-progress-wrap {
            pointer-events: none;
            overflow: hidden;
        }
        #op-reading-progress-bar {
            -webkit-transition: ' . $transition . ';
            transition: ' . $transition . ';
        }
        @media (prefers-reduced-motion: reduce) {
            #op-reading-progress-bar {
                -webkit-transition: none !important;
                transition: none !important;
            }
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );

        $js = '
        (function() {
            var bar = document.getElementById("op-reading-progress-bar");
            if (!bar) return;
            var ticking = false;
            var update = function() {
                var scrollTop = window.scrollY;
                var docHeight = document.documentElement.scrollHeight - window.innerHeight;
                var progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
                bar.style.width = progress + "%";
                ticking = false;
            };
            window.addEventListener("scroll", function() {
                if (!ticking) {
                    window.requestAnimationFrame(update);
                    ticking = true;
                }
            });
        })();
        ';

        wp_add_inline_script( 'opulentia-custom', $js );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_reading_progress', array(
            'title'    => __( 'Reading Progress Bar', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 125,
        ) );

        $wp_customize->add_setting( 'reading-progress-enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-enable', array(
            'label'   => __( 'Enable Reading Progress Bar', 'opulentia' ),
            'section' => 'opulentia_reading_progress',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'reading-progress-height', array(
            'default'           => 3,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-height', array(
            'label'       => __( 'Bar Height (px)', 'opulentia' ),
            'section'     => 'opulentia_reading_progress',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 2,
                'max'  => 10,
                'step' => 1,
            ),
        ) );

        $wp_customize->add_setting( 'reading-progress-color', array(
            'default'           => '#c9a96e',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'reading-progress-color', array(
            'label'   => __( 'Progress Bar Color', 'opulentia' ),
            'section' => 'opulentia_reading_progress',
        ) ) );

        $wp_customize->add_setting( 'reading-progress-bg-color', array(
            'default'           => 'transparent',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-bg-color', array(
            'label'       => __( 'Background Color', 'opulentia' ),
            'description' => __( 'Hex color or "transparent"', 'opulentia' ),
            'section'     => 'opulentia_reading_progress',
            'type'        => 'text',
            'input_attrs' => array( 'placeholder' => 'transparent' ),
        ) );

        $wp_customize->add_setting( 'reading-progress-gradient', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-gradient', array(
            'label'   => __( 'Enable Gradient Effect', 'opulentia' ),
            'section' => 'opulentia_reading_progress',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'reading-progress-show-on', array(
            'default'           => 'posts',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-show-on', array(
            'label'   => __( 'Show On', 'opulentia' ),
            'section' => 'opulentia_reading_progress',
            'type'    => 'select',
            'choices' => array(
                'posts' => __( 'Single Posts Only', 'opulentia' ),
                'pages' => __( 'Pages Only', 'opulentia' ),
                'both'  => __( 'Both Posts & Pages', 'opulentia' ),
                'all'   => __( 'All Pages', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'reading-progress-exclude-mobile', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-exclude-mobile', array(
            'label'   => __( 'Exclude on Mobile', 'opulentia' ),
            'section' => 'opulentia_reading_progress',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'reading-progress-position', array(
            'default'           => 'top',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-position', array(
            'label'   => __( 'Position', 'opulentia' ),
            'section' => 'opulentia_reading_progress',
            'type'    => 'select',
            'choices' => array(
                'top'    => __( 'Top', 'opulentia' ),
                'bottom' => __( 'Bottom', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'reading-progress-reduced-motion', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'reading-progress-reduced-motion', array(
            'label'   => __( 'Disable CSS Transition', 'opulentia' ),
            'section' => 'opulentia_reading_progress',
            'type'    => 'checkbox',
        ) );
    }
}

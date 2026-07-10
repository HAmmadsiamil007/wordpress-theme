<?php
/**
 * Elementor Widget Compatibility — Singleton
 *
 * Registers custom Elementor widgets that mirror the Opulentia
 * theme sections (Hero, Features, About, Brand Story, Testimonials,
 * Product Grid).
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Elementor class.
 */
class Opulentia_Elementor {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Minimum Elementor version required.
     */
    const MIN_ELEMENTOR_VERSION = '3.5.0';

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
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Initialize Elementor compatibility after plugins are loaded.
     */
    public function init() {
        if ( ! $this->has_elementor() ) {
            return;
        }

        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        add_action( 'elementor/elements/categories_registered', array( $this, 'add_category' ) );
        add_action( 'elementor/preview/enqueue_styles', array( $this, 'preview_styles' ) );
    }

    /**
     * Check if Elementor is active and meets minimum version.
     *
     * @return bool
     */
    private function has_elementor() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return false;
        }

        if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
            return false;
        }

        if ( version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR_VERSION, '<' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Register the Opulentia widget category.
     *
     * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
     */
    public function add_category( $elements_manager ) {
        $elements_manager->add_category(
            'opulentia',
            array(
                'title' => esc_html__( 'opulentia', 'opulentia' ),
                'icon'  => 'fa fa-shoe-prints',
            )
        );
    }

    /**
     * Register custom Elementor widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     */
    public function register_widgets( $widgets_manager ) {
        // Hero Banner Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_hero';
            }

            public function get_title() {
                return esc_html__( 'Hero Banner', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-header';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'hero', 'banner', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'title',
                    array(
                        'label'       => esc_html__( 'Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'TIMELESS ELEGANCE. UNMATCHED COMFORT.', 'opulentia' ),
                        'placeholder' => esc_html__( 'Enter hero title', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'subtitle',
                    array(
                        'label'       => esc_html__( 'Subtitle', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( 'Premium handcrafted shoes designed for the modern gentleman.', 'opulentia' ),
                        'placeholder' => esc_html__( 'Enter hero subtitle', 'opulentia' ),
                        'rows'        => 3,
                    )
                );

                $this->add_control(
                    'button_1_text',
                    array(
                        'label'       => esc_html__( 'Primary Button Text', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'EXPLORE COLLECTION', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'button_1_url',
                    array(
                        'label'       => esc_html__( 'Primary Button URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://your-link.com', 'opulentia' ),
                        'default'     => array(
                            'url' => '#collection',
                        ),
                    )
                );

                $this->add_control(
                    'button_2_text',
                    array(
                        'label'       => esc_html__( 'Secondary Button Text', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'OUR STORY', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'button_2_url',
                    array(
                        'label'       => esc_html__( 'Secondary Button URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://your-link.com', 'opulentia' ),
                        'default'     => array(
                            'url' => '#about',
                        ),
                    )
                );

                $this->add_control(
                    'background_image',
                    array(
                        'label'   => esc_html__( 'Background Image', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::MEDIA,
                        'default' => array(
                            'url' => '',
                        ),
                    )
                );

                $this->end_controls_section();

                // Style Section.
                $this->start_controls_section(
                    'style_section',
                    array(
                        'label' => esc_html__( 'Style', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                    )
                );

                $this->add_control(
                    'title_color',
                    array(
                        'label'     => esc_html__( 'Title Color', 'opulentia' ),
                        'type'      => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .hero__title' => 'color: {{VALUE}};',
                        ),
                        'default'   => '#ffffff',
                    )
                );

                $this->add_control(
                    'subtitle_color',
                    array(
                        'label'     => esc_html__( 'Subtitle Color', 'opulentia' ),
                        'type'      => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .hero__subtitle' => 'color: {{VALUE}};',
                        ),
                        'default'   => '#999999',
                    )
                );

                $this->add_control(
                    'overlay_color',
                    array(
                        'label'     => esc_html__( 'Overlay Color', 'opulentia' ),
                        'type'      => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .hero__overlay' => 'background: {{VALUE}};',
                        ),
                        'default'   => 'rgba(26, 26, 26, 0.7)',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $title    = $settings['title'];
                $subtitle = $settings['subtitle'];
                $btn1_txt = $settings['button_1_text'];
                $btn1_url = $settings['button_1_url']['url'];
                $btn2_txt = $settings['button_2_text'];
                $btn2_url = $settings['button_2_url']['url'];
                $bg_image = $settings['background_image']['url'];
                ?>
                <section class="hero">
                    <?php if ( $bg_image ) : ?>
                        <img class="hero__background" src="<?php echo esc_url( $bg_image ); ?>" alt="" loading="eager">
                    <?php endif; ?>
                    <div class="hero__overlay"></div>
                    <div class="hero__content">
                        <div class="hero__logo">
                            <svg class="hero__logo-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="1.5"/>
                                <text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>
                            </svg>
                            <span class="hero__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
                            <span class="hero__logo-tagline"><?php esc_html_e( 'CRAFTED FROM ORIGIN. MADE TO LAST.', 'opulentia' ); ?></span>
                        </div>
                        <?php if ( $title ) : ?>
                            <h1 class="hero__title"><?php echo esc_html( $title ); ?></h1>
                        <?php endif; ?>
                        <?php if ( $subtitle ) : ?>
                            <p class="hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                        <div class="hero__buttons">
                            <?php if ( $btn1_txt && $btn1_url ) : ?>
                                <a href="<?php echo esc_url( $btn1_url ); ?>" class="btn btn--primary">
                                    <?php echo esc_html( $btn1_txt ); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( $btn2_txt && $btn2_url ) : ?>
                                <a href="<?php echo esc_url( $btn2_url ); ?>" class="btn btn--outline">
                                    <?php echo esc_html( $btn2_txt ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        } );

        // Features Bar Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_features';
            }

            public function get_title() {
                return esc_html__( 'Features Bar', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-icon-box';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'features', 'icons', 'benefits', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Features', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'icon_choice',
                    array(
                        'label'   => esc_html__( 'Icon', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => 'tag',
                        'options' => array(
                            'tag'       => esc_html__( 'Tag / Premium', 'opulentia' ),
                            'shield'    => esc_html__( 'Shield / Quality', 'opulentia' ),
                            'clock'     => esc_html__( 'Clock / Delivery', 'opulentia' ),
                            'checkmark' => esc_html__( 'Checkmark / Guarantee', 'opulentia' ),
                            'globe'     => esc_html__( 'Globe / Worldwide', 'opulentia' ),
                            'heart'     => esc_html__( 'Heart / Loved', 'opulentia' ),
                            'diamond'   => esc_html__( 'Diamond / Luxury', 'opulentia' ),
                            'layers'    => esc_html__( 'Layers / Craftsmanship', 'opulentia' ),
                        ),
                    )
                );

                $repeater->add_control(
                    'title',
                    array(
                        'label'       => esc_html__( 'Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Feature Title', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'description',
                    array(
                        'label'       => esc_html__( 'Description', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( 'Feature description goes here.', 'opulentia' ),
                        'rows'        => 2,
                    )
                );

                $this->add_control(
                    'features_list',
                    array(
                        'label'       => esc_html__( 'Features List', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array(
                                'icon_choice' => 'tag',
                                'title'       => esc_html__( 'Premium Materials', 'opulentia' ),
                                'description' => esc_html__( 'Finest quality leather sourced ethically.', 'opulentia' ),
                            ),
                            array(
                                'icon_choice' => 'layers',
                                'title'       => esc_html__( 'Expert Craftsmanship', 'opulentia' ),
                                'description' => esc_html__( 'Handcrafted by skilled artisans.', 'opulentia' ),
                            ),
                            array(
                                'icon_choice' => 'clock',
                                'title'       => esc_html__( 'Timeless Designs', 'opulentia' ),
                                'description' => esc_html__( 'Classic styles for every occasion.', 'opulentia' ),
                            ),
                            array(
                                'icon_choice' => 'globe',
                                'title'       => esc_html__( 'Worldwide Shipping', 'opulentia' ),
                                'description' => esc_html__( 'Delivered to your doorstep anywhere.', 'opulentia' ),
                            ),
                        ),
                        'title_field' => '{{{ title }}}',
                    )
                );

                $this->end_controls_section();
            }

            private function get_feature_icon( $choice ) {
                $icons = array(
                    'tag'       => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
                    'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
                    'clock'     => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                    'checkmark' => '<path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M9 12l2 2 4-4"/>',
                    'globe'     => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
                    'heart'     => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
                    'diamond'   => '<path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83M16.62 12l-5.74 9.94"/>',
                    'layers'    => '<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                );

                if ( ! isset( $icons[ $choice ] ) ) {
                    $choice = 'tag';
                }

                return $icons[ $choice ];
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $features = $settings['features_list'];
                ?>
                <section class="features-bar">
                    <div class="container">
                        <div class="features-bar__grid">
                            <?php foreach ( $features as $feature ) : ?>
                                <div class="feature-item">
                                    <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <?php echo $this->get_feature_icon( $feature['icon_choice'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </svg>
                                    <div class="feature-item__content">
                                        <h3 class="feature-item__title"><?php echo esc_html( $feature['title'] ); ?></h3>
                                        <p class="feature-item__text"><?php echo esc_html( $feature['description'] ); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        } );

        // About / Brand Story Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_about';
            }

            public function get_title() {
                return esc_html__( 'Brand Story', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-info-box';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'about', 'brand', 'story', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'subtitle',
                    array(
                        'label'       => esc_html__( 'Subtitle', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'OUR HERITAGE', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'title',
                    array(
                        'label'       => esc_html__( 'Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'BUILT FROM HERITAGE. PERFECTED OVER TIME.', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'description',
                    array(
                        'label'       => esc_html__( 'Description', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( 'At Opulentia, every pair is a testament to true craftsmanship. From the finest materials to the smallest details, we create shoes that stand the test of time.', 'opulentia' ),
                        'rows'        => 5,
                    )
                );

                $this->add_control(
                    'image',
                    array(
                        'label'   => esc_html__( 'Image', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::MEDIA,
                        'default' => array(
                            'url' => '',
                        ),
                    )
                );

                $this->add_control(
                    'button_text',
                    array(
                        'label'       => esc_html__( 'Button Text', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'DISCOVER OUR STORY', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'button_url',
                    array(
                        'label'       => esc_html__( 'Button URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://your-link.com', 'opulentia' ),
                        'default'     => array(
                            'url' => home_url( '/about-us' ),
                        ),
                    )
                );

                $this->end_controls_section();

                // Stats Section.
                $this->start_controls_section(
                    'stats_section',
                    array(
                        'label' => esc_html__( 'Statistics', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'number',
                    array(
                        'label'   => esc_html__( 'Number', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( '500+', 'opulentia' ),
                    )
                );

                $repeater->add_control(
                    'label',
                    array(
                        'label'   => esc_html__( 'Label', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( 'Happy Clients', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'stats_list',
                    array(
                        'label'       => esc_html__( 'Statistics', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array(
                                'number' => '500+',
                                'label'  => esc_html__( 'Happy Clients', 'opulentia' ),
                            ),
                            array(
                                'number' => '100%',
                                'label'  => esc_html__( 'Italian Leather', 'opulentia' ),
                            ),
                            array(
                                'number' => '50+',
                                'label'  => esc_html__( 'Unique Designs', 'opulentia' ),
                            ),
                        ),
                        'title_field' => '{{{ number }}} — {{{ label }}}',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings    = $this->get_settings_for_display();
                $subtitle    = $settings['subtitle'];
                $title       = $settings['title'];
                $description = $settings['description'];
                $image       = $settings['image']['url'];
                $btn_text    = $settings['button_text'];
                $btn_url     = $settings['button_url']['url'];
                $stats       = $settings['stats_list'];
                ?>
                <section class="brand-story">
                    <div class="container">
                        <div class="brand-story__grid">
                            <div class="brand-story__image">
                                <?php if ( $image ) : ?>
                                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_attr_e( 'Craftsmanship', 'opulentia' ); ?>" loading="lazy">
                                <?php else : ?>
                                    <img src="https://images.unsplash.com/photo-1473188588951-666fce8e7c68?w=800&h=1000&fit=crop" alt="<?php esc_attr_e( 'Artisan Craftsmanship', 'opulentia' ); ?>" loading="lazy">
                                <?php endif; ?>
                                <div class="brand-story__image-overlay">
                                    <span class="brand-story__year"><?php esc_html_e( 'EST. 2019', 'opulentia' ); ?></span>
                                </div>
                            </div>
                            <div class="brand-story__content">
                                <?php if ( $subtitle ) : ?>
                                    <p class="brand-story__subtitle"><?php echo esc_html( $subtitle ); ?></p>
                                <?php endif; ?>
                                <?php if ( $title ) : ?>
                                    <h2 class="brand-story__title"><?php echo esc_html( $title ); ?></h2>
                                <?php endif; ?>
                                <?php if ( $description ) : ?>
                                    <p class="brand-story__text"><?php echo esc_html( $description ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $stats ) ) : ?>
                                    <div class="brand-story__stats">
                                        <?php foreach ( $stats as $stat ) : ?>
                                            <div class="brand-story__stat">
                                                <span class="brand-story__stat-number"><?php echo esc_html( $stat['number'] ); ?></span>
                                                <span class="brand-story__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ( $btn_text && $btn_url ) : ?>
                                    <a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn--primary">
                                        <?php echo esc_html( $btn_text ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
            }
        } );

        // Testimonials Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_testimonials';
            }

            public function get_title() {
                return esc_html__( 'Testimonials', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-testimonial';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'testimonial', 'review', 'client', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Testimonials', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'section_subtitle',
                    array(
                        'label'   => esc_html__( 'Section Subtitle', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( 'Client Stories', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'section_title',
                    array(
                        'label'   => esc_html__( 'Section Title', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( 'WHAT OUR CLIENTS SAY', 'opulentia' ),
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'text',
                    array(
                        'label'       => esc_html__( 'Testimonial Text', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( '"The quality is exceptional."', 'opulentia' ),
                        'rows'        => 4,
                    )
                );

                $repeater->add_control(
                    'author_name',
                    array(
                        'label'       => esc_html__( 'Author Name', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'John Doe', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'author_role',
                    array(
                        'label'       => esc_html__( 'Author Role', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Business Executive', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'rating',
                    array(
                        'label'   => esc_html__( 'Rating', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::NUMBER,
                        'min'     => 1,
                        'max'     => 5,
                        'step'    => 1,
                        'default' => 5,
                    )
                );

                $this->add_control(
                    'testimonials_list',
                    array(
                        'label'       => esc_html__( 'Testimonial Items', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array(
                                'text'        => '"The attention to detail is remarkable. These shoes are not just footwear; they\'re a statement of sophistication."',
                                'author_name' => esc_html__( 'Ahmed Hassan', 'opulentia' ),
                                'author_role' => esc_html__( 'Business Executive', 'opulentia' ),
                                'rating'      => 5,
                            ),
                            array(
                                'text'        => '"I\'ve never worn shoes this comfortable. The leather quality is exceptional and they look even better in person."',
                                'author_name' => esc_html__( 'Faisal Khan', 'opulentia' ),
                                'author_role' => esc_html__( 'Entrepreneur', 'opulentia' ),
                                'rating'      => 5,
                            ),
                            array(
                                'text'        => '"Worth every penny. The craftsmanship is Italian at its finest. I\'ve received countless compliments."',
                                'author_name' => esc_html__( 'Muhammad Shah', 'opulentia' ),
                                'author_role' => esc_html__( 'Architect', 'opulentia' ),
                                'rating'      => 5,
                            ),
                        ),
                        'title_field' => '{{{ author_name }}}',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings     = $this->get_settings_for_display();
                $subtitle     = $settings['section_subtitle'];
                $title        = $settings['section_title'];
                $testimonials = $settings['testimonials_list'];
                ?>
                <section class="testimonials">
                    <div class="container">
                        <div class="section-header">
                            <?php if ( $subtitle ) : ?>
                                <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                            <?php endif; ?>
                            <div class="gold-line"></div>
                            <?php if ( $title ) : ?>
                                <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                            <?php endif; ?>
                        </div>
                        <div class="testimonials__grid">
                            <?php foreach ( $testimonials as $testimonial ) : ?>
                                <div class="testimonial-card">
                                    <div class="testimonial-card__stars">
                                        <?php for ( $i = 0; $i < absint( $testimonial['rating'] ); $i++ ) : ?>
                                            <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="testimonial-card__text"><?php echo esc_html( $testimonial['text'] ); ?></p>
                                    <div class="testimonial-card__author">
                                        <div class="testimonial-card__avatar"><?php echo esc_html( strtoupper( substr( $testimonial['author_name'], 0, 2 ) ) ); ?></div>
                                        <div class="testimonial-card__info">
                                            <h4 class="testimonial-card__name"><?php echo esc_html( $testimonial['author_name'] ); ?></h4>
                                            <p class="testimonial-card__role"><?php echo esc_html( $testimonial['author_role'] ); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        } );

        // Product Grid Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_product_grid';
            }

            public function get_title() {
                return esc_html__( 'Product Grid', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-products';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'products', 'woocommerce', 'grid', 'shop', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Products', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'section_subtitle',
                    array(
                        'label'   => esc_html__( 'Section Subtitle', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( 'Our Collection', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'section_title',
                    array(
                        'label'   => esc_html__( 'Section Title', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( 'CLASSIC. REFINED. ICONIC.', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'section_description',
                    array(
                        'label'   => esc_html__( 'Section Description', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::TEXTAREA,
                        'default' => esc_html__( 'Discover our range of meticulously handcrafted shoes, made for those who value quality and sophistication.', 'opulentia' ),
                        'rows'    => 3,
                    )
                );

                $this->add_control(
                    'product_count',
                    array(
                        'label'   => esc_html__( 'Number of Products', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::NUMBER,
                        'min'     => 1,
                        'max'     => 12,
                        'step'    => 1,
                        'default' => 4,
                    )
                );

                $this->add_control(
                    'columns',
                    array(
                        'label'   => esc_html__( 'Columns', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => '4',
                        'options' => array(
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                        ),
                    )
                );

                $this->add_control(
                    'orderby',
                    array(
                        'label'   => esc_html__( 'Order By', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => 'menu_order',
                        'options' => array(
                            'menu_order' => esc_html__( 'Menu Order', 'opulentia' ),
                            'date'       => esc_html__( 'Date', 'opulentia' ),
                            'title'      => esc_html__( 'Title', 'opulentia' ),
                            'rand'       => esc_html__( 'Random', 'opulentia' ),
                        ),
                    )
                );

                $this->add_control(
                    'show_button',
                    array(
                        'label'        => esc_html__( 'Show "View All" Button', 'opulentia' ),
                        'type'         => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'     => esc_html__( 'Yes', 'opulentia' ),
                        'label_off'    => esc_html__( 'No', 'opulentia' ),
                        'return_value' => 'yes',
                        'default'      => 'yes',
                    )
                );

                $this->add_control(
                    'button_text',
                    array(
                        'label'     => esc_html__( 'Button Text', 'opulentia' ),
                        'type'      => \Elementor\Controls_Manager::TEXT,
                        'default'   => esc_html__( 'View All Collection', 'opulentia' ),
                        'condition' => array(
                            'show_button' => 'yes',
                        ),
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings    = $this->get_settings_for_display();
                $subtitle    = $settings['section_subtitle'];
                $title       = $settings['section_title'];
                $description = $settings['section_description'];
                $count       = absint( $settings['product_count'] );
                $columns     = absint( $settings['columns'] );
                $orderby     = sanitize_text_field( $settings['orderby'] );
                ?>
                <section class="collection-section">
                    <div class="container">
                        <div class="section-header">
                            <?php if ( $subtitle ) : ?>
                                <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                            <?php endif; ?>
                            <div class="gold-line"></div>
                            <?php if ( $title ) : ?>
                                <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                            <?php endif; ?>
                            <?php if ( $description ) : ?>
                                <p class="section-description"><?php echo esc_html( $description ); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                            <div class="product-grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
                                <?php
                                $args = array(
                                    'post_type'      => 'product',
                                    'posts_per_page' => $count,
                                    'orderby'        => $orderby,
                                    'order'          => 'ASC',
                                );

                                $products = new \WP_Query( $args );

                                if ( $products->have_posts() ) :
                                    while ( $products->have_posts() ) :
                                        $products->the_post();
                                        wc_get_template_part( 'content', 'product' );
                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                                ?>
                            </div>

                            <?php if ( 'yes' === $settings['show_button'] && ! empty( $settings['button_text'] ) ) : ?>
                                <div class="text-center">
                                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn--outline-dark">
                                        <?php echo esc_html( $settings['button_text'] ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <p><?php esc_html_e( 'Please install WooCommerce to display products.', 'opulentia' ); ?></p>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
            }
        } );

        // Counter Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_counter';
            }

            public function get_title() {
                return esc_html__( 'Counter', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-counter';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'counter', 'number', 'stat', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'number',
                    array(
                        'label'   => esc_html__( 'Number', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::NUMBER,
                        'default' => 100,
                    )
                );

                $this->add_control(
                    'label',
                    array(
                        'label'       => esc_html__( 'Label', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Satisfied Clients', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'suffix',
                    array(
                        'label'       => esc_html__( 'Suffix', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => '+',
                    )
                );

                $this->add_control(
                    'prefix',
                    array(
                        'label'       => esc_html__( 'Prefix', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => '',
                    )
                );

                $this->add_control(
                    'icon',
                    array(
                        'label'   => esc_html__( 'Icon', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => 'users',
                        'options' => array(
                            'users'     => esc_html__( 'Users', 'opulentia' ),
                            'tag'       => esc_html__( 'Tag', 'opulentia' ),
                            'shield'    => esc_html__( 'Shield', 'opulentia' ),
                            'clock'     => esc_html__( 'Clock', 'opulentia' ),
                            'heart'     => esc_html__( 'Heart', 'opulentia' ),
                            'star'      => esc_html__( 'Star', 'opulentia' ),
                            'globe'     => esc_html__( 'Globe', 'opulentia' ),
                            'layers'    => esc_html__( 'Layers', 'opulentia' ),
                        ),
                    )
                );

                $this->add_control(
                    'color',
                    array(
                        'label'     => esc_html__( 'Icon Color', 'opulentia' ),
                        'type'      => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .so-counter__icon' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .so-counter__number' => 'color: {{VALUE}};',
                        ),
                        'default'   => '#c9a96e',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $number   = absint( $settings['number'] );
                $label    = $settings['label'];
                $suffix   = $settings['suffix'];
                $prefix   = $settings['prefix'];
                $icon     = $settings['icon'];
                ?>
                <div class="so-counter" data-count="<?php echo esc_attr( $number ); ?>">
                    <div class="so-counter__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                            <?php echo $this->get_feature_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </svg>
                    </div>
                    <div class="so-counter__number">
                        <span class="so-counter__prefix"><?php echo esc_html( $prefix ); ?></span>
                        <span class="so-counter__value">0</span>
                        <span class="so-counter__suffix"><?php echo esc_html( $suffix ); ?></span>
                    </div>
                    <?php if ( $label ) : ?>
                        <p class="so-counter__label"><?php echo esc_html( $label ); ?></p>
                    <?php endif; ?>
                </div>
                <?php
            }

            private function get_feature_icon( $choice ) {
                $icons = array(
                    'tag'       => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
                    'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
                    'clock'     => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                    'heart'     => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
                    'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
                    'globe'     => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
                    'layers'    => '<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                );
                if ( ! isset( $icons[ $choice ] ) ) {
                    $choice = 'tag';
                }
                return $icons[ $choice ];
            }
        } );

        // Pricing Table Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_pricing_table';
            }

            public function get_title() {
                return esc_html__( 'Pricing Table', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-price-table';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'pricing', 'plan', 'price', 'table', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'plan_name',
                    array(
                        'label'       => esc_html__( 'Plan Name', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Standard', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'price',
                    array(
                        'label'       => esc_html__( 'Price', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => '99',
                    )
                );

                $this->add_control(
                    'currency',
                    array(
                        'label'       => esc_html__( 'Currency', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => '$',
                    )
                );

                $this->add_control(
                    'interval',
                    array(
                        'label'       => esc_html__( 'Interval', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( '/ month', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'description',
                    array(
                        'label'       => esc_html__( 'Description', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( 'Perfect for small businesses.', 'opulentia' ),
                        'rows'        => 2,
                    )
                );

                $this->add_control(
                    'featured',
                    array(
                        'label'        => esc_html__( 'Featured / Popular', 'opulentia' ),
                        'type'         => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'     => esc_html__( 'Yes', 'opulentia' ),
                        'label_off'    => esc_html__( 'No', 'opulentia' ),
                        'return_value' => 'yes',
                        'default'      => '',
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'feature_text',
                    array(
                        'label'       => esc_html__( 'Feature', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Feature', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'included',
                    array(
                        'label'        => esc_html__( 'Included', 'opulentia' ),
                        'type'         => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'     => esc_html__( 'Yes', 'opulentia' ),
                        'label_off'    => esc_html__( 'No', 'opulentia' ),
                        'return_value' => 'yes',
                        'default'      => 'yes',
                    )
                );

                $this->add_control(
                    'features_list',
                    array(
                        'label'       => esc_html__( 'Features', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array( 'feature_text' => esc_html__( 'Feature 1', 'opulentia' ), 'included' => 'yes' ),
                            array( 'feature_text' => esc_html__( 'Feature 2', 'opulentia' ), 'included' => 'yes' ),
                        ),
                        'title_field' => '{{{ feature_text }}}',
                    )
                );

                $this->add_control(
                    'button_text',
                    array(
                        'label'       => esc_html__( 'Button Text', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Get Started', 'opulentia' ),
                        'label_block' => true,
                        'separator'   => 'before',
                    )
                );

                $this->add_control(
                    'button_url',
                    array(
                        'label'       => esc_html__( 'Button URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://your-link.com', 'opulentia' ),
                        'default'     => array( 'url' => '#' ),
                    )
                );

                $this->end_controls_section();

                // Style Section.
                $this->start_controls_section(
                    'style_section',
                    array(
                        'label' => esc_html__( 'Style', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                    )
                );

                $this->add_control(
                    'accent_color',
                    array(
                        'label'     => esc_html__( 'Accent Color', 'opulentia' ),
                        'type'      => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .so-pricing-card--featured' => 'border-color: {{VALUE}};',
                            '{{WRAPPER}} .so-pricing-card__badge' => 'background: {{VALUE}};',
                        ),
                        'default'   => '#c9a96e',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings  = $this->get_settings_for_display();
                $plan      = $settings['plan_name'];
                $price     = $settings['price'];
                $currency  = $settings['currency'];
                $interval  = $settings['interval'];
                $desc      = $settings['description'];
                $features  = $settings['features_list'];
                $btn_text  = $settings['button_text'];
                $btn_url   = $settings['button_url']['url'];
                $featured  = 'yes' === $settings['featured'];

                $classes = array( 'so-pricing-card' );
                if ( $featured ) {
                    $classes[] = 'so-pricing-card--featured';
                }
                ?>
                <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
                    <?php if ( $featured ) : ?>
                        <span class="so-pricing-card__badge"><?php esc_html_e( 'Popular', 'opulentia' ); ?></span>
                    <?php endif; ?>
                    <h3 class="so-pricing-card__plan"><?php echo esc_html( $plan ); ?></h3>
                    <?php if ( $desc ) : ?>
                        <p class="so-pricing-card__desc"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>
                    <div class="so-pricing-card__price">
                        <span class="so-pricing-card__currency"><?php echo esc_html( $currency ); ?></span>
                        <span class="so-pricing-card__amount"><?php echo esc_html( $price ); ?></span>
                        <?php if ( $interval ) : ?>
                            <span class="so-pricing-card__interval"><?php echo esc_html( $interval ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $features ) ) : ?>
                        <ul class="so-pricing-card__features">
                            <?php foreach ( $features as $feature ) : ?>
                                <li class="so-pricing-card__feature <?php echo 'yes' === $feature['included'] ? 'is--included' : 'is--excluded'; ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                        <?php if ( 'yes' === $feature['included'] ) : ?>
                                            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
                                        <?php else : ?>
                                            <path d="M18 6L6 18M6 6l12 12"/>
                                        <?php endif; ?>
                                    </svg>
                                    <span><?php echo esc_html( $feature['feature_text'] ); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if ( $btn_text ) : ?>
                        <a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn--primary so-pricing-card__btn">
                            <?php echo esc_html( $btn_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php
            }
        } );

        // Team Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_team';
            }

            public function get_title() {
                return esc_html__( 'Team', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-person';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'team', 'member', 'staff', 'people', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'header_section',
                    array(
                        'label' => esc_html__( 'Header', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'section_title',
                    array(
                        'label'       => esc_html__( 'Section Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Our Team', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'section_subtitle',
                    array(
                        'label'       => esc_html__( 'Section Subtitle', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Meet the Experts', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'columns',
                    array(
                        'label'   => esc_html__( 'Columns', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => '3',
                        'options' => array(
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                        ),
                    )
                );

                $this->end_controls_section();

                $this->start_controls_section(
                    'members_section',
                    array(
                        'label' => esc_html__( 'Members', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'member_name',
                    array(
                        'label'       => esc_html__( 'Name', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'John Doe', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'role',
                    array(
                        'label'       => esc_html__( 'Role', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'CEO & Founder', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'bio',
                    array(
                        'label'       => esc_html__( 'Bio', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( 'Visionary leader with 15+ years of experience.', 'opulentia' ),
                        'rows'        => 3,
                    )
                );

                $repeater->add_control(
                    'image',
                    array(
                        'label' => esc_html__( 'Image', 'opulentia' ),
                        'type'  => \Elementor\Controls_Manager::MEDIA,
                    )
                );

                $repeater->add_control(
                    'facebook_url',
                    array(
                        'label'       => esc_html__( 'Facebook URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://facebook.com/...', 'opulentia' ),
                    )
                );

                $repeater->add_control(
                    'twitter_url',
                    array(
                        'label'       => esc_html__( 'Twitter URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://twitter.com/...', 'opulentia' ),
                    )
                );

                $repeater->add_control(
                    'linkedin_url',
                    array(
                        'label'       => esc_html__( 'LinkedIn URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://linkedin.com/...', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'members_list',
                    array(
                        'label'       => esc_html__( 'Team Members', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array(
                                'member_name' => esc_html__( 'John Doe', 'opulentia' ),
                                'role'        => esc_html__( 'CEO & Founder', 'opulentia' ),
                                'bio'         => esc_html__( 'Visionary leader.', 'opulentia' ),
                            ),
                            array(
                                'member_name' => esc_html__( 'Jane Smith', 'opulentia' ),
                                'role'        => esc_html__( 'Lead Designer', 'opulentia' ),
                                'bio'         => esc_html__( 'Award-winning designer.', 'opulentia' ),
                            ),
                        ),
                        'title_field' => '{{{ member_name }}}',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $title    = $settings['section_title'];
                $subtitle = $settings['section_subtitle'];
                $columns  = absint( $settings['columns'] );
                $members  = $settings['members_list'];

                if ( empty( $members ) ) {
                    return;
                }
                ?>
                <section class="so-team">
                    <div class="container">
                        <?php if ( $title || $subtitle ) : ?>
                            <div class="section-header">
                                <?php if ( $subtitle ) : ?>
                                    <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                                <?php endif; ?>
                                <div class="gold-line"></div>
                                <?php if ( $title ) : ?>
                                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="so-team__grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
                            <?php foreach ( $members as $member ) : ?>
                                <div class="so-team__card">
                                    <div class="so-team__image">
                                        <?php if ( ! empty( $member['image']['url'] ) ) : ?>
                                            <img src="<?php echo esc_url( $member['image']['url'] ); ?>" alt="<?php echo esc_attr( $member['member_name'] ); ?>" loading="lazy">
                                        <?php else : ?>
                                            <div class="so-team__avatar"><?php echo esc_html( strtoupper( substr( $member['member_name'], 0, 2 ) ) ); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="so-team__info">
                                        <h3 class="so-team__name"><?php echo esc_html( $member['member_name'] ); ?></h3>
                                        <p class="so-team__role"><?php echo esc_html( $member['role'] ); ?></p>
                                        <?php if ( ! empty( $member['bio'] ) ) : ?>
                                            <p class="so-team__bio"><?php echo esc_html( $member['bio'] ); ?></p>
                                        <?php endif; ?>
                                        <div class="so-team__social">
                                            <?php if ( ! empty( $member['facebook_url']['url'] ) ) : ?>
                                                <a href="<?php echo esc_url( $member['facebook_url']['url'] ); ?>" class="so-team__social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ( ! empty( $member['twitter_url']['url'] ) ) : ?>
                                                <a href="<?php echo esc_url( $member['twitter_url']['url'] ); ?>" class="so-team__social-link" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ( ! empty( $member['linkedin_url']['url'] ) ) : ?>
                                                <a href="<?php echo esc_url( $member['linkedin_url']['url'] ); ?>" class="so-team__social-link" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        } );

        // FAQ Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_faq';
            }

            public function get_title() {
                return esc_html__( 'FAQ', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-help';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'faq', 'questions', 'accordion', 'help', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'section_title',
                    array(
                        'label'       => esc_html__( 'Section Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Frequently Asked Questions', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'section_subtitle',
                    array(
                        'label'       => esc_html__( 'Section Subtitle', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Everything you need to know.', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'question',
                    array(
                        'label'       => esc_html__( 'Question', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'What materials do you use?', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'answer',
                    array(
                        'label'       => esc_html__( 'Answer', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::WYSIWYG,
                        'default'     => esc_html__( 'We use the finest Italian leather sourced from sustainable tanneries.', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'faq_items',
                    array(
                        'label'       => esc_html__( 'FAQ Items', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array(
                                'question' => esc_html__( 'What materials do you use?', 'opulentia' ),
                                'answer'   => esc_html__( 'We use the finest Italian leather sourced from sustainable tanneries.', 'opulentia' ),
                            ),
                            array(
                                'question' => esc_html__( 'How long does shipping take?', 'opulentia' ),
                                'answer'   => esc_html__( 'Domestic shipping takes 3-5 business days.', 'opulentia' ),
                            ),
                        ),
                        'title_field' => '{{{ question }}}',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $title    = $settings['section_title'];
                $subtitle = $settings['section_subtitle'];
                $items    = $settings['faq_items'];

                if ( empty( $items ) ) {
                    return;
                }
                ?>
                <section class="so-faq">
                    <div class="container">
                        <?php if ( $title || $subtitle ) : ?>
                            <div class="section-header">
                                <?php if ( $subtitle ) : ?>
                                    <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                                <?php endif; ?>
                                <div class="gold-line"></div>
                                <?php if ( $title ) : ?>
                                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="so-faq__list" itemscope="" itemtype="https://schema.org/FAQPage">
                            <?php foreach ( $items as $index => $item ) : ?>
                                <div class="so-faq__item" itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                                    <button class="so-faq__question" aria-expanded="false" aria-controls="so-faq-answer-<?php echo esc_attr( $index ); ?>">
                                        <span itemprop="name"><?php echo esc_html( $item['question'] ); ?></span>
                                        <svg class="so-faq__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M9 18l6-6-6-6"/></svg>
                                    </button>
                                    <div id="so-faq-answer-<?php echo esc_attr( $index ); ?>" class="so-faq__answer" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" role="region">
                                        <div itemprop="text"><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        } );

        // Tabs Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_tabs';
            }

            public function get_title() {
                return esc_html__( 'Tabs', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-tabs';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'tabs', 'tabbed', 'content', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Tabs', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'tab_title',
                    array(
                        'label'       => esc_html__( 'Tab Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Tab Title', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'tab_content',
                    array(
                        'label'       => esc_html__( 'Content', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::WYSIWYG,
                        'default'     => esc_html__( 'Tab content goes here.', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'tabs_list',
                    array(
                        'label'       => esc_html__( 'Tabs', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array( 'tab_title' => esc_html__( 'Tab 1', 'opulentia' ), 'tab_content' => esc_html__( 'Content 1', 'opulentia' ) ),
                            array( 'tab_title' => esc_html__( 'Tab 2', 'opulentia' ), 'tab_content' => esc_html__( 'Content 2', 'opulentia' ) ),
                            array( 'tab_title' => esc_html__( 'Tab 3', 'opulentia' ), 'tab_content' => esc_html__( 'Content 3', 'opulentia' ) ),
                        ),
                        'title_field' => '{{{ tab_title }}}',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $tabs     = $settings['tabs_list'];

                if ( empty( $tabs ) ) {
                    return;
                }
                ?>
                <div class="so-tabs">
                    <div class="so-tabs__nav" role="tablist">
                        <?php foreach ( $tabs as $index => $tab ) : ?>
                            <button class="so-tabs__tab <?php echo 0 === $index ? 'is-active' : ''; ?>"
                                    role="tab"
                                    aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                                    aria-controls="so-tab-panel-<?php echo esc_attr( $index ); ?>"
                                    id="so-tab-<?php echo esc_attr( $index ); ?>">
                                <?php echo esc_html( $tab['tab_title'] ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <?php foreach ( $tabs as $index => $tab ) : ?>
                        <div class="so-tabs__panel <?php echo 0 === $index ? 'is-active' : ''; ?>"
                             role="tabpanel"
                             aria-labelledby="so-tab-<?php echo esc_attr( $index ); ?>"
                             id="so-tab-panel-<?php echo esc_attr( $index ); ?>">
                            <?php echo wp_kses_post( wpautop( $tab['tab_content'] ) ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php
            }
        } );

        // Icon Box Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_icon_box';
            }

            public function get_title() {
                return esc_html__( 'Icon Box', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-icon-box';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'icon', 'box', 'feature', 'service', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'icon_choice',
                    array(
                        'label'   => esc_html__( 'Icon', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => 'shield',
                        'options' => array(
                            'shield'    => esc_html__( 'Shield', 'opulentia' ),
                            'tag'       => esc_html__( 'Tag', 'opulentia' ),
                            'heart'     => esc_html__( 'Heart', 'opulentia' ),
                            'star'      => esc_html__( 'Star', 'opulentia' ),
                            'globe'     => esc_html__( 'Globe', 'opulentia' ),
                            'clock'     => esc_html__( 'Clock', 'opulentia' ),
                            'users'     => esc_html__( 'Users', 'opulentia' ),
                            'layers'    => esc_html__( 'Layers', 'opulentia' ),
                            'diamond'   => esc_html__( 'Diamond', 'opulentia' ),
                        ),
                    )
                );

                $this->add_control(
                    'title',
                    array(
                        'label'       => esc_html__( 'Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Premium Quality', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'description',
                    array(
                        'label'       => esc_html__( 'Description', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( 'Finest materials and expert craftsmanship.', 'opulentia' ),
                        'rows'        => 3,
                    )
                );

                $this->add_control(
                    'icon_color',
                    array(
                        'label'     => esc_html__( 'Icon Color', 'opulentia' ),
                        'type'      => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .so-icon-box__icon' => 'color: {{VALUE}};',
                        ),
                        'default'   => '#c9a96e',
                    )
                );

                $this->add_control(
                    'show_button',
                    array(
                        'label'        => esc_html__( 'Show Button', 'opulentia' ),
                        'type'         => \Elementor\Controls_Manager::SWITCHER,
                        'label_on'     => esc_html__( 'Yes', 'opulentia' ),
                        'label_off'    => esc_html__( 'No', 'opulentia' ),
                        'return_value' => 'yes',
                        'default'      => '',
                        'separator'    => 'before',
                    )
                );

                $this->add_control(
                    'button_text',
                    array(
                        'label'       => esc_html__( 'Button Text', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Learn More', 'opulentia' ),
                        'label_block' => true,
                        'condition'   => array( 'show_button' => 'yes' ),
                    )
                );

                $this->add_control(
                    'button_url',
                    array(
                        'label'       => esc_html__( 'Button URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://your-link.com', 'opulentia' ),
                        'condition'   => array( 'show_button' => 'yes' ),
                        'default'     => array( 'url' => '#' ),
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings    = $this->get_settings_for_display();
                $icon        = $settings['icon_choice'];
                $title       = $settings['title'];
                $description = $settings['description'];
                $show_btn    = 'yes' === $settings['show_button'];
                $btn_text    = $settings['button_text'];
                $btn_url     = $settings['button_url']['url'];
                ?>
                <div class="so-icon-box">
                    <div class="so-icon-box__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="40" height="40">
                            <?php echo $this->get_feature_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </svg>
                    </div>
                    <?php if ( $title ) : ?>
                        <h3 class="so-icon-box__title"><?php echo esc_html( $title ); ?></h3>
                    <?php endif; ?>
                    <?php if ( $description ) : ?>
                        <p class="so-icon-box__desc"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                    <?php if ( $show_btn && $btn_text ) : ?>
                        <a href="<?php echo esc_url( $btn_url ); ?>" class="so-icon-box__link">
                            <?php echo esc_html( $btn_text ); ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php
            }

            private function get_feature_icon( $choice ) {
                $icons = array(
                    'tag'       => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
                    'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
                    'heart'     => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
                    'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
                    'globe'     => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
                    'clock'     => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                    'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                    'layers'    => '<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'diamond'   => '<path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83M16.62 12l-5.74 9.94"/>',
                );
                if ( ! isset( $icons[ $choice ] ) ) {
                    $choice = 'tag';
                }
                return $icons[ $choice ];
            }
        } );

        // Video Popup Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_video_popup';
            }

            public function get_title() {
                return esc_html__( 'Video Popup', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-youtube';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'video', 'popup', 'lightbox', 'youtube', 'vimeo', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'video_url',
                    array(
                        'label'       => esc_html__( 'Video URL', 'opulentia' ),
                        'description' => esc_html__( 'YouTube or Vimeo URL.', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://www.youtube.com/watch?v=...', 'opulentia' ),
                        'default'     => array( 'url' => '' ),
                    )
                );

                $this->add_control(
                    'thumbnail',
                    array(
                        'label'   => esc_html__( 'Custom Thumbnail Image', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::MEDIA,
                        'default' => array( 'url' => '' ),
                    )
                );

                $this->add_control(
                    'title',
                    array(
                        'label'       => esc_html__( 'Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Watch Our Story', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'description',
                    array(
                        'label'       => esc_html__( 'Description', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'default'     => esc_html__( 'See the craftsmanship behind every pair.', 'opulentia' ),
                        'rows'        => 2,
                    )
                );

                $this->add_control(
                    'aspect_ratio',
                    array(
                        'label'   => esc_html__( 'Aspect Ratio', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => '16/9',
                        'options' => array(
                            '16/9' => '16:9',
                            '4/3'  => '4:3',
                            '1/1'  => '1:1',
                        ),
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings    = $this->get_settings_for_display();
                $video_url   = $settings['video_url']['url'];
                $thumb_url   = $settings['thumbnail']['url'];
                $title       = $settings['title'];
                $description = $settings['description'];
                $aspect      = $settings['aspect_ratio'];

                $embed_url = '';
                if ( preg_match( '/(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/', $video_url, $matches ) ) {
                    $embed_url = 'https://www.youtube.com/embed/' . $matches[1];
                } elseif ( preg_match( '/(?:vimeo\.com)\/(.+)/', $video_url, $matches ) ) {
                    $embed_url = 'https://player.vimeo.com/video/' . $matches[1];
                } else {
                    $embed_url = $video_url;
                }
                ?>
                <div class="so-video-popup">
                    <div class="so-video-popup__wrapper" style="aspect-ratio: <?php echo esc_attr( $aspect ); ?>;">
                        <div class="so-video-popup__thumbnail">
                            <?php if ( $thumb_url ) : ?>
                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                            <?php else : ?>
                                <div class="so-video-popup__placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="64" height="64"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16" fill="currentColor" stroke="none"/></svg>
                                </div>
                            <?php endif; ?>
                            <button class="so-video-popup__play" aria-label="<?php esc_attr_e( 'Play video', 'opulentia' ); ?>" data-embed="<?php echo esc_url( $embed_url ); ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="60" height="60">
                                    <circle cx="12" cy="12" r="10" fill="rgba(0,0,0,0.6)" stroke="#fff" stroke-width="1.5"/>
                                    <polygon points="10 8 16 12 10 16" fill="#fff" stroke="none"/>
                                </svg>
                            </button>
                        </div>
                        <?php if ( $title || $description ) : ?>
                            <div class="so-video-popup__content">
                                <?php if ( $title ) : ?>
                                    <h3 class="so-video-popup__title"><?php echo esc_html( $title ); ?></h3>
                                <?php endif; ?>
                                <?php if ( $description ) : ?>
                                    <p class="so-video-popup__desc"><?php echo esc_html( $description ); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="so-video-popup__modal" aria-hidden="true">
                        <div class="so-video-popup__modal-overlay"></div>
                        <div class="so-video-popup__modal-content">
                            <button class="so-video-popup__modal-close" aria-label="<?php esc_attr_e( 'Close', 'opulentia' ); ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                            <div class="so-video-popup__modal-embed">
                                <iframe src="" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } );

        // Logo Grid Widget.
        $widgets_manager->register( new class( $this ) extends \Elementor\Widget_Base {

            public function get_name() {
                return 'Opulentia_logo_grid';
            }

            public function get_title() {
                return esc_html__( 'Logo Grid', 'opulentia' );
            }

            public function get_icon() {
                return 'eicon-gallery-grid';
            }

            public function get_categories() {
                return array( 'opulentia' );
            }

            public function get_keywords() {
                return array( 'logo', 'client', 'partner', 'grid', 'opulentia' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => esc_html__( 'Content', 'opulentia' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'section_title',
                    array(
                        'label'       => esc_html__( 'Section Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Trusted By', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $this->add_control(
                    'columns',
                    array(
                        'label'   => esc_html__( 'Columns', 'opulentia' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => '4',
                        'options' => array(
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                        ),
                    )
                );

                $repeater = new \Elementor\Repeater();

                $repeater->add_control(
                    'logo_title',
                    array(
                        'label'       => esc_html__( 'Logo Title', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::TEXT,
                        'default'     => esc_html__( 'Brand', 'opulentia' ),
                        'label_block' => true,
                    )
                );

                $repeater->add_control(
                    'logo_image',
                    array(
                        'label' => esc_html__( 'Logo Image', 'opulentia' ),
                        'type'  => \Elementor\Controls_Manager::MEDIA,
                    )
                );

                $repeater->add_control(
                    'logo_link',
                    array(
                        'label'       => esc_html__( 'Link URL', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::URL,
                        'placeholder' => esc_html__( 'https://your-link.com', 'opulentia' ),
                    )
                );

                $this->add_control(
                    'logos_list',
                    array(
                        'label'       => esc_html__( 'Logos', 'opulentia' ),
                        'type'        => \Elementor\Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => array(
                            array( 'logo_title' => 'Brand 1' ),
                            array( 'logo_title' => 'Brand 2' ),
                            array( 'logo_title' => 'Brand 3' ),
                            array( 'logo_title' => 'Brand 4' ),
                        ),
                        'title_field' => '{{{ logo_title }}}',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $title    = $settings['section_title'];
                $columns  = absint( $settings['columns'] );
                $logos    = $settings['logos_list'];

                if ( empty( $logos ) ) {
                    return;
                }
                ?>
                <section class="so-logo-showcase">
                    <div class="container">
                        <?php if ( $title ) : ?>
                            <h2 class="so-logo-showcase__title"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                        <div class="so-logo-showcase__grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
                            <?php foreach ( $logos as $logo ) : ?>
                                <div class="so-logo-showcase__item">
                                    <?php if ( ! empty( $logo['logo_image']['url'] ) && ! empty( $logo['logo_link']['url'] ) ) : ?>
                                        <a href="<?php echo esc_url( $logo['logo_link']['url'] ); ?>" target="_blank" rel="noopener noreferrer">
                                            <img src="<?php echo esc_url( $logo['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( $logo['logo_title'] ); ?>" loading="lazy">
                                        </a>
                                    <?php elseif ( ! empty( $logo['logo_image']['url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $logo['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( $logo['logo_title'] ); ?>" loading="lazy">
                                    <?php else : ?>
                                        <span class="so-logo-showcase__placeholder"><?php echo esc_html( $logo['logo_title'] ); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        } );
    }

    /**
     * Enqueue styles for Elementor preview mode.
     */
    public function preview_styles() {
        wp_enqueue_style(
            'opulentia-elementor-preview',
            Opulentia_URI . '/css/woocommerce.css',
            array(),
            Opulentia_VERSION
        );
    }
}

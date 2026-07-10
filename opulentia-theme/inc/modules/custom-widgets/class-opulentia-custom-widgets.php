<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Custom_Widgets {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
    }

    public function register_widgets() {
        register_widget( 'Opulentia_Social_Icons_Widget' );
        register_widget( 'Opulentia_Recent_Posts_Widget' );
        register_widget( 'Opulentia_Author_Bio_Widget' );
        register_widget( 'Opulentia_Contact_Info_Widget' );
    }

    public function inline_css() {
        $css = '
        .op-widget {
            background: var(--color-secondary-dark, #111);
            border: 1px solid var(--color-border, #333);
            border-left: 3px solid var(--color-gold, #c9a96e);
            padding: 18px 20px;
            margin-bottom: 20px;
            border-radius: 0;
            transition: border-color 0.3s ease;
        }
        .op-widget:hover {
            border-color: var(--color-gold, #c9a96e);
        }
        .op-widget .widget-title {
            font-family: var(--font-heading, "Playfair Display"), serif;
            color: var(--color-gold, #c9a96e);
            font-size: 1.1rem;
            margin: 0 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--color-border, #333);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .op-widget ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .op-widget ul li {
            padding: 0;
            margin: 0;
        }
        .op-widget a {
            color: var(--color-text, #f5f5f5);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .op-widget a:hover {
            color: var(--color-gold, #c9a96e);
        }
        .op-widget p {
            color: var(--color-text, #f5f5f5);
            font-family: var(--font-body, Inter), sans-serif;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0 0 10px;
        }
        .op-widget .op-muted {
            color: var(--color-text-muted, #999);
            font-size: 0.8rem;
        }
        .op-social-icons-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .op-social-icons-row a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--color-primary-dark, #1a1a1a);
            border: 1px solid var(--color-border, #333);
            color: var(--color-text, #f5f5f5);
            transition: all 0.3s ease;
        }
        .op-social-icons-row a:hover {
            background: var(--color-gold, #c9a96e);
            border-color: var(--color-gold, #c9a96e);
            color: #000;
        }
        .op-social-icons-row a svg {
            width: 18px;
            height: 18px;
        }
        .op-recent-post {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--color-border, #333);
        }
        .op-recent-post:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .op-recent-post:first-child {
            padding-top: 0;
        }
        .op-recent-post-thumb {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            overflow: hidden;
        }
        .op-recent-post-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .op-recent-post-body {
            flex: 1;
            min-width: 0;
        }
        .op-recent-post-title {
            font-family: var(--font-heading, "Playfair Display"), serif;
            font-size: 0.9rem;
            margin: 0 0 4px;
            line-height: 1.3;
        }
        .op-recent-post-title a {
            color: var(--color-text, #f5f5f5);
        }
        .op-recent-post-title a:hover {
            color: var(--color-gold, #c9a96e);
        }
        .op-recent-post-date {
            font-size: 0.75rem;
            color: var(--color-text-muted, #999);
        }
        .op-author-bio {
            text-align: center;
        }
        .op-author-avatar {
            margin-bottom: 12px;
        }
        .op-author-avatar img {
            border-radius: 50%;
            border: 2px solid var(--color-gold, #c9a96e);
        }
        .op-author-name {
            font-family: var(--font-heading, "Playfair Display"), serif;
            color: var(--color-gold, #c9a96e);
            font-size: 1.05rem;
            margin: 0 0 8px;
        }
        .op-author-desc {
            font-size: 0.85rem;
            color: var(--color-text-muted, #999);
            margin-bottom: 12px;
        }
        .op-author-link {
            display: inline-block;
            font-size: 0.8rem;
            color: var(--color-gold, #c9a96e);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--color-gold, #c9a96e);
            padding-bottom: 2px;
        }
        .op-author-link:hover {
            color: #fff;
            border-color: #fff;
        }
        .op-contact-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .op-contact-item:last-child {
            margin-bottom: 0;
        }
        .op-contact-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            color: var(--color-gold, #c9a96e);
            margin-top: 2px;
        }
        .op-contact-icon svg {
            width: 20px;
            height: 20px;
        }
        .op-contact-text {
            font-size: 0.88rem;
            color: var(--color-text, #f5f5f5);
            line-height: 1.5;
        }
        .op-contact-text a {
            color: var(--color-text, #f5f5f5);
        }
        .op-contact-text a:hover {
            color: var(--color-gold, #c9a96e);
        }
        ';
        wp_add_inline_style( 'opulentia-style', $css );
    }
}

class Opulentia_Social_Icons_Widget extends WP_Widget {

    private $networks;

    public function __construct() {
        parent::__construct(
            'Opulentia_social_icons',
            __( 'Opulentia Social Icons', 'opulentia' ),
            array(
                'description' => __( 'Displays social media profile icons with links.', 'opulentia' ),
            )
        );
        $this->networks = array(
            'facebook'  => __( 'Facebook', 'opulentia' ),
            'twitter'   => __( 'X (Twitter)', 'opulentia' ),
            'instagram' => __( 'Instagram', 'opulentia' ),
            'linkedin'  => __( 'LinkedIn', 'opulentia' ),
            'youtube'   => __( 'YouTube', 'opulentia' ),
            'pinterest' => __( 'Pinterest', 'opulentia' ),
            'github'    => __( 'GitHub', 'opulentia' ),
            'dribbble'  => __( 'Dribbble', 'opulentia' ),
            'behance'   => __( 'Behance', 'opulentia' ),
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo '<div class="op-social-icons-row">';
        foreach ( $this->networks as $key => $label ) {
            $url = ! empty( $instance[ 'url_' . $key ] ) ? $instance[ 'url_' . $key ] : '';
            if ( ! empty( $url ) ) {
                printf(
                    '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a>',
                    esc_url( $url ),
                    esc_attr( $label ),
                    $this->get_svg( $key )
                );
            }
        }
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Follow Us', 'opulentia' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php foreach ( $this->networks as $key => $label ) : ?>
            <?php $val = ! empty( $instance[ 'url_' . $key ] ) ? $instance[ 'url_' . $key ] : ''; ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'url_' . $key ) ); ?>"><?php echo esc_html( $label ); ?>:</label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'url_' . $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'url_' . $key ) ); ?>" type="url" value="<?php echo esc_url( $val ); ?>" placeholder="https://">
            </p>
        <?php endforeach; ?>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        foreach ( $this->networks as $key => $label ) {
            $instance[ 'url_' . $key ] = ! empty( $new_instance[ 'url_' . $key ] ) ? esc_url_raw( $new_instance[ 'url_' . $key ] ) : '';
        }
        return $instance;
    }

    private function get_svg( $network ) {
        $svgs = array(
            'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>',
            'twitter' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
            'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
            'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
            'youtube' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
            'pinterest' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',
            'github' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>',
            'dribbble' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 24C5.385 24 0 18.615 0 12S5.385 0 12 0s12 5.385 12 12-5.385 12-12 12zm10.12-10.358c-.35-.11-3.17-.953-6.384-.438 1.34 3.684 1.887 6.684 1.992 7.308 2.3-1.555 3.936-4.02 4.395-6.87zm-6.115 7.808c-.153-.9-.75-4.032-2.19-7.77l-.066.02c-5.79 2.015-7.86 6.025-8.04 6.4 1.73 1.358 3.92 2.166 6.29 2.166 1.42 0 2.77-.29 4-.816zm-11.62-2.58c.232-.4 3.045-5.055 8.332-6.765.135-.045.27-.084.405-.12-.26-.585-.54-1.167-.832-1.74C7.17 11.775 2.206 11.71 1.756 11.7l-.004.312c0 2.633.998 5.037 2.634 6.855zm-2.42-8.955c.46.008 4.683.026 9.477-1.248-1.698-3.018-3.53-5.558-3.8-5.928-2.868 1.35-5.01 3.99-5.676 7.17zm7.707-7.467c.282.38 2.145 2.914 3.822 6 3.645-1.365 5.19-3.44 5.373-3.702-1.81-1.61-4.19-2.586-6.795-2.586-.825 0-1.63.1-2.4.29zm10.052 4.083c-.195.255-1.973 2.49-5.76 4.06a31.34 31.34 0 01.677 1.47c.067.15.135.3.195.45 3.573-.45 7.265.28 7.59.36-.075-2.43-1.04-4.64-2.7-6.34z"/></svg>',
            'behance' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7.807 3.949c.718.046 1.382.145 1.992.297.61.152 1.146.381 1.607.687.461.306.828.704 1.102 1.193.275.49.412 1.102.412 1.837 0 .642-.14 1.2-.42 1.674-.28.474-.659.875-1.136 1.203.642.275 1.14.66 1.494 1.156.354.496.53 1.1.53 1.813 0 .826-.172 1.526-.516 2.1-.344.574-.798 1.036-1.364 1.387-.565.351-1.201.61-1.91.777-.708.167-1.43.25-2.167.25H0V3.949h7.807zm-4.84.985v5.355h4.109c.382 0 .74-.06 1.076-.179.336-.12.632-.298.888-.535.256-.237.46-.54.61-.908.15-.368.225-.814.225-1.336 0-.413-.064-.78-.191-1.102-.128-.322-.305-.595-.533-.819-.227-.224-.497-.395-.809-.514-.312-.12-.652-.179-1.02-.179H2.967zm0 9.72V20.2h5.086c.398 0 .784-.07 1.158-.21.374-.14.705-.348.993-.624.289-.276.52-.625.694-1.046.174-.421.261-.92.261-1.495 0-.566-.087-1.05-.261-1.452-.174-.402-.411-.733-.71-.993-.3-.26-.649-.452-1.048-.576-.398-.124-.82-.186-1.264-.186H2.967zM22.5 15.104c0 1.467-.466 2.616-1.398 3.448-.932.832-2.108 1.248-3.528 1.248-1.42 0-2.594-.416-3.522-1.248-.929-.832-1.393-1.981-1.393-3.448 0-1.475.464-2.631 1.393-3.466.928-.836 2.102-1.254 3.522-1.254 1.42 0 2.596.418 3.528 1.254.932.835 1.398 1.991 1.398 3.466zm-1.848.057c0-.872-.253-1.55-.759-2.034s-1.153-.73-1.938-.73c-.785 0-1.44.234-1.966.703-.526.468-.834 1.133-.924 1.996h5.604c.003-.13.003-.26 0-.39l.179-1.545h-5.93c.178-.588.485-1.063.922-1.424.436-.361.937-.597 1.503-.708.565-.111 1.102-.114 1.61-.008.509.106.977.329 1.403.668.427.34.74.786.938 1.34h1.96c-.14-.504-.382-.965-.723-1.383-.342-.418-.76-.763-1.255-1.035-.496-.272-1.037-.447-1.625-.525-.589-.079-1.182-.06-1.78.058a5.376 5.376 0 00-1.76.7 4.53 4.53 0 00-1.266 1.104c-.338.408-.569.86-.692 1.355-.124.495-.138.983-.04 1.464.098.481.316.936.653 1.365.337.428.772.764 1.305 1.007.533.243 1.062.389 1.587.437.525.048.991 0 1.397-.143.406-.143.783-.352 1.13-.628.348-.276.63-.603.846-.983.217-.38.33-.798.34-1.256h-1.86zm2.178-6.775H15.67v-1.31h5.16v1.31z"/></svg>',
        );
        return isset( $svgs[ $network ] ) ? $svgs[ $network ] : '';
    }
}

class Opulentia_Recent_Posts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'Opulentia_recent_posts',
            __( 'Opulentia Recent Posts', 'opulentia' ),
            array(
                'description' => __( 'Displays recent posts with optional thumbnail and date.', 'opulentia' ),
            )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $number   = ! empty( $instance['number'] ) ? min( max( absint( $instance['number'] ), 1 ), 10 ) : 5;
        $show_thumb = ! empty( $instance['show_thumb'] );
        $show_date  = ! empty( $instance['show_date'] );
        $posts = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => $number,
            'ignore_sticky_posts' => true,
            'no_found_rows'  => true,
        ) );
        if ( $posts->have_posts() ) {
            echo '<div class="op-recent-posts">';
            while ( $posts->have_posts() ) {
                $posts->the_post();
                echo '<div class="op-recent-post">';
                if ( $show_thumb && has_post_thumbnail() ) {
                    echo '<div class="op-recent-post-thumb">';
                    the_post_thumbnail( array( 60, 60 ), array( 'alt' => get_the_title() ) );
                    echo '</div>';
                }
                echo '<div class="op-recent-post-body">';
                printf(
                    '<h4 class="op-recent-post-title"><a href="%s">%s</a></h4>',
                    esc_url( get_permalink() ),
                    esc_html( get_the_title() )
                );
                if ( $show_date ) {
                    printf(
                        '<span class="op-recent-post-date">%s</span>',
                        esc_html( get_the_date() )
                    );
                }
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            wp_reset_postdata();
        }
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title      = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'opulentia' );
        $number     = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_thumb = ! empty( $instance['show_thumb'] );
        $show_date  = ! empty( $instance['show_date'] );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts (1-10):', 'opulentia' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" max="10" value="<?php echo esc_attr( $number ); ?>">
        </p>
        <p>
            <input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumb' ) ); ?>" type="checkbox" value="1" <?php checked( $show_thumb ); ?>>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>"><?php esc_html_e( 'Show thumbnail', 'opulentia' ); ?></label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" type="checkbox" value="1" <?php checked( $show_date ); ?>>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>"><?php esc_html_e( 'Show date', 'opulentia' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']      = sanitize_text_field( $new_instance['title'] );
        $instance['number']     = min( max( absint( $new_instance['number'] ), 1 ), 10 );
        $instance['show_thumb'] = ! empty( $new_instance['show_thumb'] ) ? 1 : 0;
        $instance['show_date']  = ! empty( $new_instance['show_date'] ) ? 1 : 0;
        return $instance;
    }
}

class Opulentia_Author_Bio_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'Opulentia_author_bio',
            __( 'Opulentia Author Bio', 'opulentia' ),
            array(
                'description' => __( 'Displays the author avatar, name, description, and posts link.', 'opulentia' ),
            )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $author_id = ! empty( $instance['author_id'] ) ? absint( $instance['author_id'] ) : 0;
        if ( ! $author_id && is_author() ) {
            $author_id = get_queried_object_id();
        }
        if ( ! $author_id ) {
            $author_id = get_the_author_meta( 'ID' );
        }
        if ( ! $author_id ) {
            echo '<p class="op-muted">' . esc_html__( 'No author selected.', 'opulentia' ) . '</p>';
            echo $args['after_widget'];
            return;
        }
        $avatar      = get_avatar( $author_id, 80 );
        $display_name = get_the_author_meta( 'display_name', $author_id );
        $description  = get_the_author_meta( 'description', $author_id );
        $posts_url    = get_author_posts_url( $author_id );
        echo '<div class="op-author-bio">';
        if ( $avatar ) {
            echo '<div class="op-author-avatar">' . $avatar . '</div>';
        }
        if ( $display_name ) {
            printf( '<h4 class="op-author-name">%s</h4>', esc_html( $display_name ) );
        }
        if ( $description ) {
            printf( '<p class="op-author-desc">%s</p>', esc_html( $description ) );
        }
        if ( $posts_url ) {
            printf(
                '<a class="op-author-link" href="%s">%s</a>',
                esc_url( $posts_url ),
                esc_html__( 'View all posts', 'opulentia' )
            );
        }
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title     = ! empty( $instance['title'] ) ? $instance['title'] : __( 'About the Author', 'opulentia' );
        $author_id = ! empty( $instance['author_id'] ) ? absint( $instance['author_id'] ) : 0;
        $authors   = get_users( array(
            'who'      => 'authors',
            'orderby'  => 'display_name',
            'order'    => 'ASC',
        ) );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'author_id' ) ); ?>"><?php esc_html_e( 'Select author:', 'opulentia' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author_id' ) ); ?>">
                <option value="0" <?php selected( $author_id, 0 ); ?>><?php esc_html_e( 'Auto (current context)', 'opulentia' ); ?></option>
                <?php foreach ( $authors as $author ) : ?>
                    <option value="<?php echo esc_attr( $author->ID ); ?>" <?php selected( $author_id, $author->ID ); ?>><?php echo esc_html( $author->display_name ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p class="op-muted" style="font-size:0.85rem;color:#999;">
            <?php esc_html_e( 'Choose "Auto" to show the current author on author archive pages, or select a specific author.', 'opulentia' ); ?>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']     = sanitize_text_field( $new_instance['title'] );
        $instance['author_id'] = absint( $new_instance['author_id'] );
        return $instance;
    }
}

class Opulentia_Contact_Info_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'Opulentia_contact_info',
            __( 'Opulentia Contact Info', 'opulentia' ),
            array(
                'description' => __( 'Displays contact information with address, phone, email, and hours.', 'opulentia' ),
            )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $address = ! empty( $instance['address'] ) ? $instance['address'] : '';
        $phone   = ! empty( $instance['phone'] ) ? $instance['phone'] : '';
        $email   = ! empty( $instance['email'] ) ? $instance['email'] : '';
        $hours   = ! empty( $instance['hours'] ) ? $instance['hours'] : '';
        echo '<div class="op-contact-info">';
        if ( $address ) {
            echo '<div class="op-contact-item">';
            echo '<span class="op-contact-icon">' . $this->get_svg( 'map-pin' ) . '</span>';
            printf( '<span class="op-contact-text">%s</span>', nl2br( esc_html( $address ) ) );
            echo '</div>';
        }
        if ( $phone ) {
            echo '<div class="op-contact-item">';
            echo '<span class="op-contact-icon">' . $this->get_svg( 'phone' ) . '</span>';
            printf(
                '<span class="op-contact-text"><a href="tel:%s">%s</a></span>',
                esc_attr( preg_replace( '/[^0-9+\-() ]/', '', $phone ) ),
                esc_html( $phone )
            );
            echo '</div>';
        }
        if ( $email ) {
            echo '<div class="op-contact-item">';
            echo '<span class="op-contact-icon">' . $this->get_svg( 'mail' ) . '</span>';
            printf(
                '<span class="op-contact-text"><a href="mailto:%s">%s</a></span>',
                esc_attr( sanitize_email( $email ) ),
                esc_html( $email )
            );
            echo '</div>';
        }
        if ( $hours ) {
            echo '<div class="op-contact-item">';
            echo '<span class="op-contact-icon">' . $this->get_svg( 'clock' ) . '</span>';
            printf( '<span class="op-contact-text">%s</span>', nl2br( esc_html( $hours ) ) );
            echo '</div>';
        }
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title   = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Contact Info', 'opulentia' );
        $address = ! empty( $instance['address'] ) ? $instance['address'] : '';
        $phone   = ! empty( $instance['phone'] ) ? $instance['phone'] : '';
        $email   = ! empty( $instance['email'] ) ? $instance['email'] : '';
        $hours   = ! empty( $instance['hours'] ) ? $instance['hours'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>"><?php esc_html_e( 'Address:', 'opulentia' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'address' ) ); ?>" rows="3"><?php echo esc_textarea( $address ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>"><?php esc_html_e( 'Phone:', 'opulentia' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'phone' ) ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>"><?php esc_html_e( 'Email:', 'opulentia' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="email" value="<?php echo esc_attr( $email ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'hours' ) ); ?>"><?php esc_html_e( 'Business Hours:', 'opulentia' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'hours' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hours' ) ); ?>" rows="3"><?php echo esc_textarea( $hours ); ?></textarea>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']   = sanitize_text_field( $new_instance['title'] );
        $instance['address'] = sanitize_textarea_field( $new_instance['address'] );
        $instance['phone']   = sanitize_text_field( $new_instance['phone'] );
        $instance['email']   = sanitize_email( $new_instance['email'] );
        $instance['hours']   = sanitize_textarea_field( $new_instance['hours'] );
        return $instance;
    }

    private function get_svg( $icon ) {
        $svgs = array(
            'map-pin' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
            'phone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>',
            'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        );
        return isset( $svgs[ $icon ] ) ? $svgs[ $icon ] : '';
    }
}

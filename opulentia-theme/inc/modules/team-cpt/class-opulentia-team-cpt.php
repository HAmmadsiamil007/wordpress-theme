<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Team_Cpt {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomy' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_team', array( $this, 'save_meta' ) );
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
        add_shortcode( 'op_team_grid', array( $this, 'shortcode_grid' ) );
    }

    public function register_post_type() {
        $labels = array(
            'name'               => __( 'Team', 'opulentia' ),
            'singular_name'      => __( 'Team Member', 'opulentia' ),
            'add_new'            => __( 'Add New', 'opulentia' ),
            'add_new_item'       => __( 'Add New Team Member', 'opulentia' ),
            'edit_item'          => __( 'Edit Team Member', 'opulentia' ),
            'new_item'           => __( 'New Team Member', 'opulentia' ),
            'view_item'          => __( 'View Team Member', 'opulentia' ),
            'search_items'       => __( 'Search Team', 'opulentia' ),
            'not_found'          => __( 'No team members found', 'opulentia' ),
            'not_found_in_trash' => __( 'No team members found in trash', 'opulentia' ),
            'all_items'          => __( 'All Team', 'opulentia' ),
            'menu_name'          => __( 'Team', 'opulentia' ),
        );

        register_post_type( 'team', array(
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array( 'slug' => 'team' ),
            'supports'     => array( 'title', 'editor', 'thumbnail' ),
            'menu_icon'    => 'dashicons-groups',
            'show_in_rest' => true,
        ) );
    }

    public function register_taxonomy() {
        register_taxonomy( 'team_category', 'team', array(
            'hierarchical' => true,
            'labels'       => array(
                'name'              => __( 'Team Categories', 'opulentia' ),
                'singular_name'     => __( 'Team Category', 'opulentia' ),
                'search_items'      => __( 'Search Categories', 'opulentia' ),
                'all_items'         => __( 'All Categories', 'opulentia' ),
                'parent_item'       => __( 'Parent Category', 'opulentia' ),
                'parent_item_colon' => __( 'Parent Category:', 'opulentia' ),
                'edit_item'         => __( 'Edit Category', 'opulentia' ),
                'update_item'       => __( 'Update Category', 'opulentia' ),
                'add_new_item'      => __( 'Add New Category', 'opulentia' ),
                'new_item_name'     => __( 'New Category Name', 'opulentia' ),
                'menu_name'         => __( 'Categories', 'opulentia' ),
            ),
            'rewrite'      => array( 'slug' => 'team/category' ),
            'show_in_rest' => true,
        ) );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'opulentia_team_details',
            __( 'Team Member Details', 'opulentia' ),
            array( $this, 'render_meta_box' ),
            'team',
            'normal',
            'high'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'opulentia_team_meta', 'opulentia_team_meta_nonce' );
        $position    = get_post_meta( $post->ID, '_team_position', true );
        $bio         = get_post_meta( $post->ID, '_team_bio', true );
        $email       = get_post_meta( $post->ID, '_team_email', true );
        $social      = get_post_meta( $post->ID, '_team_social_links', true );
        ?>
        <p>
            <label for="team_position"><?php _e( 'Position', 'opulentia' ); ?></label><br>
            <input type="text" id="team_position" name="team_position" value="<?php echo esc_attr( $position ); ?>" style="width:100%">
        </p>
        <p>
            <label for="team_bio"><?php _e( 'Bio', 'opulentia' ); ?></label><br>
            <textarea id="team_bio" name="team_bio" rows="4" style="width:100%"><?php echo esc_textarea( $bio ); ?></textarea>
        </p>
        <p>
            <label for="team_email"><?php _e( 'Email', 'opulentia' ); ?></label><br>
            <input type="email" id="team_email" name="team_email" value="<?php echo esc_attr( $email ); ?>" style="width:100%">
        </p>
        <p>
            <label for="team_social_links"><?php _e( 'Social Links', 'opulentia' ); ?></label><br>
            <textarea id="team_social_links" name="team_social_links" rows="4" style="width:100%" placeholder="<?php esc_attr_e( 'Label|URL (one per line)', 'opulentia' ); ?>"><?php echo esc_textarea( $social ); ?></textarea>
        </p>
        <?php
    }

    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['opulentia_team_meta_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['opulentia_team_meta_nonce'], 'opulentia_team_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['team_position'] ) ) {
            update_post_meta( $post_id, '_team_position', sanitize_text_field( $_POST['team_position'] ) );
        }
        if ( isset( $_POST['team_bio'] ) ) {
            update_post_meta( $post_id, '_team_bio', sanitize_textarea_field( $_POST['team_bio'] ) );
        }
        if ( isset( $_POST['team_email'] ) ) {
            update_post_meta( $post_id, '_team_email', sanitize_email( $_POST['team_email'] ) );
        }
        if ( isset( $_POST['team_social_links'] ) ) {
            update_post_meta( $post_id, '_team_social_links', sanitize_textarea_field( $_POST['team_social_links'] ) );
        }
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_team', array(
            'title'    => __( 'Team', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 125,
        ) );

        $wp_customize->add_setting( 'team-grid-columns', array(
            'default'           => 3,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'team-grid-columns', array(
            'label'   => __( 'Grid Columns', 'opulentia' ),
            'section' => 'opulentia_team',
            'type'    => 'select',
            'choices' => array(
                2 => __( '2 Columns', 'opulentia' ),
                3 => __( '3 Columns', 'opulentia' ),
                4 => __( '4 Columns', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'team-hover-effect', array(
            'default'           => 'overlay',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'team-hover-effect', array(
            'label'   => __( 'Hover Effect', 'opulentia' ),
            'section' => 'opulentia_team',
            'type'    => 'select',
            'choices' => array(
                'none'    => __( 'None', 'opulentia' ),
                'flip'    => __( 'Flip', 'opulentia' ),
                'overlay' => __( 'Overlay', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'team-show-social', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'team-show-social', array(
            'label'   => __( 'Show Social Links', 'opulentia' ),
            'section' => 'opulentia_team',
            'type'    => 'checkbox',
        ) );
    }

    public function shortcode_grid( $atts ) {
        $atts = shortcode_atts( array(
            'columns'  => Opulentia_get_option( 'team-grid-columns', 3 ),
            'category' => '',
            'count'    => -1,
        ), $atts );

        $args = array(
            'post_type'      => 'team',
            'posts_per_page'  => intval( $atts['count'] ),
        );

        if ( ! empty( $atts['category'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'team_category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $atts['category'] ),
                ),
            );
        }

        $query = new WP_Query( $args );
        if ( ! $query->have_posts() ) {
            return '<p>' . __( 'No team members found.', 'opulentia' ) . '</p>';
        }

        $cols       = intval( $atts['columns'] );
        $effect     = Opulentia_get_option( 'team-hover-effect', 'overlay' );
        $show_social = Opulentia_get_option( 'team-show-social', true );

        $output = '<div class="op-team-grid" style="--op-team-col:' . $cols . '">';

        while ( $query->have_posts() ) {
            $query->the_post();
            $position = get_post_meta( get_the_ID(), '_team_position', true );
            $email    = get_post_meta( get_the_ID(), '_team_email', true );
            $social   = get_post_meta( get_the_ID(), '_team_social_links', true );

            $output .= '<article class="op-team-item op-team-hover--' . esc_attr( $effect ) . '">';
            if ( has_post_thumbnail() ) {
                $output .= '<div class="op-team-photo">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</div>';
            }
            $output .= '<div class="op-team-info">';
            $output .= '<h3 class="op-team-name">' . get_the_title() . '</h3>';
            if ( $position ) {
                $output .= '<span class="op-team-position">' . esc_html( $position ) . '</span>';
            }
            if ( $email ) {
                $output .= '<div class="op-team-email"><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></div>';
            }
            if ( $show_social && $social ) {
                $lines = explode( "\n", $social );
                $output .= '<div class="op-team-social">';
                foreach ( $lines as $line ) {
                    $parts = explode( '|', trim( $line ) );
                    if ( count( $parts ) === 2 ) {
                        $label = trim( $parts[0] );
                        $url   = trim( $parts[1] );
                        $output .= '<a href="' . esc_url( $url ) . '" class="op-team-social-link" target="_blank" rel="noopener">' . esc_html( $label ) . '</a>';
                    }
                }
                $output .= '</div>';
            }
            $output .= '</div>';
            $output .= '</article>';
        }

        wp_reset_postdata();
        $output .= '</div>';

        return $output;
    }

    public function inline_css() {
        if ( ! is_singular( 'team' ) && ! is_post_type_archive( 'team' ) && ! is_tax( 'team_category' ) ) {
            global $post;
            if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'op_team_grid' ) ) {
                return;
            }
        }

        $css = '
        .op-team-grid {
            display: grid;
            grid-template-columns: repeat(var(--op-team-col, 3), 1fr);
            gap: 24px;
        }
        .op-team-item {
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .op-team-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .op-team-photo {
            overflow: hidden;
        }
        .op-team-photo img {
            width: 100%;
            height: auto;
            display: block;
        }
        .op-team-info {
            padding: 16px;
        }
        .op-team-name {
            margin: 0 0 4px;
            font-family: var(--font-heading);
            font-size: 1.1rem;
            color: var(--color-text);
        }
        .op-team-position {
            font-size: 0.85rem;
            color: var(--color-gold);
            display: block;
            margin-bottom: 8px;
        }
        .op-team-email {
            margin-bottom: 8px;
        }
        .op-team-email a {
            color: var(--color-text-muted);
            font-size: 0.85rem;
            text-decoration: none;
        }
        .op-team-email a:hover {
            color: var(--color-accent);
        }
        .op-team-social {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .op-team-social-link {
            font-size: 0.8rem;
            color: var(--color-accent);
            text-decoration: none;
            padding: 2px 8px;
            border: 1px solid var(--color-accent);
            border-radius: 4px;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .op-team-social-link:hover {
            background: var(--color-accent);
            color: #fff;
        }
        .op-team-hover--flip {
            perspective: 1000px;
        }
        .op-team-hover--flip .op-team-photo {
            transition: transform 0.5s ease;
            transform-style: preserve-3d;
        }
        .op-team-hover--flip:hover .op-team-photo {
            transform: rotateY(180deg);
        }
        .op-team-hover--overlay .op-team-photo {
            position: relative;
        }
        .op-team-hover--overlay .op-team-photo::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, var(--color-accent), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .op-team-hover--overlay .op-team-item:hover .op-team-photo::after {
            opacity: 0.4;
        }
        @media (max-width: 768px) {
            .op-team-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .op-team-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}

add_action( 'init', array( 'Opulentia_Team_Cpt', 'get_instance' ) );
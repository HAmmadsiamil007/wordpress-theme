<?php
/**
 * Custom Widgets for SoleOrigine Theme
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Social Links Widget
 */
class SoleOrigine_Social_Links_Widget extends WP_Widget {

    /**
     * Widget constructor.
     */
    public function __construct() {
        parent::__construct(
            'soleorigine_social_links',
            __( 'SoleOrigine Social Links', 'soleorigine' ),
            array(
                'description' => __( 'Displays social media links.', 'soleorigine' ),
            )
        );
    }

    /**
     * Front-end display of widget.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $social_links = array(
            'facebook'  => __( 'Facebook', 'soleorigine' ),
            'instagram' => __( 'Instagram', 'soleorigine' ),
            'twitter'   => __( 'Twitter', 'soleorigine' ),
            'youtube'   => __( 'YouTube', 'soleorigine' ),
            'pinterest' => __( 'Pinterest', 'soleorigine' ),
        );

        echo '<div class="social-links">';
        foreach ( $social_links as $social => $label ) {
            $url = get_theme_mod( 'social_' . $social, '' );
            if ( ! empty( $url ) ) {
                printf(
                    '<a href="%s" target="_blank" rel="noopener noreferrer" class="social-links__item" aria-label="%s">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>',
                    esc_url( $url ),
                    esc_attr( $label )
                );
            }
        }
        echo '</div>';

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Follow Us', 'soleorigine' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'soleorigine' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p><?php esc_html_e( 'Social media URLs are configured in the Customizer.', 'soleorigine' ); ?></p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        return $instance;
    }
}

/**
 * About Widget
 */
class SoleOrigine_About_Widget extends WP_Widget {

    /**
     * Widget constructor.
     */
    public function __construct() {
        parent::__construct(
            'soleorigine_about',
            __( 'SoleOrigine About', 'soleorigine' ),
            array(
                'description' => __( 'Displays about information with image.', 'soleorigine' ),
            )
        );
    }

    /**
     * Front-end display of widget.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $image = ! empty( $instance['image'] ) ? $instance['image'] : '';
        $text = ! empty( $instance['text'] ) ? $instance['text'] : '';

        echo '<div class="about-widget">';
        if ( $image ) {
            printf(
                '<img src="%s" alt="%s" class="about-widget__image">',
                esc_url( $image ),
                esc_attr( $instance['title'] )
            );
        }
        if ( $text ) {
            printf(
                '<p class="about-widget__text">%s</p>',
                wp_kses_post( $text )
            );
        }
        echo '</div>';

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'About Us', 'soleorigine' );
        $image = ! empty( $instance['image'] ) ? $instance['image'] : '';
        $text = ! empty( $instance['text'] ) ? $instance['text'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'soleorigine' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Image URL:', 'soleorigine' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="url" value="<?php echo esc_url( $image ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text:', 'soleorigine' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="4"><?php echo esc_textarea( $text ); ?></textarea>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['image'] = esc_url_raw( $new_instance['image'] );
        $instance['text'] = wp_kses_post( $new_instance['text'] );
        return $instance;
    }
}

/**
 * Newsletter Widget
 */
class SoleOrigine_Newsletter_Widget extends WP_Widget {

    /**
     * Widget constructor.
     */
    public function __construct() {
        parent::__construct(
            'soleorigine_newsletter',
            __( 'SoleOrigine Newsletter', 'soleorigine' ),
            array(
                'description' => __( 'Displays newsletter signup form.', 'soleorigine' ),
            )
        );
    }

    /**
     * Front-end display of widget.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $text = ! empty( $instance['text'] ) ? $instance['text'] : __( 'Subscribe to our newsletter for the latest updates and exclusive offers.', 'soleorigine' );

        echo '<div class="newsletter-widget">';
        printf(
            '<p class="newsletter-widget__text">%s</p>',
            esc_html( $text )
        );
        ?>
        <form class="newsletter-widget__form" action="#" method="post">
            <input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email address', 'soleorigine' ); ?>" required>
            <button type="submit" class="btn btn--primary"><?php esc_html_e( 'Subscribe', 'soleorigine' ); ?></button>
        </form>
        <?php
        echo '</div>';

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Newsletter', 'soleorigine' );
        $text = ! empty( $instance['text'] ) ? $instance['text'] : __( 'Subscribe to our newsletter for the latest updates and exclusive offers.', 'soleorigine' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'soleorigine' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text:', 'soleorigine' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="3"><?php echo esc_textarea( $text ); ?></textarea>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['text'] = sanitize_textarea_field( $new_instance['text'] );
        return $instance;
    }
}

/**
 * Register Widgets
 */
function soleorigine_register_widgets() {
    register_widget( 'SoleOrigine_Social_Links_Widget' );
    register_widget( 'SoleOrigine_About_Widget' );
    register_widget( 'SoleOrigine_Newsletter_Widget' );
}
add_action( 'widgets_init', 'soleorigine_register_widgets' );

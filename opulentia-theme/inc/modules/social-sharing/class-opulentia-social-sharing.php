<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Social_Sharing {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_filter( 'the_content', array( $this, 'render_buttons' ) );
		add_action( 'woocommerce_share', array( $this, 'render_wc_buttons' ) );
	}

	private function get_networks() {
		return array(
			'facebook'  => __( 'Facebook', 'opulentia' ),
			'twitter'   => __( 'X (Twitter)', 'opulentia' ),
			'linkedin'  => __( 'LinkedIn', 'opulentia' ),
			'pinterest' => __( 'Pinterest', 'opulentia' ),
			'whatsapp'  => __( 'WhatsApp', 'opulentia' ),
			'email'     => __( 'Email', 'opulentia' ),
			'copy'      => __( 'Copy Link', 'opulentia' ),
		);
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_social_sharing',
			array(
				'title'    => __( 'Social Sharing', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 105,
			)
		);

		$wp_customize->add_setting(
			'social-sharing-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'social-sharing-enable',
			array(
				'label'   => __( 'Enable Social Sharing', 'opulentia' ),
				'section' => 'opulentia_social_sharing',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'social-sharing-position',
			array(
				'default'           => 'after',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'social-sharing-position',
			array(
				'label'   => __( 'Button Position', 'opulentia' ),
				'section' => 'opulentia_social_sharing',
				'type'    => 'select',
				'choices' => array(
					'before' => __( 'Before Content', 'opulentia' ),
					'after'  => __( 'After Content', 'opulentia' ),
					'both'   => __( 'Both', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'social-sharing-style',
			array(
				'default'           => 'icons-labels',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'social-sharing-style',
			array(
				'label'   => __( 'Button Style', 'opulentia' ),
				'section' => 'opulentia_social_sharing',
				'type'    => 'select',
				'choices' => array(
					'icons'        => __( 'Icons Only', 'opulentia' ),
					'icons-labels' => __( 'Icons + Labels', 'opulentia' ),
					'labels'       => __( 'Labels Only', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'social-sharing-posts',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'social-sharing-posts',
			array(
				'label'   => __( 'Show on Posts', 'opulentia' ),
				'section' => 'opulentia_social_sharing',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'social-sharing-pages',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'social-sharing-pages',
			array(
				'label'   => __( 'Show on Pages', 'opulentia' ),
				'section' => 'opulentia_social_sharing',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'social-sharing-products',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'social-sharing-products',
			array(
				'label'   => __( 'Show on Products', 'opulentia' ),
				'section' => 'opulentia_social_sharing',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'social-sharing-accent',
			array(
				'default'           => 'var(--color-gold)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'social-sharing-accent',
			array(
				'label'       => __( 'Accent Color', 'opulentia' ),
				'section'     => 'opulentia_social_sharing',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
			)
		);

		foreach ( $this->get_networks() as $key => $label ) {
			$wp_customize->add_setting(
				'social-sharing-' . $key,
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				'social-sharing-' . $key,
				array(
					'label'   => $label,
					'section' => 'opulentia_social_sharing',
					'type'    => 'checkbox',
				)
			);
		}
	}

	public function render_buttons( $content ) {
		if ( ! Opulentia_get_option( 'social-sharing-enable', true ) ) {
			return $content;
		}
		if ( ! is_singular() ) {
			return $content;
		}
		if ( is_singular( 'post' ) && ! Opulentia_get_option( 'social-sharing-posts', true ) ) {
			return $content;
		}
		if ( is_singular( 'page' ) && ! Opulentia_get_option( 'social-sharing-pages', false ) ) {
			return $content;
		}
		if ( is_singular( 'product' ) ) {
			return $content;
		}

		$position = Opulentia_get_option( 'social-sharing-position', 'after' );
		$buttons  = $this->build_buttons();

		if ( 'before' === $position ) {
			return $buttons . $content;
		}
		if ( 'after' === $position ) {
			return $content . $buttons;
		}
		return $buttons . $content . $buttons;
	}

	public function render_wc_buttons() {
		if ( ! Opulentia_get_option( 'social-sharing-enable', true ) ) {
			return;
		}
		if ( ! is_singular( 'product' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'social-sharing-products', true ) ) {
			return;
		}
		echo $this->build_buttons();
	}

	private function build_buttons() {
		$networks = $this->get_networks();
		$style    = Opulentia_get_option( 'social-sharing-style', 'icons-labels' );
		$title    = rawurlencode( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) );
		$url      = rawurlencode( get_permalink() );
		$thumb    = has_post_thumbnail() ? rawurlencode( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ) : '';

		$html = '<div class="op-social-sharing"><span class="op-social-sharing__label">' . esc_html__( 'Share:', 'opulentia' ) . '</span><div class="op-social-sharing__buttons">';

		foreach ( $networks as $key => $label ) {
			if ( ! Opulentia_get_option( 'social-sharing-' . $key, true ) ) {
				continue;
			}

			$share_url = '';
			switch ( $key ) {
				case 'facebook':
					$share_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
					break;
				case 'twitter':
					$share_url = 'https://twitter.com/intent/tweet?text=' . $title . '&url=' . $url;
					break;
				case 'linkedin':
					$share_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url;
					break;
				case 'pinterest':
					$share_url = 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title . ( $thumb ? '&media=' . $thumb : '' );
					break;
				case 'whatsapp':
					$share_url = 'https://wa.me/?text=' . $title . '%20' . $url;
					break;
				case 'email':
					$share_url = 'mailto:?subject=' . $title . '&body=' . $url;
					break;
				case 'copy':
					$html .= '<button class="op-social-sharing__btn op-social-sharing__btn--copy" data-url="' . esc_url( get_permalink() ) . '" aria-label="' . esc_attr__( 'Copy link', 'opulentia' ) . '">';
					$html .= $this->get_svg( $key );
					if ( 'icons-labels' === $style ) {
						$html .= '<span class="op-social-sharing__btn-label">' . esc_html( $label ) . '</span>';
					} elseif ( 'labels' === $style ) {
						$html .= '<span>' . esc_html( $label ) . '</span>';
					}
					$html .= '</button>';
					continue 2;
			}

			$html .= '<a href="' . esc_url( $share_url ) . '" class="op-social-sharing__btn op-social-sharing__btn--' . $key . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $label ) . '">';
			$html .= $this->get_svg( $key );
			if ( 'icons-labels' === $style ) {
				$html .= '<span class="op-social-sharing__btn-label">' . esc_html( $label ) . '</span>';
			} elseif ( 'labels' === $style ) {
				$html .= '<span>' . esc_html( $label ) . '</span>';
			}
			$html .= '</a>';
		}

		$html .= '</div></div>';
		return $html;
	}

	private function get_svg( $network ) {
		$svgs = array(
			'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>',
			'twitter'   => '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
			'pinterest' => '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',
			'whatsapp'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
			'email'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
			'copy'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>',
		);
		return isset( $svgs[ $network ] ) ? $svgs[ $network ] : '';
	}

	public function inline_css() {
		if ( ! Opulentia_get_option( 'social-sharing-enable', true ) ) {
			return;
		}

		$accent = Opulentia_get_option( 'social-sharing-accent', 'var(--color-gold)' );

		$css = '
        .op-social-sharing {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 0;
            margin: 24px 0;
            border-top: 1px solid var(--color-border);
            border-bottom: 1px solid var(--color-border);
        }
        .op-social-sharing__label {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            white-space: nowrap;
        }
        .op-social-sharing__buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .op-social-sharing__btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 6px;
            color: var(--color-text);
            text-decoration: none;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            min-height: 36px;
        }
        .op-social-sharing__btn:hover {
            background: ' . $accent . ';
            color: #000;
            border-color: ' . $accent . ';
        }
        .op-social-sharing__btn--copy {
            font-family: inherit;
        }
        .op-social-sharing__btn--copy.copied {
            background: #22c55e;
            color: #fff;
            border-color: #22c55e;
        }
        .op-social-sharing__btn-label {
            font-size: 0.8rem;
        }
        @media (max-width: 576px) {
            .op-social-sharing {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );

		wp_add_inline_script(
			'opulentia-custom',
			'
        document.addEventListener("click", function(e) {
            var btn = e.target.closest(".op-social-sharing__btn--copy");
            if (!btn) return;
            var url = btn.getAttribute("data-url");
            if (!url) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    var orig = btn.innerHTML;
                    btn.classList.add("copied");
                    btn.innerHTML = "<svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" width=\"18\" height=\"18\"><polyline points=\"20 6 9 17 4 12\"/></svg> <span class=\"op-social-sharing__btn-label\">Copied!</span>";
                    setTimeout(function() {
                        btn.classList.remove("copied");
                        btn.innerHTML = orig;
                    }, 2500);
                });
            }
        });
        '
		);
	}
}

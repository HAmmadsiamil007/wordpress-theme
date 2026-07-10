/**
 * Customizer JavaScript for opulentia theme
 *
 * Handles live preview of customizer settings — colors, fonts,
 * layout toggles, blog, WooCommerce, header controls, and typography.
 * Colors update CSS custom properties on body for instant preview.
 *
 * @package opulentia
 */

( function( $ ) {
    'use strict';

    // =========================================================================
    // Helper: Update a CSS custom property on the body element
    // =========================================================================

    function updateCssVar( property, value ) {
        if ( value ) {
            $( 'body' )[0].style.setProperty( property, value );
        }
    }

    /**
     * Helper: Update an element's CSS property.
     */
    function updateElementCss( selector, cssProp, value ) {
        if ( value ) {
            $( selector ).css( cssProp, value );
        }
    }

    // =========================================================================
    // Site Identity
    // =========================================================================

    wp.customize( 'blogname', function( value ) {
        value.bind( function( to ) {
            $( '.site-title a' ).text( to );
        } );
    } );

    wp.customize( 'blogdescription', function( value ) {
        value.bind( function( to ) {
            $( '.site-description' ).text( to );
        } );
    } );

    // =========================================================================
    // Global Colors — update CSS custom properties live
    // =========================================================================

    wp.customize( 'color_primary_dark', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-primary-dark', to ); } } );
    } );

    wp.customize( 'color_secondary_dark', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-secondary-dark', to ); } } );
    } );

    wp.customize( 'color_accent', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-accent', to ); } } );
    } );

    wp.customize( 'color_accent_hover', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-accent-hover', to ); } } );
    } );

    wp.customize( 'color_gold', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-gold', to ); } } );
    } );

    wp.customize( 'color_gold_hover', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-gold-hover', to ); } } );
    } );

    wp.customize( 'color_text', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-text', to ); } } );
    } );

    wp.customize( 'color_border', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-border', to ); } } );
    } );

    wp.customize( 'color_header_bg', function( value ) {
        value.bind( function( to ) { if ( to ) { $( '.site-header' ).css( 'background-color', to ); } } );
    } );

    wp.customize( 'color_footer_bg', function( value ) {
        value.bind( function( to ) { if ( to ) { $( '.site-footer' ).css( 'background-color', to ); } } );
    } );

    wp.customize( 'color_link', function( value ) {
        value.bind( function( to ) { if ( to ) { updateCssVar( '--color-gold', to ); } } );
    } );

    wp.customize( 'color_button_bg', function( value ) {
        value.bind( function( to ) { if ( to ) { $( '.btn--primary' ).css( 'background-color', to ).css( 'border-color', to ); } } );
    } );

    wp.customize( 'color_button_text', function( value ) {
        value.bind( function( to ) { if ( to ) { $( '.btn--primary' ).css( 'color', to ); } } );
    } );

    // =========================================================================
    // Typography — Live Preview
    // =========================================================================

    // Heads up: Typography settings use "optionize" format (Opulentia_settings[...])
    // For live preview, we watch the base setting ID (without option prefix).

    /**
     * Helper: Live preview for font-family setting.
     */
    function bindFontFamily( settingId, selector ) {
        wp.customize( settingId, function( value ) {
            value.bind( function( to ) {
                if ( to ) {
                    updateElementCss( selector, 'font-family', "'" + to + "', serif" );
                }
            } );
        } );
    }

    /**
     * Helper: Live preview for font-weight setting.
     */
    function bindFontWeight( settingId, selector ) {
        wp.customize( settingId, function( value ) {
            value.bind( function( to ) {
                if ( to ) {
                    updateElementCss( selector, 'font-weight', to );
                }
            } );
        } );
    }

    /**
     * Helper: Live preview for text-transform setting.
     */
    function bindTextTransform( settingId, selector ) {
        wp.customize( settingId, function( value ) {
            value.bind( function( to ) {
                if ( to ) {
                    updateElementCss( selector, 'text-transform', to );
                } else {
                    updateElementCss( selector, 'text-transform', '' );
                }
            } );
        } );
    }

    /**
     * Helper: Live preview for letter-spacing setting.
     */
    function bindLetterSpacing( settingId, selector ) {
        wp.customize( settingId, function( value ) {
            value.bind( function( to ) {
                if ( to ) {
                    var spacing = isNaN( parseFloat( to ) ) ? to : parseFloat( to ) + 'px';
                    updateElementCss( selector, 'letter-spacing', spacing );
                }
            } );
        } );
    }

    // ── Headings (General) ──

    wp.customize( 'typo-headings-family', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( 'h1, h2, h3, h4, h5, h6' ).css( 'font-family', "'" + to + "', serif" );
            }
        } );
    } );

    wp.customize( 'typo-headings-weight', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( 'h1, h2, h3, h4, h5, h6' ).css( 'font-weight', to ); }
        } );
    } );

    wp.customize( 'typo-headings-line-height', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( 'h1, h2, h3, h4, h5, h6' ).css( 'line-height', to ); }
        } );
    } );

    wp.customize( 'typo-headings-transform', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( 'h1, h2, h3, h4, h5, h6' ).css( 'text-transform', to ); }
        } );
    } );

    // ── Body ──

    wp.customize( 'typo-body-family', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( 'body' ).css( 'font-family', "'" + to + "', sans-serif" ); }
        } );
    } );

    wp.customize( 'typo-body-weight', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( 'body' ).css( 'font-weight', to ); }
        } );
    } );

    wp.customize( 'typo-body-line-height', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( 'body' ).css( 'line-height', to ); }
        } );
    } );

    // ── Site Title ──

    wp.customize( 'typo-site-title-weight', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( '.site-title, .site-logo__text' ).css( 'font-weight', to ); }
        } );
    } );

    wp.customize( 'typo-site-title-transform', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( '.site-title, .site-logo__text' ).css( 'text-transform', to ); }
        } );
    } );

    // ── Navigation ──

    wp.customize( 'typo-nav-weight', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( '.main-navigation a' ).css( 'font-weight', to ); }
        } );
    } );

    wp.customize( 'typo-nav-transform', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( '.main-navigation a' ).css( 'text-transform', to ); }
        } );
    } );

    wp.customize( 'typo-nav-spacing', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                var spacing = isNaN( parseFloat( to ) ) ? to : parseFloat( to ) + 'px';
                $( '.main-navigation a' ).css( 'letter-spacing', spacing );
            }
        } );
    } );

    // ── Buttons ──

    wp.customize( 'typo-btn-weight', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( '.btn, .button' ).css( 'font-weight', to ); }
        } );
    } );

    wp.customize( 'typo-btn-transform', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( '.btn, .button' ).css( 'text-transform', to ); }
        } );
    } );

    wp.customize( 'typo-btn-spacing', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                var spacing = isNaN( parseFloat( to ) ) ? to : parseFloat( to ) + 'px';
                $( '.btn, .button' ).css( 'letter-spacing', spacing );
            }
        } );
    } );

    // ── Widget Titles ──

    wp.customize( 'typo-widget-weight', function( value ) {
        value.bind( function( to ) {
            if ( to ) { $( '.widget__title, .widget-title' ).css( 'font-weight', to ); }
        } );
    } );

    // =========================================================================
    // Hero Section
    // =========================================================================

    wp.customize( 'hero_title', function( value ) {
        value.bind( function( to ) {
            $( '.hero__title' ).text( to );
        } );
    } );

    wp.customize( 'hero_subtitle', function( value ) {
        value.bind( function( to ) {
            $( '.hero__subtitle' ).text( to );
        } );
    } );

    wp.customize( 'hero_button_1_text', function( value ) {
        value.bind( function( to ) {
            $( '.hero__buttons .btn--primary' ).text( to );
        } );
    } );

    wp.customize( 'hero_button_2_text', function( value ) {
        value.bind( function( to ) {
            $( '.hero__buttons .btn--outline' ).text( to );
        } );
    } );

    wp.customize( 'hero_background', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( '.hero__background' ).attr( 'src', to );
            }
        } );
    } );

    // =========================================================================
    // About Section
    // =========================================================================

    wp.customize( 'about_title', function( value ) {
        value.bind( function( to ) {
            $( '.about-content__title' ).text( to );
        } );
    } );

    wp.customize( 'about_subtitle', function( value ) {
        value.bind( function( to ) {
            $( '.about-content__subtitle' ).text( to );
        } );
    } );

    wp.customize( 'about_text', function( value ) {
        value.bind( function( to ) {
            $( '.about-content__text' ).text( to );
        } );
    } );

    wp.customize( 'about_image', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( '.about-image img' ).attr( 'src', to );
            }
        } );
    } );

    // =========================================================================
    // Collection Section
    // =========================================================================

    wp.customize( 'collection_title', function( value ) {
        value.bind( function( to ) {
            $( '.collection-section .section-title' ).text( to );
        } );
    } );

    wp.customize( 'collection_subtitle', function( value ) {
        value.bind( function( to ) {
            $( '.collection-section .section-subtitle' ).text( to );
        } );
    } );

    // =========================================================================
    // Footer Section
    // =========================================================================

    wp.customize( 'footer_copyright', function( value ) {
        value.bind( function( to ) {
            $( '.footer-copyright' ).html( to );
        } );
    } );

    wp.customize( 'blog_title', function( value ) {
        value.bind( function( to ) {
            $( '.blog-section .page-header__title, .posts-grid ~ .page-header__title' ).text( to );
        } );
    } );

    // =========================================================================
    // Builder Controls — Live Preview
    // =========================================================================

    // Header layout — structural, refresh required.
    // No live JS binding since it changes HTML structure.

    // Header component visibility toggles.
    wp.customize( 'header_show_search', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.header-actions__btn[aria-label="Search"]' ).hide();
            } else {
                $( '.header-actions__btn[aria-label="Search"]' ).show();
            }
        } );
    } );

    wp.customize( 'header_show_account', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.header-actions__btn[aria-label="My Account"]' ).hide();
            } else {
                $( '.header-actions__btn[aria-label="My Account"]' ).show();
            }
        } );
    } );

    wp.customize( 'header_show_cart', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.header-actions__btn--cart' ).hide();
            } else {
                $( '.header-actions__btn--cart' ).show();
            }
        } );
    } );

    // Top bar component toggles.
    wp.customize( 'header_top_bar_tagline', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.header-top__left' ).hide();
            } else {
                $( '.header-top__left' ).show();
            }
        } );
    } );

    wp.customize( 'header_top_bar_shipping', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.header-top__right' ).hide();
            } else {
                $( '.header-top__right' ).show();
            }
        } );
    } );

    // Footer column count — structural, refresh required.

    // Footer component visibility.
    wp.customize( 'footer_show_brand', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.footer-brand' ).hide();
            } else {
                $( '.footer-brand' ).show();
            }
        } );
    } );

    wp.customize( 'footer_show_social', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.footer-social' ).hide();
            } else {
                $( '.footer-social' ).show();
            }
        } );
    } );

    wp.customize( 'footer_show_payment_icons', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.footer-payments' ).hide();
            } else {
                $( '.footer-payments' ).show();
            }
        } );
    } );

    // Header top bar visibility.
    wp.customize( 'header_top_bar', function( value ) {
        value.bind( function( to ) {
            if ( ! to ) {
                $( '.header-top' ).hide();
            } else {
                $( '.header-top' ).show();
            }
        } );
    } );

    // Header layout changes require refresh.
    // Content layout, sidebar position, sticky header also need refresh.

    // =========================================================================
    // Breadcrumbs — Live Preview
    // =========================================================================

    wp.customize( 'breadcrumb_separator', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( '.breadcrumbs__separator' ).text( to );
            }
        } );
    } );

    wp.customize( 'breadcrumb_home_text', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( '.breadcrumbs__link--home' ).text( to );
            }
        } );
    } );

    wp.customize( 'breadcrumb_show_current', function( value ) {
        value.bind( function( to ) {
            $( '.breadcrumbs__current' ).toggle( to );
        } );
    } );

} )( jQuery );

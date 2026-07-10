/**
 * Navigation functionality for opulentia theme
 *
 * Handles mobile menu toggle, off-canvas panel, and accessibility
 *
 * @package opulentia
 */

( function() {
    'use strict';

    var mobileMenuToggle = document.querySelector( '.mobile-menu-toggle' );
    var siteNavigation = document.getElementById( 'site-navigation' );
    var headerActions = document.querySelector( '.header-actions' );

    // Off-canvas panel support
    var offCanvasPanel = document.getElementById( 'off-canvas-panel' );
    var offCanvasClose = document.querySelector( '.off-canvas-panel__close' );

    // --- Standard Mobile Menu Toggle ---

    if ( mobileMenuToggle && siteNavigation ) {
        mobileMenuToggle.addEventListener( 'click', function() {
            // If off-canvas layout is active, use that instead.
            if ( offCanvasPanel && document.querySelector( '.site-header--off-canvas' ) ) {
                toggleOffCanvas();
                return;
            }

            var isOpen = siteNavigation.classList.contains( 'is-open' );

            if ( isOpen ) {
                closeMenu();
            } else {
                openMenu();
            }
        } );
    }

    function openMenu() {
        siteNavigation.classList.add( 'is-open' );
        mobileMenuToggle.classList.add( 'is-active' );
        mobileMenuToggle.setAttribute( 'aria-expanded', 'true' );
        document.body.classList.add( 'menu-open' );

        // Move header actions inside nav for mobile
        if ( headerActions ) {
            siteNavigation.appendChild( headerActions );
        }

        // Set focus to first menu item
        var firstMenuItem = siteNavigation.querySelector( 'a' );
        if ( firstMenuItem ) {
            firstMenuItem.focus();
        }

        // Trap focus within menu
        document.addEventListener( 'keydown', trapFocus );
    }

    function closeMenu() {
        siteNavigation.classList.remove( 'is-open' );
        mobileMenuToggle.classList.remove( 'is-active' );
        mobileMenuToggle.setAttribute( 'aria-expanded', 'false' );
        document.body.classList.remove( 'menu-open' );

        // Move header actions back
        var actionsContainer = document.querySelector( '.header-actions-container' );
        if ( headerActions && actionsContainer ) {
            actionsContainer.appendChild( headerActions );
        }

        // Remove menu focus trap
        document.removeEventListener( 'keydown', trapFocus );
    }

    function trapFocus( event ) {
        if ( event.key !== 'Tab' ) {
            return;
        }

        var focusableElements = siteNavigation.querySelectorAll(
            'a[href], button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
        );

        var firstElement = focusableElements[0];
        var lastElement = focusableElements[focusableElements.length - 1];

        if ( event.shiftKey ) {
            if ( document.activeElement === firstElement ) {
                lastElement.focus();
                event.preventDefault();
            }
        } else {
            if ( document.activeElement === lastElement ) {
                firstElement.focus();
                event.preventDefault();
            }
        }
    }

    function escKeyHandler( event ) {
        if ( event.key === 'Escape' ) {
            if ( offCanvasPanel && offCanvasPanel.classList.contains( 'is-open' ) ) {
                closeOffCanvas();
            } else if ( siteNavigation && siteNavigation.classList.contains( 'is-open' ) ) {
                closeMenu();
            }
        }
    }
    document.addEventListener( 'keydown', escKeyHandler );

    // Close menu on window resize to desktop
    var resizeTimer;
    window.addEventListener( 'resize', function() {
        clearTimeout( resizeTimer );
        resizeTimer = setTimeout( function() {
            if ( window.innerWidth > 992 ) {
                if ( siteNavigation && siteNavigation.classList.contains( 'is-open' ) ) {
                    closeMenu();
                }
            }
        }, 250 );
    } );

    // --- Off-Canvas Panel Toggle ---

    function toggleOffCanvas() {
        if ( offCanvasPanel.classList.contains( 'is-open' ) ) {
            closeOffCanvas();
        } else {
            openOffCanvas();
        }
    }

    function openOffCanvas() {
        offCanvasPanel.classList.add( 'is-open' );
        offCanvasPanel.setAttribute( 'aria-hidden', 'false' );
        document.body.classList.add( 'off-canvas-open' );

        if ( mobileMenuToggle ) {
            mobileMenuToggle.classList.add( 'is-active' );
            mobileMenuToggle.setAttribute( 'aria-expanded', 'true' );
        }

        // Set focus to close button and trap focus
        if ( offCanvasClose ) {
            offCanvasClose.focus();
        }
        document.addEventListener( 'keydown', trapOffCanvasFocus );
    }

    function closeOffCanvas() {
        offCanvasPanel.classList.remove( 'is-open' );
        offCanvasPanel.setAttribute( 'aria-hidden', 'true' );
        document.body.classList.remove( 'off-canvas-open' );

        if ( mobileMenuToggle ) {
            mobileMenuToggle.classList.remove( 'is-active' );
            mobileMenuToggle.setAttribute( 'aria-expanded', 'false' );
            mobileMenuToggle.focus();
        }

        document.removeEventListener( 'keydown', trapOffCanvasFocus );
    }

    /**
     * Trap focus within the off-canvas panel for accessibility.
     */
    function trapOffCanvasFocus( event ) {
        if ( event.key !== 'Tab' || ! offCanvasPanel ) {
            return;
        }

        var focusableElements = offCanvasPanel.querySelectorAll(
            'a[href], button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
        );

        if ( focusableElements.length === 0 ) {
            return;
        }

        var firstElement = focusableElements[0];
        var lastElement = focusableElements[focusableElements.length - 1];

        if ( event.shiftKey ) {
            if ( document.activeElement === firstElement ) {
                lastElement.focus();
                event.preventDefault();
            }
        } else {
            if ( document.activeElement === lastElement ) {
                firstElement.focus();
                event.preventDefault();
            }
        }
    }

    // Close off-canvas on overlay click
    if ( offCanvasPanel ) {
        var overlay = offCanvasPanel.querySelector( '.off-canvas-panel__overlay' );
        if ( overlay ) {
            overlay.addEventListener( 'click', function() {
                closeOffCanvas();
            } );
        }

        // Close on close button click
        if ( offCanvasClose ) {
            offCanvasClose.addEventListener( 'click', function() {
                closeOffCanvas();
            } );
        }

        // Initial aria state
        offCanvasPanel.setAttribute( 'aria-hidden', 'true' );
    }

    // --- Smooth scroll for anchor links ---
    document.querySelectorAll( 'a[href^="#"]' ).forEach( function( anchor ) {
        anchor.addEventListener( 'click', function( event ) {
            var targetId = this.getAttribute( 'href' );
            if ( targetId === '#' ) {
                return;
            }

            var target = document.querySelector( targetId );
            if ( target ) {
                event.preventDefault();
                var header = document.querySelector( '.site-header' );
                var headerHeight = header ? header.offsetHeight : 0;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo( {
                    top: targetPosition,
                    behavior: 'smooth'
                } );

                // Close mobile menu if open
                if ( siteNavigation && siteNavigation.classList.contains( 'is-open' ) ) {
                    closeMenu();
                }

                // Close off-canvas if open
                if ( offCanvasPanel && offCanvasPanel.classList.contains( 'is-open' ) ) {
                    closeOffCanvas();
                }
            }
        } );
    } );
} )();

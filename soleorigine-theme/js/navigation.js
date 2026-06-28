/**
 * Navigation functionality for SoleOrigine theme
 *
 * Handles mobile menu toggle and accessibility
 *
 * @package SoleOrigine
 */

( function() {
    'use strict';

    var mobileMenuToggle = document.querySelector( '.mobile-menu-toggle' );
    var siteNavigation = document.getElementById( 'site-navigation' );
    var headerActions = document.querySelector( '.header-actions' );

    if ( ! mobileMenuToggle || ! siteNavigation ) {
        return;
    }

    // Toggle mobile menu
    mobileMenuToggle.addEventListener( 'click', function() {
        var isOpen = siteNavigation.classList.contains( 'is-open' );

        if ( isOpen ) {
            closeMenu();
        } else {
            openMenu();
        }
    } );

    function openMenu() {
        siteNavigation.classList.add( 'is-open' );
        mobileMenuToggle.classList.add( 'is-active' );
        mobileMenuToggle.setAttribute( 'aria-expanded', 'true' );
        document.body.style.overflow = 'hidden';

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
        document.body.style.overflow = '';

        // Move header actions back
        if ( headerActions && headerActions.parentNode !== document.querySelector( '.header-actions-container' ) ) {
            document.querySelector( '.header-actions-container' ).appendChild( headerActions );
        }

        // Remove focus trap
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

    // Close menu on Escape key
    document.addEventListener( 'keydown', function( event ) {
        if ( event.key === 'Escape' && siteNavigation.classList.contains( 'is-open' ) ) {
            closeMenu();
            mobileMenuToggle.focus();
        }
    } );

    // Close menu on window resize to desktop
    var resizeTimer;
    window.addEventListener( 'resize', function() {
        clearTimeout( resizeTimer );
        resizeTimer = setTimeout( function() {
            if ( window.innerWidth > 768 && siteNavigation.classList.contains( 'is-open' ) ) {
                closeMenu();
            }
        }, 250 );
    } );

    // Smooth scroll for anchor links
    document.querySelectorAll( 'a[href^="#"]' ).forEach( function( anchor ) {
        anchor.addEventListener( 'click', function( event ) {
            var targetId = this.getAttribute( 'href' );
            if ( targetId === '#' ) {
                return;
            }

            var target = document.querySelector( targetId );
            if ( target ) {
                event.preventDefault();
                var headerHeight = document.querySelector( '.site-header' ).offsetHeight;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo( {
                    top: targetPosition,
                    behavior: 'smooth'
                } );

                // Close mobile menu if open
                if ( siteNavigation.classList.contains( 'is-open' ) ) {
                    closeMenu();
                }
            }
        } );
    } );
} )();

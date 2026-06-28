/**
 * Custom JavaScript for SoleOrigine theme
 *
 * Handles header behavior, scroll effects, and animations
 *
 * @package SoleOrigine
 */

( function() {
    'use strict';

    // Header scroll behavior
    var header = document.querySelector( '.site-header' );
    var lastScrollTop = 0;

    function handleHeaderScroll() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if ( scrollTop > 100 ) {
            header.classList.add( 'scrolled' );
        } else {
            header.classList.remove( 'scrolled' );
        }

        lastScrollTop = scrollTop;
    }

    window.addEventListener( 'scroll', handleHeaderScroll );
    handleHeaderScroll();

    // Intersection Observer for scroll animations
    if ( 'IntersectionObserver' in window ) {
        var animateElements = document.querySelectorAll( '.animate-on-scroll' );

        var observer = new IntersectionObserver( function( entries ) {
            entries.forEach( function( entry ) {
                if ( entry.isIntersecting ) {
                    entry.target.classList.add( 'animated' );
                    observer.unobserve( entry.target );
                }
            } );
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        } );

        animateElements.forEach( function( element ) {
            observer.observe( element );
        } );
    }

    // Lazy loading for images
    if ( 'loading' in HTMLImageElement.prototype ) {
        var lazyImages = document.querySelectorAll( 'img[loading="lazy"]' );
        lazyImages.forEach( function( img ) {
            if ( img.dataset.src ) {
                img.src = img.dataset.src;
            }
        } );
    } else {
        // Fallback for browsers that don't support native lazy loading
        var lazyImages = document.querySelectorAll( 'img[loading="lazy"]' );

        if ( lazyImages.length > 0 ) {
            var lazyImageObserver = new IntersectionObserver( function( entries ) {
                entries.forEach( function( entry ) {
                    if ( entry.isIntersecting ) {
                        var lazyImage = entry.target;
                        if ( lazyImage.dataset.src ) {
                            lazyImage.src = lazyImage.dataset.src;
                        }
                        lazyImageObserver.unobserve( lazyImage );
                    }
                } );
            } );

            lazyImages.forEach( function( img ) {
                lazyImageObserver.observe( img );
            } );
        }
    }

    // Search toggle
    var searchToggle = document.querySelector( '.header-actions__btn[aria-label*="Search"]' );
    if ( searchToggle ) {
        searchToggle.addEventListener( 'click', function() {
            var searchOverlay = document.querySelector( '.search-overlay' );
            if ( searchOverlay ) {
                searchOverlay.classList.toggle( 'is-open' );
                if ( searchOverlay.classList.contains( 'is-open' ) ) {
                    var searchInput = searchOverlay.querySelector( 'input[type="search"]' );
                    if ( searchInput ) {
                        searchInput.focus();
                    }
                }
            }
        } );
    }

    // Add to cart button animation
    var addToCartButtons = document.querySelectorAll( '.add_to_cart_button' );
    addToCartButtons.forEach( function( button ) {
        button.addEventListener( 'click', function() {
            this.classList.add( 'loading' );
            this.disabled = true;
        } );

        // WooCommerce AJAX add to cart
        if ( typeof wc_add_to_cart_params !== 'undefined' ) {
            document.body.addEventListener( 'added_to_cart', function() {
                button.classList.remove( 'loading' );
                button.disabled = false;
            } );
        }
    } );

    // Product image hover effect
    var productCards = document.querySelectorAll( '.product-card' );
    productCards.forEach( function( card ) {
        var image = card.querySelector( '.product-card__image img' );
        if ( image ) {
            card.addEventListener( 'mouseenter', function() {
                image.style.transform = 'scale(1.05)';
            } );

            card.addEventListener( 'mouseleave', function() {
                image.style.transform = 'scale(1)';
            } );
        }
    } );

    // Parallax effect for hero section
    var hero = document.querySelector( '.hero' );
    if ( hero ) {
        window.addEventListener( 'scroll', function() {
            var scrollTop = window.pageYOffset;
            var heroBackground = hero.querySelector( '.hero__background' );
            if ( heroBackground && scrollTop < window.innerHeight ) {
                heroBackground.style.transform = 'translateY(' + ( scrollTop * 0.3 ) + 'px)';
            }
        } );
    }

    // Back to top button
    var backToTop = document.createElement( 'button' );
    backToTop.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 15l-6-6-6 6"/></svg>';
    backToTop.className = 'back-to-top';
    backToTop.setAttribute( 'aria-label', 'Back to top' );
    backToTop.style.display = 'none';
    document.body.appendChild( backToTop );

    window.addEventListener( 'scroll', function() {
        if ( window.pageYOffset > 300 ) {
            backToTop.style.display = 'flex';
        } else {
            backToTop.style.display = 'none';
        }
    } );

    backToTop.addEventListener( 'click', function() {
        window.scrollTo( {
            top: 0,
            behavior: 'smooth'
        } );
    } );
} )();

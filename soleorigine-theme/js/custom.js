/**
 * Custom JavaScript for SoleOrigine theme
 *
 * GSAP-powered animations, scroll effects, and interactive behaviors
 *
 * @package SoleOrigine
 */

(function () {
    'use strict';

    // Wait for DOM + GSAP to be available
    document.addEventListener('DOMContentLoaded', function () {
        // Register GSAP plugins
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            if (typeof ScrollToPlugin !== 'undefined') {
                gsap.registerPlugin(ScrollToPlugin);
            }
            initGSAPAnimations();
        }

        initHeaderBehavior();
        initMobileMenu();
        initSearchOverlay();
        initBackToTop();
        initProductInteractions();
        initSmoothAnchors();
    });

    /* ------------------------------------------------
     *  GSAP Animations
     * ------------------------------------------------ */
    function initGSAPAnimations() {
        // Default easing
        gsap.defaults({ ease: 'power3.out', duration: 1 });

        // ── Hero Section ──────────────────────────────
        var hero = document.querySelector('.hero');
        if (hero) {
            var heroTl = gsap.timeline();

            heroTl
                .from('.hero__subtitle', {
                    y: 40,
                    opacity: 0,
                    duration: 0.8,
                })
                .from('.hero__title', {
                    y: 60,
                    opacity: 0,
                    duration: 1,
                }, '-=0.5')
                .from('.hero__description', {
                    y: 40,
                    opacity: 0,
                    duration: 0.8,
                }, '-=0.6')
                .from('.hero__cta .btn', {
                    y: 30,
                    opacity: 0,
                    stagger: 0.15,
                    duration: 0.7,
                }, '-=0.5')
                .from('.hero__scroll-indicator', {
                    opacity: 0,
                    y: 20,
                    duration: 0.6,
                }, '-=0.3');

            // Hero parallax background
            var heroBg = hero.querySelector('.hero__background');
            if (heroBg) {
                gsap.to(heroBg, {
                    y: 150,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: hero,
                        start: 'top top',
                        end: 'bottom top',
                        scrub: 1.5,
                    },
                });
            }

            // Hero content fade-out on scroll
            var heroContent = hero.querySelector('.hero__content');
            if (heroContent) {
                gsap.to(heroContent, {
                    y: -80,
                    opacity: 0,
                    scrollTrigger: {
                        trigger: hero,
                        start: 'center center',
                        end: 'bottom top',
                        scrub: 1,
                    },
                });
            }
        }

        // ── Features Bar (stagger-in) ────────────────
        var featuresBar = document.querySelector('.features-bar');
        if (featuresBar) {
            gsap.from('.features-bar .feature-item', {
                y: 50,
                opacity: 0,
                stagger: 0.12,
                duration: 0.8,
                scrollTrigger: {
                    trigger: featuresBar,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
            });
        }

        // ── Section headings (fade-up) ───────────────
        document.querySelectorAll('.section-header').forEach(function (header) {
            gsap.from(header.children, {
                y: 50,
                opacity: 0,
                stagger: 0.15,
                duration: 0.9,
                scrollTrigger: {
                    trigger: header,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
            });
        });

        // ── Product cards (stagger-in) ───────────────
        var productGrids = document.querySelectorAll('.products-grid, .product-grid');
        productGrids.forEach(function (grid) {
            gsap.from(grid.querySelectorAll('.product-card'), {
                y: 60,
                opacity: 0,
                stagger: 0.1,
                duration: 0.8,
                scrollTrigger: {
                    trigger: grid,
                    start: 'top 80%',
                    toggleActions: 'play none none none',
                },
            });
        });

        // ── Category grid items ──────────────────────
        var categoryGrid = document.querySelector('.category-grid');
        if (categoryGrid) {
            gsap.from(categoryGrid.querySelectorAll('.category-card'), {
                scale: 0.9,
                opacity: 0,
                stagger: 0.08,
                duration: 0.7,
                scrollTrigger: {
                    trigger: categoryGrid,
                    start: 'top 80%',
                    toggleActions: 'play none none none',
                },
            });
        }

        // ── Elite Collection numbered items ──────────
        var eliteGrid = document.querySelector('.elite-collection__grid');
        if (eliteGrid) {
            gsap.from(eliteGrid.querySelectorAll('.elite-item'), {
                y: 80,
                opacity: 0,
                stagger: 0.12,
                duration: 0.9,
                scrollTrigger: {
                    trigger: eliteGrid,
                    start: 'top 80%',
                    toggleActions: 'play none none none',
                },
            });
        }

        // ── Brand Story split ────────────────────────
        var brandStory = document.querySelector('.brand-story');
        if (brandStory) {
            var brandTl = gsap.timeline({
                scrollTrigger: {
                    trigger: brandStory,
                    start: 'top 70%',
                    toggleActions: 'play none none none',
                },
            });

            brandTl
                .from('.brand-story__image', {
                    x: -80,
                    opacity: 0,
                    duration: 1,
                })
                .from('.brand-story__content > *', {
                    x: 80,
                    opacity: 0,
                    stagger: 0.15,
                    duration: 0.9,
                }, '-=0.7');
        }

        // ── Testimonials ─────────────────────────────
        var testimonials = document.querySelector('.testimonials');
        if (testimonials) {
            gsap.from('.testimonials .testimonial-card', {
                y: 60,
                opacity: 0,
                stagger: 0.15,
                duration: 0.9,
                scrollTrigger: {
                    trigger: testimonials,
                    start: 'top 80%',
                    toggleActions: 'play none none none',
                },
            });
        }

        // ── Instagram Feed ───────────────────────────
        var instagram = document.querySelector('.instagram-feed');
        if (instagram) {
            gsap.from('.instagram-feed .instagram-item', {
                scale: 0.85,
                opacity: 0,
                stagger: 0.08,
                duration: 0.7,
                scrollTrigger: {
                    trigger: instagram,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
            });
        }

        // ── Trust Badges ─────────────────────────────
        var trustBadges = document.querySelector('.trust-badges');
        if (trustBadges) {
            gsap.from('.trust-badges .trust-badge', {
                y: 40,
                opacity: 0,
                stagger: 0.1,
                duration: 0.7,
                scrollTrigger: {
                    trigger: trustBadges,
                    start: 'top 90%',
                    toggleActions: 'play none none none',
                },
            });
        }

        // ── Footer columns ───────────────────────────
        var footer = document.querySelector('.site-footer');
        if (footer) {
            gsap.from('.footer-grid > *', {
                y: 40,
                opacity: 0,
                stagger: 0.1,
                duration: 0.8,
                scrollTrigger: {
                    trigger: footer,
                    start: 'top 90%',
                    toggleActions: 'play none none none',
                },
            });
        }

        // ── Generic .animate-on-scroll fallback ──────
        document.querySelectorAll('.animate-on-scroll').forEach(function (el) {
            gsap.from(el, {
                y: 50,
                opacity: 0,
                duration: 0.9,
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
            });
        });

        // ── Gold accent line reveals ─────────────────
        document.querySelectorAll('.gold-line').forEach(function (line) {
            gsap.from(line, {
                scaleX: 0,
                duration: 1.2,
                ease: 'power2.inOut',
                scrollTrigger: {
                    trigger: line,
                    start: 'top 90%',
                    toggleActions: 'play none none none',
                },
            });
        });
    }

    /* ------------------------------------------------
     *  Header scroll behavior
     * ------------------------------------------------ */
    function initHeaderBehavior() {
        var header = document.querySelector('.site-header');
        if (!header) return;

        var lastScroll = 0;
        var ticking = false;

        function onScroll() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 80) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            // Hide/show on scroll direction (desktop only)
            if (window.innerWidth > 992) {
                if (scrollTop > lastScroll && scrollTop > 300) {
                    header.classList.add('header-hidden');
                } else {
                    header.classList.remove('header-hidden');
                }
            }

            lastScroll = scrollTop;
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(onScroll);
                ticking = true;
            }
        });

        onScroll();
    }

    /* ------------------------------------------------
     *  Mobile menu
     * ------------------------------------------------ */
    function initMobileMenu() {
        var toggle = document.querySelector('.mobile-menu-toggle');
        var nav = document.querySelector('.main-navigation');
        if (!toggle || !nav) return;

        toggle.addEventListener('click', function () {
            var expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));
            toggle.classList.toggle('is-active');
            nav.classList.toggle('is-open');
            document.body.classList.toggle('menu-open');
        });

        // Close on escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.remove('is-active');
                nav.classList.remove('is-open');
                document.body.classList.remove('menu-open');
            }
        });
    }

    /* ------------------------------------------------
     *  Search overlay
     * ------------------------------------------------ */
    function initSearchOverlay() {
        var searchToggle = document.querySelector('.header-actions__btn[aria-label*="Search"]');
        var overlay = document.querySelector('.search-overlay');
        if (!searchToggle) return;

        searchToggle.addEventListener('click', function () {
            if (overlay) {
                overlay.classList.toggle('is-open');
                if (overlay.classList.contains('is-open')) {
                    var input = overlay.querySelector('input[type="search"]');
                    if (input) setTimeout(function () { input.focus(); }, 200);
                }
            }
        });

        if (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    overlay.classList.remove('is-open');
                }
            });
        }
    }

    /* ------------------------------------------------
     *  Back to top
     * ------------------------------------------------ */
    function initBackToTop() {
        var btn = document.querySelector('.back-to-top');
        if (!btn) {
            // Create button if not present in PHP
            btn = document.createElement('button');
            btn.className = 'back-to-top';
            btn.setAttribute('aria-label', 'Back to top');
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 15l-6-6-6 6"/></svg>';
            document.body.appendChild(btn);
        }

        var ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    if (window.pageYOffset > 400) {
                        btn.classList.add('is-visible');
                    } else {
                        btn.classList.remove('is-visible');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        });

        btn.addEventListener('click', function () {
            if (typeof ScrollToPlugin !== 'undefined') {
                gsap.to(window, { scrollTo: 0, duration: 1.2, ease: 'power3.inOut' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    /* ------------------------------------------------
     *  Product card interactions
     * ------------------------------------------------ */
    function initProductInteractions() {
        // Add to cart button loading state
        document.querySelectorAll('.add_to_cart_button').forEach(function (button) {
            button.addEventListener('click', function () {
                this.classList.add('loading');
                this.disabled = true;
            });

            if (typeof wc_add_to_cart_params !== 'undefined') {
                document.body.addEventListener('added_to_cart', function () {
                    button.classList.remove('loading');
                    button.disabled = false;
                });
            }
        });

        // Product card image hover with GSAP
        document.querySelectorAll('.product-card').forEach(function (card) {
            var image = card.querySelector('.product-card__image img, .woocommerce-loop-product__image img');
            if (!image) return;

            card.addEventListener('mouseenter', function () {
                gsap.to(image, { scale: 1.06, duration: 0.5, ease: 'power2.out' });
            });

            card.addEventListener('mouseleave', function () {
                gsap.to(image, { scale: 1, duration: 0.5, ease: 'power2.out' });
            });
        });

        // Collection tab switching
        document.querySelectorAll('.collection-tabs__tab').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.collection-tabs__tab').forEach(function (t) {
                    t.classList.remove('is-active');
                });
                this.classList.add('is-active');

                var target = this.dataset.target;
                if (target) {
                    var panels = document.querySelectorAll('.collection-panel');
                    panels.forEach(function (panel) {
                        if (panel.id === target) {
                            panel.classList.add('is-active');
                            gsap.from(panel.querySelectorAll('.product-card'), {
                                y: 40,
                                opacity: 0,
                                stagger: 0.08,
                                duration: 0.6,
                            });
                        } else {
                            panel.classList.remove('is-active');
                        }
                    });
                }
            });
        });
    }

    /* ------------------------------------------------
     *  Smooth anchor scrolling
     * ------------------------------------------------ */
    function initSmoothAnchors() {
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var href = this.getAttribute('href');
                if (href === '#' || href.length < 2) return;

                var target = document.querySelector(href);
                if (!target) return;

                e.preventDefault();

                if (typeof ScrollToPlugin !== 'undefined') {
                    gsap.to(window, {
                        scrollTo: { y: target, offsetY: 80 },
                        duration: 1,
                        ease: 'power3.inOut',
                    });
                } else {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                // Close mobile menu if open
                var nav = document.querySelector('.main-navigation');
                var toggle = document.querySelector('.mobile-menu-toggle');
                if (nav && nav.classList.contains('is-open')) {
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.classList.remove('is-active');
                    nav.classList.remove('is-open');
                    document.body.classList.remove('menu-open');
                }
            });
        });
    }
})();

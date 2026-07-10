(function () {
    'use strict';

    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    var docEl = document.documentElement;
    if (docEl.classList.contains('op-animations-disabled')) return;

    var cfg = window.OpulentiaAnim || {};
    var ease = cfg.ease || 'power3.out';
    var globalDuration = cfg.duration || 1;

    gsap.registerPlugin(ScrollTrigger);

    document.addEventListener('DOMContentLoaded', function () {
        if (cfg.reveal && cfg.reveal.enable) {
            initScrollReveal();
        }
        if (cfg.parallax && cfg.parallax.enable) {
            initParallax();
        }
        if (cfg.stagger && cfg.stagger.enable) {
            initStagger();
        }
        if (cfg.counter && cfg.counter.enable) {
            initCounters();
        }
        if (cfg.text && cfg.text.enable) {
            initTextSplit();
        }
    });

    /* ------------------------------------------------
     *  Scroll Reveal
     * ------------------------------------------------ */
    function initScrollReveal() {
        var els = document.querySelectorAll('[data-op-reveal]');
        if (!els.length) return;

        var r = cfg.reveal;

        els.forEach(function (el) {
            var effect = el.getAttribute('data-op-reveal') || r.effect;
            var dir = el.getAttribute('data-op-direction') || r.direction;
            var dist = parseInt(el.getAttribute('data-op-distance')) || r.distance;
            var dur = parseFloat(el.getAttribute('data-op-duration')) || r.duration;
            var del = parseFloat(el.getAttribute('data-op-delay')) || r.delay;
            var trigger = el.getAttribute('data-op-trigger') || r.trigger;

            var vars = { opacity: 0, ease: ease, duration: dur, delay: del };
            var fromVars = { ease: ease, duration: dur, delay: del, opacity: 1 };

            if (effect === 'slide' || effect === 'fade') {
                if (dir === 'up') { vars.y = dist; fromVars.y = 0; }
                else if (dir === 'down') { vars.y = -dist; fromVars.y = 0; }
                else if (dir === 'left') { vars.x = dist; fromVars.x = 0; }
                else if (dir === 'right') { vars.x = -dist; fromVars.x = 0; }
            }

            if (effect === 'fade') {
                delete vars.y;
                delete vars.x;
            }

            if (effect === 'zoom') {
                vars.scale = 0.8;
                fromVars.scale = 1;
            }

            if (effect === 'rotate') {
                vars.rotation = dir === 'left' ? -15 : dir === 'right' ? 15 : dir === 'down' ? 15 : -15;
                fromVars.rotation = 0;
            }

            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: trigger,
                    toggleActions: 'play none none none',
                },
                duration: dur,
                delay: del,
                ease: ease,
                opacity: 0,
                y: vars.y || 0,
                x: vars.x || 0,
                scale: vars.scale || 1,
                rotation: vars.rotation || 0,
                onComplete: function () {
                    el.classList.add('op-reveal-visible');
                },
            });
        });
    }

    /* ------------------------------------------------
     *  Parallax
     * ------------------------------------------------ */
    function initParallax() {
        var els = document.querySelectorAll('[data-op-parallax]');
        if (!els.length) return;

        var p = cfg.parallax;

        els.forEach(function (el) {
            var speed = parseFloat(el.getAttribute('data-op-parallax-speed')) || p.speed;
            var dir = el.getAttribute('data-op-parallax-direction') || p.direction;

            var movement = dir === 'down' ? speed * 200 : speed * -200;

            gsap.to(el, {
                y: movement,
                ease: 'none',
                scrollTrigger: {
                    trigger: el,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: 1.5,
                },
            });
        });
    }

    /* ------------------------------------------------
     *  Stagger
     * ------------------------------------------------ */
    function initStagger() {
        var containers = document.querySelectorAll('[data-op-stagger]');
        if (!containers.length) return;

        var s = cfg.stagger;

        containers.forEach(function (container) {
            var items = container.children;
            if (!items.length) return;

            var delay = parseFloat(container.getAttribute('data-op-stagger-delay')) || s.delay;
            var axis = container.getAttribute('data-op-stagger-axis') || s.axis;

            var fromVars = { opacity: 0, stagger: delay, duration: 0.7, ease: ease };
            if (axis === 'y') fromVars.y = 40;
            else fromVars.x = 40;

            gsap.from(items, {
                scrollTrigger: {
                    trigger: container,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
                opacity: 0,
                y: axis === 'y' ? 40 : 0,
                x: axis === 'x' ? 40 : 0,
                stagger: delay,
                duration: 0.7,
                ease: ease,
            });
        });
    }

    /* ------------------------------------------------
     *  Counter Animation
     * ------------------------------------------------ */
    function initCounters() {
        var counters = document.querySelectorAll('[data-op-counter]');
        if (!counters.length) return;

        var c = cfg.counter;

        counters.forEach(function (counter) {
            var valueEl = counter.querySelector('[data-op-counter-value]');
            if (!valueEl) return;

            var target = parseInt(counter.getAttribute('data-op-counter')) || 100;
            var duration = parseInt(counter.getAttribute('data-op-counter-duration')) || c.duration;
            var animated = false;

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && !animated) {
                        animated = true;
                        animateValue(valueEl, target, duration);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(counter);
        });
    }

    function animateValue(el, target, duration) {
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.round(eased * target);
            el.textContent = current.toLocaleString();

            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                el.textContent = target.toLocaleString();
            }
        }

        window.requestAnimationFrame(step);
    }

    /* ------------------------------------------------
     *  Text Split
     * ------------------------------------------------ */
    function initTextSplit() {
        var els = document.querySelectorAll('[data-op-text-split]');
        if (!els.length) return;

        var t = cfg.text;

        els.forEach(function (el) {
            var type = el.getAttribute('data-op-text-split') || t.type;
            var text = el.textContent.trim();
            if (!text) return;

            el.textContent = '';
            el.style.opacity = '1';

            if (type === 'words') {
                var words = text.split(/\s+/);
                words.forEach(function (word, i) {
                    var span = document.createElement('span');
                    span.className = 'op-text-split__word';
                    span.textContent = word;
                    el.appendChild(span);
                    if (i < words.length - 1) {
                        el.appendChild(document.createTextNode(' '));
                    }
                });
            } else {
                var chars = text.split('');
                chars.forEach(function (char) {
                    var span = document.createElement('span');
                    span.className = 'op-text-split__char';
                    span.textContent = char === ' ' ? '\u00A0' : char;
                    el.appendChild(span);
                });
            }

            var items = el.querySelectorAll('.op-text-split__word, .op-text-split__char');
            gsap.from(items, {
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
                y: 40,
                opacity: 0,
                stagger: type === 'words' ? 0.06 : 0.02,
                duration: 0.7,
                ease: ease,
            });
        });
    }
})();

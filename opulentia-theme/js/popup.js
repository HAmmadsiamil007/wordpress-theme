(function () {
    'use strict';

    var data = window.OpulentiaPopup || {};
    if (!data.enable) return;

    var popup = document.getElementById('op-popup');
    var overlay = document.getElementById('op-popup-overlay');
    var closeBtn = document.getElementById('op-popup-close');
    if (!popup) return;

    var frequency = data.frequency || 'session';
    var cookieName = 'op_popup_seen';

    function getCookie(name) {
        var match = document.cookie.match('(?:^|;)\\s*' + name + '=([^;]*)');
        return match ? decodeURIComponent(match[1]) : '';
    }

    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + days * 86400000);
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/';
    }

    function hasReachedFrequency() {
        if (frequency === 'always') return false;
        if (frequency === 'session') {
            return sessionStorage.getItem(cookieName) === '1';
        }
        if (frequency === 'day') {
            return getCookie(cookieName + '_day') === '1';
        }
        if (frequency === 'week') {
            return getCookie(cookieName + '_week') === '1';
        }
        return false;
    }

    function markSeen() {
        if (frequency === 'session') {
            sessionStorage.setItem(cookieName, '1');
        } else if (frequency === 'day') {
            setCookie(cookieName + '_day', '1', 1);
        } else if (frequency === 'week') {
            setCookie(cookieName + '_week', '1', 7);
        }
    }

    function meetsConditions() {
        if (data.showOn && data.showOn !== 'all') {
            var bodyClass = document.body.className;
            if (data.showOn === 'home' && !bodyClass.match(/(^|\s)home(\s|$)/) && !bodyClass.match(/(^|\s)front-page(\s|$)/)) return false;
            if (data.showOn === 'pages' && !bodyClass.match(/(^|\s)page(\s|$)/)) return false;
            if (data.showOn === 'posts' && !bodyClass.match(/(^|\s)post(\s|$)/) && !bodyClass.match(/(^|\s)single-post(\s|$)/)) return false;
            if (data.showOn === 'products' && !bodyClass.match(/(^|\s)woocommerce(\s|$)/) && !bodyClass.match(/(^|\s)product(\s|$)/)) return false;
        }

        if (data.devices && data.devices !== 'all') {
            var isMobile = window.innerWidth <= 768;
            if (data.devices === 'desktop' && isMobile) return false;
            if (data.devices === 'mobile' && !isMobile) return false;
        }

        if (data.userRoles && data.userRoles !== 'all') {
            if (data.userRoles === 'logged-in' && !data.isLoggedIn) return false;
            if (data.userRoles === 'logged-out' && data.isLoggedIn) return false;
        }

        return true;
    }

    function showPopup() {
        if (popup.classList.contains('is-visible')) return;

        popup.style.display = '';
        if (overlay) overlay.style.display = '';

        var type = data.type || 'modal';

        if (type === 'notification') {
            gsap.fromTo(popup, { y: -100, opacity: 0 }, { y: 0, opacity: 1, duration: 0.5, ease: 'power3.out' });
        } else if (type === 'slide-in') {
            gsap.fromTo(popup, { x: 100, opacity: 0 }, { x: 0, opacity: 1, duration: 0.5, ease: 'power3.out' });
        } else if (type === 'fullscreen') {
            gsap.fromTo(popup, { opacity: 0, scale: 0.95 }, { opacity: 1, scale: 1, duration: 0.4, ease: 'power2.out' });
            if (overlay) gsap.fromTo(overlay, { opacity: 0 }, { opacity: 1, duration: 0.3 });
        } else {
            gsap.fromTo(popup, { opacity: 0, scale: 0.9, y: 30 }, { opacity: 1, scale: 1, y: 0, duration: 0.4, ease: 'power3.out' });
            if (overlay) gsap.fromTo(overlay, { opacity: 0 }, { opacity: 1, duration: 0.3 });
        }

        popup.classList.add('is-visible');
        if (overlay) overlay.classList.add('is-visible');
        markSeen();
    }

    function hidePopup() {
        if (!popup.classList.contains('is-visible')) return;

        gsap.to(popup, { opacity: 0, scale: 0.95, y: -20, duration: 0.3, ease: 'power2.in', onComplete: function () {
            popup.style.display = 'none';
            popup.classList.remove('is-visible');
            if (overlay) {
                overlay.style.display = 'none';
                overlay.classList.remove('is-visible');
            }
        } });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', hidePopup);
    }

    if (overlay) {
        overlay.addEventListener('click', hidePopup);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') hidePopup();
    });

    if (!meetsConditions()) return;
    if (hasReachedFrequency()) return;

    var trigger = data.trigger || 'time';

    if (trigger === 'time') {
        var delay = (data.delay || 3) * 1000;
        setTimeout(showPopup, delay);
    } else if (trigger === 'scroll') {
        var scrollPercent = (data.scrollPercent || 50) / 100;
        var fired = false;
        window.addEventListener('scroll', function () {
            if (fired) return;
            var scrollTop = window.scrollY;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var percent = docHeight > 0 ? scrollTop / docHeight : 1;
            if (percent >= scrollPercent) {
                fired = true;
                showPopup();
            }
        }, { passive: true });
    } else if (trigger === 'exit') {
        var exitFired = false;
        document.addEventListener('mouseleave', function (e) {
            if (exitFired) return;
            if (e.clientY <= 0) {
                exitFired = true;
                showPopup();
            }
        });
    } else if (trigger === 'click' && data.clickSelector) {
        var els = document.querySelectorAll(data.clickSelector);
        for (var i = 0; i < els.length; i++) {
            els[i].addEventListener('click', function (e) {
                e.preventDefault();
                showPopup();
            });
        }
    }
})();

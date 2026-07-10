(function () {
  'use strict';

  var COOKIE_NAME = 'Opulentia_gdpr_consent';

  function getCookie(name) {
    var match = document.cookie.match(new RegExp('(?:^|;)\\s*' + name + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
  }

  function setCookie(value) {
    var days = OpulentiaGDPR.lifespan || 365;
    var date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = COOKIE_NAME + '=' + encodeURIComponent(value) + ';expires=' + date.toUTCString() + ';path=/;SameSite=Lax';
  }

  function hasConsented() {
    return !!getCookie(COOKIE_NAME);
  }

  function showBar() {
    var bar = document.getElementById('op-gdpr-bar');
    if (bar) {
      bar.classList.add('op-gdpr-bar--visible');
    }
  }

  function hideBar() {
    var bar = document.getElementById('op-gdpr-bar');
    if (bar) {
      bar.classList.remove('op-gdpr-bar--visible');
    }
  }

  function getPreferences() {
    var cats = document.querySelectorAll('.op-gdpr-bar__preferences input[type="checkbox"]');
    var prefs = {};
    cats.forEach(function (cb) {
      prefs[cb.name.replace('gdpr_', '')] = cb.checked;
    });
    return prefs;
  }

  function saveConsent(consent) {
    var data = new URLSearchParams();
    data.set('action', 'Opulentia_save_consent');
    data.set('nonce', OpulentiaGDPR.nonce);
    data.set('consent', JSON.stringify(consent));

    fetch(OpulentiaGDPR.ajaxUrl, {
      method: 'POST',
      body: data
    }).then(function (r) { return r.json(); }).then(function (res) {
      if (res.success) {
        setCookie(JSON.stringify(res.data));
        hideBar();
      }
    });
  }

  if (hasConsented()) {
    return;
  }

  document.addEventListener('DOMContentLoaded', function () {
    showBar();

    document.querySelectorAll('[data-action="accept"]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var all = {};
        Object.keys(OpulentiaGDPR.categories).forEach(function (k) { all[k] = true; });
        saveConsent(all);
      });
    });

    document.querySelectorAll('[data-action="customize"]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var prefs = document.getElementById('op-gdpr-preferences');
        if (prefs) {
          prefs.style.display = prefs.style.display === 'none' ? 'block' : 'none';
        }
      });
    });

    document.querySelectorAll('[data-action="save"]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        saveConsent(getPreferences());
      });
    });
  });
})();

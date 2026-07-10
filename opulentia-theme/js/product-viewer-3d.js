(function () {
    'use strict';

    var d = window.Opulentia3D || {};
    if (!d.modelUrl) return;

    var placement = d.placement || 'replace';

    if (placement === 'lightbox') {
        var trigger = document.getElementById('op-3d-lightbox-trigger');
        if (!trigger) return;

        var lb = document.createElement('div');
        lb.className = 'op-3d-viewer__lightbox';
        lb.innerHTML = '<button class="op-3d-viewer__lightbox-close">&times;</button><model-viewer id="op-model-lb" src="' + d.modelUrl + '" auto-rotate="' + d.autoRotate + '" camera-controls shadow-intensity="1"></model-viewer>';
        document.body.appendChild(lb);

        var m = lb.querySelector('model-viewer');
        m.setAttribute('camera-orbit', '0deg 75deg ' + d.zoomMax + 'm');
        m.setAttribute('min-camera-orbit', 'auto auto ' + d.zoomMin + 'm');
        m.setAttribute('max-camera-orbit', 'auto auto ' + d.zoomMax + 'm');
        m.setAttribute('interpolation-decay', '200');
        m.setAttribute('environment-image', 'neutral');

        trigger.addEventListener('click', function () {
            lb.classList.add('is-open');
        });

        lb.querySelector('.op-3d-viewer__lightbox-close').addEventListener('click', function () {
            lb.classList.remove('is-open');
        });

        lb.addEventListener('click', function (e) {
            if (e.target === lb) lb.classList.remove('is-open');
        });

        return;
    }

    var viewer = document.querySelector('.op-3d-viewer model-viewer');
    if (!viewer) return;

    viewer.setAttribute('src', d.modelUrl);
    viewer.setAttribute('camera-controls', '');
    viewer.setAttribute('shadow-intensity', '1');
    viewer.setAttribute('environment-image', 'neutral');

    if (d.autoRotate) {
        viewer.setAttribute('auto-rotate', '');
        viewer.setAttribute('rotation-per-second', d.rotateSpeed + 'deg');
    }

    viewer.setAttribute('min-camera-orbit', 'auto auto ' + d.zoomMin + 'm');
    viewer.setAttribute('max-camera-orbit', 'auto auto ' + d.zoomMax + 'm');
    viewer.setAttribute('interpolation-decay', '200');

    if (d.bgColor) {
        viewer.style.backgroundColor = d.bgColor;
    }
})();

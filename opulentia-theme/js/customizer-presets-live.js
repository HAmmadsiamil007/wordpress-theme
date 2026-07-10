(function ($, api) {
    'use strict';

    api.bind('preview-ready', function () {
        api.preview.bind('opulentia-apply-preset', function (settings) {
            for (var key in settings) {
                if (settings.hasOwnProperty(key)) {
                    api(key).set(settings[key]);
                }
            }
        });
    });

})(jQuery, wp.customize);

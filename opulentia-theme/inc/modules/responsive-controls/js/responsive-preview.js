(function ($) {
  'use strict';

  wp.customize.bind('preview-ready', function () {
    var devices = ['tablet', 'mobile'];

    devices.forEach(function (device) {
      ['body_size', 'h1_size', 'h2_size', 'h3_size', 'content_width', 'heading_scale'].forEach(function (setting) {
        wp.customize('op_responsive_' + device + '_' + setting, function (value) {
          value.bind(function () {
            wp.customize.preview.send('refresh');
          });
        });
      });
    });
  });
})(jQuery);

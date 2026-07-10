(function ($, api) {
    'use strict';

    var data = window.OpulentiaPresets || {};

    api.bind('ready', function () {
        var applySelect = $('#customize-control-op_preset_apply select');
        var saveName = $('#customize-control-op_preset_save_name input');

        if (applySelect.length) {
            var container = applySelect.closest('li');
            container.append(
                '<p style="margin-top:10px">' +
                '<button class="button button-primary" id="op-preset-apply-btn" style="width:100%">' +
                'Apply Preset' +
                '</button>' +
                '</p>'
            );

            var preview = container.find('.preset-preview');
            if (!preview.length) {
                container.append(
                    '<div class="preset-preview" style="margin-top:10px;padding:10px;background:#f0f0f1;border-radius:4px;font-size:12px;display:none"></div>'
                );
                preview = container.find('.preset-preview');
            }

            applySelect.on('change', function () {
                var slug = $(this).val();
                if (slug && data.presets[slug]) {
                    preview.html('<strong>' + data.presets[slug].name + '</strong><br>' + data.presets[slug].description).show();
                } else {
                    preview.hide();
                }
            });

            $('#op-preset-apply-btn').on('click', function () {
                var slug = applySelect.val();
                if (!slug) { return; }
                var btn = $(this);
                btn.text('Applying...').prop('disabled', true);

                $.post(data.ajaxUrl, {
                    action: 'opulentia_apply_preset',
                    preset: slug,
                    nonce: data.nonce
                }, function (response) {
                    if (response.success) {
                        wp.customize.previewer.save();
                        alert(response.data.message);
                    } else {
                        alert(response.data.message || 'Error applying preset.');
                    }
                    btn.text('Apply Preset').prop('disabled', false);
                });
            });
        }

        if (saveName.length) {
            var saveContainer = saveName.closest('li');
            saveContainer.append(
                '<p style="margin-top:10px">' +
                '<button class="button button-secondary" id="op-preset-save-btn" style="width:100%">' +
                'Save Current Settings as Preset' +
                '</button>' +
                '</p>' +
                '<p style="margin-top:10px">' +
                '<button class="button button-secondary" id="op-preset-export-btn" style="width:100%">' +
                'Export Presets' +
                '</button>' +
                '</p>'
            );

            $('#op-preset-save-btn').on('click', function () {
                var name = saveName.val();
                if (!name) { alert('Please enter a preset name.'); return; }
                var btn = $(this);
                btn.text('Saving...').prop('disabled', true);

                $.post(data.ajaxUrl, {
                    action: 'opulentia_save_preset',
                    name: name,
                    nonce: data.nonce
                }, function (response) {
                    if (response.success) {
                        alert(response.data.message);
                        saveName.val('');
                    } else {
                        alert(response.data.message || 'Error saving preset.');
                    }
                    btn.text('Save Current Settings as Preset').prop('disabled', false);
                });
            });

            $('#op-preset-export-btn').on('click', function () {
                var btn = $(this);
                btn.text('Exporting...').prop('disabled', true);

                $.post(data.ajaxUrl, {
                    action: 'opulentia_export_presets',
                    nonce: data.nonce
                }, function (response) {
                    if (response.success) {
                        var blob = new Blob([response.data.data], { type: 'application/json' });
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = response.data.filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    } else {
                        alert(response.data.message || 'Error exporting presets.');
                    }
                    btn.text('Export Presets').prop('disabled', false);
                });
            });
        }
    });

})(jQuery, wp.customize);

(function ($, api) {
    'use strict';

    var d = window.OpulentiaColorPalette || {};

    api.bind('ready', function () {
        var section = api.section('op_color_palette');
        if (!section) return;

        var container = section.container;
        if (!container.length) return;

        container.find('.customize-section-content').append(
            '<div style="padding:10px 15px">' +
            '<button class="button button-primary" id="op-palette-generate" style="width:100%;margin-bottom:10px">Generate Palette</button>' +
            '<div id="op-palette-result" style="display:none"></div>' +
            '<div style="margin-top:10px">' +
            '<label><strong>Extract from Image</strong></label>' +
            '<input type="file" id="op-palette-image" accept="image/*" style="width:100%;margin-top:5px">' +
            '</div>' +
            '<div id="op-palette-image-result" style="margin-top:10px;display:none"></div>' +
            '<hr style="margin:15px 0">' +
            '<label><strong>WCAG Contrast Checker</strong></label>' +
            '<div style="display:flex;gap:5px;margin-top:5px">' +
            '<input type="text" id="op-contrast-fg" placeholder="Foreground #hex" style="width:48%">' +
            '<input type="text" id="op-contrast-bg" placeholder="Background #hex" style="width:48%">' +
            '</div>' +
            '<button class="button" id="op-contrast-check" style="width:100%;margin-top:5px">Check Contrast</button>' +
            '<div id="op-contrast-result" style="margin-top:5px;display:none"></div>' +
            '</div>'
        );

        $('#op-palette-generate').on('click', function () {
            var baseColor = api('op_palette_base').get();
            var harmony = api('op_palette_harmony').get();
            var btn = $(this);
            btn.text('Generating...').prop('disabled', true);

            $.post(d.ajaxUrl, {
                action: 'opulentia_generate_palette',
                base_color: baseColor,
                harmony: harmony,
                nonce: d.nonce
            }, function (response) {
                if (response.success) {
                    var colors = response.data.palette;
                    var html = '<div style="margin-top:10px"><strong>Generated Palette</strong></div><div style="display:flex;gap:5px;margin-top:5px;flex-wrap:wrap">';
                    for (var i = 0; i < colors.length; i++) {
                        html += '<div style="width:50px;height:50px;border-radius:4px;background:' + colors[i] + ';border:1px solid #ccc;cursor:pointer" title="' + colors[i] + '" data-color="' + colors[i] + '"></div>';
                    }
                    html += '</div><button class="button" id="op-palette-apply" style="width:100%;margin-top:8px">Apply Palette</button>';
                    $('#op-palette-result').html(html).show();
                }
                btn.text('Generate Palette').prop('disabled', false);
            });
        });

        $(document).on('click', '#op-palette-apply', function () {
            var colors = [];
            $('#op-palette-result [data-color]').each(function () {
                colors.push($(this).data('color'));
            });
            $.post(d.ajaxUrl, {
                action: 'opulentia_apply_palette',
                colors: JSON.stringify(colors),
                nonce: d.nonce
            }, function (response) {
                if (response.success) {
                    alert(response.data.message);
                }
            });
        });

        document.getElementById('op-palette-image').addEventListener('change', function (e) {
            if (!e.target.files.length) return;
            var file = e.target.files[0];
            var formData = new FormData();
            formData.append('action', 'opulentia_extract_image_colors');
            formData.append('nonce', d.nonce);
            formData.append('image', file);

            $('#op-palette-image-result').html('<p>Extracting colors...</p>').show();

            $.ajax({
                url: d.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        var colors = response.data.colors;
                        var html = '<strong>Extracted Colors</strong><div style="display:flex;gap:5px;margin-top:5px;flex-wrap:wrap">';
                        for (var i = 0; i < colors.length; i++) {
                            html += '<div style="width:50px;height:50px;border-radius:4px;background:' + colors[i] + ';border:1px solid #ccc" title="' + colors[i] + '"></div>';
                        }
                        html += '</div>';
                        $('#op-palette-image-result').html(html);
                    } else {
                        $('#op-palette-image-result').html('<p style="color:#d63638">' + (response.data.message || 'Error extracting colors.') + '</p>');
                    }
                },
                error: function () {
                    $('#op-palette-image-result').html('<p style="color:#d63638">Error extracting colors.</p>');
                }
            });
        });

        $('#op-contrast-check').on('click', function () {
            var fg = $('#op-contrast-fg').val();
            var bg = $('#op-contrast-bg').val();
            if (!fg || !bg) { alert('Enter both foreground and background colors.'); return; }

            $.post(d.ajaxUrl, {
                action: 'opulentia_check_contrast',
                foreground: fg,
                background: bg,
                nonce: d.nonce
            }, function (response) {
                if (response.success) {
                    var r = response.data;
                    var html = '<strong>Ratio:</strong> ' + r.ratio + ':1<br>' +
                        '<strong>AA:</strong> ' + (r.aa ? '✅ Pass' : '❌ Fail') + ' (4.5:1)<br>' +
                        '<strong>AAA:</strong> ' + (r.aaa ? '✅ Pass' : '❌ Fail') + ' (7:1)<br>' +
                        '<strong>AA Large:</strong> ' + (r.a18 ? '✅ Pass' : '❌ Fail') + ' (3:1)';
                    $('#op-contrast-result').html(html).show();
                }
            });
        });
    });

})(jQuery, wp.customize);

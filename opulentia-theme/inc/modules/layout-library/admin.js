(function ($) {
    'use strict';

    var i18n = OpLayoutLib.i18n;

    /* ── Category/Industry Filter ── */
    $('#op-filter-category, #op-filter-industry').on('change', function () {
        var cat = $('#op-filter-category').val();
        var ind = $('#op-filter-industry').val();

        $('#op-layout-grid .op-layout-card').each(function () {
            var card = $(this);
            var match = true;

            if (cat && card.data('category') !== cat) {
                match = false;
            }
            if (ind && card.data('industry') !== ind) {
                match = false;
            }

            card.toggleClass('hidden', !match);
        });
    });

    /* ── Import Layout ── */
    $('#op-layout-grid').on('click', '.op-import-layout', function () {
        var btn = $(this);
        var slug = btn.data('slug');

        if (!confirm(i18n.importConfirm)) return;

        btn.prop('disabled', true).text('Importing...');

        $.post(OpLayoutLib.ajaxUrl, {
            action: 'op_import_layout',
            slug: slug,
            nonce: OpLayoutLib.nonce
        }, function (res) {
            if (res.success) {
                alert(i18n.importSuccess);
                if (res.data.edit_url) {
                    window.open(res.data.edit_url, '_blank');
                }
            } else {
                alert(res.data.message || i18n.importError);
            }
        }).fail(function () {
            alert(i18n.importError);
        }).always(function () {
            btn.prop('disabled', false).text(i18n.import);
        });
    });

    /* ── Preview Modal ── */
    $('#op-layout-grid').on('click', '.op-preview-layout', function () {
        var slug = $(this).data('slug');
        var card = $(this).closest('.op-layout-card');
        var html = card.find('.op-layout-card__preview').html();

        $('#op-preview-modal .op-modal__body').html(
            '<h2>' + card.find('h3').text() + '</h2>' +
            '<div class="op-preview-content">' + html + '</div>' +
            '<p>' + card.find('p').text() + '</p>'
        );
        $('#op-preview-modal').show();
    });

    $('.op-modal__close, .op-modal__backdrop').on('click', function () {
        $('#op-preview-modal').hide();
    });

    /* ── Export Custom Layout ── */
    $('#op-export-layout').on('click', function () {
        var name = prompt('Layout name:');
        if (!name) return;

        var content = prompt('Paste block editor HTML content:');
        if (!content) return;

        $.post(OpLayoutLib.ajaxUrl, {
            action: 'op_export_layout',
            name: name,
            content: content,
            nonce: OpLayoutLib.nonce
        }, function (res) {
            if (res.success && res.data.json) {
                var blob = new Blob([res.data.json], { type: 'application/json' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = name.toLowerCase().replace(/\s+/g, '-') + '.json';
                a.click();
                URL.revokeObjectURL(url);
                alert(i18n.exportSuccess);
            } else {
                alert(res.data.message || i18n.exportError);
            }
        });
    });

    /* ── Import JSON File ── */
    $('#op-import-file').on('change', function () {
        var file = this.files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function (e) {
            try {
                var data = JSON.parse(e.target.result);
                if (!data.content) {
                    alert('Invalid layout file: missing content.');
                    return;
                }

                $.post(OpLayoutLib.ajaxUrl, {
                    action: 'op_import_layout',
                    slug: data.slug || 'custom',
                    content: data.content,
                    name: data.name || 'Imported Layout',
                    nonce: OpLayoutLib.nonce
                }, function (res) {
                    if (res.success) {
                        alert(i18n.importSuccess);
                    } else {
                        alert(res.data.message || i18n.importError);
                    }
                });
            } catch (err) {
                alert('Invalid JSON file.');
            }
        };
        reader.readAsText(file);
        this.value = '';
    });
})(jQuery);

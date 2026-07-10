(function ($) {
    'use strict';

    var data = window.OpulentiaPerf || {};

    $('#op-perf-scan-btn').on('click', function () {
        var btn = $(this);
        btn.text('Scanning...').prop('disabled', true);
        $('#op-perf-scan-results').html('');

        $.post(data.ajaxUrl, {
            action: 'opulentia_performance_scan',
            nonce: data.nonce
        }, function (response) {
            if (response.success) {
                var r = response.data;
                $('#op-perf-scan-results').html(
                    '<div class="notice notice-success inline" style="margin:0">' +
                    '<p><strong>' + r.module_count + '</strong> modules — total asset weight: <strong>' + r.total_size + '</strong>. Largest: <strong>' + r.largest + '</strong>.</p>' +
                    '</div>'
                );
            } else {
                $('#op-perf-scan-results').html(
                    '<div class="notice notice-error inline" style="margin:0"><p>' + (response.data && response.data.message ? response.data.message : 'Scan failed.') + '</p></div>'
                );
            }
            btn.text('Run Performance Scan').prop('disabled', false);
        });
    });

    $('#op-perf-pageSpeed-btn').on('click', function () {
        var btn = $(this);
        btn.text('Checking...').prop('disabled', true);

        $.post(data.ajaxUrl, {
            action: 'opulentia_pageSpeed_check',
            nonce: data.nonce
        }, function (response) {
            if (response.success) {
                var score = response.data.score;
                var label = response.data.score_label;
                $('#op-perf-lighthouse').text(score).css('color', score >= 90 ? '#46b450' : (score >= 50 ? '#f0b849' : '#d63638'));
                $('#op-perf-pageSpeed-btn').after(
                    '<p style="margin-top:10px">Score: <strong>' + score + '/100</strong> (' + label + ') — <a href="https://pagespeed.web.dev/report?url=' + encodeURIComponent(response.data.url) + '" target="_blank">Full Report</a></p>'
                );
            } else {
                alert(response.data && response.data.message ? response.data.message : 'PageSpeed check failed.');
            }
            btn.text('Check PageSpeed Score').prop('disabled', false);
        });
    });

})(jQuery);

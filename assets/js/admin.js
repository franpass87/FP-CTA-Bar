(function ($) {
    'use strict';

    $(function () {
        // Color pickers
        $('.fp-cta-bar-color').wpColorPicker();

        // Sortable links
        $('#fp-cta-bar-links').sortable({
            handle: '.fp-cta-bar-link-handle',
            placeholder: 'fp-cta-bar-link-row ui-sortable-placeholder',
            tolerance: 'pointer',
            update: reindexLinks
        });

        // Add link
        $('#fp-cta-bar-add-link').on('click', function () {
            var nextIndex = getNextIndex();
            var tpl = $('#tmpl-fp-cta-bar-link-row').html();
            tpl = tpl.replace(/\{\{INDEX\}\}/g, nextIndex);
            $('#fp-cta-bar-links').append(tpl);
        });

        // Duplicate link
        $(document).on('click', '.fp-cta-bar-link-duplicate', function () {
            var $row = $(this).closest('.fp-cta-bar-link-row');
            var nextIndex = getNextIndex();
            var $clone = $row.clone();
            $clone.attr('data-index', nextIndex);
            $clone.find('input, select').each(function () {
                var $el = $(this);
                var name = $el.attr('name');
                if (name) {
                    $el.attr('name', name.replace(/\[links\]\[\d+\]/, '[links][' + nextIndex + ']'));
                }
            });
            $row.after($clone);
            reindexLinks();
        });

        // Remove link
        $(document).on('click', '.fp-cta-bar-link-remove', function () {
            var msg = (typeof fpCtaBar !== 'undefined' && fpCtaBar.i18n && fpCtaBar.i18n.confirmRemove) ? fpCtaBar.i18n.confirmRemove : '';
            if (msg && !confirm(msg)) {
                return;
            }
            $(this).closest('.fp-cta-bar-link-row').remove();
            reindexLinks();
        });

        // Import
        $('#fp-cta-bar-import').on('click', function () {
            var file = document.getElementById('fp-cta-bar-import-file').files[0];
            if (!file) {
                alert('Seleziona un file JSON.');
                return;
            }
            var formData = new FormData();
            formData.append('action', 'fp_cta_bar_import');
            formData.append('nonce', (fpCtaBar && fpCtaBar.importNonce) ? fpCtaBar.importNonce : '');
            formData.append('file', file);
            $.ajax({
                url: (fpCtaBar && fpCtaBar.ajaxUrl) ? fpCtaBar.ajaxUrl : '',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (r) {
                    if (r.success && r.data && r.data.redirect) {
                        window.location.href = r.data.redirect;
                    } else {
                        alert((fpCtaBar && fpCtaBar.i18n && fpCtaBar.i18n.importError) ? fpCtaBar.i18n.importError : 'Errore');
                    }
                },
                error: function () {
                    alert((fpCtaBar && fpCtaBar.i18n && fpCtaBar.i18n.importError) ? fpCtaBar.i18n.importError : 'Errore');
                }
            });
        });

        // Preview update (debounced)
        var previewTimeout;
        function updatePreview() {
            var opt = 'fp_cta_bar_settings';
            var bg = $('input[name="' + opt + '[bg_color]"]').val() || '#000000';
            var text = $('input[name="' + opt + '[text_color]"]').val() || '#ffffff';
            var labelIt = $('input[name="fp_cta_bar_settings[main_label_it]"]').val() || 'PRENOTA';
            var mode = $('input[name="fp_cta_bar_settings[display_mode]"]:checked').val();
            var $box = $('#fp-cta-bar-preview-box');
            $box.attr('style', '--fpctabar-bg:' + bg + ';--fpctabar-text:' + text + ';--fpctabar-border:' + text + ';--fpctabar-panel-bg:#111;');
            $box.removeClass('fpctabar--fullwidth fpctabar--button fpctabar--left fpctabar--right');
            if (mode === 'button-left' || mode === 'button-right') {
                $box.addClass('fpctabar--button fpctabar--' + (mode === 'button-left' ? 'left' : 'right'));
            } else {
                $box.addClass('fpctabar--fullwidth');
            }
            $('#fp-cta-bar-preview-label').text(labelIt);
        }
        $('input, select').on('change input', function () {
            clearTimeout(previewTimeout);
            previewTimeout = setTimeout(updatePreview, 200);
        });
        updatePreview();

        function getNextIndex() {
            var max = -1;
            $('#fp-cta-bar-links .fp-cta-bar-link-row').each(function () {
                var idx = parseInt($(this).attr('data-index'), 10);
                if (idx > max) max = idx;
            });
            return max + 1;
        }

        function reindexLinks() {
            $('#fp-cta-bar-links .fp-cta-bar-link-row').each(function (i) {
                var $row = $(this);
                $row.attr('data-index', i);
                $row.find('input, select').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[links\]\[\d+\]/, '[links][' + i + ']'));
                    }
                });
            });
        }
    });
})(jQuery);

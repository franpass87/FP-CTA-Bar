(function ($) {
    'use strict';

    $(function () {
        // Color pickers
        $('.fp-cta-bar-color').wpColorPicker();

        // Sortable links
        $('#fp-cta-bar-links').sortable({
            handle: '.fp-cta-bar-link-handle',
            placeholder: 'fpctabar-link-row ui-sortable-placeholder',
            tolerance: 'pointer',
            update: reindexLinks
        });

        // Add link
        $('#fp-cta-bar-add-link').on('click', function () {
            var nextIndex = getNextIndex();
            var tpl = $('#tmpl-fp-cta-bar-link-row').html();
            tpl = tpl.replace(/\{\{INDEX\}\}/g, nextIndex);
            $('#fp-cta-bar-links').append(tpl);
            refreshIconPickers($('#fp-cta-bar-links .fp-cta-bar-link-row').last());
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
            refreshIconPickers($clone);
            reindexLinks();
        });
        $(document).on('click', '.fp-cta-bar-icon-trigger', function (e) {
            e.preventDefault();
            var $picker = $(this).closest('.fpctabar-icon-picker');
            var $grid = $picker.find('.fp-cta-bar-icon-grid').first();
            var isOpen = !($grid.prop('hidden'));
            closeAllIconPickers();
            if (!isOpen) {
                $grid.prop('hidden', false);
                $(this).attr('aria-expanded', 'true');
                $picker.addClass('is-open');
                $picker.closest('.fpctabar-link-row').addClass('fpctabar-link-row--icon-open');
                $picker.closest('.fpctabar-field').addClass('fpctabar-field--icon-open');
            }
        });
        $(document).on('click', '.fp-cta-bar-icon-option', function (e) {
            e.preventDefault();
            var $option = $(this);
            var $picker = $option.closest('.fpctabar-icon-picker');
            var $input = $picker.find('.fp-cta-bar-icon-input').first();
            $input.val($option.attr('data-icon') || '');
            syncIconPicker($picker);
            closeAllIconPickers();
            $input.trigger('change');
        });
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.fpctabar-icon-picker').length) {
                closeAllIconPickers();
            }
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
            var panelText = $('input[name="' + opt + '[panel_text_color]"]').val() || text;
            var panelBg = $('input[name="' + opt + '[panel_bg_color]"]').val() || '#111111';
            var border = $('input[name="' + opt + '[border_color]"]').val() || text;
            var labelIt = $('input[name="fp_cta_bar_settings[main_label_it]"]').val() || 'PRENOTA';
            var mode = $('input[name="fp_cta_bar_settings[display_mode]"]:checked').val();
            var $box = $('#fp-cta-bar-preview-box');
            $box.attr('style', '--fpctabar-bg:' + bg + ';--fpctabar-text:' + text + ';--fpctabar-panel-text:' + panelText + ';--fpctabar-border:' + border + ';--fpctabar-panel-bg:' + panelBg + ';');
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
        refreshIconPickers($(document));

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

        function refreshIconPickers($scope) {
            var $pickers = $scope.find('.fp-cta-bar-icon-picker');
            if ($scope.hasClass && $scope.hasClass('fp-cta-bar-icon-picker')) {
                $pickers = $pickers.add($scope);
            }
            $pickers.each(function () {
                syncIconPicker($(this));
            });
        }

        function syncIconPicker($picker) {
            if (!$picker || !$picker.length) {
                return;
            }

            var $input = $picker.find('.fp-cta-bar-icon-input').first();
            var value = (($input.val() || '') + '').trim();
            var $triggerIcon = $picker.find('.fp-cta-bar-icon-trigger-icon').first();
            var $triggerLabel = $picker.find('.fp-cta-bar-icon-trigger-label').first();
            var $options = $picker.find('.fp-cta-bar-icon-option');
            var matched = false;

            $options.removeClass('is-active').each(function () {
                var $option = $(this);
                if (($option.attr('data-icon') || '') === value) {
                    matched = true;
                    $option.addClass('is-active');
                    $triggerLabel.text($option.attr('data-label') || '');
                }
            });

            var presets = (typeof fpCtaBar !== 'undefined' && fpCtaBar.iconPresets) ? fpCtaBar.iconPresets : {};
            $triggerIcon.attr('class', 'fp-cta-bar-icon-trigger-icon');
            $triggerIcon.empty();
            if (presets[value]) {
                if (/^fpctabar-emoji-/.test(value)) {
                    $triggerIcon.html('<span class="fpctabar-admin-icon-emoji" aria-hidden="true">' + presets[value] + '</span>');
                } else {
                    var isBrand = /^fpctabar-/.test(value);
                    var wrapClass = 'fpctabar-admin-icon-svg' + (isBrand ? ' fpctabar-admin-icon-svg--brand' : '');
                    $triggerIcon.html('<span class="' + wrapClass + '" aria-hidden="true">' + presets[value] + '</span>');
                }
            } else if (value && value.indexOf('dashicons') !== -1) {
                $triggerIcon.addClass(value);
            } else if (!value) {
                $triggerIcon.addClass('dashicons dashicons-minus fpctabar-admin-icon-fallback');
            } else {
                $triggerIcon.addClass(value);
            }

            if (!matched) {
                $triggerLabel.text(value ? 'Personalizzata salvata' : 'Nessuna');
                $options.filter('[data-icon=""]').addClass('is-active');
            }
        }

        function closeAllIconPickers() {
            var $pickers = $('.fpctabar-icon-picker');
            if (!$pickers.length) {
                return;
            }
            $pickers.removeClass('is-open');
            $pickers.find('.fp-cta-bar-icon-grid').prop('hidden', true);
            $pickers.find('.fp-cta-bar-icon-trigger').attr('aria-expanded', 'false');
            $('.fpctabar-link-row--icon-open').removeClass('fpctabar-link-row--icon-open');
            $('.fpctabar-field--icon-open').removeClass('fpctabar-field--icon-open');
        }
    });
})(jQuery);

(function ($) {
    'use strict';

    var $panel  = $('#any2html-panel');
    if ( ! $panel.length) return;

    var $input  = $('#any2html-input');
    var $btn    = $('#any2html-convert');
    var $status = $('#any2html-status');

    $('#any2html-md-clear').on('click', function () {
        $input.val('').focus();
        $status.attr('class', '').text('');
    });

    $btn.on('click', function () {
        if ($btn.prop('disabled')) return;

        var markdown = $input.val().trim();
        if ( ! markdown) {
            setStatus('Please enter some Markdown first.', 'error');
            return;
        }

        $btn.prop('disabled', true);
        setStatus('Converting\u2026', 'loading');

        $.post(any2htmlEditor.ajaxUrl, {
            action:      'any2html_convert',
            markdown:    markdown,
            _ajax_nonce: any2htmlEditor.nonce
        })
        .done(function (res) {
            if (res.success) {
                insertHtml(res.data.html);
                setStatus('Inserted into editor.', 'success');
                $input.val('');
            } else if (res.data && res.data.key_invalid) {
                setStatus(
                    res.data.message + ' <a href="' + res.data.settings_url + '">Re-validate key \u2192</a>',
                    'error',
                    true
                );
            } else {
                setStatus('Error: ' + (res.data && res.data.message ? res.data.message : 'Unknown error.'), 'error');
            }
        })
        .fail(function () {
            setStatus('Request failed. Please try again.', 'error');
        })
        .always(function () {
            $btn.prop('disabled', false);
        });
    });

    function insertHtml(html) {
        var elementId = 'content';
        if (document.body.classList.contains('block-editor-page')) {
            var block = wp.blocks.createBlock('core/freeform', { content: html });
            wp.data.dispatch('core/editor').insertBlocks(block);
        } else if (window.tinyMCE && tinyMCE.get(elementId) && ! tinyMCE.get(elementId).isHidden()) {
            var editor      = tinyMCE.get(elementId);
            var placeholder = 'any2html_marker_' + Math.random().toString().replace(/\./g, '');
            editor.selection.setContent(placeholder);
            editor.setContent(editor.getContent().replace(new RegExp(placeholder, 'g'), function () {
                return html;
            }));
        } else {
            var el = document.getElementById(elementId);
            if (el) {
                var start = el.selectionStart;
                el.value  = el.value.substring(0, start) + html + el.value.substring(el.selectionEnd);
            }
        }
    }

    function setStatus(msg, type, isHtml) {
        $status.attr('class', 'any2html-status any2html-status--' + type);
        if (isHtml) {
            $status.html(msg);
        } else {
            $status.text(msg);
        }
    }

}(jQuery));

(function ($) {
    'use strict';

    var $panel = $('#any2html-panel');
    if (!$panel.length) return;

    var $input = $('#any2html-input');
    var $btn = $('#any2html-convert');
    var $status = $('#any2html-status');
    var $progress = $('#a2h-progress');
    var $bar = $('#a2h-progress-bar');
    var $dropzone = $('#any2html-upload-area');
    var $fileInput = $('#any2html-file-input');
    var $fileChip = $('#a2h-file-chip');
    var $fileName = $('#a2h-file-name');
    var $fileSize = $('#a2h-file-size');
    var $tabs = $('.a2h-tab');

    var MAX_BYTES = 5 * 1024 * 1024;
    var activeTab = 'md';
    var selectedFile = null;

    /* ── Tabs ── */
    $tabs.on('click', function () {
        var target = $(this).is('#a2h-tab-file') ? 'file' : 'md';
        switchTab(target);
    });

    function switchTab(tab) {
        activeTab = tab;
        $('#a2h-tab-md').toggleClass('a2h-tab--active', tab === 'md').attr('aria-selected', tab === 'md');
        $('#a2h-tab-file').toggleClass('a2h-tab--active', tab === 'file').attr('aria-selected', tab === 'file');
        $('#a2h-pane-md').prop('hidden', tab !== 'md');
        $('#a2h-pane-file').prop('hidden', tab !== 'file');
        clearStatus();
    }

    /* ── Clear markdown ── */
    $('#any2html-md-clear').on('click', function () {
        $input.val('').focus();
        clearStatus();
    });

    /* ── Dropzone interactions ── */
    $dropzone.on('click', function () { $fileInput.trigger('click'); });

    $dropzone.on('dragover dragenter', function (e) {
        e.preventDefault();
        $dropzone.addClass('a2h-dropzone--hover');
    }).on('dragleave dragend', function () {
        $dropzone.removeClass('a2h-dropzone--hover');
    }).on('drop', function (e) {
        e.preventDefault();
        $dropzone.removeClass('a2h-dropzone--hover');
        var file = e.originalEvent.dataTransfer.files[0];
        if (file) selectFile(file);
    });

    $fileInput.on('change', function () {
        if (this.files[0]) selectFile(this.files[0]);
        this.value = '';
    });

    $('#a2h-file-remove').on('click', function (e) {
        e.stopPropagation();
        clearFile();
    });

    function selectFile(file) {
        var allowed = /^(application\/pdf|image\/.+)$/;
        if (!allowed.test(file.type)) {
            setStatus('Unsupported file type. Please upload a PDF or image.', 'error');
            return;
        }
        if (file.size > MAX_BYTES) {
            setStatus('File is too large. Maximum allowed size is 5 MB.', 'error');
            return;
        }
        selectedFile = file;
        $fileName.text(file.name);
        $fileSize.text('(' + formatBytes(file.size) + ')');
        $fileChip.prop('hidden', false);
        $dropzone.addClass('a2h-dropzone--has-file');
        clearStatus();
    }

    function clearFile() {
        selectedFile = null;
        $fileChip.prop('hidden', true);
        $dropzone.removeClass('a2h-dropzone--has-file');
        clearStatus();
    }

    /* ── Convert button ── */
    $btn.on('click', function () {
        if ($btn.prop('disabled')) return;
        clearStatus();

        if (activeTab === 'file') {
            if (!selectedFile) {
                setStatus('Please select a file first.', 'error');
                return;
            }
            uploadFile(selectedFile);
        } else {
            var markdown = $input.val().trim();
            if (!markdown) {
                setStatus('Please enter some Markdown first.', 'error');
                return;
            }
            convertMarkdown(markdown);
        }
    });

    /* ── Markdown → HTML ── */
    function convertMarkdown(markdown) {
        setBusy('Converting\u2026');
        $.post(any2htmlEditor.ajaxUrl, {
            action: 'any2html_convert',
            markdown: markdown,
            _ajax_nonce: any2htmlEditor.nonce
        })
            .done(function (res) {
                if (res.success) {
                    insertHtml(res.data.html);
                    setStatus('Done \u2014 inserted into editor.', 'success');
                    $input.val('');
                } else {
                    handleError(res);
                }
            })
            .fail(function () { setStatus('Request failed. Please try again.', 'error'); })
            .always(setIdle);
    }

    /* ── File → markdown → HTML ── */
    function uploadFile(file) {
        setBusy('Uploading\u2026');
        showProgress(true);

        var formData = new FormData();
        formData.append('action', 'any2html_file_convert');
        formData.append('_ajax_nonce', any2htmlEditor.fileNonce);
        formData.append('file', file);

        $.ajax({
            url: any2htmlEditor.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        var pct = Math.round((e.loaded / e.total) * 80);
                        $bar.css('width', pct + '%');
                        if (pct >= 80) setStatus('Processing\u2026', 'loading');
                    }
                });
                return xhr;
            }
        })
            .done(function (res) {
                $bar.css('width', '100%');
                if (res.success) {
                    insertHtml(res.data.html);
                    setStatus('Done \u2014 inserted into editor.', 'success');
                    clearFile();
                } else {
                    handleError(res);
                }
            })
            .fail(function () { setStatus('Request failed. Please try again.', 'error'); })
            .always(function () {
                setIdle();
                setTimeout(function () { showProgress(false); }, 600);
            });
    }

    /* ── Insert HTML into editor ── */
    function insertHtml(html) {
        var id = 'content';
        if (document.body.classList.contains('block-editor-page')) {
            var block = wp.blocks.createBlock('core/freeform', { content: html });
            wp.data.dispatch('core/editor').insertBlocks(block);
        } else if (window.tinyMCE && tinyMCE.get(id) && !tinyMCE.get(id).isHidden()) {
            var ed = tinyMCE.get(id);
            var marker = 'a2h_' + Math.random().toString(36).slice(2);
            ed.selection.setContent(marker);
            ed.setContent(ed.getContent().replace(new RegExp(marker, 'g'), function () { return html; }));
        } else {
            var el = document.getElementById(id);
            if (el) {
                var s = el.selectionStart;
                el.value = el.value.substring(0, s) + html + el.value.substring(el.selectionEnd);
            }
        }
    }

    /* ── Helpers ── */
    function setBusy(label) {
        $btn.prop('disabled', true).find('span.a2h-btn-label').text(label);
        setStatus(label, 'loading');
    }

    function setIdle() {
        $btn.prop('disabled', false).find('span.a2h-btn-label').text('Convert to HTML');
    }

    function showProgress(show) {
        $progress.prop('hidden', !show);
        if (!show) $bar.css('width', '0%');
    }

    function setStatus(msg, type, isHtml) {
        $status.attr('class', 'any2html-status any2html-status--' + (type || ''));
        isHtml ? $status.html(msg) : $status.text(msg);
    }

    function clearStatus() {
        $status.attr('class', '').empty();
    }

    function handleError(res) {
        if (res.data && res.data.key_invalid) {
            setStatus(
                res.data.message + ' <a href="' + res.data.settings_url + '">Re-validate \u2192</a>',
                'error', true
            );
        } else {
            setStatus('Error: ' + (res.data && res.data.message ? res.data.message : 'Unknown error.'), 'error');
        }
    }

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

}(jQuery));

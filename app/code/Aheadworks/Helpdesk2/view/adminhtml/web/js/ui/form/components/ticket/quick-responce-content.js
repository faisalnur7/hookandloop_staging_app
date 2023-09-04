define([
    'jquery'
], function ($) {
    'use strict';

    return {
        contentElementSelector: '#aw_helpdesk2_ticket_view_form_content',

        setContent: function(content, wysiwygId = 'aw_helpdesk2_ticket_view_form_content') {
            var tinyEditor = window.tinyMCE.get(wysiwygId);
            if (tinyEditor) {
                tinyEditor.execCommand('mceInsertClipboardContent', false, {
                    content: content
                });
                tinyEditor.focus();
            } else {
                var content = $(this.contentElementSelector);
                content.val(content.val() + content)
            }
        }
    }
});

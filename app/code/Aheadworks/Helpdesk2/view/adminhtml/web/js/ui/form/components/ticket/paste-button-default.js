define([
    'jquery',
    'Magento_Ui/js/form/element/wysiwyg',
    'mage/adminhtml/events',
], function ($, Wysiwyg, varienGlobalEvents) {
    'use strict';

    return Wysiwyg.extend({
        defaults: {
            paste_as_text: true
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            varienGlobalEvents.attachEventHandler('wysiwygEditorInitialized', function () {
                var tinyEditor = window.tinyMCE.get(this.wysiwygId);
                if (tinyEditor) {
                    tinyEditor.execCommand('mceTogglePlainTextPaste', false, {});
                }
            }.bind(this));

            return this;
        },
    });
});

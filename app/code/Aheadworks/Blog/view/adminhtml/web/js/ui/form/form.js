define([
    'Magento_Ui/js/form/form',
    './adapter',
    'Aheadworks_Blog/js/preview/popup/post'
], function (Form, adapter, postPreviewPopup) {
    'use strict';

    return Form.extend({
        /**
         * Initialize adapter
         * @returns {*}
         */
        initAdapter: function () {
            adapter.on({
                'reset': this.reset.bind(this),
                'save': this.save.bind(this, true, 'save'),
                'update': this.save.bind(this, true, 'update'),
                'schedule': this.save.bind(this, true, 'schedule'),
                'publish': this.save.bind(this, true, 'publish'),
                'saveAsDraft': this.save.bind(this, false, 'save_as_draft'),
                'saveAsDraftAndDuplicate': this.save.bind(this, false, 'save_as_draft_and_duplicate'),
                'saveAndContinue': this.save.bind(this, false, 'save_and_continue'),
                'preview': this.preview.bind(this)
            });

            return this;
        },

        /**
         * Save action
         * @param {String} redirect
         * @param {String} action
         */
        save: function (redirect, action) {
            this.source.set('data.action', action);
            this._super(redirect);
        },

        /**
         * Preview action
         */
        preview: function () {
            var postData = this.source.data,
                previewUrl = this.source.get('preview_url');

            postPreviewPopup.create(previewUrl, postData);
        }
    });
});

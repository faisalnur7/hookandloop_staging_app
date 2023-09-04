define([
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/modal/confirm',
    'Aheadworks_Helpdesk2/js/action/ticket/send-request',
    'Aheadworks_Helpdesk2/js/model/backend-message-manager',
    'Aheadworks_Helpdesk2/js/model/block-loader'
], function (Column, confirmation, sendRequest, messageManager, blockLoader) {
    'use strict';

    return Column.extend({
        defaults: {
            modules: {
                target: '${ $.targetElementName }'
            }
        },

        loader: {},

        /**
         * Init block loader
         */
        initLoader: function () {
            this.loader = {};
            blockLoader(this.loader, '[data-index=' + this.index + ']');
        },

        /**
         * Check if button is displayed
         *
         * @param {Object} record
         * @returns {Boolean}
         */
        isButtonDisplayed: function (record) {
            return record['type'] === 'customer-message'
                || record['type'] === 'admin-message';
        },

        /**
         * Delete message
         *
         * @param {Object} record
         */
        deleteMessage: function (record) {
            this.initLoader();

            var self = this,
                reloadComponent = this.reloadComponent || false;

            confirmation({
                title: self.popup.title,
                content: self.popup.content,
                actions: {
                    confirm: function () {
                        self.loader.show();
                        sendRequest(self.requestUrl, record, reloadComponent)
                            .done(function (response) {
                                messageManager.addSuccessMessage(response.message, 5000);
                            })
                            .always(function () {
                                self.loader.hide();
                            });
                    },
                    cancel: function () {
                        return false;
                    }
                }
            });
        },
    });
});

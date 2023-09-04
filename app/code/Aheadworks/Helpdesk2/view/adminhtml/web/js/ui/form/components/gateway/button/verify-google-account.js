define([
    'Aheadworks_Helpdesk2/js/ui/form/components/gateway/button/verify-account'
], function (Button) {
    'use strict';

    return Button.extend({
        defaults: {
            scope: 'https%3A%2F%2Fmail.google.com%2F%20email'
        },

        /**
         * Prepare request URL
         *
         * @param {Object} action
         * @return {String}
         */
        prepareRequestUrl: function (action) {
            return action.url +
                '?response_type=' + this.responseType +
                '&access_type=' + this.accessType +
                '&client_id=' + this.source.get('data.client_id') +
                '&redirect_uri=' + action.redirectUrl +
                '&state' +
                '&scope=' + this.scope +
                '&prompt=' + this.approvalPrompt;
        }
    });
});

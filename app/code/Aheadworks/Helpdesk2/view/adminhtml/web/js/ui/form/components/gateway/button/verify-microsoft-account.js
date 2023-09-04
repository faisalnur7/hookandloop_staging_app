define([
    'Aheadworks_Helpdesk2/js/ui/form/components/gateway/button/verify-account'
], function (Button) {
    'use strict';

    return Button.extend({
        defaults: {
            scope: 'offline_access%20https%3A%2F%2Foutlook.office.com%2FIMAP.AccessAsUser.All',
            responseMode: 'query'
        },

        /**
         * Prepare request URL
         *
         * @param {Object} action
         * @return {String}
         */
        prepareRequestUrl: function (action) {
            return action.url.replace('@tenant_id', this.source.get('data.tenant_id')) +
                '?response_type=' + this.responseType +
                '&client_id=' + this.source.get('data.client_id') +
                '&redirect_uri=' + action.redirectUrl +
                '&scope=' + this.scope +
                '&response_mode=' + this.responseMode +
                '&prompt=' + this.approvalPrompt;
        }
    });
});

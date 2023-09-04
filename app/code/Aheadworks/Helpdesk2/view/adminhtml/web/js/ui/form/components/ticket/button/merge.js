define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/components/button',
    'Aheadworks_Helpdesk2/js/model/compose-payload-from-source',
    'Aheadworks_Helpdesk2/js/action/ticket/send-request',
    'Aheadworks_Helpdesk2/js/model/block-loader',
    'Aheadworks_Helpdesk2/js/model/ticket/merge-confirm'
], function ($, _, registry, Button, composePayload, sendRequest, blockLoader, ticketMergeConfirm) {
    'use strict';

    return Button.extend({
        defaults: {
            blockLoaderSelector: null
        },

        loader: {},

        /**
         * Init
         *
         * @private
         */
        initObservable: function () {
            this._super();
            if (this.blockLoaderSelector) {
                this.initLoader();
            }

            return this;
        },

        /**
         * Merge action for button on view ticket form
         *
         * @param action
         * @private
         */
        applyAction: function (action) {
            var selectTicketsUrl = action.selectTicketsUrl,
                mergeTicketsUrl = action.mergeTicketsUrl,
                payload;

            var checkedTickets = [];
            var checkBoxBlocks = $('.merge-checkbox');
            var mergeCheckBoxes = $('input[name="tickets_merge_checkbox"]');
            var mergeButton = $('[data-index="merge_tickets"]');
            checkBoxBlocks.show();
            mergeButton.html('Merge with the current ticket');
            if (mergeButton.data('ready-to-merge')) {
                mergeCheckBoxes.each(function(i, obj) {
                    var isChecked = $(this).is(":checked");
                    if (isChecked) {
                        checkedTickets.push($(this).data('uid'));
                    }
                });
            }
            mergeButton.data('ready-to-merge', true);
            if (checkedTickets.length) {
                payload = composePayload(action.payload || []);
                ticketMergeConfirm.setProcessUrls(selectTicketsUrl, mergeTicketsUrl);
                ticketMergeConfirm.processTicketsConfirm(
                    payload.uid,
                    payload.entity_id,
                    checkedTickets.join(',')
                );
            }
        },

        /**
         * Clear value after success request
         *
         * @param components
         * @private
         */
        clear: function(components) {
            _.each(components, function (componentName) {
                var component = registry.get(componentName);
                if (component) {
                    component.reset();
                }
            }, this);
        },

        /**
         * Init block loader
         * @private
         */
        initLoader: function () {
            this.loader = {};
            blockLoader(this.loader, this.blockLoaderSelector);
        }
    });
});

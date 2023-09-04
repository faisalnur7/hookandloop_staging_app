define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/components/multiline',
    'uiLayout',
    'Aheadworks_Helpdesk2/js/action/ticket/send-request',
    'Aheadworks_Helpdesk2/js/model/compose-payload-from-source',
    'Aheadworks_Helpdesk2/js/model/block-loader',
    'Aheadworks_Helpdesk2/js/model/backend-message-manager'
], function ($, _, Multiline, layout, sendRequest, composePayload, blockLoader, messageManager) {
    'use strict';

    return Multiline.extend({
        defaults: {
            previewMode: true,
            isEditModeAllowed: true,
            template: 'Aheadworks_Helpdesk2/ui/form/element/ticket/preview/group/multiline',
            requestUrl: '',
            payload: [],
            reloadComponent: false,
            bindAttributeMap: {},
            labelVisible: false,
            label: '',
            isDifferedFromDefault: false,
            showFallbackReset: false,
            additionalClasses: {}
        },

        loader: {},

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super()
                .setInitialValue()
                ._setClasses();

            return this;
        },

        /**
         * Init block loader
         */
        initLoader: function () {
            this.loader = {};
            blockLoader(this.loader, '[data-index=' + this.index + ']');
        },

        /**
         * Enable edit mode
         */
        enableEditMode: function () {
            this.previewMode(false);
        },

        /**
         * Enable preview mode
         */
        enablePreviewMode: function () {
            this.previewMode(true);
        },

        /**
         * Init observable
         * @returns {*}
         */
        initObservable: function () {
            this._super()
                .observe('previewMode');

            return this;
        },

        /**
         * Set initial value
         * @returns {*}
         */
        setInitialValue: function () {
            this._super();
            this.previewMode.subscribe(
                this.onPreviewModeChange, this
            );
            this.initLoader();

            return this;
        },

        /**
         * Handler for change value
         */
        onValueChange: function() {
            var value = this.value();
            console.log(value);
        },

        /**
         * Handler for change previewMode
         */
        onPreviewModeChange: function() {
            if (this.previewMode()) {
                $("[data-multiline-preview-index='" + this.index +"']").show();
                $("[data-multiline-fields-index='" + this.index +"']").hide();
            } else {
                $("[data-multiline-preview-index='" + this.index +"']").hide();
                $("[data-multiline-fields-index='" + this.index +"']").show();
            }
        },

        /**
         * Save button click handler
         */
        onSaveButtonClick: function () {
            var value = this.value();

            if (value !== null) {
                this.save();
            }

            this.enablePreviewMode();
        },

        /**
         * Send request to backend
         */
        save: function () {
            var self = this,
                payload;

            payload = composePayload(this.payload);
            this.loader.show();

            if (payload[this.index].length) {
                payload[this.index] = payload[this.index].join("\n");
            }

            return sendRequest(this.requestUrl, payload, this.reloadComponent)
                .always(function () {
                    self.loader.hide();
                })
                .done(function (response) {
                    messageManager.addSuccessMessage(response.message, 5000);
                    self.enablePreviewMode();
                });
        },

        /**
         * Retrieve bind data attribute map
         *
         * @return {exports.defaults.bindAttributeMap|{}}
         */
        getBindAttributeMap: function () {
            var self = this,
                preparedMap = {};

            _.each(this.bindAttributeMap, function (item, index) {
                if (_.has(self, item)) {
                    preparedMap[index] = self[item];
                }
            });

            return preparedMap;
        },

        /**
         * Get preview
         * @returns {string}
         */
        getPreview: function () {
            var multilineValue = this.value(),
                result = [];
            _.each(multilineValue, function (value) {
                if (value.length !== 0) {
                    result.push(value);
                }
            }, this);

            return result.join(', ');
        }
    });
});

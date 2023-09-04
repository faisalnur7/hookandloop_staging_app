define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/select',
    'Aheadworks_Helpdesk2/js/ui/form/components/ticket/quick-responce-content'
], function ($, _, Select, quickResponseContent) {
    'use strict';

    return Select.extend({
        defaults: {
            modules: {
                target: '${ $.targetName }'
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();

            if (_.isEmpty(this.options())) {
                this.visible(false);
            }

            return this;
        },

        /**
         * Handler for change value
         */
        onUpdate: function() {
            var option,
                id = this.value(),
                tinyEditor
            if (id) {
                option = this.getOption(id);
                quickResponseContent.setContent(option.response, this.target().wysiwygId)
                this.clear();
            }
        },
    });
});

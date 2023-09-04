define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            wrapperClass: ''
        },

        /**
         * @inheritDoc
         */
        getLabel: function (record) {
            if (record[this.index] > 0) {
                this.wrapperClass = "aw-customer-rating rating-" + record[this.index];
            } else {
                this.wrapperClass = ''
            }
            return this.wrap();
        },

        /**
         * Wrap value
         *
         * @private
         * @return {String}
         */
        wrap: function () {
            var template =
                _.template('<span class="<%= wrapperClass %>"></span>');

            return template({
                wrapperClass: this.wrapperClass
            });
        }
    });
});

define([
    'jquery',
    'Magento_Ui/js/grid/columns/column'
], function ($, Column) {
    'use strict';

    return Column.extend({

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            this.showMergeButton();
            return this;
        },

        /**
         * Show merge button
         */
        showMergeButton: function () {
            $('[data-index="merge_tickets"]').show();
        },

        /**
         * Retrieve message type
         *
         * @param {Object} row
         * @returns {String}
         */
        getCheckboxId: function(row) {
            return 'merge-checkbox-' + row['entity_id'];
        },

        /**
         * Get entity id from row
         *
         * @param row
         * @returns {*}
         */
        getEntityId: function (row) {
            return row['entity_id'];
        },

        /**
         * Get uid from row
         *
         * @param row
         * @returns {*}
         */
        getUid: function (row) {
            return row['uid'];
        },

        /**
         * Overrides base method, because this component
         * can't have global field action
         *
         * @returns {Boolean}
         */
        hasFieldAction: function () {
            return false;
        }
    });
});

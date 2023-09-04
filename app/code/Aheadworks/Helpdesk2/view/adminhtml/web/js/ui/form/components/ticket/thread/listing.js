define([
    'underscore',
    'Magento_Ui/js/lib/spinner',
    'Magento_Ui/js/grid/listing',
    'mage/apply/main',
    'jquery'
], function (_, loader, Listing, mage, $) {
    'use strict';

    return Listing.extend({
        defaults: {
            tabs: []
        },

        /**
         * Retrieve tab label
         *
         * @param {Object} tab
         * @return {string}
         */
        getTabLabel: function(tab) {
            var count,
                label = tab.label;

            if (tab.showCountInLabel) {
                count = this.getRows(tab.messageType).length;
                label += ' (' + count + ')';
            }

            return label;
        },

        /**
         * Retrieve listing rows filtered by message type
         *
         * @param messageType
         * @return {Array}
         */
        getRows: function (messageType) {
            var rows = this.rows;

            if (messageType) {
                rows = _.filter(rows, function (row) {
                    if (_.isString(messageType)) {
                        return row.type === messageType
                    } else if (Array.isArray(messageType)) {
                        return _.contains(messageType, row.type)
                    }
                    return true;
                });
            }

            return rows;
        },

        /**
         * Hides loader.
         */
        hideLoader: function () {
            loader.get(this.name).hide();
            this.selectDefaultTab();
        },

        /**
         * @inheritDoc
         */
        onDataReloaded: function () {
            this._super();
            mage.apply();
        },

        /**
         * Select default tab
         */
        selectDefaultTab: function () {
            var activeTabIndex = this.defaultActiveTabIndex;
            if (activeTabIndex) {
                $(function () {
                    var tab = $("div").find("[aria-controls="+ activeTabIndex +"]");
                    if (typeof tab.collapsible === "function" && !tab.collapsible("option","active")) {
                        tab.collapsible("activate");
                    }
                });
            }
        }
    });
});

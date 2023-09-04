define([
    'Magento_Ui/js/form/element/abstract',
    'jquery'
], function (Abstract, $) {
    'use strict';

    return Abstract.extend({
        defaults: {
            listens: {
                applySelectProducts: 'applySelectProducts',
                clearProductListing: 'clearProductListing',
                openProductListing: 'openProductListing',
                changeSku: 'changeSku'
            },
            imports: {
                onSelectedChange: '${ $.selectionsProvider }:selected'
            },
            modules: {
                insertListingComponent: '${ $.selectionsProvider }',
                insertComponent: '${ $.productProvider }'
            }
        },
        selecting: false,

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe([
                    'insertListingComponent'
                ]);
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();
        },

        /**
         * Close product listing
         */
        applySelectProducts: function () {
            localStorage.setItem('currentFieldUid', null)
            this.closeProducts()
        },

        /**
         * Clear skus and deselectAll
         */
        clearProductListing: function () {
            this.value('');
            this.insertListingComponent().deselectAll()
        },

        /**
         * Open product listing and selected saved sku
         */
        openProductListing: function () {
            var self = this

            if (!self.insertListingComponent()) {
                alert('Component insert products loading...')
                return;
            }

            self.insertListingComponent().rows.subscribe(function () {
                self.checkSelected()
            })
            localStorage.setItem('currentFieldUid', this.uid)
            this.checkSelected()
            this.insertComponent().visible(true)
        },

        /**
         * Hide product listing
         */
        closeProducts: function () {
            this.insertComponent().visible(false)
        },

        /**
         * Select saved sku
         */
        checkSelected: function () {
            var self = this,
                selectedProducts = this.valueToArray()

            if (self.selecting || localStorage.getItem('currentFieldUid') !== self.uid) {
                return
            }

            self.selecting = true;
            self.insertListingComponent().deselectAll()
            self.insertListingComponent().rows._latestValue.forEach(function (item, index) {
                if ($.inArray(item.sku, selectedProducts) !== -1) {
                    self.insertListingComponent().select(index, true)
                }
            })
            self.selecting = false;
        },

        /**
         * Modify product listing from data input
         */
        changeSku: function () {
            var self = this

            if (localStorage.getItem('currentFieldUid') !== self.uid) {
                return
            }

            setTimeout(function () {
                self.checkSelected()
            }, 100)
        },

        /**
         * Update input data after select product keeping missing sku on the current page
         *
         * @param selected
         */
        onSelectedChange: function (selected) {
            var self = this,
                selectedProducts = [],
                saveProducts = this.valueToArray()

            if (self.selecting || localStorage.getItem('currentFieldUid') !== self.uid) {
                return
            }

            self.insertListingComponent().rows._latestValue.filter(function (item) {
                if ($.inArray(item.entity_id, selected) !== -1) {
                    selectedProducts.push(item.sku)
                }
                if ($.inArray(item.sku, saveProducts) !== -1) {
                    var index = saveProducts.indexOf(item.sku);
                    if (index !== -1) {
                        saveProducts.splice(index, 1);
                    }
                }
            })
            selectedProducts = selectedProducts.concat(saveProducts)
            selectedProducts = selectedProducts.filter((v, i, a) => a.indexOf(v) == i)
            self.value(selectedProducts.join(','));
        },

        /**
         * String value to array
         *
         * @returns {*[]|*|string[]}
         */
        valueToArray: function () {
            if (this.value() && this.value().length) {
                return this.value().split(',')
            }
            return []
        }
    });
});

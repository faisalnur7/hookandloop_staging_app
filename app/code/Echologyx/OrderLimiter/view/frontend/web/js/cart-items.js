define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'uiRegistry'
], function ($, ko, Component, customerData, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_CustomProductPage/product/cart-items'
        },

        observableProperties: [
            'items'
        ],

        /** @inheritdoc */
        initialize: function () {
            this._super();

            var self = this;

            customerData.get('cart').subscribe(
                function (cartData) {
                    self.items(cartData.items);
                }
            );

            this.items(customerData.get('cart')().items); //get cart items
            console.log(this.items());
            let totalQty = 0;
            this.items().forEach(item => {
                totalQty+=item.qty;
            });

            window.totalQuantity = totalQty;
        },

        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);

            return this;
        }
    });
});
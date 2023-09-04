define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/cart/totals-processor/default',
        'Magento_Checkout/js/model/cart/cache'
    ],
    function (ko, $, defaultTotal, cartCache) {
        'use strict';
        return Component.extend({
            updateamount:function () {//your function to update amount
                console.log('custsom');
                //your code to update amount
                //after successfull execution you need to add these lines.
                cartCache.set('totals',null);
                defaultTotal.estimateTotals();
            }
        });
    }
);
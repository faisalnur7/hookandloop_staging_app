/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote,total) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_SalesRule/summary/discount'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            },
            getTitle: function() {
                var title=total.getSegment('discount');

                if (!this.totals()) {
                    return null;
                }
                return title.title;
            },
            getCouponCode: function() {

                if (!this.totals()) {
                    return null;
                }
                return this.totals()['coupon_code'];
            },
            getPureValue: function() {
                var price = 0;
                if (this.totals() && this.totals().discount_amount) {
                    price = parseFloat(this.totals().discount_amount);
                }
                return price;
            },
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
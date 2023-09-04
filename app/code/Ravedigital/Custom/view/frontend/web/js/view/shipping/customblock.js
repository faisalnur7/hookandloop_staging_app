define(
    [
        'uiComponent',
        'jquery',
        'ko'
    ],
    function(
        Component,
        $,
        ko
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ravedigital_Custom/checkout/shipping/customblock'
            },

            initialize: function () {
                var self = this;
                this._super();
            }

        });
    }
);
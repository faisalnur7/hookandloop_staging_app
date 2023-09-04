define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Dckap_CustomFields/js/model/isFieldValidate'
    ],
    function (Component, additionalValidators, shipFieldValidation) {
        'use strict';
        additionalValidators.registerValidator(shipFieldValidation);
        return Component.extend({});
    }
);
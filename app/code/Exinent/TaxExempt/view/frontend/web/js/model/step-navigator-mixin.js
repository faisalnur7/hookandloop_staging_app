define([ 
    'jquery', 
    'mage/utils/wrapper' 
], function ($, wrapper) { 
    'use strict'; 
    return function(stepNavigator){ 
        var customGetActiveItemIndex = wrapper.wrap(stepNavigator.getActiveItemIndex, function(originalGetActiveItemIndex){ 
            var activeIndex = originalGetActiveItemIndex(), 
                body = $('body'), 
                shippingClass = 'checkout-shipping-step', 
                paymentClass = 'checkout-payment-step'; 
            if (activeIndex){ 
                body.removeClass(shippingClass); 
                body.addClass(paymentClass); 
                 $('#tax-relief-form').hide();
            } else { 
                body.removeClass(paymentClass); 
                body.addClass(shippingClass);
                 $(window).on("resize", function (e) {
                    checkScreenSize();
                });
                checkScreenSize();
                function checkScreenSize(){
                    var newWindowWidth = $(window).width();
                    if (newWindowWidth < 481) {
                        $('.opc-estimated-wrapper').prepend($('#tax-relief-form'));
                    }
                    else
                    {
                        $('#opc-sidebar').prepend($('#tax-relief-form'));
                    }
                     $('#tax-relief-form').show();
                }
            } 
            return activeIndex; 
        }); 
        stepNavigator.getActiveItemIndex = customGetActiveItemIndex; 
        return stepNavigator; 
    }; 
});
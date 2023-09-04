define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'Magento_Checkout/js/checkout-data'
], function ($, Component, quote, stepNavigator, sidebarModel, checkoutData) {
    'use strict';
    
    var mixin = {

        getShippingMethodTitle: function () {
            var shippingMethod = quote.shippingMethod();
            return shippingMethod ? shippingMethod['carrier_title'] : '';
        },
         getShippingMethodTitleAndOption: function () {
            var shippingMethod = quote.shippingAddress();
            return shippingMethod.postcode ? true : '';
            
        },
        canShowShippingAccountInfo: function () {
            var shippingMethod = quote.shippingMethod();
            var shipping_zipcode = $("input[name=postcode]").val()?$("input[name=postcode]").val():null;
            var selected_shipping = $('.shipping-address-item').hasClass('selected-item')?'true':'false';
            if ((shippingMethod && shippingMethod['carrier_code'] == 'shippingoptions') && (shipping_zipcode!=null || selected_shipping=='true')){
                $('#s_method_shippingoptions').show();
                var method = $('[name="shipping_option_field[shipping_options_method]"]').val();
                var value = $('[name="shipping_option_field[shipping_options_method]"]').attr("rel");
                // $('#shipping-options-details-list-wrapper').css('display', 'inline-block');
                $('#shipping-options-details-list-wrapper').css('display', 'grid');
                if(method!='' && method!='undefined' && (shipping_zipcode!=null || selected_shipping=='true')){
                    var temp = setInterval(function () {
                        if ($('#shipping-options-details-list-wrapper').length && (shipping_zipcode!=null || selected_shipping=='true')) {
                            $('#s_method_shippingoptions').show();
                            $('.col-price').show();
                            $('#s_method_shippingoptions').prop("checked", true);
                            // $('#shipping-options-details-list-wrapper').css('display', 'inline-block');
                            $('#shipping-options-details-list-wrapper').css('display', 'grid');
                            clearInterval(temp);
                        } else if(shipping_zipcode == null || selected_shipping == false){
                            $('#s_method_shippingoptions').prop("checked", false);
                            $('#s_method_shippingoptions').hide();
                            $('.col-price').hide();
                            clearInterval(temp);
                        } 
                    }, 1000);
                    setTimeout(function () {
                        var method = $('[name="shipping_option_field[shipping_options_method]"]').val();
                        var value = $('[name="shipping_option_field[shipping_options_method]"]').attr("rel");
                         //$('#s_method_shippingoptions_shippingoptions').hide();
                        if($('#shipping-options-details-list-wrapper').length && method!='' && method!='undefined' && (shipping_zipcode!=null || selected_shipping=='true')){
                            $('[name="shipping_option_field[shipping_options_method]"]').val(value).trigger('click');
                            $('[name="shipping_option_field[shipping_options_method]"]').val(method).trigger('change');
                        } 
                    }, 2000);
                }
                return true;
            } else if((shipping_zipcode!=null || selected_shipping=='true')) {
                $('#s_method_shippingoptions').prop("checked", false);
                return false;
            } else {
                $('#s_method_shippingoptions').prop("checked", false);
                $('#s_method_shippingoptions').hide();
                return false;
            }
            //return (shippingMethod && shippingMethod['carrier_code'] == 'shippingoptions') ? true : false;
        },
        
        getShippingOptionMethod: function () {
            return $('[name="shipping_option_field[shipping_options_method]"]').val();
          
        },
        
        getShippingOptionService: function () {
            return $('[name="shipping_option_field[shipping_options_service]"]').val();
        
        },

        getShippingOptionsAccountNumber: function () {
            return $('[name="shipping_option_field[shipping_options_account_number]"]').val();
          
        },

        getShippingOptionsAccountZipCodes: function () {
            return $('[name="shipping_option_field[shipping_options_account_zip_codes]"]').val();
       
        }
   };

   return function (target) {
       return target.extend(mixin);
   };

});
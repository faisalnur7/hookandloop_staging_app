/*
* *
*  @author DCKAP Team
*  @copyright Copyright (c) 2018 DCKAP (https://www.dckap.com)
*  @package Dckap_CustomFields
*/
define(
   [
       'ko',
       'Magento_Checkout/js/model/quote',
       'Magento_Checkout/js/model/resource-url-manager',
       'mage/storage',
       'Magento_Checkout/js/model/payment-service',
       'Magento_Checkout/js/model/payment/method-converter',
       'Magento_Checkout/js/model/error-processor',
       'Magento_Checkout/js/model/full-screen-loader',
       'Magento_Checkout/js/action/select-billing-address'
   ],
    function (
       ko,
       quote,
       resourceUrlManager,
       storage,
       paymentService,
       methodConverter,
       errorProcessor,
       fullScreenLoader,
       selectBillingAddressAction
   ) {
       'use strict';

       return {
           saveShippingInformation: function () {
               var payload;

               var shippingMethod = quote.shippingMethod().method_code+'_'+quote.shippingMethod().carrier_code;

               var shipping_options_method = null;
               var shipping_options_service = null;
               var shipping_options_account_number = null;
               var shipping_options_account_zip_codes = null;
               var shippingValidationResult = false;
              if(shippingMethod == "shippingoptions_shippingoptions") {
                  if(jQuery('[name="shipping_option_field[shipping_options_method]"]').val() === 'fedex' || jQuery('[name="shipping_option_field[shipping_options_method]"]').val()=== 'ups' || jQuery('[name="shipping_option_field[shipping_options_method]"]').val()=== 'dhl'){
                      jQuery('.mage-error').remove();
                      shippingValidationResult = true;
                      if(jQuery('[name="shipping_option_field[shipping_options_service]"]').val()){  
                        jQuery('.mage-error').remove();
                        //shippingValidationResult = true;
                        if(jQuery('[name="shipping_option_field[shipping_options_account_number]"]').val()!==''){
                          jQuery('.mage-error').remove();
                          shippingValidationResult = true;
                          if(jQuery('[name="shipping_option_field[shipping_options_account_zip_codes]"]').val()!==''){
                            jQuery('.mage-error').remove();
                            shippingValidationResult = true;
                          } else {
                            jQuery('.mage-error').remove();
                            jQuery('[name="shipping_option_field[shipping_options_account_zip_codes]"]').parents('.field').append('<div class="mage-error">* This is required field</div>');
                            jQuery('.mage-error').css({'color':'red','text-align':'center'});
                            shippingValidationResult = false;
                          }
                        } else {
                          jQuery('.mage-error').remove();
                          jQuery('[name="shipping_option_field[shipping_options_account_number]"]').parents('.field').append('<div class="mage-error">* This is required field</div>');
                          jQuery('.mage-error').css({'color':'red','text-align':'center'});
                          shippingValidationResult = false;
                        }
                      } else {
                        jQuery('.mage-error').remove();
                        jQuery('[name="shipping_option_field[shipping_options_service]"]').parents('.field').append('<div class="mage-error">* This is required field</div>');                 
                        jQuery('.mage-error').css({'color':'red','text-align':'center'});
                        shippingValidationResult = false;
                      }
                  } else {
                    jQuery('.mage-error').remove();
                    jQuery('[name="shipping_option_field[shipping_options_method]"]').parents('.field').append('<div class="mage-error">* This is required field</div>');                  
                    jQuery('.mage-error').css({'color':'red','text-align':'center'});
                    shippingValidationResult = false;
                  }
                  if(shippingValidationResult == true){
                    shipping_options_method = jQuery('[name="shipping_option_field[shipping_options_method]"]').val();
                    shipping_options_service = jQuery('[name="shipping_option_field[shipping_options_service]"]').val();
                    shipping_options_account_number = jQuery('[name="shipping_option_field[shipping_options_account_number]"]').val();
                    shipping_options_account_zip_codes = jQuery('[name="shipping_option_field[shipping_options_account_zip_codes]"]').val();
                    
                  } /*else {
                    window.location.replace(BASE_URL+'checkout#shipping');
                  }*/
              } else{
                shippingValidationResult = true;
              }
              if(shippingValidationResult){
                if (!quote.billingAddress()) {
                        selectBillingAddressAction(quote.shippingAddress());
                  }
                 payload = {
                     addressInformation: {
                         shipping_address: quote.shippingAddress(),
                         billing_address: quote.billingAddress(),
                         shipping_method_code: quote.shippingMethod().method_code,
                         shipping_carrier_code: quote.shippingMethod().carrier_code,
                         extension_attributes: {
                             shipping_options_method : shipping_options_method,
                             shipping_options_service: shipping_options_service,
                             shipping_options_account_number: shipping_options_account_number,
                             shipping_options_account_zip_codes: shipping_options_account_zip_codes
                         }
                     }
                };
              } 
               //return false;
              
               fullScreenLoader.startLoader();

               return storage.post(
                   resourceUrlManager.getUrlForSetShippingInformation(quote),
                   JSON.stringify(payload)
               ).done(
                   function (response) {
                       quote.setTotals(response.totals);
                       paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                       fullScreenLoader.stopLoader();
                   }
               ).fail(
                   function (response) {
                       errorProcessor.process(response);
                       fullScreenLoader.stopLoader();
                   }
               );
           }
       };
   }
);

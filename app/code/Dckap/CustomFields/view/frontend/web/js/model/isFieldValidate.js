define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';

        return {

            /**
             * Validate checkout agreements
             *
             * @returns {Boolean}
             */
            validate: function () {
                var shippingValidationResult = false;

                var allShippingoptionData = window.checkoutConfig.allShippingoptions;
                var allShippingoptionVal = $.parseJSON(allShippingoptionData);
                var optionValues = []; 

                if($('body').hasClass('checkout-index-index')){
                    //if($("#shipping-options-details-list-wrapper").is(':visible')){
                        if($('.checkout-index-index #s_method_shippingoptions').prop('checked')){
                            if($('[name="shipping_option_field[shipping_options_method]"]').val() === 'fedex' || $('[name="shipping_option_field[shipping_options_method]"]').val()=== 'ups' || $('[name="shipping_option_field[shipping_options_method]"]').val()=== 'dhl'){
                                $('.mage-error').remove();
                                shippingValidationResult = true;
                                $.each(allShippingoptionVal, function (index, value) {
                                    var optionResponse = value.split(',');
                                    $.each(optionResponse, function (indexOption, SelectedOptionValue) {
                                      optionValues.push(SelectedOptionValue);
                                    });
                                });
                                //if($('[name="shipping_option_field[shipping_options_service]"]').val()){    
                                if($('#shipping_save_processor').html()){    
                                    $('.mage-error').remove();
                                    shippingValidationResult = true;
                                    if($('[name="shipping_option_field[shipping_options_account_number]"]').val()!==''){
                                        $('.mage-error').remove();
                                        shippingValidationResult = true;
                                        if($('[name="shipping_option_field[shipping_options_account_zip_codes]"]').val()!==''){
                                            $('.mage-error').remove();
                                            shippingValidationResult = true;
                                        } else {
                                            $('.mage-error').remove();
                                            $('[name="shipping_option_field[shipping_options_account_zip_codes]"]').parents('.field').append('<div class="mage-error">* This is required field</div>');
                                            $('.mage-error').css({'color':'red','text-align':'center'});
                                            shippingValidationResult = false;
                                        }
                                    } else {
                                        $('.mage-error').remove();
                                        $('[name="shipping_option_field[shipping_options_account_number]"]').parents('.field').append('<div class="mage-error">* This is required field</div>');
                                        $('.mage-error').css({'color':'red','text-align':'center'});
                                        shippingValidationResult = false;
                                    }
                                } else {
                                    $('.mage-error').remove();
                                    $('[name="shipping_option_field[shipping_options_service]"]').parents('.field').append('<div class="mage-error">* This is required field</div>');                                   
                                    $('.mage-error').css({'color':'red','text-align':'center'});
                                    shippingValidationResult = false;
                                    window.location.replace(BASE_URL+'checkout#shipping');
                                }
                            } else {
                                $('.mage-error').remove();
                                $('[name="shipping_option_field[shipping_options_method]"]').parents('.field').append('<div class="mage-error">* this is required field</div>');                                    
                                $('.mage-error').css({'color':'red','text-align':'right','padding-right':'18px'});
                                shippingValidationResult = false;
                                window.location.replace(BASE_URL+'checkout#shipping');
                            }
                        }else {
                            shippingValidationResult = true;
                        }
                    //} 
                }
                
                return shippingValidationResult;
            }
        };
    }
);

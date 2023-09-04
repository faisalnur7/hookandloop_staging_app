define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * Change currently selected option
         *
         * @param {String} id
         */
        selectOption: function(id){ 
            var allShippingoptionData = window.checkoutConfig.allShippingoptions;
            var allShippingoptionVal = $.parseJSON(allShippingoptionData);
            $('select[name="shipping_option_field[shipping_options_service]"]').empty();
            $.each(allShippingoptionVal, function (index, value) {
            var optionValues = [];   
               if(index == $("#"+id).val()){ 
                    var optionResponse = value.split(',');
                    optionValues = '<option value="">Please select</option>';
                    $.each(optionResponse, function (indexOption, SelectedOptionValue) {
                      var selected ='';
                      if(window.checkoutConfig.quoteData.shipping_options_method == $('[name="shipping_option_field[shipping_options_method]"]').val() && window.checkoutConfig.quoteData.shipping_options_service == SelectedOptionValue){
                        selected = 'selected';
                      }
                      optionValues+= '<option value="'+SelectedOptionValue+'" '+selected+'>'+SelectedOptionValue+'</option>';
                    });
                $('select[name="shipping_option_field[shipping_options_service]"]').empty().append(optionValues);  
                }
            });   
        },
    });
    
});

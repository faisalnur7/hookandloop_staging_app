requirejs(['jquery'], function (jQuery) {
    jQuery(function ($) {
        $(document).ready(function () {

            var checkExist = setInterval(function () {
                if ($('#checkout .table-checkout-shipping-method tbody td.col-method input').length) {
                    $('#checkout .table-checkout-shipping-method tbody td.col-method input').on('change', function () {
                        var curElement = jQuery(this).parents('tr').find('td.col-carrier').attr('id');
                        if (curElement == 'label_carrier_shippingoptions_shippingoptions') {
                            var shipping_zipcode = $("input[name=postcode]").val()?$("input[name=postcode]").val():null;
                            var selected_shipping = $('.shipping-address-item').hasClass('selected-item')?'true':'false';
                            //$('#shipping-options-details-list-wrapper').css('display', 'inline-block');
                            $('#s_method_shippingoptions').prop("checked", true);
                                var temp = setInterval(function () {
                                    if ($('#shipping-options-details-list-wrapper').length && (shipping_zipcode!=null || selected_shipping=='true')) {
                                        //alert('test 18');
                                        $('#s_method_shippingoptions').show();
                                        $('.col-price').show();
                                        $('#s_method_shippingoptions').prop("checked", true);
                                        $('#shipping-options-details-list-wrapper').css('display', 'inline-block');
                                        clearInterval(temp);
                                    } else if(shipping_zipcode == null || selected_shipping == false){
                                        //alert('test 25');
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
                                if(method!='' && method!='undefined' && (shipping_zipcode!=null || selected_shipping=='true')){
                                    $('[name="shipping_option_field[shipping_options_method]"]').val(value).trigger('click');
                                    $('[name="shipping_option_field[shipping_options_method]"]').val(method).trigger('change');
                                } 
                            }, 2000);
                        }
                    });
                    clearInterval(checkExist);
                }
            }, 1000); // check every 100ms
            /*if(document.getElementById("shipping-options-details-list-wrapper") !== null) {
                var elementProcess = setInterval(function() {
                if(document.getElementById('shipping-options-details-list-wrapper').style.display == "none") {
                clearInterval(elementProcess);
                }
                document.getElementById('shipping-options-details-list-wrapper').style.display = "none"; 
                }, 2000);
            }*/
          
            
            $(document).on('click','#add_coupon_code_button',function(){
            // $(document).ajaxComplete(function(){
                var shipsfree = setInterval(function(){
                    if($('#s_method_freeshipping_freeshipping').prop('checked')==true){
                        $('#s_method_freeshipping_freeshipping').trigger('click');
                        clearInterval(shipsfree);
                    }
                },1000);
                
            // });
            });
               
            
            //end

        });
    });
});

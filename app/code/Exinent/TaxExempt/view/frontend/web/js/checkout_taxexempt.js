requirejs(['jquery', 'mage/mage', 'mage/url', 'jquery/ui'], function ($, mage, url, ui) {
    'use strict';

    $(document).ready(function () {
        var checkExist = setInterval(function () {
            if ($('.checkout-shipping-method').length)
            {
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
                }
                $('#tax-relief-form').show();
                clearInterval(checkExist);
            }
        }, 1000);
        
        if ($('#customer-has-tax-relief').prop('checked'))
            $('#tax-relief-form .form-list').show();
        var flag = 0;
        $('#tax-relief-code , #customer-region').change(function () {
            var error = 0;
            var tax = $('#tax-relief-code').val();
            var region = $('#customer-region').val();
            if (tax.length > 0) {
                if ((tax.match(/[0-9][-]*$/) == null) || tax.length < 6) {
                    error = 1;
                    $("#advice-required-cart-tax").text("Invalid Tax Code.");
                    $('#advice-required-cart-tax').show();
                } else {
                    $('#advice-required-cart-tax').hide();
                }
                if (region == 'Please select region, state or province') {
                    error = 1;
                    $('#advice-required-cart-tax-state').show();
                }
                else {
                    $('#advice-required-cart-tax-state').hide();
                }
//                if ((tax <= 0) || (tax.match(/^\d{3}\-\d{2}\-\d{5}$/)!='') ) {
            } else if (region !== 'Please select region, state or province') {
                error = 1;
                $("#advice-required-cart-tax").text("This is a required field.");
                $('#advice-required-cart-tax').show();
            } else {
                $('#advice-required-cart-tax').hide();
                $('#advice-required-cart-tax-state').hide();
            }
            if (tax.length > 0 && region !== 'Please select region, state or province')
            {
                if ((tax.match(/[0-9][-]*$/) == null) || tax.length < 6) {
                    //return false;
                } else {
                    flag = 1;
                }
            }
            if(flag == 1){
                triggercall(tax,region);
            }
            if(error == 1){
                flag = 0;
            }
        });
        function triggercall(tax,region){
            var apirequest = url.build('taxexempt/index/index');
                    var form = $('form#form-validate');
                    $.ajax({
                        type: 'POST',
                        url: apirequest,
                        cache: false,
                        showLoader: true,
                        data: {'tax': tax, 'region': region},
                        success: function (data) {
                            $(".methods-shipping input[type='radio']:checked").trigger("click");
                        }
                    });
        }

        $('#customer-has-tax-relief').change(function () {
            if ($('#customer-has-tax-relief').prop('checked'))
                $('#tax-relief-form .form-list').show();
            else {
                $('#tax-relief-form .form-list').hide();
                $('#tax-relief-code').val('');
                var tax = $('#tax-relief-code').val();
                var region = $('#customer-region').val();
                triggercall(tax,region);
                $("#customer-region").val($("#customer-region option:first").val());
            }
        });
    });

});
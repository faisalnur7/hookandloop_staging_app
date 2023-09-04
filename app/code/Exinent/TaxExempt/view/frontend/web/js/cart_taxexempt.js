require(['jquery', 'mage/mage', 'mage/url', 'jquery/ui'], function ($, mage, url, ui) {

    'use strict';

    $(document).ready(function () {

        $('#block-shipping').hide();
        var checkExist = setInterval(function () {
            if ($('select[name="region_id"]').length)
            {
                $('select[name="region_id"]').val($('select[name="region_id"] option:first').val());
                $('#block-shipping').show();
                clearInterval(checkExist);
            }
        }, 500);
         if ($('#customer-has-tax-relief').prop('checked'))
            $('#tax-relief-form .form-list').show();
        var flag = 0;
        $('#tax-relief-code , #customer-region').change(function () {
            var tax = $('#tax-relief-code').val();
            var region = $('#customer-region').val();
            var apirequest = url.build('taxexempt/index/index');
            $.ajax({
                type: 'POST',
                url: apirequest,
                data: {'tax': tax, 'region': region},
                success: function (data) {
                    if (data.result == 'Tax Exempted') {
                        $('.action.update').trigger('click');
                    }
                }
            });
            if (tax.length > 0) {
                if (region == 'Please select region, state or province')
                    $('#advice-required-cart-tax-state').show();
                else
                    $('#advice-required-cart-tax-state').hide();
//                if ((tax <= 0) || (tax.match(/^\d{3}\-\d{2}\-\d{5}$/)!='') ) {
                if ((tax.match(/[0-9][-]*$/) == null) || tax.length < 8) {
                    $("#advice-required-cart-tax").text("Invalid Tax Code.");
                    $('#advice-required-cart-tax').show();
                } else
                    $('#advice-required-cart-tax').hide();
            } else if (region !== 'Please select region, state or province') {
                $("#advice-required-cart-tax").text("This is a required field.");
                $('#advice-required-cart-tax').show();
            } else {
                $('#advice-required-cart-tax').hide();
                $('#advice-required-cart-tax-state').hide();
            }

        });

         $('#customer-has-tax-relief').change(function () {
            if ($('#customer-has-tax-relief').prop('checked'))
                $('#tax-relief-form .form-list').show();
            else {
                $('#tax-relief-form .form-list').hide();
                $('#tax-relief-code').val('');
                $("#customer-region").val($("#customer-region option:first").val());
            }
        });
    });

});
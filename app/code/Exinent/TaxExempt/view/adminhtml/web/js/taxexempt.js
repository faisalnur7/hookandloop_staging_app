require(['jquery', 'mage/mage', 'mage/url', 'jquery/ui'], function ($, mage, url, ui) {

    'use strict';

    $(document).ready(function () {
        $(document).on( 'change', '#tax-relief-code , #customer-region', function () {
            var tax = $('#tax-relief-code').val();
            var region = $('#customer-region').val();

            if (tax.length > 0) {
                if (region == 'Please select region, state or province')
                    $('#advice-required-cart-tax-state').show();
                else
                    $('#advice-required-cart-tax-state').hide();
                if ((tax <= 0) || (!$.isNumeric(tax))) {
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
            var apirequest = url.build('/hookandloop99/admin/taxexemptadmin/index/index');
            $.ajax({
                type: 'POST',
                url: apirequest,
                data: {'tax': tax, 'region': region},
                success: function (data) {
                    console.log('data.result');
                }
            });
        });

        $('#customer-has-tax-relief').change(function () {
            if ($('#customer-has-tax-relief').prop('checked'))
                $('#tax-relief-form .form-list').show();
            else
                $('#tax-relief-form .form-list').hide();
        });
    });
    
});
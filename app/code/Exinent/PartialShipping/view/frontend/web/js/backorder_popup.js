requirejs(['jquery', 'mage/url'], function (jQuery, url) {
    jQuery(function ($) {
        $(document).ready(function () {

            var backorderMsg = document.getElementById('backorder-msg');

            $("#checkout").on('click', '.partial-shipment', function () {
                var backorder = 0;
                $('.item-msg.notice p').each(function () {
                    if ($(this).text() != '') {
                        backorder = 1;
                    }
                });
                if (backorder) {
                    var backorderOption = $('#backorder-choice').find('input[name="partialshipment-choice"]').val();
                    if (backorderOption !== '') {
                        document.getElementById('backorder-background').style.display = 'block';
                        document.getElementById('no').innerHTML = 'No, Please ship this order complete in ' + backorderMsg.innerHTML;

                        var confirmBox = $("#confirmBox");
                        confirmBox.find(".message").text();

                        confirmBox.find(".yes").click(function () {
                            $msg = document.getElementById('yes').innerHTML;
                            $('#backorder-choice').find('input[name="partialshipment-choice"]').val($msg);
                            document.getElementById('backorder-background').style.display = 'none';
                            $("html, body").animate({scrollTop: 400}, 600);
                            var apirequest = url.build('partialshipping/index/index');
                            var requestdata = $('#backorder-choice').find('input[name="partialshipment-choice"]').val();
                            jQuery.ajax({
                                type: 'POST',
                                url: apirequest,
                                data: {choice: requestdata},
                                success: function (data) {
                                    console.log(data.result);
                                }
                            });
                            $('#onestepcheckout_place_order_button .btn-checkout').trigger('click');
                            return false;
                        });

                        confirmBox.find(".no").click(function () {
                            $msg = document.getElementById('no').innerHTML;
                            $('#backorder-choice').find('input[name="partialshipment-choice"]').val($msg);
                            document.getElementById('backorder-background').style.display = 'none';
                            $("html, body").animate({scrollTop: 400}, 600);
                            var apirequest = url.build('partialshipping/index/index');
                            var requestdata = $('#backorder-choice').find('input[name="partialshipment-choice"]').val();
                            jQuery.ajax({
                                type: 'POST',
                                url: apirequest,
                                data: {choice: requestdata},
                                success: function (data) {
                                    console.log(data.result);
                                }
                            });
                            $('#onestepcheckout_place_order_button .btn-checkout').trigger('click');
                            return false;
                        });

                        confirmBox.show();

                        $('#backorder-background').click(function () {
                            if ($('#backorder-background').css('display') == 'block') {
                                $('#backorder-background').hide();
                                confirmBox.hide();
                                return false;
                            }
                        });
                        $('#confirmBox .close-btn').click(function () {
                            if ($('#backorder-background').css('display') == 'block') {
                                $('#backorder-background').hide();
                                confirmBox.hide();
                                return false;
                            }
                        });
                    }
                    $('#onestepcheckout_place_order_button .btn-checkout').trigger('click');
                } else {
                    $('#onestepcheckout_place_order_button .btn-checkout').trigger('click');
                }
            });
        });
    });
});
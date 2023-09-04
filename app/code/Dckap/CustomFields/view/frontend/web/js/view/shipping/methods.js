/*
 * *
 *  Copyright © 2016 MW. All rights reserved.
 *  See COPYING.txt for license details.
 *  
 */
/*global define*/
define(
        [
            'jquery',
            'underscore',
            'Magento_Ui/js/form/form',
            'ko',
            'Magento_Checkout/js/model/quote',
            'Magento_Checkout/js/model/shipping-service',
            'Magento_Checkout/js/action/select-shipping-method',
            'Magento_Checkout/js/action/set-shipping-information',
            'Magento_Checkout/js/checkout-data',
            'Magento_Catalog/js/price-utils',
            'Magento_Checkout/js/model/shipping-rates-validator',
            'Magento_Checkout/js/model/step-navigator'
        ],
        function (
                $,
                _,
                Component,
                ko,
                quote,
                shippingService,
                selectShippingMethodAction,
                setShippingInformationAction,
                checkoutData,
                priceUtils,
                shippingRatesValidator,
                stepNavigator
                ) {
            'use strict';


            return Component.extend({
                defaults: {
                    template: 'Dckap_CustomFields/checkout/shipping/method/item',
                },
                default_shipping_carrier: ko.observable(window.checkoutConfig.default_shipping),
                visible: ko.observable(!quote.isVirtual()),
                isLoading: shippingService.isLoading,
                loading: ko.observable(false),
                savedDefault: ko.observable(false),
                shippingOption: ko.observable(false),
                /**
                 * @return {exports}
                 */

                initialize: function () {
                    var self = this;
                    shippingRatesValidator.validateDelay = 500;
                    self._super();
                    if (window.checkoutConfig.selectedShippingMethod) {
                        selectShippingMethodAction(window.checkoutConfig.selectedShippingMethod);
                    }
                    self.hasShippingMethod = ko.pureComputed(function () {
                        var hasMethod = false;
                        if (quote.shippingMethod()) {
                            var stillAvailable = self.isShippingOnList(quote.shippingMethod().carrier_code, quote.shippingMethod().method_code);
                            hasMethod = (stillAvailable) ? true : false;
                        }
                        return hasMethod;
                    }),
                            /*quote.shippingMethod.subscribe(function () {
                                self.errorValidationMessage(false);
                            });*/

                    
                    shippingService.getShippingRates().subscribe(function () {
                        if (!self.loading() || self.loading() == false) {
                            if (self.hasShippingMethod() == true) {
                                self.selectShippingMethod(quote.shippingMethod());
                            } else {
                                var method = self.getDefaultMethod();
                                if (method !== false) {
                                    self.selectShippingMethod(method);
                                }
                            }
                        }
                    });
                    setTimeout(function () {
                        if (self.savedDefault() == false) {
                            shippingService.getShippingRates().valueHasMutated();
                            self.savedDefault(true);
                        }
                    }, 1000);
                    return this;
                },
                /**
                 * Shipping Method View
                 */
                rates: shippingService.getShippingRates(),
                isSelected: ko.computed(function () {
                    return quote.shippingMethod() ?
                            quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code
                            : null;
                }
                ),
                /**
                 * @param {Object} shippingMethod
                 * @return {Boolean}
                 */
                selectShippingMethod: function (shippingMethod) {
                    var self = this;
                    var shipping_zipcode = $("input[name=postcode]").val()?$("input[name=postcode]").val():null;
                    var selected_shipping = $('.shipping-address-item').hasClass('selected-item')?'true':'false';
                    selectShippingMethodAction(shippingMethod);
                    checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);
                    //$('#shipping-options-details-list-wrapper').css('display', 'inline-block');
                    if (shippingMethod.carrier_code + '_' + shippingMethod.method_code == "shippingoptions_shippingoptions") {
                        $('#s_method_shippingoptions_shippingoptions').prop("checked", true);
                        if ($('input#s_method_shippingoptions_shippingoptions').hasClass('ajaxcall') && (shipping_zipcode!=null || selected_shipping=='true')){
                            $('#shipping-options-details-list-wrapper').css('display', 'inline-block');
                            this.setShippingInformation();
                            self.shippingOption(true);
                            return true;
                        } else{
                            var temp = setInterval(function () {
                                
                                if ($('#shipping-options-details-list-wrapper').length && (shipping_zipcode!=null || selected_shipping=='true')) {
                                    $('#s_method_shippingoptions_shippingoptions').show();
                                    $('.col-price').show();
                                    $('#s_method_shippingoptions_shippingoptions').prop("checked", true);
                                    $('#shipping-options-details-list-wrapper').css('display', 'inline-block');
                                    clearInterval(temp);
                                } else if(shipping_zipcode == null || selected_shipping == false){
                                    $('#s_method_shippingoptions_shippingoptions').prop("checked", false);
                                    $('#s_method_shippingoptions_shippingoptions').hide();
                                    $('.col-price').hide();
                                    clearInterval(temp);
                                } 
                            }, 1000);
                        }

                        setTimeout(function () {
                             var method = $('[name="shipping_option_field[shipping_options_method]"]').val();
                             var value = $('[name="shipping_option_field[shipping_options_method]"]').attr("rel");
                             //$('#s_method_shippingoptions_shippingoptions').hide();
                            if(method!='' && method!='undefined' && (shipping_zipcode!=null || selected_shipping=='true')){
                                $('[name="shipping_option_field[shipping_options_method]"]').val(value).trigger('click');
                                $('[name="shipping_option_field[shipping_options_method]"]').val(method).trigger('change');
                            } 
                        }, 2000);

                    } else
                    {   //console.log('Methods.js: ELSE');
                        $('[name="shipping_option_field[shipping_options_method]"]').prop("checked", false);
                        $('#shipping-options-details-list-wrapper').hide();
                        this.setShippingInformation();
                        self.shippingOption(true);
                        return true;
                    }
                },
                validateCustomFieldsShipping: function () {

                    var shippingMethod = quote.shippingMethod().method_code + '_' + quote.shippingMethod().carrier_code;
                    console.log(shippingMethod);
                    if (this.source.get('ShippingOptions') && shippingMethod == "shippingoptions_shippingoptions") {
                        var val = $('[name="shipping_option_field[shipping_options_method]"]').val();
                        if (val == "fedex")
                        {
                            if ($('[name="shipping_option_field[shipping_options_account_number]"]').val().length != 9) {
                                return false;
                            } else if ($('[name="shipping_option_field[shipping_options_account_number]"]').val().length == '') {
                                return false;
                            }
                        }
                    }
                    return true;
                },
                /**
                 * Set shipping information handler
                 */
                /*setShippingInformation: function () {
                    if (this.validateCustomFieldsShipping()) {
                        var self = this;
                        self.loading(true);
                       
                        setShippingInformationAction().done(
                                function () {
                                    stepNavigator.setHash("payment");
                                    showLoader.payment(false);
                                    showLoader.review(false);
                                    self.loading(false);
                                }
                        ).fail(
                                function () {
                                    showLoader.payment(false);
                                    showLoader.review(false);
                                    self.loading(false);
                                }
                        ).always(function () {
                            saveDefaultPayment();
                            self.loading(false);
                        });
                    }
                },*/,
                /**
                 * @param {Object} shippingMethod
                 * @return {Boolean}
                 */
                getShippingList: function () {
                    var list = [];
                    var rates = this.rates();
                    //if(rates && rates.length > 0){
                    ko.utils.arrayForEach(rates, function (method) {
                        if (list.length > 0) {
                            var notfound = true;
                            ko.utils.arrayForEach(list, function (carrier) {
                                if (carrier && carrier.code == method.carrier_code) {
                                    carrier.methods.push(method);
                                    notfound = false;
                                }
                            });
                            if (notfound == true) {
                                var carrier = {
                                    code: method.carrier_code,
                                    title: method.carrier_title,
                                    methods: [method]
                                }
                                list.push(carrier);
                            }
                        } else {
                            var carrier = {
                                code: method.carrier_code,
                                title: method.carrier_title,
                                methods: [method]
                            }
                            list.push(carrier);
                        }
                    });
                    //}
                    return list;
                },
                isShippingOnList: function (carrier_code, method_code) {
                    var list = this.getShippingList();
                    if (list.length > 0) {
                        var carrier = ko.utils.arrayFirst(list, function (carrier) {
                            return (carrier.code == carrier_code);
                        });
                        if (carrier && carrier.methods.length > 0) {
                            var method = ko.utils.arrayFirst(carrier.methods, function (method) {
                                return (method.method_code == method_code);
                            });
                            return (method) ? true : false;
                        } else {
                            return false;
                        }
                    }
                    return false;
                },
                getDefaultMethod: function () {
                    var self = this;
                    var list = this.getShippingList();
                    if (list.length > 0) {
                        var carrier = ko.utils.arrayFirst(list, function (data) {
                            return (self.default_shipping_carrier()) ? (data.code == self.default_shipping_carrier()) : false;
                        });
                        if (carrier && carrier.methods.length > 0) {
                            var method = ko.utils.arrayFirst(carrier.methods, function () {
                                return true;
                            });
                            return (method) ? method : false;
                        } else {
                            if (list.length == 1) {
                                carrier = ko.utils.arrayFirst(list, function() {
                                    return true;
                                });
                                if (carrier.methods.length == 1) {
                                    var method = ko.utils.arrayFirst(carrier.methods, function() {
                                        return true;
                                    });
                                    return (method)?method:false;
                                }
                            }
                            return false;
                        }
                    }
                    return false;
                },
                formatPrice: function (amount) {
                    amount = parseFloat(amount);
                    var priceFormat = window.checkoutConfig.priceFormat;
                    return priceUtils.formatPrice(amount, priceFormat)
                }
            });
        }
);

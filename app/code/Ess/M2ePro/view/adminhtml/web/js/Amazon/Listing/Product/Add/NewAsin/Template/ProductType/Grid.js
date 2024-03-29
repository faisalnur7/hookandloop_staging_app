define([
    'Magento_Ui/js/modal/modal',
    'M2ePro/Plugin/Messages',
    'M2ePro/Listing/View/Grid',
    'M2ePro/Amazon/Listing/View/Action',
    'M2ePro/Amazon/Listing/Product/Template/ProductType'
], function (modal, MessageObj) {

    window.AmazonListingProductAddNewAsinProductTypeGrid = Class.create(ListingViewGrid, {

        // ---------------------------------------

        getComponent: function () {
            return 'amazon';
        },

        // ---------------------------------------

        getMaxProductsInPart: function () {
            return 1000;
        },

        // ---------------------------------------

        prepareActions: function ($super) {
            $super();
            this.actionHandler = new AmazonListingViewAction(this);
            this.templateProductTypeHandler = new AmazonListingProductProductType(this);

            this.actions = Object.extend(this.actions, {

                setProductTypeAction: (function () {
                    this.mapToNewAsin(this.getSelectedProductsString())
                }).bind(this),
                resetProductTypeAction: (function () {
                    this.unmapFromNewAsinForProducts(this.getSelectedProductsString())
                }).bind(this),

                setProductTypeByCategoryAction: (function () {
                    this.mapToNewAsin(this.getSelectedProductsStringFromCategory())
                }).bind(this),
                resetProductTypeByCategoryAction: (function () {
                    this.unmapFromNewAsinForProducts(this.getSelectedProductsStringFromCategory())
                }).bind(this)
            });
        },

        // ---------------------------------------

        parseResponse: function (response) {
            if (!response.responseText.isJSON()) {
                return;
            }

            return response.responseText.evalJSON();
        },

        // ---------------------------------------

        afterInitPage: function ($super) {
            $super();
        },

        setProductTypeRowAction: function (id) {
            this.mapToNewAsin(id);
        },

        resetProductTypeRowAction: function (id) {
            this.unmapFromNewAsinForProducts(id);
        },

        // ---------------------------------------

        setProductTypeByCategoryRowAction: function (id) {
            this.mapToNewAsin(this.getSelectedProductsStringFromCategory(id));
        },

        resetProductTypeByCategoryRowAction: function (id) {
            this.unmapFromNewAsinForProducts(this.getSelectedProductsStringFromCategory(id));
        },

        // ---------------------------------------

        getSelectedProductsStringFromCategory: function (categoryIds) {
            var productsIdsStr = '';

            categoryIds = categoryIds || this.getGridMassActionObj().checkedString;
            categoryIds = explode(',', categoryIds);

            categoryIds.each(function (categoryId) {

                if (productsIdsStr != '') {
                    productsIdsStr += ',';
                }
                productsIdsStr += $('products_ids_' + categoryId).value;
            });

            return productsIdsStr;
        },

        unmapFromNewAsinForProducts: function(productsIds)
        {
            var self = this;
            self.unmapFromNewAsin(
                productsIds,
                function(productsIds) {
                    self.templateProductTypeHandler.unassign(productsIds)
                }
            );
        },

        // ---------------------------------------

        mapToNewAsin: function (listingProductIds) {
            var self = this;
            new Ajax.Request(M2ePro.url.get('amazon_listing_product/mapToNewAsin'), {
                method: 'post',
                parameters: {
                    products_ids: listingProductIds
                },
                onSuccess: function (transport) {

                    if (!transport.responseText.isJSON()) {
                        self.alert(transport.responseText);
                        return;
                    }

                    var response = transport.responseText.evalJSON();

                    self.templateProductTypeHandler.gridHandler.unselectAllAndReload();

                    if (response.products_ids.length > 0) {
                        ListingGridObj.templateProductTypeHandler.openProductTypePopUp(
                                response.products_ids,
                                M2ePro.translator.translate('productTypePopupTitle'),
                                response.html,
                                1
                        );
                    } else {
                        if (response.messages.length > 0) {
                            MessageObj.clear();
                            response.messages.each(function (msg) {
                                MessageObj['add' + msg.type[0].toUpperCase() + msg.type.slice(1)](msg.text);
                            });
                        }
                    }
                }
            });
        },

        unmapFromNewAsin: function (productsIds, callback)
        {
            var self = this;

            self.templateProductTypeHandler.gridHandler.unselectAll();

            new Ajax.Request(M2ePro.url.get('amazon_listing_product/unmapFromAsin'), {
                method: 'post',
                parameters: {
                    products_ids: productsIds,
                    listing_id: self.listingId
                },
                onSuccess: function (transport) {

                    if (!transport.responseText.isJSON()) {
                        self.alert(transport.responseText);
                        return;
                    }

                    var response = transport.responseText.evalJSON();

                    if (response.type == 'success') {
                        callback(productsIds);
                    }
                }
            });
        },

        mapToProductType: function (el, productTypeId, mapToGeneralId) {
            var self = this;

            new Ajax.Request(M2ePro.url.get('amazon_listing_product_template_productType/assign'), {
                method: 'post',
                parameters: {
                    products_ids: ListingGridObj.templateProductTypeHandler.productTypePopup.productsIds,
                    product_type_id: productTypeId
                },
                onSuccess: function (transport) {
                    if (!transport.responseText.isJSON()) {
                        self.alert(transport.responseText);
                        return;
                    }

                    var response = transport.responseText.evalJSON();
                    self.mapToNewAsin(response.products_ids);
                }
            });

            ListingGridObj.templateProductTypeHandler.productTypePopup.modal('closeModal');
        },

        checkCategoryProducts: function (url) {
            var self = this;

            new Ajax.Request(M2ePro.url.get('amazon_listing_product_add/checkNewAsinCategoryProducts'), {
                method: 'post',
                onSuccess: function (transport) {

                    if (transport.responseText == 1) {
                        setLocation(url);
                    } else {
                        if (!transport.responseText.isJSON()) {
                            self.alert(transport.responseText);
                            return;
                        }

                        var response = transport.responseText.evalJSON();

                        MessageObj.clear();
                        MessageObj['add' + response.type[0].toUpperCase() + response.type.slice(1)](response.text);
                    }
                }
            });
        },

        checkManualProducts: function (url) {
            var self = this;

            new Ajax.Request(M2ePro.url.get('amazon_listing_product_add/checkNewAsinManualProducts'), {
                method: 'post',
                onSuccess: function (transport) {

                    if (transport.responseText == 1) {
                        setLocation(url);
                    } else {
                        if (!transport.responseText.isJSON()) {
                            self.alert(transport.responseText);
                            return;
                        }

                        var response = transport.responseText.evalJSON();

                        if ($('new_asin_skip_popup_content')) {
                            $('new_asin_skip_popup_content').remove();
                        }
                        $('html-body').insert({bottom: response.html});

                        $('total_count').innerHTML = response.total_count;
                        $('failed_count').update(response.failed_count);

                        self.skipPopup = jQuery('#new_asin_skip_popup_content');

                        modal({
                            title: M2ePro.translator.translate('setProductType'),
                            type: 'popup',
                            buttons: [ {
                                text: M2ePro.translator.translate('Cancel'),
                                class: 'action-secondary action-dismiss',
                                click: function () {
                                    self.skipPopup.modal('closeModal');
                                }
                            }, {
                                text: M2ePro.translator.translate('Continue'),
                                class: 'action-primary action-accept',
                                click: function () {
                                    setLocation(response.continueUrl);
                                    self.skipPopup.modal('closeModal');
                                }
                            }]
                        }, self.skipPopup);

                        self.skipPopup.modal('openModal');
                    }
                }
            });
        },

        //----------------------------------------

        stepNewAsinBack: function()
        {
            var self = this;
            self.unmapFromNewAsin(
                null,
                function(productsIds) {
                    new Ajax.Request(M2ePro.url.get('amazon_listing_product_add/resetProductTypeTemplate'), {
                        method: 'post',
                        parameters: {
                            listing_id: self.listingId
                        },
                        onSuccess: function (transport) {
                            var response = transport.responseText.evalJSON();
                            setLocation(response.back_url);
                        }
                    });
                }
            )
        }

        // ---------------------------------------
    });

});

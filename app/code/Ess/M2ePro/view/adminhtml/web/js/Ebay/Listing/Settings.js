define([
    'M2ePro/Common'
], function() {
    window.EbayListingSettings = Class.create(Common, {

        // ---------------------------------------

        initialize: function() {
        },

        initObservers: function() {
            $('template_shipping_id').observe('change', function() {
                if ($('template_shipping_id').value) {
                    $('edit_shipping_template_link').show();
                } else {
                    $('edit_shipping_template_link').hide();
                }
            });
            $('template_shipping_id').simulate('change');

            $('template_shipping_id').observe('change', function() {
                EbayListingSettingsObj.hideEmptyOption(this);
            });
            if ($('template_shipping_id').value) {
                $('template_shipping_id').simulate('change');
            }

            $('template_return_policy_id').observe('change', function() {
                if ($('template_return_policy_id').value) {
                    $('edit_return_policy_template_link').show();
                } else {
                    $('edit_return_policy_template_link').hide();
                }
            });
            $('template_return_policy_id').simulate('change');

            $('template_return_policy_id').observe('change', function() {
                EbayListingSettingsObj.hideEmptyOption(this);
            });
            if ($('template_return_policy_id').value) {
                $('template_return_policy_id').simulate('change');
            }

            $('template_selling_format_id').observe('change', function() {
                if ($('template_selling_format_id').value) {
                    $('edit_selling_format_template_link').show();
                } else {
                    $('edit_selling_format_template_link').hide();
                }
            });
            $('template_selling_format_id').simulate('change');

            $('template_selling_format_id').observe('change', function() {
                EbayListingSettingsObj.checkSellingFormatMessages();
                EbayListingSettingsObj.hideEmptyOption(this);
            });
            if ($('template_selling_format_id').value) {
                $('template_selling_format_id').simulate('change');
            }

            $('template_description_id').observe('change', function() {
                if ($('template_description_id').value) {
                    $('edit_description_template_link').show();
                } else {
                    $('edit_description_template_link').hide();
                }
            });
            $('template_description_id').simulate('change');

            $('template_description_id').observe('change', function() {
                EbayListingSettingsObj.hideEmptyOption(this);
            });
            if ($('template_description_id').value) {
                $('template_description_id').simulate('change');
            }

            $('template_synchronization_id').observe('change', function() {
                if ($('template_synchronization_id').value) {
                    $('edit_synchronization_template_link').show();
                } else {
                    $('edit_synchronization_template_link').hide();
                }
            });
            $('template_synchronization_id').simulate('change');

            $('template_synchronization_id').observe('change', function() {
                EbayListingSettingsObj.hideEmptyOption(this);
            });
            if ($('template_synchronization_id').value) {
                $('template_synchronization_id').simulate('change');
            }
        },

        // ---------------------------------------

        saveClick: function(url) {
            if (typeof categories_selected_items != 'undefined') {
                array_unique(categories_selected_items);

                var selectedCategories = implode(',', categories_selected_items);

                $('selected_categories').value = selectedCategories;
            }

            if (typeof url == 'undefined' || url == '') {
                url = M2ePro.url.formSubmit + 'back/' + base64_encode('list') + '/';
            }

            this.submitForm(url);
        },

        // ---------------------------------------

        checkSellingFormatMessages: function() {
            var storeId = $('store_id').value;
            var marketplaceId = $('marketplace_id').value;

            if (storeId.empty() || storeId < 0 || marketplaceId.empty() || marketplaceId < 0) {
                return;
            }

            var id = $('template_selling_format_id').value,
                nick = 'selling_format',
                storeId = storeId,
                marketplaceId = marketplaceId,
                container = 'template_selling_format_messages',
                callback = function() {
                    var refresh = $(container).down('a.refresh-messages');
                    if (refresh) {
                        refresh.observe('click', function() {
                            this.checkSellingFormatMessages();
                        }.bind(this));
                    }
                }.bind(this);

            TemplateManagerObj.checkMessages(
                id,
                nick,
                '',
                storeId,
                marketplaceId,
                container,
                callback
            );
        },

        // ---------------------------------------

        reload: function(url, id) {
            new Ajax.Request(url, {
                asynchronous: false,
                onSuccess: function(transport) {

                    var data = transport.responseText.evalJSON(true);

                    var options = '';

                    var firstItemValue = '';
                    var currentValue = $(id).value;

                    data.each(function(paris) {
                        var key = (typeof paris.key != 'undefined') ? paris.key : paris.id;
                        var val = (typeof paris.value != 'undefined') ? paris.value : paris.title;
                        options += '<option value="' + key + '">' + val + '</option>\n';

                        if (firstItemValue == '') {
                            firstItemValue = key;
                        }
                    });

                    $(id).update();
                    $(id).insert(options);

                    if (currentValue != '') {
                        $(id).value = currentValue;
                    } else {
                        if (M2ePro.formData[id] > 0) {
                            $(id).value = M2ePro.formData[id];
                        } else {
                            $(id).value = firstItemValue;
                        }
                    }

                    $(id).simulate('change');
                }
            });
        },

        // ---------------------------------------

        addNewTemplate: function(url, callback) {
            var win = window.open(url);

            var intervalId = setInterval(function() {
                if (!win.closed) {
                    return;
                }

                clearInterval(intervalId);

                callback && callback();

            }, 1000);
        },

        editTemplate: function(url, id, callback) {
            var win = window.open(url + 'id/' + id);

            var intervalId = setInterval(function() {
                if (!win.closed) {
                    return;
                }

                clearInterval(intervalId);

                callback && callback();

            }, 1000);
        },

        // ---------------------------------------

        newShippingTemplateCallback: function() {
            var noteEl = $('template_shipping_note');

            EbayListingSettingsObj.reload(M2ePro.url.get('getShippingTemplates'), 'template_shipping_id');
            if ($('template_shipping_id').children.length > 0) {
                $('template_shipping_id').show();
                noteEl && $('template_shipping_note').show();
                $('template_shipping_label').hide();
            } else {
                $('template_shipping_id').hide();
                noteEl && $('template_shipping_note').hide();
                $('template_shipping_label').show();
            }
        },

        newReturnPolicyTemplateCallback: function() {
            var noteEl = $('template_return_policy_note');

            EbayListingSettingsObj.reload(M2ePro.url.get('getReturnPolicyTemplates'), 'template_return_policy_id');
            if ($('template_return_policy_id').children.length > 0) {
                $('template_return_policy_id').show();
                noteEl && $('template_return_policy_note').show();
                $('template_return_policy_label').hide();
            } else {
                $('template_return_policy_id').hide();
                noteEl && $('template_return_policy_note').hide();
                $('template_return_policy_label').show();
            }
        },

        newSellingFormatTemplateCallback: function() {
            var noteEl = $('template_selling_format_note');

            EbayListingSettingsObj.reload(M2ePro.url.get('getSellingFormatTemplates'), 'template_selling_format_id');
            if ($('template_selling_format_id').children.length > 0) {
                $('template_selling_format_id').show();
                noteEl && $('template_selling_format_note').show();
                $('template_selling_format_label').hide();
            } else {
                $('template_selling_format_id').hide();
                noteEl && $('template_selling_format_note').hide();
                $('template_selling_format_label').show();
            }
        },

        newDescriptionTemplateCallback: function() {
            var noteEl = $('template_description_note');

            EbayListingSettingsObj.reload(M2ePro.url.get('getDescriptionTemplates'), 'template_description_id');
            if ($('template_description_id').children.length > 0) {
                $('template_description_id').show();
                noteEl && $('template_description_note').show();
                $('template_description_label').hide();
            } else {
                $('template_description_id').hide();
                noteEl && $('template_description_note').hide();
                $('template_description_label').show();
            }
        },

        newSynchronizationTemplateCallback: function() {
            var noteEl = $('template_synchronization_note');

            EbayListingSettingsObj.reload(M2ePro.url.get('getSynchronizationTemplates'), 'template_synchronization_id');
            if ($('template_synchronization_id').children.length > 0) {
                $('template_synchronization_id').show();
                noteEl && $('template_synchronization_note').show();
                $('template_synchronization_label').hide();
            } else {
                $('template_synchronization_id').hide();
                noteEl && $('template_synchronization_note').hide();
                $('template_synchronization_label').show();
            }
        }

        // ---------------------------------------
    });
});

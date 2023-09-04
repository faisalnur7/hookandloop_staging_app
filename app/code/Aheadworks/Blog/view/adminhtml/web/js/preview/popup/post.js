define(
    [
        'jquery',
        'Magento_Ui/js/lib/spinner',
        'mage/template',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, spinner, mageTemplate, modal) {
        'use strict';

        return {

            /**
             * Popup container selector
             */
            popupContainerSelector: 'aw-blog-preview-popup',

            /**
             * Popup options data
             */
            popupOptions: {
                autoOpen: true,
                responsive: true,
                clickableOverlay: false,
                innerScroll: true,
                modalClass: 'aw-blog-post-preview-modal',
                title: $.mage.__('Post Preview'),
                buttons: [{
                    text: $.mage.__('Ok'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }]
            },

            /**
             * Preview template to display inside popup container
             */
            previewTemplate: mageTemplate(
                '<iframe id="aw-blog-post-view-iframe" src="<%- post_view_url %>"></iframe>'
            ),

            /**
             * Create popup with post preview iframe
             *
             * @param previewUrl
             * @param postData
             * @returns {boolean}
             */
            create: function (previewUrl, postData) {
                var self = this;

                spinner.show();

                $.ajax({
                    url: previewUrl,
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data: postData
                    },
                    success: function(response) {
                        spinner.hide();

                        if (response.error) {
                            self.onError(response.message);
                            return false;
                        }

                        self.showPopup(response.url)
                        return true;
                    }
                });
            },

            /**
             * Show preview popup
             *
             * @param postViewUrl
             */
            showPopup: function (postViewUrl) {
                var popupContainer = $('<div>', {
                    'id': this.popupContainerSelector
                });
                this.removeOldPopups();
                popupContainer.empty();
                modal(this.popupOptions, popupContainer);
                popupContainer.html(
                    this.previewTemplate(
                        {
                            post_view_url: postViewUrl
                        }
                    )
                );

                popupContainer.modal('openModal');
                popupContainer.trigger('contentUpdated');
            },

            /**
             * Remove old popups
             */
            removeOldPopups: function() {
                var modals = $('.' + this.popupOptions.modalClass);

                if (modals.length) {
                    modals.each(function() {
                        $(this).remove();
                    });
                }
            },

            /**
             * Ajax request error handler
             *
             * @param errorMessage
             */
            onError: function (errorMessage) {
                alert({
                    content: $.mage.__(errorMessage),
                });
            }
        };
    }
);

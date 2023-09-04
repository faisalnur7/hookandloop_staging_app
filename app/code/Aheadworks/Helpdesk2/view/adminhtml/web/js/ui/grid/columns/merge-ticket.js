define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    'text!Aheadworks_Helpdesk2/template/ui/form/merge/ticket-selection.html',
    'Magento_Ui/js/modal/alert',
    'Aheadworks_Helpdesk2/js/model/ticket/merge-confirm',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, ticketSelectTemplate, malert, ticketMergeConfirm) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {'data-grid-html-cell': true},
            ticketInfoUrl: '',
            selectTicketsUrl: '',
            mergeTicketsUrl: '',
            mainTicketInfo: {}
        },

        /**
         * Get label
         *
         * @param row
         * @returns {*}
         */
        getLabel: function (row) {
            return row[this.index + '_html']
        },

        /**
         * Get entity id
         *
         * @param row
         * @returns {*}
         */
        getEntityId: function (row) {
            return row[this.index + '_entityId'];
        },

        /**
         * Get ticket uid
         *
         * @param row
         * @returns {*}
         */
        getTicketUid: function (row) {
            return row[this.index + '_ticket_uid'];
        },

        /**
         * Process tickets select
         *
         * @param row
         */
        processTicketsSelect: function (row) {
            var self = this;
            $.ajax({
                url: this.ticketInfoUrl,
                type: "POST",
                showLoader: true,
                data: {form_key: window.FORM_KEY, entity_id: this.getEntityId(row)},
                complete: function (response) {
                    var ticketInfo = response.responseJSON.data;
                    self.mainTicketInfo = ticketInfo.main_ticket;
                    self.callTicketSelectModal(ticketInfo, row);
                },
                done: function (response) {
                    return true;
                },
                error: function (response) {
                    console.log(JSON.parse(JSON.stringify(response.responseText)));
                }
            });
        },

        /**
         * Get selected tickets input value
         *
         * @param row
         * @returns {string}
         */
        getSelectedTicketsInputVal: function (row) {
            var entityId = this.getEntityId(row);
            var inputVal = $('input[name=merge_tickets-'+ entityId +']').val();
            return inputVal.trim();
        },

        /**
         * Show message
         *
         * @param title
         * @param content
         */
        showMessage: function (title, content) {
            malert({
                title: title,
                content: content,
                clickableOverlay: false,
                actions: {
                    always: function () {}
                }
            });
        },

        /**
         * Clear old popups
         */
        clearOldPopups: function () {
            $('.merge-ticket-form').remove();
        },

        /**
         * Call ticket select modal
         *
         * @param ticketInfo
         * @param row
         */
        callTicketSelectModal: function (ticketInfo, row) {
            var self = this;
            var entityId = this.getEntityId(row);
            var modalHtml = mageTemplate(ticketSelectTemplate, {
                ticketInfo: ticketInfo,
                entityId: entityId,
                linkText: $.mage.__('Go to Details Page')
            });
            this.clearOldPopups();
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []
            }).trigger('openModal');

            $('input[name=merge_tickets-'+ entityId +']').val('');
            $('[data-button-tm-id="' + entityId + '"]').click(function () {
                if (self.getSelectedTicketsInputVal(row).length > 0) {
                    ticketMergeConfirm.setProcessUrls(self.selectTicketsUrl, self.mergeTicketsUrl);
                    ticketMergeConfirm.processTicketsConfirm(
                        self.getTicketUid(row),
                        self.getEntityId(row),
                        self.getSelectedTicketsInputVal(row),
                        previewPopup
                    );
                } else {
                    self.showMessage($.mage.__('Warning'), $.mage.__('Input field must be filled!'))
                }
            });

            $('.add-to-merge-list-button').click(function () {
                var mergeInput = $('input[name=merge_tickets-'+ entityId +']');
                var inputVal = mergeInput.val();
                var uid = $(this).text();
                if (inputVal.length > 0) {
                    var inputUidList = inputVal.split(',');
                    if ($.inArray(uid, inputUidList) === -1) {
                        inputVal = inputVal + ',' + uid;
                    }
                } else {
                    inputVal = uid;
                }
                mergeInput.val(inputVal);
            });
        },

        /**
         * Preview
         *
         * @param row
         */
        preview: function (row) {
            this.processTicketsSelect(row);
        },

        /**
         * Get field handler
         *
         * @param row
         * @returns {*}
         */
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        },
    });
});

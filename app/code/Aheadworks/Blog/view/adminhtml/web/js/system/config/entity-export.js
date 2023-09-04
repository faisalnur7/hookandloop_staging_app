define([
    'underscore',
    "jquery",
    "mage/mage",
    "mage/validation",
    'mage/translate'
], function(_, $) {
    "use strict";

    $.widget('mage.awBlogEntityExport', {
        options: {
            parentContainerSelector: '#aw_blog_entity_export',
            exportFilterGridSelector: '.export_filter_grid',
            exportFilesSelector: '.aw_blog_export_files',
            messageContainerSelector: 'export-message',
            entitySelector: '#aw_blog_entity_export_entity',
            durationMessages: 3000
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this.moveFilesGrid();
            this._bind(this.element);
            this.createMessageContainer();
            this.preselectEntity();
        },

        /**
         * Preselect an object to show the grid
         */
        preselectEntity: function () {
            $(this.options.entitySelector).trigger('change');
        },

        /**
         * Create message container
         */
        createMessageContainer: function () {
            var messageContainer = $('<div>', {
                id: this.options.messageContainerSelector
            });

            $(this.options.parentContainerSelector).prepend(messageContainer);
        },

        /**
         * Change entity type
         *
         * @param event
         */
        changeEntityType: function(event) {
            var element = $(event.target);
            $.ajax({
                url: this.options.action + 'entity/' + element.val(),
                type: 'POST',
                data: {
                    'form_key': FORM_KEY
                },
                showLoader: true,
                success: function(result) {
                    if (!result.error && result) {
                        result = $(result).wrap('<div>', {
                            class: 'export_filter_form'
                        });
                        var exportGrid = element.parents(this.options.parentContainerSelector)
                            .find(this.options.exportFilterGridSelector);

                        if (!exportGrid.length) {
                            exportGrid = $('<div>', {
                                'class': 'export_filter_grid'
                            });
                            exportGrid.insertBefore('.aw_blog_export_files');
                        }

                        exportGrid.html(result);

                        exportGrid.append(`<button type="button" class="continue-button">${$.mage.__('Export')}</button>`);
                    } else if (result.error) {
                        this.showMessage(result.error, this.options.durationMessages, 'error');

                    }
                }.bind(this),
                error: function (error) {
                    this.showMessage(error.responseText, this.options.durationMessages, 'error');
                }.bind(this)
            });
        },

        /**
         * Show message after ajax
         */
        showMessage: function(message, duration, type) {
            switch (type) {
                case 'error':
                    $('#export-message').addClass('message message-error error').html(message).show();
                    break
                case 'success':
                    $('#export-message').addClass('message message-success success').html(message).show();
                    break
            }
            _.debounce(this.hideMessage, duration)();
        },

        /**
         * Hide message after showed message
         */
        hideMessage: function() {
            $('#export-message').attr('class', '').hide();
        },

        /**
         * Event binding
         */
        _bind: function (element) {
            $(element).on('change', this.changeEntityType.bind(this));
            $(document).on('click', '.continue-button', this.submitTableForm.bind(this));
        },

        /**
         * Move grid files in parent container
         */
        moveFilesGrid: function() {
            $(this.options.exportFilesSelector).appendTo(this.options.parentContainerSelector);
        },

        /**
         * Create table form
         */
        createTableForm: function() {
            if (!$('#export_filter_form').length) {
                var form = jQuery('<form>', {action: this.options.actionExport, method: 'post', id:"export_filter_form"});
                jQuery('.export_filter_grid').wrap(form);
            }

            return $('#export_filter_form');
        },

        /**
         * Submit table form
         */
        submitTableForm: function() {
            var form = this.createTableForm();
            form.mage('validation', {});

            this.addEntityInput(form);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                showLoader: true,
                success: function(result) {
                    if (result && result.success) {
                        this.showMessage(result.success, this.options.durationMessages, 'success');
                    } else if (result.error) {
                        this.showMessage(result.error, this.options.durationMessages, 'error');
                    }
                }.bind(this),
                error: function (error) {
                    this.showMessage(error.responseText, this.options.durationMessages, 'error');
                }.bind(this)
            });
        },

        /**
         * Add entity input in form
         */
        addEntityInput: function(form) {
            var entityInputVal = $(this.options.entitySelector).val(),
                inputEntity = $('<input>', {
                    name: 'entity',
                    type: 'hidden'
                });

            if (!form.find('input[name=entity]').length) {
                form.append(inputEntity);
            }

            inputEntity = form.find('input[name=entity]');
            inputEntity.val(entityInputVal);
        }
    });

    return $.mage.awBlogEntityExport;
});
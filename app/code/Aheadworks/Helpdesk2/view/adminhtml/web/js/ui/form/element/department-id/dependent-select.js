define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, Select) {
    'use strict';

    return Select.extend({
        prevValue: null,

        setInitialValue: function () {
            this._super();
            this.on('value', this.onValueChange.bind(this));
            this.prevValue = this.value();

            return this;
        },

        /**
         * Handler for change value
         */
        onValueChange: function() {
            var value = this.value();
            var agentIdDataProvider = uiRegistry.get(this.provider.concat(".", 'agent_id'));

            if (value !== null && value !== this.prevValue && agentIdDataProvider) {
                var data = [];
                _.each(agentIdDataProvider.initialOptions, function(initialOption) {
                    _.each(initialOption.department_ids, function(department_id) {
                        if (department_id == value) {
                            data.push(initialOption);
                        }
                    });
                });

                agentIdDataProvider.setOptions(data);
            }
            this.prevValue = value;
        }
    });
});

var config = {
   /* map: {
        '*': {
            ajaxQty: 'Exinent_TaxExempt/js/customscript'
        }
    }*/

    config: { 
        mixins: { 
            'Magento_Checkout/js/model/step-navigator': { 
                'Exinent_TaxExempt/js/model/step-navigator-mixin': true 
            } 
        } 
    } 
};
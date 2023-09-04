define([
    'jquery',
    'Aheadworks_Blog/js/lazyload'
], function($) {
    return function(config, element)
    {
        $(element).lazyload({
            effect : "fadeIn"
        });
    };
});

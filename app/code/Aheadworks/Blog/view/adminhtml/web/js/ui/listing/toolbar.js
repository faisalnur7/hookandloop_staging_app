define([
    'jquery',
    'Magento_Ui/js/grid/toolbar'
], function ($, Toolbar) {
    return Toolbar.extend({
        defaults: {
            isDocumentHeightChanged: false
        },

        onWindowScroll: function () {
            var scrollTop = window.pageYOffset,
                scrollLeft = window.pageXOffset;

            if (this._wScrollTop !== scrollTop) {
                if (this.canWindowScrollToTop()) {
                    this._wScrollTop = scrollTop;
                    this.onWindowScrollTop(scrollTop);
                }
            }

            if (this._wScrollLeft !== scrollLeft) {
                this._wScrollLeft = scrollLeft;

                this.onWindowScrollLeft(scrollLeft);
            }

            this._scrolled = false;
        },

        /**
         * Check if Can Window Scroll to Top
         */
        canWindowScrollToTop: function () {
            var documentHeight = $(document).height() - $(window).height(),
                result = false;

            if (this._wScrollTop > documentHeight) {
                this.isDocumentHeightChanged = true;
            } else if(this.isDocumentHeightChanged) {
                this.isDocumentHeightChanged = false;
            } else {
                result = true;
            }

            return result;
        }
    });
});

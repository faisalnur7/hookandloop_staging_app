define([
    'jquery'
], function ($) {
    "use strict";

    $.widget('mage.awBlogPostViewToggle', {
        listingItemSelector: ".blog-posts",
        gridViewClass: 'grid-view',
        urlParameter: 'post_list_mode',
        paginationItemsSelector: ".blog-pagination a",

        /**
         * Initialize widget
         */
        _create: function () {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function () {
            this._on({'click button': this._toggleView});
        },

        /**
         * On toggle view event handler.
         *
         * @param {Event} event
         * @private
         */
        _toggleView: function (event) {
            var viewMode = $(event.currentTarget).data('view-mode'),
                $blogPosts = $(this.element).parent().find(this.listingItemSelector);

            if (!$(event.currentTarget).hasClass('active')) {
                $blogPosts.toggleClass(this.gridViewClass);
                $(event.delegateTarget).find('button').removeClass('active');
                $(event.currentTarget).addClass('active');
                $('.blog-post-lazyload-image').trigger('appear');

                this._updateLinks(viewMode);
            }
        },

        /**
         * Update links with new view mode
         *
         * @param {String} viewMode
         * @private
         */
        _updateLinks: function (viewMode) {
            var self = this,
                newCurrentUrl,
                $blogPaginationItems = $(this.element).parent().find(this.paginationItemsSelector);

            if (history.pushState) {
                newCurrentUrl = this._modifyUrl(window.location.href, viewMode);
                history.pushState(null, null, newCurrentUrl);
            }

            $blogPaginationItems.each(function() {
                var url = $(this).attr('href'),
                    newUrl = self._modifyUrl(url, viewMode);

                $(this).attr('href', newUrl);
            })
        },

        /**
         * Modify url with view new view mode parameter
         *
         * @param {String} url
         * @param {String} viewMode
         * @return {string}
         * @private
         */
        _modifyUrl: function (url, viewMode) {
            url = new URL(url);
            url.searchParams.set(this.urlParameter, viewMode);
            return url.toString();
        }
    });

    return $.mage.awBlogPostViewToggle;
});

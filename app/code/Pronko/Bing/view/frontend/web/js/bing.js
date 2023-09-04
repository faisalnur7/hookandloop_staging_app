/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
/*browser:true*/
/*global define*/
define([], function() {
    'use strict';

    return function (options) {
        window.uetq = window.uetq || [];
        window.uetq.push({'gv': options.amount});
    }
});
var config = {
    map: {
        '*': {
            blogPostMetaCharCount:  'Aheadworks_Blog/js/post/char-count',
            wordpressImport:  'Aheadworks_Blog/js/system/config/wordpress-import',
            entityImport:  'Aheadworks_Blog/js/system/config/entity-import',
            entityExport:  'Aheadworks_Blog/js/system/config/entity-export',
            blogCategoryTree:  'Aheadworks_Blog/js/category-tree',
            widgetInstance:  'Aheadworks_Blog/js/widget-instance'
        }
    },
    shim: {
        'jquerytokenize':           ['jquery'],
        'jquerytree':               ['jquery']
    },
    paths: {
        'jquerytokenize':           'Aheadworks_Blog/js/lib/jquery.tokenize',
        'jquerytree':               'Aheadworks_Blog/js/lib/jstree'
    }
};

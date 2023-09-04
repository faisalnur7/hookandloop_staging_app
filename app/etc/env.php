<?php
return [
    'backend' => [
        'frontName' => 'admnhnl'
    ],
    'crypt' => [
        'key' => '3f6fc0c9760b1e04a7317f6808362c1d'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'db',
                'dbname' => 'magento',
                'username' => 'magento',
                'password' => 'magento',
                'active' => '1',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'db'
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'translate' => 1,
        'config_webservice' => 1,
        'compiled_config' => 1,
        'fishpig_wordpress' => 1,
        'wp_gtm_categories' => 1,
        'google_product' => 0,
        'vertex' => 1
    ],
    'install' => [
        'date' => 'Mon, 05 Nov 2018 09:04:10 +0000'
    ],
    'downloadable_domains' => [
        'www.hookandloop.com'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'f2e_'
            ],
            'page_cache' => [
                'id_prefix' => 'f2e_'
            ]
        ],
        'allow_parallel_generation' => false,
        'graphql' => [
            'id_salt' => 'Wk6s2isXgbC9yx5zsgVhkbWWSkE4rj2V'
        ]
    ],
    'lock' => [
        'provider' => 'db'
    ],
    'directories' => [
        'document_root_is_pub' => true
    ]
];

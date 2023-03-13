<?php

// config for Labdacaraka/WebInstaller
return [
    /**
     * Marketplace Settings
     * Supported: envato
     */
    'marketplace' => [
        'envato' => [
            'sandbox' => env('ENVATO_SANDBOX', true),
            'api_url' => 'https://api.envato.com/v3/market/',
            'sandbox_url' => 'https://sandbox.bailey.sh/v3/market/',
            'verify_endpoint' => 'author/sale?code=',
            'purchase_code' => env('ENVATO_PURCHASE_CODE'),
            'item_id' => env('ENVATO_ITEM_ID'),
            'username' => env('ENVATO_USERNAME'),
            'token' => env('ENVATO_PERSONAL_TOKEN', 'vWHYVzh043De2aehXh7pgmbpNSJDmUdW'),
            'sandbox_token' => 'cFAKETOKENabcREPLACEMExyzcdefghj',
        ],
    ],

    /**
     * Setting Requirements
     */
    'minimum_php_version' => '8.1',
    'required_php_extensions' => [
        'openssl',
        'mbstring',
        'tokenizer',
        'xml',
        'ctype',
        'json',
        'curl',
        'gd',
        'fileinfo',
    ],
    'required_php_settings' => [
        'max_execution_time' => 300,
        'memory_limit' => '256M',
        'upload_max_filesize' => '20M',
        'post_max_size' => '100M',
        'max_input_vars' => 1000,
    ],

    /**
     * Setting directory Permissions
     */
    'writeable_directories' => [
        'storage',
        'bootstrap/cache',
    ],

    'available_environments' => [
        'local',
        'development',
        'staging',
        'production',
    ],

    'default_login_accounts' => [
        [
            'email' => 'admin@labdacaraka.com',
            'password' => '123123',
            'name' => 'Admin',
        ],
    ],

];

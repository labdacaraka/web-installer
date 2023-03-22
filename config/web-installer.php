<?php

// config for Labdacaraka/WebInstaller
return [
    /**
     * Marketplace Settings
     * Used for checking purchase code
     * Marketplace: Envato
     */
    'marketplace' => [
        'envato' => [
            'sandbox' => env('ENVATO_SANDBOX', false),
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
     * Used for checking requirements before installation
     * PHP Version, PHP Extensions, PHP Settings
     * Example: 'minimum_php_version' => '7.4', 'required_php_extensions' => ['openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'curl', 'gd', 'fileinfo']
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
     * Used for writing permissions to directories
     * Example: storage, bootstrap/cache
     */
    'writeable_directories' => [
        'storage',
        'bootstrap/cache',
    ],

    /**
     * Available environments
     * Example: Local, Development, Staging, Production
     */
    'available_environments' => [
        'local',
        'development',
        'staging',
        'production',
    ],

    /**
     * Project initialization commands
     * key: command name, value: array of arguments
     *
     * Example: 'config:cache' => ['--env' => 'production']
     * Example: 'route:cache' => []
     */
    'project_init_commands' => [
        'optimize:clear' => [],
        'migrate:fresh' => [],
        'db:seed' => [],
        'storage:link' => [],
        'optimize' => [],
    ],

    /**
     * Default login accounts
     * Used for showing default login accounts after installation
     */
    'default_login_accounts' => [
        [
            'email' => 'admin@labdacaraka.com',
            'password' => '123123',
            'name' => 'Admin',
        ],
    ],

];

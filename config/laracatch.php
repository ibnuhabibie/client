<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Disabled Environments
    |--------------------------------------------------------------------------
    |
    | Environments where Laracatch is disabled. This value
    | has precedence over the enabled value below.
    |
    */

    'disabled_environments' => ['testing'],

    /*
    |--------------------------------------------------------------------------
    | Laracatch Settings
    |--------------------------------------------------------------------------
    |
    | Laracatch is enabled by default, when debug is set to true in app.php.
    | You can override the value by setting enabled to true or false.
    |
    */

    'enabled' => env('LARACATCH_ENABLED', null),

    /*
    |--------------------------------------------------------------------------
    | Code Editor
    |--------------------------------------------------------------------------
    |
    | Choose your preferred editor to use when clicking any edit button.
    |
    | Supported: "phpstorm", "vscode", "vscode-insiders",
    |            "sublime", "atom"
    |
    */

    'code_editor' => env('LARACATCH_EDITOR', 'phpstorm'),

    /*
    |--------------------------------------------------------------------------
    | Share
    |--------------------------------------------------------------------------
    |
    | You can share local errors with colleagues or others around the world.
    |
    | If necessary, you can completely disable sharing below.
    |
    */

    'share' => env('LARACATCH_SHARING', true),

    /*
    |--------------------------------------------------------------------------
    | Sharing Url
    |--------------------------------------------------------------------------
    |
    | If you decide to set up your own server to share
    | the errors, you can specify its base url below.
    |
    */

    'share_url' => env('LARACATCH_SHARING_BASE_URL', 'https://laracatch.com/api'),

     /*
     |--------------------------------------------------------------------------
     | Storage settings
     |--------------------------------------------------------------------------
     |
     | Laracatch stores data for session/ajax requests and commands/jobs.
     | By default, file storage (in the storage folder) is used. Redis and PDO
     | can also be used. For PDO, run the package migrations first.
     |
     */

    'storage' => [
        'enabled'    => env('LARACATCH_STORAGE_ENABLED', true),
        'driver'     => 'file', // file, pdo, redis
        'path'       => storage_path('laracatch'), // For file driver
        'connection' => null,   // Leave null for default connection (Redis/PDO)
        'retention'  => 24, // 24 hours
    ],

     /*
     |--------------------------------------------------------------------------
     | DataCollectors
     |--------------------------------------------------------------------------
     |
     | Enable/disable DataCollectors
     |
     */

    'collectors' => [
        'dumps' => true,
        'breadcrumbs' => true,
        'logs' => true,
        'queries' => true,
        'events' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Provider Options
    |--------------------------------------------------------------------------
    |
    | These options determine which sensible information will be
    | collected and optionally transmitted to Laracatch.
    |
    */

    'data_providers' => [
        'anonymize_client_ip' => false,
        'collect_git_information' => false,
        'report_query_bindings' => true,
        'report_view_data' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Map Remote Paths
    |--------------------------------------------------------------------------
    |
    | If you are using a remote dev server, like Laravel Homestead, Docker, or
    | even a remote VPS, it will be necessary to specify your path mapping.
    |
    | "remote" is an absolute base path for your sites or projects
    | in Homestead, Vagrant, Docker, or another remote development server.
    |
    | Example value: "/home/vagrant/code"
    |
    | "local" is an absolute base path for your sites or projects
    | on your local computer where your IDE or code editor is running on.
    |
    | Example values: "/Users/<name>/code", "C:\Users\<name>\Documents\Code"
    |
    */

    'file_paths' => [
        'remote' => env('LARACATCH_REMOTE_FILE_PATH', ''),
        'local' => env('LARACATCH_LOCAL_FILE_PATH', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Laracatch Endpoint prefix
    |--------------------------------------------------------------------------
    |
    | Laracatch registers a couple of routes when it is enabled. Below you may
    | specify a route prefix that will be used to host all internal links.
    |
    */

    'route_prefix' => '_laracatch',

    'theme' => env('LARACATCH_THEME', 'light')
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    |
    | The name of the database table where captured emails will be stored.
    |
    */
    'table' => env('EMAIL_PREVIEW_TABLE', 'captured_emails'),

    /*
    |--------------------------------------------------------------------------
    | Retention Days
    |--------------------------------------------------------------------------
    |
    | The number of days to retain captured emails before they are
    | automatically cleaned up by the cleanup command.
    |
    */
    'retention_days' => env('EMAIL_PREVIEW_RETENTION_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes for viewing captured emails.
    |
    */
    'routes' => [
        'enabled' => env('EMAIL_PREVIEW_ROUTES_ENABLED', true),
        'prefix' => env('EMAIL_PREVIEW_ROUTE_PREFIX', 'internal/emails'),
        'name' => env('EMAIL_PREVIEW_ROUTE_NAME', 'internal.captured-emails'),
        'middleware' => explode(',', env('EMAIL_PREVIEW_MIDDLEWARE', 'auth,super_admin')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Cleanup
    |--------------------------------------------------------------------------
    |
    | Automatically cleanup old emails. Still requires the scheduler to be
    | configured in your app/Console/Kernel.php file.
    |
    */
    'auto_cleanup' => env('EMAIL_PREVIEW_AUTO_CLEANUP', false),
];

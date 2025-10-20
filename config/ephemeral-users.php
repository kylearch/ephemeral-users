<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Throw on Persist Attempt
    |--------------------------------------------------------------------------
    |
    | When true, an EphemeralPersistenceException will be thrown when code
    | attempts to persist an ephemeral user. Set to false in production if
    | you want to silently prevent persistence without breaking the app.
    |
    */

    'throw_on_persist' => env('EPHEMERAL_THROW_ON_PERSIST', true),

    /*
    |--------------------------------------------------------------------------
    | Log Persist Attempts
    |--------------------------------------------------------------------------
    |
    | When true, all attempts to persist ephemeral users will be logged.
    | This is useful for identifying code paths that need refactoring.
    |
    */

    'log_attempts' => env('EPHEMERAL_LOG_PERSIST', true),

    /*
    |--------------------------------------------------------------------------
    | Log Channel
    |--------------------------------------------------------------------------
    |
    | The log channel to use for ephemeral user logging. Defaults to 'stack'
    | but you can create a dedicated channel (e.g., 'upgrade') for better
    | organization of these specific log entries.
    |
    */

    'log_channel' => env('EPHEMERAL_LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Level
    |--------------------------------------------------------------------------
    |
    | The log level to use when logging ephemeral persist attempts.
    | Options: debug, info, notice, warning, error, critical, alert, emergency
    |
    | For custom "UPGRADE" level semantics, use 'info' with dedicated channel.
    |
    */

    'log_level' => env('EPHEMERAL_LOG_LEVEL', 'warning'),
];

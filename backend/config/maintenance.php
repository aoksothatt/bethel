<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TiDB Production Compatibility Diagnostic Feature Flag
    |--------------------------------------------------------------------------
    | Must be explicitly set to 'true' in production environment to enable.
    | Strictly defaults to false if missing or set to any other value.
    */
    'enable_tidb_diagnostic' => filter_var(env('ENABLE_TIDB_DIAGNOSTIC', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | One-Time Production Maintenance Feature Flag
    |--------------------------------------------------------------------------
    | Must be explicitly set to 'true' in production environment to enable.
    | Strictly defaults to false if missing or set to any other value.
    */
    'enable_one_time_import' => filter_var(env('ENABLE_ONE_TIME_IMPORT', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Configured Production Administrator Identity
    |--------------------------------------------------------------------------
    | Must be explicitly configured. Fails closed (rejects execution) if empty.
    | Zero fallback email is provided.
    */
    'admin_email' => env('ADMIN_EMAIL'),
];

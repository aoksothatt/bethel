<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Single Page Application Fallback)
|--------------------------------------------------------------------------
|
| Any web route (excluding /api/*) is passed to the Vue 3 Single Page Application
| if built into public/index.html, or returns the API status indicator.
|
*/

Route::get('/{any}', function () {
    $spaIndex = public_path('index.html');
    if (file_exists($spaIndex)) {
        return response()->file($spaIndex);
    }
    return response()->json([
        'app' => 'Team Jersey Tracker API',
        'version' => 'Season 2026',
        'status' => 'online',
        'endpoints' => [
            'public_squad' => '/api/players',
            'admin_login' => '/api/admin/login',
        ]
    ]);
})->where('any', '^(?!api).*$');

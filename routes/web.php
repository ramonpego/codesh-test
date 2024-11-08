<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return database connection status, last cron time up em memory usage
    return [
        'database' => \DB::connection()->getPdo() ? 'OK' : 'Failed',
        'last_cron' => \Cache::get('last_cron', 'Never'),
        'memory_usage' => memory_get_usage(true),
    ];
});

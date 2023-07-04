<?php

use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;

Route::view('/', 'template');

Route::prefix('dashboard')->group(function () {
    Route::post('geofencing', [ DashboardController::class, 'geofencing' ]);
    Route::post('records_sensors_day', [ DashboardController::class, 'recordsSensorsDay' ]);
});
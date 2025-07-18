<?php

use App\Http\Controllers\Metrics\MultiUserLicenseMetricsController;
use App\Http\Controllers\Metrics\QESMetricsController;
use App\Http\Controllers\Metrics\ValuationMetricsController;
use Illuminate\Support\Facades\Route;

Route::prefix('metrics')->group(function () {
    Route::prefix('qes')->group(function () {
        Route::get('total-unlocked-users', [QESMetricsController::class, 'totalUnlockedUsers']);
        Route::get('active-users', [QESMetricsController::class, 'activeUsers']);
        Route::get('total-signed-protocols', [QESMetricsController::class, 'totalSignedProtocols']);
        Route::get('signed-protocols-over-time', [QESMetricsController::class, 'signedProtocolsOverTime']);
    });

    Route::prefix('valuation')->group(function () {
        Route::get('total-unlocked-users', [ValuationMetricsController::class, 'totalUnlockedUsers']);
        Route::get('active-users', [ValuationMetricsController::class, 'activeUsers']);
        Route::get('total-valuations', [ValuationMetricsController::class, 'totalValuations']);
        Route::get('valuations-over-time', [ValuationMetricsController::class, 'valuationsOverTime']);
    });

    Route::prefix('multi-user-license')->group(function () {
        Route::get('total-sub-users', [MultiUserLicenseMetricsController::class, 'totalSubUsers']);
        Route::get('active-sub-users', [MultiUserLicenseMetricsController::class, 'activeSubUsers']);
        Route::get('protocols-signed-by-sub-users', [MultiUserLicenseMetricsController::class, 'protocolsSignedBySubUsers']);
    });
});


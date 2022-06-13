<?php

use Illuminate\Support\Facades\Route;
use Outl1ne\PageManager\Http\Controllers\PageManagerController;

Route::prefix('nova-vendor/page-manager')->group(function () {
    Route::get('/{type}/{slug}/fields', [PageManagerController::class, 'getFields']);
});

Route::delete('/nova-api/page-manager/{panelType}/{resourceType}/{locale}/{resourceId}/field/{fieldAttribute}', [PageManagerController::class, 'deleteFile']);

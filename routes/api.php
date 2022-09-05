<?php

use Illuminate\Support\Facades\Route;
use Outl1ne\PageManager\Http\Controllers\PageManagerController;

Route::prefix('nova-vendor/page-manager')->group(function () {
    Route::get('/{type}/{slug}/fields', [PageManagerController::class, 'getFields']);
});

Route::patch('/nova-api/page-manager/{panelType}/{resourceType}/{locale}/{resourceId}/update-fields', [PageManagerController::class, 'syncUpdateFields']);
Route::get('/nova-api/page-manager/{panelType}/{resourceType}/{locale}/{resourceId}/download/{fieldAttribute}', [PageManagerController::class, 'downloadFile']);
Route::delete('/nova-api/page-manager/{panelType}/{resourceType}/{locale}/{resourceId}/field/{fieldAttribute}', [PageManagerController::class, 'deleteFile']);

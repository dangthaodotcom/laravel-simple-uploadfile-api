<?php

use Dt\Media\Http\Controllers\UploadController;

Route::middleware(['api'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::middleware(['verified'])->group(function () {
                Route::patch('/upload', [UploadController::class, 'upload'])->name('media.upload');
            });
        });
    });
});




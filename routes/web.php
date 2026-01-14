<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorageController;

Route::get('/', function () {
    return view('icloud');
});

Route::prefix('api/storage')->group(function () {
    Route::get('items', [StorageController::class, 'index']);
    Route::post('folders', [StorageController::class, 'createFolder']);
    Route::post('upload', [StorageController::class, 'upload']);
});

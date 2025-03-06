<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\GoogleSheetController;
use App\Http\Controllers\API\ItemGenerationController;
use App\Http\Controllers\API\SheetController;

Route::get('/fetch', [GoogleSheetController::class, 'fetch']);
Route::get('/fetch/{count}', [GoogleSheetController::class, 'fetch']);
Route::get('/items/sheet-url', [SheetController::class, 'getSheetUrl']);
Route::post('/items/sheet-url', [SheetController::class, 'saveSheetUrl']);
Route::post('/items/generate', [ItemGenerationController::class, 'generate']);
Route::post('/items/clear', [ItemGenerationController::class, 'clear']);
Route::apiResource('items', ItemController::class);

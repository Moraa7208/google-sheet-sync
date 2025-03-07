<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['message' => 'Database connection successful!']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Database connection failed: ' . $e->getMessage()], 500);
    }
});

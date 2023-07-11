<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PassportAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::get('category', [CategoryController::class, 'index'])->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [PassportAuthController::class, 'logout']);
    Route::post('category', [CategoryController::class, 'create']);
});

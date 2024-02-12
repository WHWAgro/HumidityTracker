<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('signup', [App\Http\Controllers\ApiController::class, 'register']);
Route::post('login', [App\Http\Controllers\ApiController::class, 'login']);
Route::post('verify_otp', [App\Http\Controllers\ApiController::class, 'verify_otp']);
Route::post('create_password', [App\Http\Controllers\ApiController::class, 'create_password']);
Route::post('forgot_password', [App\Http\Controllers\ApiController::class, 'forgot_password']);
Route::post('resend_code', [App\Http\Controllers\ApiController::class, 'resend_code']);


Route::middleware('auth:api')->group( function () {
    Route::post('track_humidity', [App\Http\Controllers\ApiController::class, 'track_humidity']);
    Route::post('track_humidity_android', [App\Http\Controllers\ApiController::class, 'track_humidity_android']);
    Route::post('add_humidity', [App\Http\Controllers\ApiController::class, 'add_humidity']);
    Route::post('edit_humidity', [App\Http\Controllers\ApiController::class, 'edit_humidity']);
    Route::post('delete_humidity', [App\Http\Controllers\ApiController::class, 'delete_humidity']);
});

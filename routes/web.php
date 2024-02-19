<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('login');
});

// Auth::routes();

Route::get('send_temp_user_mail',[App\Http\Controllers\AuthController::class, 'send_temp_user_mail']);
Route::get('login',[App\Http\Controllers\AuthController::class, 'index']);
Route::post('login',[App\Http\Controllers\AuthController::class, 'login']);
Route::get('invitation',[App\Http\Controllers\AuthController::class, 'invitation']);
Route::post('invitation',[App\Http\Controllers\AuthController::class, 'accept_invitation']);
Route::get('verification_code',[App\Http\Controllers\AuthController::class, 'verification_code']);
Route::post('resend_code',[App\Http\Controllers\AuthController::class, 'resend_code']);
Route::post('verify_otp',[App\Http\Controllers\AuthController::class, 'verify_otp']);
Route::get('create_password',[App\Http\Controllers\AuthController::class, 'create_password']);
Route::post('create_password',[App\Http\Controllers\AuthController::class, 'create_password']);
Route::get('forgot_password',[App\Http\Controllers\AuthController::class, 'forgot_password']);
Route::post('forgot_password',[App\Http\Controllers\AuthController::class, 'forgot_password']);
Route::get('success',[App\Http\Controllers\AuthController::class, 'success']);
Route::post('logout',[App\Http\Controllers\AuthController::class, 'logout']);
Route::get('track_humidity',[App\Http\Controllers\AuthController::class, 'track_humidity']);
Route::post('track_humidity',[App\Http\Controllers\AuthController::class, 'track_humidity']);

Route::get('privacy-policy',[App\Http\Controllers\HomeController::class, 'privacy_policy']);
Route::get('terms-conditions',[App\Http\Controllers\HomeController::class, 'terms_conditions']);

Route::middleware('admin_auth')->group(function () {
    Route::get('field_names',[App\Http\Controllers\DashboardController::class, 'field_names']);
    Route::get('forecast',[App\Http\Controllers\DashboardController::class, 'index']);
    Route::post('forecast',[App\Http\Controllers\DashboardController::class, 'index']);
    Route::get('forecast_data',[App\Http\Controllers\DashboardController::class, 'forecast_data']);
    Route::post('forecast_data',[App\Http\Controllers\DashboardController::class, 'forecast_data']);
    Route::get('edit_humidity',[App\Http\Controllers\DashboardController::class, 'edit_humidity']);
    Route::post('update_humidity',[App\Http\Controllers\DashboardController::class, 'update_humidity']);
    Route::post('delete_data',[App\Http\Controllers\DashboardController::class, 'delete_data']);
    Route::get('export-csv', [App\Http\Controllers\DashboardController::class, 'exportCSV']);
    Route::get('export-csv-forecast', [App\Http\Controllers\DashboardController::class, 'exportCSV_forecast']);
    Route::post('graph_filter', [App\Http\Controllers\DashboardController::class, 'graph_filter']);
    Route::get('export-csv-resume', [App\Http\Controllers\DashboardController::class, 'exportCSV_resume']);
    Route::get('resume', [App\Http\Controllers\DashboardController::class, 'resume']);
});



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('migrate_fresh', function () {
    $exitCode = Artisan::call('migrate:fresh');
});
Route::get('migrate', function () {
    $exitCode = Artisan::call('migrate');
});

Route::get('passport_install', function () {
    $exitCode = Artisan::call('passport:install');
});

Route::get('make_mail', function () {
    $exitCode = Artisan::call('make:mail TemporaryUsersMail');
});

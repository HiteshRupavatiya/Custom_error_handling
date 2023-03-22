<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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

Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('verify-email/{token}', 'verifyEmail');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});

Route::controller(UserController::class)->prefix('user')->middleware('auth:api')->group(function () {
    Route::post('list', 'list');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::post('change-password', 'changePassword');
    Route::post('logout', 'logout');
});

Route::controller(PhoneController::class)->prefix('phone')->middleware('auth:api')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
});

Route::controller(CompanyController::class)->prefix('company')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::post('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

Route::controller(EmployeeController::class)->prefix('employee')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

Route::controller(TaskController::class)->prefix('task')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

Route::controller(JobController::class)->prefix('job')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

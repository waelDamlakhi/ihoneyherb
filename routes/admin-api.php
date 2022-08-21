<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\SocialMediaController;

/*
|--------------------------------------------------------------------------
| API Routes For Admin
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "admin/api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);

Route::controller(DepartmentController::class)->group(function ()
{
    Route::post('create-department', 'create');
    Route::get('departments', 'read');
    Route::get('edit-department', 'edit');
    Route::post('update-department', 'update');
    Route::delete('delete-department', 'delete');
    Route::get('primary-departments', 'getPrimaryDepartments');
});

Route::controller(SocialMediaController::class)->group(function ()
{
    Route::post('create-socialMedia', 'create');
    Route::get('socialMedia', 'read');
    Route::get('edit-socialMedia', 'edit');
    Route::post('update-socialMedia', 'update');
    Route::delete('delete-socialMedia', 'delete');
});
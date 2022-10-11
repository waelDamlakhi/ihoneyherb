<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\PhoneController;

/*
|--------------------------------------------------------------------------
| API Routes For User
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "user/api" middleware group. Enjoy building your API!
|
*/


Route::controller(AuthController::class)->group(function ()
{
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::controller(PhoneController::class)->group(function ()
{
    Route::post('create-phone', 'create');
    Route::get('phones', 'read');
    Route::get('edit-phone', 'edit');
    Route::delete('delete-phone', 'delete');
    Route::put('update-phone', 'update');
    Route::put('set-phone-default', 'setPohoneDefault');
});

Route::controller(AddressController::class)->group(function ()
{
    Route::post('create-address', 'create');
    Route::get('addresses', 'read');
    Route::get('edit-address', 'edit');
    Route::delete('delete-address', 'delete');
    Route::put('update-address', 'update');
    Route::put('set-address-default', 'setAddressDefault');
});
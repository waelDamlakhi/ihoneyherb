<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes For Every One
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::controller(AuthController::class)->group(function ()
{
    Route::middleware(['tokenAuth'])->group(function () 
    {
        Route::get('profile', 'me');
        Route::get('refresh-token', 'refresh');
        Route::get('logout', 'logout');
    });
});
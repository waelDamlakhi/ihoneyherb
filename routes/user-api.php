<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
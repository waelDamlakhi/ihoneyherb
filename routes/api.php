<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

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

Route::controller(CategoryController::class)->group(function ()
{
    Route::get('categories-discount', 'getCategoriesDiscount');
    Route::get('primary-categories', 'getPrimaryCategories');
});

Route::controller(ProductController::class)->group(function ()
{
    Route::get('new-products', 'getNewProducts');
    Route::get('banners-products', 'getProductsBanners');
    Route::get('best-seller-products', 'getBestSellerProducts');
    Route::get('top-rated-products', 'getTopRatedProducts');
    Route::get('search-products', 'searchProducts');
});
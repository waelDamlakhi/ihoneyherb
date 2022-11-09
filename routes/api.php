<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SocialMediaController;

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
    Route::get('forget-password', 'forgetPassword');
});

Route::controller(CategoryController::class)->group(function ()
{
    Route::get('categories-discount', 'getCategoriesDiscount');
    Route::get('parent-categories', 'getParentCategories');
    Route::get('child-categories', 'getChildCategories');
    Route::get('categories-for-filter', 'getCategoriesForFilter');
});

Route::controller(ProductController::class)->group(function ()
{
    Route::get('banners-products', 'getProductsBanners');
    Route::get('search-product', 'searchProducts');
    Route::get('products', 'getProducts');
    Route::get('product-details', 'getProductDetails');
});

Route::controller(SocialMediaController::class)->group(function ()
{
    Route::get('follow-us', 'getFollowUs');
    Route::get('contact-us', 'getContactUs');
    Route::post('send-message', 'sendMessage');
});

Route::controller(BranchController::class)->group(function ()
{
    Route::get('branches', 'getBranches');
});
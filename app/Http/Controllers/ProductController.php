<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use GeneralFunctions;
    
    /**
     * Get All Products From Newest To Oldest.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewProducts()
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'department_id')->withAvg('users AS rate', 'product_user.rate')->orderby('id', 'DESC')->get();
            return $this->makeResponse("Success", 200, "These Are All Products From Newest To Oldest", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Products Banners.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsBanners()
    {
        try 
        {
            $productsBanners = Banner::select('id', 'name')->with(
                [
                    'products' => function ($products)
                    {
                        $products->select('products.id', 'AED', 'SAR', 'USD');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, "These Are All Products Banners", $productsBanners);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    /**
     * Get All Products From Best Seller To worse Seller.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBestSellerProducts()
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'department_id')->withSum('orders AS bestSeller', 'order_product.quantity')->orderby('bestSeller', 'DESC')->get();
            return $this->makeResponse("Success", 200, "These Are All Products From Best Seller To worse Seller", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Products From Top Rated To Low Rated.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopRatedProducts()
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'department_id')->withAvg('users AS rate', 'product_user.rate')->orderby('rate', 'DESC')->get();
            return $this->makeResponse("Success", 200, "These Are All Products From Top Rated To Low Rated", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Products That The User Is Looking For.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), ['search' => 'required|string']);
            if ($validator->fails()) 
                return $this->makeResponse("Faild", 422, "InVailed Inputs", $validator->errors());
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'department_id')->whereTranslationLike('name', '%' . $request->search . '%', app()->getLocale())->get();
            return $this->makeResponse("Success", 200, "These Are All Products You Are Looking For", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use GeneralFunctions;
    
    /**
     * Get All Products From Newest To Oldest.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewProducts(ProductRequest $request)
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'quantity', 'department_id')->with(
                [
                    'department' => function ($department)
                    {
                        $department->select('id')->with(
                            [
                                'discount' => function ($discount)
                                {
                                    $discount->select('discount', 'department_id')->where(
                                        [
                                            ['end', '>=', Carbon::today()],
                                            ['start', '<=', Carbon::today()]
                                        ]
                                    );
                                }
                            ]
                        );
                    },
                    'translations' => function ($translation) 
                    {
                        $translation->select('name', 'product_id', 'locale');
                    }
                ]
            )->withAvg('users AS rate', 'product_user.rate')->orderby('id', 'DESC')->limit($request->limit)->get();
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
                        $products->select('products.id', 'AED', 'SAR', 'USD', 'department_id')->with(
                            [
                                'department' => function ($department)
                                {
                                    $department->select('id')->with(
                                        [
                                            'discount' => function ($discount)
                                            {
                                                $discount->select('discount', 'department_id')->where(
                                                    [
                                                        ['end', '>=', Carbon::today()],
                                                        ['start', '<=', Carbon::today()]
                                                    ]
                                                );
                                            }
                                        ]
                                    );
                                },
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
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
    public function getBestSellerProducts(ProductRequest $request)
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'quantity', 'department_id')->with(
                [
                    'department' => function ($department)
                    {
                        $department->select('id')->with(
                            [
                                'discount' => function ($discount)
                                {
                                    $discount->select('discount', 'department_id')->where(
                                        [
                                            ['end', '>=', Carbon::today()],
                                            ['start', '<=', Carbon::today()]
                                        ]
                                    );
                                }
                            ]
                        );
                    },
                    'translations' => function ($translation) 
                    {
                        $translation->select('name', 'product_id', 'locale');
                    }
                ]
            )->withSum('orders AS bestSeller', 'order_product.quantity')->orderby('bestSeller', 'DESC')->limit($request->limit)->whereHas('orders')->get();
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
    public function getTopRatedProducts(ProductRequest $request)
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'quantity', 'department_id')->with(
                [
                    'department' => function ($department)
                    {
                        $department->select('id')->with(
                            [
                                'discount' => function ($discount)
                                {
                                    $discount->select('discount', 'department_id')->where(
                                        [
                                            ['end', '>=', Carbon::today()],
                                            ['start', '<=', Carbon::today()]
                                        ]
                                    );
                                }
                            ]
                        );
                    },
                    'translations' => function ($translation) 
                    {
                        $translation->select('name', 'product_id', 'locale');
                    }
                ]
            )->withAvg('users AS rate', 'product_user.rate')->orderby('rate', 'DESC')->limit($request->limit)->whereHas('users')->get();
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

<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Support\Carbon;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    use GeneralFunctions;
    
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
     * Get All Products That The User Is Looking For.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(ProductRequest $request)
    {
        try 
        {
            $products = Product::select('id', 'imageUrl', 'department_id')->with(
                [
                    'translations' => function ($translation) use($request)
                    {
                        $translation->select('name', 'product_id', 'locale');
                    }
                ]
            )->whereTranslationLike('name', '%' . $request->search . '%')->get();
            return $this->makeResponse("Success", 200, "These Are All Products You Are Looking For", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    
    /**
     * Get All Products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(ProductRequest $request)
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'imageUrl', 'quantity', 'department_id')
            ->with(
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
            )
            ->withSum('orders AS salesCount', 'order_product.quantity')
            ->withAvg('users AS rate', 'product_user.rate')
            ->whereTranslationLike('name', '%' . $request->search . '%')
            ->where(
                function ($query) use($request)
                {
                    if (isset($request->categories))
                    {
                        $query->whereIn('department_id', $request->categories);
                        if (in_array(null, $request->categories))
                            $query->orWhereNull('department_id');
                    }
                }
            )
            ->orderby($request->sort, $request->order)->paginate($request->limit);
            return $this->makeResponse("Success", 200, "These Are All Products", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

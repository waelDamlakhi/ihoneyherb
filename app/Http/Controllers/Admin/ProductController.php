<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\QuantityAdjustments;
use App\Traits\GeneralFunctions;
use Exception;

class ProductController extends Controller
{
    use GeneralFunctions;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('tokenAuth:admin-api');
    }
    
    /**
     * Create A New Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ProductRequest $request)
    {
        try 
        {
            $request->request->add(array_merge(
                [
                    'USD' => (float) number_format($request->AED / 3.66, 2),
                    'SAR' => $request->AED * 9.5,
                    'operation_type' => 'in',
                ], 
                $this->uploadFiles($request), 
                $this->uploadFiles($request, 'banner', 'bannerUrl', 'bannerPath')
            ));
            $product = Product::create($request->all());
            $request->request->add(['product_id' => $product->id, 'description' => 'بداية الكمية']);
            QuantityAdjustments::create($request->all());
            return $this->makeResponse("Success", 200, "Product Added Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

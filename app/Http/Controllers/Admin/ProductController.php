<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use App\Traits\GeneralFunctions;
use App\Models\QuantityAdjustments;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

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
    
    /**
     * Get Department Data For Edit It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(ProductRequest $request)
    {
        try 
        {
            $product = Product::select('id', 'AED', 'imageUrl', 'bannerUrl', 'department_id')->with(
                [
                    'department' => function ($department)
                    {
                        $department->select('id');
                    }
                ]
            )->find($request->id);
            return $this->makeResponse("Success", 200, "This Is Product Data", $product);
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
    public function read()
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'quantity', 'imageUrl', 'department_id', 'admin_id')->with(
                [
                    'admin' => function ($admin) 
                    {
                        $admin->select('id', 'name');
                    }, 
                    'department' => function ($department)
                    {
                        $department->select('id');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, "This All Products", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request)
    {
        try 
        {
            $product = Product::with(
                [
                    'department' => function ($department)
                    {
                        $department->select('id');
                    }
                ]
            )->find($request->id);
            if (!empty($request->file('photo'))) 
            {
                unlink($product->imagePath);
                $request->request->add($this->uploadFiles($request));
            }
            if (!empty($request->file('banner'))) 
            {
                unlink($product->bannerPath);
                $request->request->add($this->uploadFiles($request, 'banner', 'bannerUrl', 'bannerPath'));
            }
            $request->request->add(
                [
                    'USD' => (float) number_format($request->AED / 3.66, 2),
                    'SAR' => $request->AED * 9.5
                ]
            );
            $product->update($request->all());
            $product = $product->fresh(
                [
                    'department' => function ($department)
                    {
                        $department->select('id');
                    }
                ]
            );
            return $this->makeResponse("Success", 200, "Product Updated Successfully", $product);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * delete Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(ProductRequest $request)
    {
        try 
        {
            $product = Product::find($request->id);
            unlink($product->imagePath);
            unlink($product->bannerPath);
            $product->deleteTranslations();
            $product->delete();
            return $this->makeResponse("Success", 200, "Product Deleted Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}
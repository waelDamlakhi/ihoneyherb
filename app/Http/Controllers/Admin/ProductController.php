<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use App\Models\Department;
use App\Models\ProductPicture;
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
     * Get All Departments Which Has Perant Or Does Not Have Children For Put Product Inside Them.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartmentsForProduct()
    {
        try 
        {
            $departments = Department::select('id')->with(
                [
                    'translations' => function ($translation) 
                    {
                        $translation->select('name', 'department_id', 'locale');
                    }
                ]
            )->whereDoesntHave('children')->get();
            return $this->makeResponse("Success", 200, "This All Departments", $departments);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
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
                $this->uploadFiles($request->file('photo'))
            ));
            $product = Product::create($request->all());
            if (!empty($request->file('otherPhoto'))) 
            {
                foreach ($request->file('otherPhoto') as $photo) 
                {
                    ProductPicture::create(array_merge(['product_id' => $product->id], $this->uploadFiles($photo)));
                }
            }
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
            $product = Product::select('id', 'AED', 'quantity', 'weight', 'imageUrl', 'department_id', 'unit_id')->with(
                [
                    'department' => function ($department)
                    {
                        $department->select('id')->with('translations');
                    },
                    'translations',
                    'pictures',
                    'unit' => function ($unit)
                    {
                        $unit->with('translations');
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
    public function read(ProductRequest $request)
    {
        try 
        {
            $products = Product::select('id', 'AED', 'SAR', 'USD', 'quantity', 'weight', 'imageUrl', 'department_id', 'admin_id', 'unit_id')->with(
                [
                    'admin' => function ($admin) 
                    {
                        $admin->select('id', 'name');
                    }, 
                    'department' => function ($department)
                    {
                        $department->select('id')->with('translations');
                    },
                    'translations',
                    'unit' => function ($unit)
                    {
                        $unit->select('id')->with('translations');
                    }
                ]
            )->paginate($request->limit);
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
                        $department->select('id')->with('translations');
                    },
                    'translations',
                    'unit' => function ($unit)
                    {
                        $unit->with('translations');
                    }
                ]
            )->find($request->id);
            if (!empty($request->file('photo'))) 
            {
                unlink($product->imagePath);
                $request->request->add($this->uploadFiles($request->file('photo')));
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
                        $department->select('id')->with('translations');
                    },
                    'translations',
                    'unit' => function ($unit)
                    {
                        $unit->with('translations');
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
            $product = Product::with('pictures')->find($request->id);
            foreach ($product->pictures as $picture) 
            {
                unlink($picture->imagePath);
            }
            unlink($product->imagePath);
            $product->delete();
            return $this->makeResponse("Success", 200, "Product Deleted Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}
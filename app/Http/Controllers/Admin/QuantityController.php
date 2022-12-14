<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuantityRequest;
use App\Models\Product;
use App\Models\QuantityAdjustments;
use App\Traits\GeneralFunctions;
use Exception;

class QuantityController extends Controller
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
     * Create A New Quantity Adjustment Operation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(QuantityRequest $request)
    {
        try 
        {
            $product = Product::find($request->product_id);
            if ($request->operation_type == 'in') 
                $product->quantity += $request->quantity;
            else
                if ($product->quantity >= $request->quantity)
                    $product->quantity -= $request->quantity;
                else
                    throw new Exception(__('ProductLang.ThereIsNotEnoughQuantity'), 422);
            $product->save();
            QuantityAdjustments::create($request->all());
            return $this->makeResponse("Success", 200, __('ProductLang.QuantityAdjustmentOperationAddedSuccessfully'));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Quantity Adjustment Operations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(QuantityRequest $request)
    {
        try 
        {
            $quantityAdjustment = QuantityAdjustments::with(
                [
                    'admin' => function ($admin) 
                    {
                        $admin->select('id', 'name');
                    }, 
                    'product' => function ($product) 
                    {
                        $product->select('id', 'unit_id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                },
                                'unit' => function ($unit)
                                {
                                    $unit->select('id')->with('translations');
                                }
                            ]
                        );
                    }
                ]
            )->paginate($request->limit);
            return $this->makeResponse("Success", 200, __('ProductLang.TheseAreAllQuantityAdjustmentOperations'), $quantityAdjustment);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get Quantity Adjustment Operation Data For Edit It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(QuantityRequest $request)
    {
        try 
        {
            $quantityAdjustment = QuantityAdjustments::with(
                [
                    'product' => function ($product) 
                    {
                        $product->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            )->find($request->id);
            return $this->makeResponse("Success", 200, __('ProductLang.ThisIsQuantityAdjustmentOperationData'), $quantityAdjustment);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Products For Quantity Adjustment Operations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsForQuantityAdjustment()
    {
        try 
        {
            $products = Product::select('id')->with(
                [
                    'translations' => function ($translation) 
                    {
                        $translation->select('name', 'product_id', 'locale');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, __('ProductLang.TheseAreAllProducts'), $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Quantity Adjustment Operation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(QuantityRequest $request)
    {
        try 
        {
            $quantityAdjustment = QuantityAdjustments::with(
                [
                    'product' => function ($product) 
                    {
                        $product->select('id', 'quantity')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            )->find($request->id);
            $oldProduct = $quantityAdjustment->product;
            if ($quantityAdjustment->operation_type == 'in')
                $oldProduct->quantity -= $quantityAdjustment->quantity;
            else
                $oldProduct->quantity += $quantityAdjustment->quantity;
            if ($request->product_id != $quantityAdjustment->product->id) 
            {
                $product = Product::find($request->product_id);
            }
            else
                $product = $oldProduct;
            if ($request->operation_type == 'in') 
                $product->quantity += $request->quantity;
            else
                if ($product->quantity >= $request->quantity)
                    $product->quantity -= $request->quantity;
                else
                    throw new Exception(__('ProductLang.ThereIsNotEnoughQuantity'), 422);
            if ($product != $oldProduct)
                $oldProduct->save();
            $product->save();
            $quantityAdjustment->update($request->all());
            $quantityAdjustment = $quantityAdjustment->fresh(
                [
                    'product' => function ($product) 
                    {
                        $product->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            );
            return $this->makeResponse("Success", 200, __('ProductLang.QuantityAdjustmentOperationUpdatedSuccessfully'), $quantityAdjustment);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * delete Department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(QuantityRequest $request)
    {
        try 
        {
            $quantityAdjustment = QuantityAdjustments::find($request->id);
            $product = Product::find($quantityAdjustment->product->id);
            if ($quantityAdjustment->operation_type == 'in')
                $product->quantity -= $quantityAdjustment->quantity;
            else
                $product->quantity += $quantityAdjustment->quantity;
            $product->save();
            $quantityAdjustment->delete();
            return $this->makeResponse("Success", 200, __('ProductLang.QuantityAdjustmentOperationDeletedSuccessfully'));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

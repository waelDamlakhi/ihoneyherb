<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPictureRequest;
use App\Models\ProductPicture;
use App\Traits\GeneralFunctions;
use Exception;

class ProductPictureController extends Controller
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
     * Create Product Pictures.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ProductPictureRequest $request)
    {
        try 
        {
            foreach ($request->file('photo') as $photo) 
            {
                $productPictures[] = ProductPicture::create(array_merge(['product_id' => $request->product_id], $this->uploadFiles($photo)));
            }
            return $this->makeResponse("Success", 200, __('ProductLang.ProductPictureAddedSuccessfully'), $productPictures);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Product Pictures.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductPictureRequest $request)
    {
        try 
        {
            $productPicture = ProductPicture::find($request->id);
            unlink($productPicture->imagePath);
            $request->request->add($this->uploadFiles($request->file('photo')));
            $productPicture->update($request->all());
            return $this->makeResponse("Success", 200, __('ProductLang.ProductPictureUpdatedSuccessfully'), $productPicture);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Delete Product Pictures.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(ProductPictureRequest $request)
    {
        try 
        {
            $productPicture = ProductPicture::find($request->id);
            unlink($productPicture->imagePath);
            $productPicture->delete();
            return $this->makeResponse("Success", 200, __('ProductLang.ProductPictureDeletedSuccessfully'));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

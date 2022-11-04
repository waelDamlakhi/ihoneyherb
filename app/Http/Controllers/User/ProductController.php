<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;

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
        $this->middleware('tokenAuth:user-api');
    }
    
    /**
     * Create A Comment For User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ProductRequest $request)
    {
        try 
        {
            $product = Product::with('users')->find($request->product_id);
            $product->users()->syncWithoutDetaching([$request->user_id => ['rate' => $request->rate, 'comment' => $request->comment]]);
            return $this->makeResponse("Success", 200, "Comment Added Successfully", ['user_id' => $request->user_id, 'name' => $request->user->name]);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

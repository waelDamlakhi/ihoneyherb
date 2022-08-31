<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
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
     * Create A New Department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ProductRequest $request)
    {
        try 
        {
            // $request->request->add($this->uploadFiles($request));
            // Department::create($request->all());
            return $this->makeResponse("Success", 200, "Department Added Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

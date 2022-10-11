<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Department;
use Illuminate\Support\Carbon;
use App\Traits\GeneralFunctions;
use App\Models\DepartmentDiscount;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use GeneralFunctions;
    
    /**
     * Get All Categories That Have A Discount Today.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoriesDiscount()
    {
        try 
        {
            $departmentsDiscount = DepartmentDiscount::select('discount', 'department_id')->with(
                [ 
                    'department' => function ($department) 
                    {
                        $department->select('id', 'imageUrl');
                    }
                ]
            )->where(
                [
                    ['end', '>=', Carbon::today()],
                    ['start', '<=', Carbon::today()]
                ]
            )->get();
            return $this->makeResponse("Success", 200, "These Are All Categories That Have A Discount Today", $departmentsDiscount);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Primary Categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrimaryCategories()
    {
        try 
        {
            $departments = Department::select('id', 'imageUrl')->where('department_id', null)->whereDoesntHave('products')->get();
            return $this->makeResponse("Success", 200, "This All Primary Departments", $departments);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

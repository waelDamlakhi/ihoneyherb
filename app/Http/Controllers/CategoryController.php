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
                        $department->select('id', 'imageUrl')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'department_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            )->where(
                [
                    ['end', '>=', Carbon::today()],
                    ['start', '<=', Carbon::today()]
                ]
            )->get();
            return $this->makeResponse("Success", 200, __("CategoryLang.TheseAreAllCategoriesThatHaveADiscountToday"), $departmentsDiscount);
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
            $departments = Department::select('id', 'imageUrl')->with(
                [
                    'translations' => function ($translation) 
                    {
                        $translation->select('name', 'department_id', 'locale');
                    }
                ]
            )->where('department_id', null)->whereDoesntHave('products')->limit(7)->get();
            return $this->makeResponse("Success", 200, __('CategoryLang.TheseAreAllPrimaryDepartments'), $departments);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Departments Which Have Products For Filter.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoriesForFilter()
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
            )->whereHas('products')->get();
            return $this->makeResponse("Success", 200, __('CategoryLang.TheseAreAllDepartments'), $departments);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

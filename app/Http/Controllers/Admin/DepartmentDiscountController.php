<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Department;
use App\Traits\GeneralFunctions;
use App\Models\DepartmentDiscount;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentDiscountRequest;

class DepartmentDiscountController extends Controller
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
     * Create A New Department Discount.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(DepartmentDiscountRequest $request)
    {
        try 
        {
            $departmentsDiscounts = DepartmentDiscount::where(
                [
                    ['department_id', $request->department_id],
                    ['end', '>=', $request->start],
                    ['start', '<=', $request->end]
                ]
            )->get();
            if (COUNT($departmentsDiscounts) > 0) 
                throw new Exception(__('CategoryLang.ThisDateOverlapsWithAnotherDate'), 422);
            $request->merge(['discount' => $request->discount / 100]);
            DepartmentDiscount::create($request->all());
            return $this->makeResponse("Success", 200, __('CategoryLang.DepartmentDiscountAddedSuccessfully'), $departmentsDiscounts);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Departments Which Have Products For Give one Of Them A Dicount.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartmentsForDiscount()
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
    
    /**
     * Get All Departments Discounts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        try 
        {
            $departmentsDiscounts = DepartmentDiscount::with(
                [
                    'admin' => function ($admin) 
                    {
                        $admin->select('id', 'name');
                    }, 
                    'department' => function ($department) 
                    {
                        $department->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'department_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, __('CategoryLang.TheseAreAllDepartmentsDiscounts'), $departmentsDiscounts);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get Department Discount Data For Edit It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(DepartmentDiscountRequest $request)
    {
        try 
        {
            $departmentDiscount = DepartmentDiscount::with(
                [ 
                    'department' => function ($department) 
                    {
                        $department->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'department_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            )->find($request->id);
            $departmentDiscount->discount *= 100;
            return $this->makeResponse("Success", 200, __('CategoryLang.ThisIsDepartmentDiscountData'), $departmentDiscount);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Department Discount.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentDiscountRequest $request)
    {
        try 
        {
            $departmentDiscount = DepartmentDiscount::with(
                [ 
                    'department' => function ($department) 
                    {
                        $department->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'department_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            )->find($request->id);
            if ($request->start != $departmentDiscount->start || $request->end != $departmentDiscount->end || $request->department_id != $departmentDiscount->department_id) 
            {
                $departmentsDiscounts = DepartmentDiscount::where(
                    [
                        ['department_id', $request->department_id],
                        ['end', '>=', $request->start],
                        ['start', '<=', $request->end],
                        ['id', '!=', $request->id]
                    ]
                )->get();
                if (COUNT($departmentsDiscounts) > 0) 
                    throw new Exception(__('CategoryLang.ThisDateOverlapsWithAnotherDate'), 422);
            }
            $request->merge(['discount' => $request->discount / 100]);
            $departmentDiscount->update($request->all());
            $departmentDiscount = $departmentDiscount->fresh(
                [
                    'department' => function ($department) 
                    {
                        $department->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'department_id', 'locale');
                                }
                            ]
                        );
                    }
                ]
            );
            return $this->makeResponse("Success", 200, __('CategoryLang.DepartmentDiscountUpdatedSuccessfully'), $departmentDiscount);
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
    public function delete(DepartmentDiscountRequest $request)
    {
        try 
        {
            $departmentDiscount = DepartmentDiscount::find($request->id);
            $departmentDiscount->delete();
            return $this->makeResponse("Success", 200, __('CategoryLang.DepartmentDiscountDeletedSuccessfully'));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

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
                return $this->makeResponse("Faild", 422, app()->getLocale() == 'en' ? 'This Date Overlaps With Another Date': 'هذا التاريخ يتداخل مع تاريخ أخر');
            $request->merge(['discount' => $request->discount / 100]);
            DepartmentDiscount::create($request->all());
            return $this->makeResponse("Success", 200, "Department Discount Added Successfully", $departmentsDiscounts);
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
            $departments = Department::select('id')->whereHas('products')->get();
            foreach ($departments as $department)
                $department->makeHidden('translations');
            return $this->makeResponse("Success", 200, "This All Departments", $departments);
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
                        $department->select('id');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, "This All Departments Discounts", $departmentsDiscounts);
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
                        $department->select('id');
                    }
                ]
            )->find($request->id);
            $departmentDiscount->discount *= 100;
            return $this->makeResponse("Success", 200, "This Is Department Discount Data", $departmentDiscount);
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
                        $department->select('id');
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
                    return $this->makeResponse("Faild", 422, app()->getLocale() == 'en' ? 'This Date Overlaps With Another Date': 'هذا التاريخ يتداخل مع تاريخ أخر');
            }
            $request->merge(['discount' => $request->discount / 100]);
            $departmentDiscount->update($request->all());
            $departmentDiscount = $departmentDiscount->fresh(
                [
                    'department' => function ($department) 
                    {
                        $department->select('id');
                    }
                ]
            );
            return $this->makeResponse("Success", 200, "Department Discount Updated Successfully", $departmentDiscount);
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
            return $this->makeResponse("Success", 200, "Department Discount Deleted Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

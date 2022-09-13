<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Traits\GeneralFunctions;
use Exception;

class DepartmentController extends Controller
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
    public function create(DepartmentRequest $request)
    {
        try 
        {
            $request->request->add($this->uploadFiles($request->file('photo')));
            Department::create($request->all());
            return $this->makeResponse("Success", 200, "Department Added Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Primary Departments For Give one Of Them A Child.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrimaryDepartments()
    {
        try 
        {
            $departments = Department::select('id')->where('department_id', null)->whereDoesntHave('products')->get();
            foreach ($departments as $department)
                $department->makeHidden('translations');
            return $this->makeResponse("Success", 200, "This All Primary Departments", $departments);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Departments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        try 
        {
            $departments = Department::with(
                [
                    'admin' => function ($admin) 
                    {
                        $admin->select('id', 'name');
                    }, 
                    'parent' => function ($department) 
                    {
                        $department->select('id');
                    }
                ]
            )->get();
            // foreach ($departments as $department) 
            // {
            //     if (COUNT($department->children) > 0)
            //         $department->hasChildren = true;
            //     else
            //         $department->hasChildren = false;
            //     $department->makeHidden('children');
            // }
            return $this->makeResponse("Success", 200, "This All Departments", $departments);
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
    public function edit(DepartmentRequest $request)
    {
        try 
        {
            $department = Department::with(
                [
                    'parent' => function ($primaryDepartment) 
                    {
                        $primaryDepartment->select('id');
                    }
                ]
            )->find($request->id);
            if (COUNT($department->children) > 0)
                $department->hasChildren = true;
            else
                $department->hasChildren = false;
            $department->makeHidden('children');
            return $this->makeResponse("Success", 200, "This Is Department Data", $department);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentRequest $request)
    {
        try 
        {
            $department = Department::with(
                [
                    'parent' => function ($primaryDepartment) 
                    {
                        $primaryDepartment->select('id');
                    }
                ]
            )->find($request->id);
            if (!empty($request->file('photo'))) 
            {
                unlink($department->imagePath);
                $request->request->add($this->uploadFiles($request->file('photo')));
            }
            $department->update($request->all());
            $department = $department->fresh(
                [
                    'parent' => function ($primaryDepartment) 
                    {
                        $primaryDepartment->select('id');
                    }
                ]
            );
            return $this->makeResponse("Success", 200, "Department Updated Successfully", $department);
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
    public function delete(DepartmentRequest $request)
    {
        try 
        {
            $department = Department::with('products')->find($request->id);
            unlink($department->imagePath);
            $department->deleteTranslations();
            $department->delete();
            return $this->makeResponse("Success", 200, "Department Deleted Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

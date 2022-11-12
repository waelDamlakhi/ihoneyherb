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
            return $this->makeResponse("Success", 200, __('CategoryLang.DepartmentAddedSuccessfully'));
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
            $departments = Department::select('id')->with('translations' )->where('department_id', null)->whereDoesntHave('products')->get();
            return $this->makeResponse("Success", 200, __('CategoryLang.TheseAreAllPrimaryDepartments'), $departments);
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
    public function read(DepartmentRequest $request)
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
                        $department->select('id')->with('translations');
                    },
                    'translations'
                ]
            )->withCount('children AS hasChildren')->paginate($request->limit);
            return $this->makeResponse("Success", 200, __('CategoryLang.TheseAreAllDepartments'), $departments);
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
                        $primaryDepartment->select('id')->with('translations');
                    },
                    'translations'
                ]
            )->withCount('children AS hasChildren')->find($request->id);
            return $this->makeResponse("Success", 200, __('CategoryLang.ThisIsDepartmentData'), $department);
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
                        $primaryDepartment->select('id')->with('translations');
                    },
                    'translations'
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
                        $primaryDepartment->select('id')->with('translations');
                    },
                    'translations'
                ]
            );
            return $this->makeResponse("Success", 200, __('CategoryLang.DepartmentUpdatedSuccessfully'), $department);
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
            $department = Department::find($request->id);
            unlink($department->imagePath);
            $department->delete();
            return $this->makeResponse("Success", 200, __('CategoryLang.DepartmentDeletedSuccessfully'));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

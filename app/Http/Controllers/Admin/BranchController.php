<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Branch;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;

class BranchController extends Controller
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
    public function create(BranchRequest $request)
    {
        try 
        {
            $addresses = [$request->country, $request->city, $request->address, $request->phone];
            $countries = Branch::with(
                [
                    'children' => function ($city) use ($request)
                    {
                        $city->with(
                            [
                                'children' => function ($address) use ($request)
                                {
                                    $address->with(
                                        [
                                            'children' => function ($phone) use ($request)
                                            {
                                                $phone->where('address', $request->phone);
                                            }
                                        ]
                                    )->where('address', $request->address);
                                }
                            ]
                        )->where('address', $request->city);
                    }
                ]
            )->where('address', $request->country)->get();
            if (COUNT($countries) == 0) 
            {
                return $this->insertBranch($addresses, $request->admin_id);
            }
            else
            {
                if (COUNT($countries[0]->children) == 0) 
                {
                    $addresses = array_slice($addresses, 1);
                    return $this->insertBranch($addresses, $request->admin_id, $countries[0]);
                }
                else
                {
                    if (COUNT($countries[0]->children[0]->children) == 0) 
                    {
                        $addresses = array_slice($addresses, 2);
                        return $this->insertBranch($addresses, $request->admin_id, $countries[0]->children[0]);
                    }
                    else
                    {
                        $addresses = array_slice($addresses, 3);
                        return $this->insertBranch($addresses, $request->admin_id, $countries[0]->children[0]->children[0]);
                    }
                }
            }
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    /**
     * Insert A New Address In Branches Table.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function insertBranch(array $addresses, int $admin_id, object $branch = null)
    {
        try 
        {
            $newBranch = new Branch();
            for ($i = 0; $i < COUNT($addresses); $i++) 
            { 
                $newBranch = Branch::create(
                    [
                        'admin_id' => $admin_id,
                        'branch_id' => $i > 0 ? $newBranch->id : (!is_null($branch) ? $branch->id : null),
                        'address' => $addresses[$i]
                    ]
                );
            }
            return $this->makeResponse("Success", 200, __('BranchLang.BranchAddedSuccessfully'));
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    /**
     * Get All Branches.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(BranchRequest $request)
    {
        try 
        {
            $branches = Branch::with(
                [
                    'admin' => function($admin)
                    {
                        $admin->select('id', 'name');
                    }
                ]
            )->where('branch_id', ($request->has('address_id') ? $request->address_id : ($request->has('city_id') ? $request->city_id : ($request->has('country_id') ? $request->country_id : null))))->get();
            return $this->makeResponse("Success", 200, __('BranchLang.TheseAreAllBranches'), $branches);
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    /**
     * Edit Branch.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(BranchRequest $request)
    {
        try 
        {
            $branch = Branch::with(
                [
                    'parent' => function ($city)
                    {
                        $city->with(
                            [
                                'parent' => function ($address)
                                {
                                    $address->with('parent');
                                }
                            ]
                        );
                    }
                ]
            )->find($request->phone_id);
            return $this->makeResponse("Success", 200, __('BranchLang.ThisIsBranchData'), $branch);
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    /**
     * Update Branch.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BranchRequest $request)
    {
        try 
        {
            $branch = Branch::find($request->phone_id);
            $branch->address = $request->phone;
            $branch->save();
            return $this->makeResponse("Success", 200, __('BranchLang.BranchUpdatedSuccessfully'), $branch);
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    /**
     * Delete Branch.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(BranchRequest $request)
    {
        try 
        {
            $id = $request->has('phone_id') ? $request->phone_id : ($request->has('address_id') ? $request->address_id : ($request->has('city_id') ? $request->city_id : $request->country_id));
            
            $branch = Branch::with(
                [
                    'parent' => function ($parent)
                    {
                        $parent->with(
                            [
                                'parent' => function ($parentParent)
                                {
                                    $parentParent->with(
                                        [
                                            'parent' => function ($parentParentParent)
                                            {
                                                $parentParentParent->withCount('children As childrenCount');
                                            }
                                        ]
                                    )->withCount('children As childrenCount');
                                }
                            ]
                        )->withCount('children As childrenCount');
                    }
                ]
            )->find($id);
            if (!is_null($branch->parent) && $branch->parent->childrenCount == 1)
                if (!is_null($branch->parent->parent) && $branch->parent->parent->childrenCount == 1)
                    if (!is_null($branch->parent->parent->parent) && $branch->parent->parent->parent->childrenCount == 1)
                        $branchId = $branch->parent->parent->parent->id;
                    else
                        $branchId = $branch->parent->parent->id;
                else
                    $branchId = $branch->parent->id;
            else
                $branchId = null;
            if (!is_null($branchId))
                $branch = Branch::find($branchId);
            $branch->delete();
            return $this->makeResponse("Success", 200, __('BranchLang.BranchDeletedSuccessfully'));
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

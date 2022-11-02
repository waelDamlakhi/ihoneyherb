<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Unit;
use App\Traits\GeneralFunctions;
use App\Http\Requests\UnitRequest;
use App\Http\Controllers\Controller;

class UnitController extends Controller
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
     * Create A New Unit.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(UnitRequest $request)
    {
        try 
        {
            Unit::create($request->all());
            return $this->makeResponse("Success", 200, __('UnitLang.UnitAddedSuccessfully'));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Units.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        try 
        {
            $units = Unit::with(
                [
                    'translations',
                    'admin' => function ($admin)
                    {
                        $admin->select('id', 'name');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, __('UnitLang.TheseAreAllUnits'), $units);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get Unit Data For Edit It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UnitRequest $request)
    {
        try 
        {
            $unit = Unit::with('translations')->find($request->id);
            return $this->makeResponse("Success", 200, __('UnitLang.ThisIsUnitData'), $unit);
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
    public function update(UnitRequest $request)
    {
        try 
        {
            $unit = Unit::with('translations')->find($request->id);
            $unit->update($request->all());
            $unit = $unit->fresh('translations');
            return $this->makeResponse("Success", 200, __('UnitLang.UnitUpdatedSuccessfully'), $unit);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * delete Unit.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(UnitRequest $request)
    {
        try 
        {
            $unit = Unit::withCount('products AS productCount')->find($request->id);
            if ($unit->productCount == 0)
                $unit->delete();
            else
                throw new Exception(__('UnitLang.ThisUnitHasBeenUsedInProducts,YouCanNotDeleteIt'), 501);
            return $this->makeResponse("Success", 200, __('UnitLang.UnitDeletedSuccessfully'));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

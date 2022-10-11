<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Traits\GeneralFunctions;
use App\Http\Requests\AddressRequest;
use App\Http\Controllers\Controller;

class AddressController extends Controller
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
     * Create A New Phone Number.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(AddressRequest $request)
    {
        try 
        {
            $request->request->add(['type' => 'additional']);
            Address::create($request->all());
            return $this->makeResponse("Success", 200, "Address Added Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All phones.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request)
    {
        try 
        {
            $addresses = Address::where('user_id', $request->user_id)->get();
            return $this->makeResponse("Success", 200, "This All Addresses", $addresses);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get Phone Data For Edit It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(AddressRequest $request)
    {
        try 
        {
            $address = Address::where('user_id', $request->user_id)->find($request->id);
            return $this->makeResponse("Success", 200, "This Is Address Data", $address);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Phone.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AddressRequest $request)
    {
        try 
        {
            $address = Address::where('user_id', $request->user_id)->find($request->id);
            $address->update($request->all());
            return $this->makeResponse("Success", 200, "Address Updated Successfully", $address);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Delete Phone.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(AddressRequest $request)
    {
        try 
        {
            $address = Address::where('user_id', $request->user_id)->find($request->id);
            if ($address->type == 'default') 
                return $this->makeResponse("Faild", 422, app()->getLocale() == 'en' ? 'Can Not Delete Default Address': "لا يمكن حذف العنوان الافتراضي");
            $address->delete();
            return $this->makeResponse("Success", 200, "Address deleted Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Set Phone As Default.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAddressDefault(AddressRequest $request)
    {
        try 
        {
            Address::where(
                [
                    ['user_id', $request->user_id],
                    ['type', 'default']
                ]
            )->update(['type' => 'additional']);
            Address::where(
                [
                    ['user_id', $request->user_id],
                    ['id', $request->id]
                ]
            )->update(['type' => 'default']);
            return $this->makeResponse("Success", 200, "Address Has Been Set As Default Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

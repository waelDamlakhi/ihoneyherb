<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\Phone;
use Illuminate\Http\Request;
use App\Traits\GeneralFunctions;
use App\Http\Requests\PhoneRequest;
use App\Http\Controllers\Controller;

class PhoneController extends Controller
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
    public function create(PhoneRequest $request)
    {
        try 
        {
            $request->request->add(['type' => 'additional']);
            Phone::create($request->all());
            return $this->makeResponse("Success", 200, "Phone Added Successfully");
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
            $phones = Phone::where('user_id', $request->user_id)->get();
            return $this->makeResponse("Success", 200, "This All Phones", $phones);
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
    public function edit(PhoneRequest $request)
    {
        try 
        {
            $phone = Phone::where('user_id', $request->user_id)->find($request->id);
            return $this->makeResponse("Success", 200, "This Is Phone Data", $phone);
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
    public function update(PhoneRequest $request)
    {
        try 
        {
            $phone = Phone::where('user_id', $request->user_id)->find($request->id);
            $phone->update($request->all());
            return $this->makeResponse("Success", 200, "Phone Updated Successfully", $phone);
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
    public function delete(PhoneRequest $request)
    {
        try 
        {
            $phone = Phone::where('user_id', $request->user_id)->find($request->id);
            if ($phone->type == 'default') 
                return $this->makeResponse("Faild", 422, app()->getLocale() == 'en' ? 'Can Not Delete Default Phone': "لا يمكن حذف الهاتف الافتراضي");
            $phone->delete();
            return $this->makeResponse("Success", 200, "Phone deleted Successfully");
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
    public function setPohoneDefault(PhoneRequest $request)
    {
        try 
        {
            Phone::where(
                [
                    ['user_id', $request->user_id],
                    ['type', 'default']
                ]
            )->update(['type' => 'additional']);
            Phone::where(
                [
                    ['user_id', $request->user_id],
                    ['id', $request->id]
                ]
            )->update(['type' => 'default']);
            return $this->makeResponse("Success", 200, "Phone Has Been Set As Default Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

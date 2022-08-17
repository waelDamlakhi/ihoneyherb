<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialMediaRequest;
use App\Models\SocialMedia;
use App\Traits\GeneralFunctions;
use Exception;

class SocialMediaController extends Controller
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
     * Create A New Social Media.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(SocialMediaRequest $request)
    {
        try 
        {
            $filepath = $this->uploadFiles($request);
            $request->request->add(['image' => $filepath]);
            SocialMedia::create($request->all());
            return $this->makeResponse("Success", 200, "Social Media Added Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Social Media.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        try 
        {
            $socialMedia = SocialMedia::with(
                [
                    'admin' => function ($admin) 
                    {
                        $admin->select('id', 'name');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, "This All Social Media", $socialMedia);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild",  $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get Social Media Data For Edit It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(SocialMediaRequest $request)
    {
        try 
        {
            $socialMedia = SocialMedia::find($request->id);
            return $this->makeResponse("Success", 200, "This is Social Media Data", $socialMedia);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild",  $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Social Media.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SocialMediaRequest $request)
    {
        try 
        {
            $socialMedia = SocialMedia::find($request->id);
            if (!empty($request->file('photo'))) 
            {
                unlink($socialMedia->image);
                $filepath = $this->uploadFiles($request);
                $request->request->add(['image' => $filepath]);
            }
            $socialMedia->update($request->all());
            return $this->makeResponse("Success", 200, "Social Media Updated Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild",  $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * delete Department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(SocialMediaRequest $request)
    {
        try 
        {
            $socialMedia = SocialMedia::find($request->id);
            unlink($socialMedia->image);
            $socialMedia->delete();
            return $this->makeResponse("Success", 200, "Social Media Deleted Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild",  $e->getCode(), $e->getmessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialMediaRequest;
use App\Models\SocialMedia;

class SocialMediaController extends Controller
{
    use GeneralFunctions;
    
    /**
     * Get All Social Media User Can Follow Us With It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFollowUs()
    {
        try 
        {
            $socialMedia = SocialMedia::select('info', 'imageUrl')->where('type', 'application')->get();
            return $this->makeResponse("Success", 200, __("SocialMediaLang.TheseAreAllSocialMedia"), $socialMedia);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Phones And Emails For User Can Contact Us With It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContactUs()
    {
        try 
        {
            $socialMedia = SocialMedia::select('type', 'info', 'imageUrl')->whereIn('type', ['tel', 'email'])->get();
            return $this->makeResponse("Success", 200, __("SocialMediaLang.TheseAreAllSocialMedia"), $socialMedia);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Send Mail For Admin Email From Users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(SocialMediaRequest $request)
    {
        try 
        {
            $socialMedia = SocialMedia::select('info')->where('type', 'email')->first();
            $response = $this->sendMail(
                $socialMedia->info,
                'Inquiry About A Product', 
                'inquiry', 
                [
                    'message' => $request->message,
                    'name' => $request->name,
                    'email' => $request->email
                ], 
                [
                    'email' => $request->email, 
                    'name' => $request->name
                ]
            );
            if (is_object($response))
                throw new Exception($response->getMessage(), $response->getCode());
            return $this->makeResponse("Success", 200, __("SocialMediaLang.MessageSentSuccessfully"));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

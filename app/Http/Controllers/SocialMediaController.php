<?php

namespace App\Http\Controllers;

use Exception;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
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
}

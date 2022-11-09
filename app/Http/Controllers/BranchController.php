<?php

namespace App\Http\Controllers;

use Exception;
use App\Traits\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Models\Branch;

class BranchController extends Controller
{
    use GeneralFunctions;
    
    /**
     * Get All Branches Informations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBranches()
    {
        try 
        {
            $branches = Branch::with(
                [
                    'children' => function ($city)
                    {
                        $city->with(
                            [
                                'children' => function ($address)
                                {
                                    $address->with('children');
                                }
                            ]
                        );
                    }
                ]
            )->where('branch_id', null)->get();
            return $this->makeResponse("Success", 200, __("SocialMediaLang.TheseAreAllSocialMedia"), $branches);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}

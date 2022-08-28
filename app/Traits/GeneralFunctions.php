<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait GeneralFunctions {

    /*
        ***********************************************
        ** Function Return Json Response For All Api **
        ***********************************************
    */
    public function makeResponse($statue, $code, $msg = "", $data = array()) 
    {
        return response()->json(
            [
                'Statue' => $statue,
                'Code' => $code,
                'Message' => $msg,
                'Data' => $data
            ]
        );
    }

    /*
        *********************************************
        **** Function Upload Files To The Server ****
        *********************************************
    */
    public function uploadFiles(Request $request, $inputName = 'photo')
    {
        $file = $request->file($inputName);
        $fileName = Time() . "_" . $file->getClientOriginalName();
        $folderPath = public_path('images'); 
        $file->move($folderPath, $fileName);
        return [
            "imageUrl" => env('APP_URL') . '/public/images/' . $fileName,
            "imagePath" => $folderPath . '\\' . $fileName
        ];
    }

}
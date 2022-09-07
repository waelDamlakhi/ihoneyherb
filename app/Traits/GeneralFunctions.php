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
    public function uploadFiles($file, $fileUrl = 'imageUrl', $filePath = 'imagePath')
    {
        $fileName = Time() + rand() . "_" . $file->getClientOriginalName();
        $folderPath = public_path('images'); 
        $file->move($folderPath, $fileName);
        return [
            $fileUrl => env('APP_URL') . '/public/images/' . $fileName,
            $filePath => $folderPath . '\\' . $fileName
        ];
    }

}
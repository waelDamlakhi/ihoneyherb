<?php

namespace App\Traits;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


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
            $fileUrl => env('APP_URL') . 'images/' . $fileName,
            $filePath => $folderPath . '\\' . $fileName
        ];
    }

    /*
        ********************************************
        *** Function Send mails From The Server ***
        ********************************************
    */
    public function sendMail($email, $subject, $view, $data = array(), $from = array())
    {
        try 
        {
            if (COUNT($from) == 0)
                $from = [
                    'email' => env('MAIL_USERNAME'),
                    'name' => env('MAIL_FROM_NAME')
                ];
            //Load Composer's autoloader
            require base_path("vendor/autoload.php");
    
            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);
            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');   //  sender username
            $mail->Password = env('MAIL_PASSWORD');       // sender password
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');                  // encryption - ssl/tls
            $mail->Port = env('MAIL_PORT', 587);
            $mail->setFrom($from['email'], $from['name']);
            $mail->addAddress($email); 

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = view($view, $data);
            $mail->send();
            return true;
        } 
        catch (Exception $e) 
        {
            return $e;
        }
    }
}
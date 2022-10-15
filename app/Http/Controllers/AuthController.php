<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\GeneralFunctions;
use App\Http\Requests\AuthRequest;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Phone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;

class AuthController extends Controller
{
    use GeneralFunctions;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request)
    {
        try 
        {
            $guard = $request->path() == 'admin/api/login' ? 'admin-api' : 'user-api';
            $credentials = $request->only(['userName', 'password']);
            $token = Auth::guard($guard)->attempt($credentials);
            if (!$token)
                return $this->makeResponse('Failed', 403, "Access Denied");
            if ($guard == 'admin-api') 
                $data = [
                    'Token' => 'Bearer ' . $token,
                    'Type' => 'Admin'
                ];
            else
                $data = [
                    'Token' => 'Bearer ' . $token,
                    'Type' => 'Client',
                    'EmailValidation' => (bool)Auth::guard($guard)->user()['emailValidation']
                ];
            return $this->makeResponse('Success', 200, "Access Granted", $data);
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Create A New User Account.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRequest $request)
    {
        try 
        {
            $credentials = $request->only(['userName', 'password']);
            $emailCode = random_int(100000, 999999);
            $response = $this->sendMail($request->email, "Email Verification Code", "<h2>Hello " . $request->name . "</h2><h3>Welcome To IHoneyHerb Store</h3><h4>Your Verification Code Is : <strong>" . $emailCode . "</strong></h4>");
            if ($response != true)
                return $this->makeResponse('Failed', $response->getCode(), $response->getMessage());
            $request->merge(
                [
                    'password' => bcrypt($request->password),
                    'emailCode' => $emailCode,
                    'codeExpirationDate' => Carbon::now()->addMinutes(1.30)
                ]
            );
            $user = User::create($request->all());
            $request->request->add(['user_id' => $user->id, 'type' => 'default']);
            Phone::create($request->request->all());
            Address::create($request->request->all());
            $token = Auth::guard('user-api')->attempt($credentials);
            return $this->makeResponse('Success', 200, "Access Granted", array('Token' => 'Bearer ' . $token, 'Type' => 'Client', 'EmailValidation' => 'false'));
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try 
        {
            $user = Auth::guard($request->guard)->user();
            return $this->makeResponse('Success', 200, "This Is Your Profile Data", array('name' => $user['name'], 'userName' => $user['userName']));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Verify From Client Email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        try 
        {
            $user = Auth::guard($request->guard)->user();
            if ($user['emailValidation'])
                return $this->makeResponse('Failed', 422, "You Have Already Confirmed Your Email");
            if ($user['emailCode'] != $request->code)
                return $this->makeResponse('Failed', 422, "Invalid Verification Code");
            if ($user['codeExpirationDate'] < Carbon::now()) 
                return $this->makeResponse('Failed', 422, "Verification Code Timed Out");
            $client = User::find($user['id']);
            $client->emailValidation = true;
            $client->save();
            return $this->makeResponse('Success', 200, "Email Verified Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * ReSend Email Verify Code.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reSendEmailVerifyCode(Request $request)
    {
        try 
        {
            $user = Auth::guard($request->guard)->user();
            if ($user['emailValidation'])
                return $this->makeResponse('Failed', 422, "You Have Already Confirmed Your Email");
            if ($user['codeExpirationDate'] >= Carbon::now()) 
                return $this->makeResponse('Failed', 422, "Verification Code Timed Out");
            $emailCode = random_int(100000, 999999);
            $response = $this->sendMail($request->email, "Email Verification Code", "<h2>Hello " . $request->name . "</h2><h3>Welcome To IHoneyHerb Store</h3><h4>Your Verification Code Is : <strong>" . $emailCode . "</strong></h4>");
            if ($response != true)
                return $this->makeResponse('Failed', $response->getCode(), $response->getMessage());
            $request->merge(
                [
                    'id' => $user['id'],
                    'emailCode' => $emailCode,
                    'codeExpirationDate' => Carbon::now()->addMinutes(1.30)
                ]
            );
            $client = User::find($user['id']);
            $client->update($request->all());
            return $this->makeResponse('Success', 200, "Email Verification Code Has Been Successfully ReSent");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try 
        {
            auth()->logout();
            return $this->makeResponse('Success', 200, "Successfully logged out");
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        if ($request->guard == 'admin-api') 
            $data = [
                'Token' => 'Bearer ' . Auth::refresh(),
                'Type' => 'Admin'
            ];
        else
            $data = [
                'Token' => 'Bearer ' . Auth::refresh(),
                'Type' => 'Client',
                'EmailValidation' => (bool)Auth::guard($request->guard)->user()['emailValidation']
            ];
        return $this->makeResponse('Success', 200, "Token Has Refreshed Successfully", $data);
    }
}

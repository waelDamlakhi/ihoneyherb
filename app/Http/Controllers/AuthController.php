<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\GeneralFunctions;
use App\Http\Requests\AuthRequest;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Admin;
use App\Models\Phone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
                return $this->makeResponse('Failed', 403, __("AuthLang.AccessDenied"));
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
            return $this->makeResponse('Success', 200, __("AuthLang.AccessGranted"), $data);
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
            $response = $this->sendMail($request->email, "emailVerificationCode", ['subject' => 'Email Verification Code', 'name' => $request->name, 'code' => $emailCode]);
            if (is_object($response))
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
            return $this->makeResponse('Success', 200, __("AuthLang.AccessGranted"), array('Token' => 'Bearer ' . $token, 'Type' => 'Client', 'EmailValidation' => 'false'));
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
            return $this->makeResponse('Success', 200, __("AuthLang.YourProfileData"), array('name' => $user['name'], 'userName' => $user['userName']));
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
                throw new Exception(__('AuthLang.ConfirmedYourEmail'), 422);
            if ($user['emailCode'] != $request->code)
                throw new Exception(__('AuthLang.InvalidVerificationCode'), 422);
            if ($user['codeExpirationDate'] < Carbon::now()) 
                throw new Exception(__('AuthLang.VerificationCodeTimedOut'), 422);
            $client = User::find($user['id']);
            $client->emailValidation = true;
            $client->save();
            return $this->makeResponse('Success', 200, __("AuthLang.EmailVerifiedSuccessfully"));
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
                throw new Exception(__('AuthLang.ConfirmedYourEmail'), 422);
            if ($user['codeExpirationDate'] >= Carbon::now()) 
                throw new Exception(__('AuthLang.VerificationCodeHasNotExpired'), 422);
            $emailCode = random_int(100000, 999999);
            $response = $this->sendMail($user->email, "emailVerificationCode", ['subject' => 'Email Verification Code', 'name' => $user->name, 'code' => $emailCode]);
            if (is_object($response))
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
            return $this->makeResponse('Success', 200, __("AuthLang.EmailVerificationCodeHasBeenSuccessfullyReSent"));
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
            return $this->makeResponse('Success', 200, __("AuthLang.SuccessfullyLoggedOut"));
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Forget Password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(Request $request)
    {
        try 
        {
            $ttl = 4;
            $user = User::where('email', $request->email)->first();
            if (!$user) 
            {
                $user = Admin::where('email', $request->email)->first();
                if (!$user) 
                    throw new Exception(__('AuthLang.ThisEmailIsNotExist'), 422);
                else
                    $token = Auth::guard('admin-api')->setTTL($ttl)->login($user);
            }
            else
                $token = Auth::guard('user-api')->setTTL($ttl)->login($user);
            $response = $this->sendMail($request->email, "forgetPassword", ['subject' => 'Forget Your Password', 'name' => $user->name, 'token' => $token]);
            if (is_object($response))
                return $this->makeResponse('Failed', $response->getCode(), $response->getMessage());
            return $this->makeResponse('Success', 200, __("AuthLang.TheSpecifiedMailHasBeenContactedPleaseCheckYourInbox"));
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
        return $this->makeResponse('Success', 200, __("AuthLang.TokenHasRefreshedSuccessfully"), $data);
    }
}

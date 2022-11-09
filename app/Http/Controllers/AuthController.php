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
            if (!$token = Auth::guard($guard)->attempt($request->only(['userName', 'password'])))
                throw new Exception(__('AuthLang.AccessDenied'), 401);
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
            $response = $this->sendMail($request->email, 'Email Verification Code', "emailVerificationCode", ['name' => $request->name, 'code' => $emailCode]);
            if (is_object($response))
                throw new Exception($response->getMessage(), $response->getCode());
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
            return $this->makeResponse('Success', 200, __("AuthLang.AccessGranted"), array('Token' => 'Bearer ' . $token, 'Type' => 'Client', 'EmailValidation' => false));
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
    public function me(AuthRequest $request)
    {
        try 
        {
            return $this->makeResponse('Success', 200, __("AuthLang.YourProfileData"), array('name' => $request->user->name, 'userName' => $request->user->userName));
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
    public function verifyEmail(AuthRequest $request)
    {
        try 
        {
            if ($request->user->emailValidation)
                throw new Exception(__('AuthLang.ConfirmedYourEmail'), 422);
            if ($request->user->emailCode != $request->code)
                throw new Exception(__('AuthLang.InvalidVerificationCode'), 422);
            if ($request->user->codeExpirationDate < Carbon::now()) 
                throw new Exception(__('AuthLang.VerificationCodeTimedOut'), 422);
            $client = User::find($request->user->id);
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
    public function reSendEmailVerifyCode(AuthRequest $request)
    {
        try 
        {
            if ($request->user->emailValidation)
                throw new Exception(__('AuthLang.ConfirmedYourEmail'), 422);
            if ($request->user->codeExpirationDate >= Carbon::now()) 
                throw new Exception(__('AuthLang.VerificationCodeHasNotExpired'), 422);
            $emailCode = random_int(100000, 999999);
            $response = $this->sendMail($request->user->email, 'Email Verification Code', "emailVerificationCode", ['name' => $request->user->name, 'code' => $emailCode]);
            if (is_object($response))
                return $this->makeResponse('Failed', $response->getCode(), $response->getMessage());
            $request->merge(
                [
                    'emailCode' => $emailCode,
                    'codeExpirationDate' => Carbon::now()->addMinutes(1.30)
                ]
            );
            $client = User::find($request->user->id);
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
    public function forgetPassword(AuthRequest $request)
    {
        try 
        {
            $token = Auth::guard($request->guard)->setTTL(4)->tokenById($request->user->id);
            $response = $this->sendMail($request->email, 'Forget Your Password', "forgetPassword", ['name' => $request->user->name, 'token' => $token]);
            if (is_object($response))
                throw new Exception($response->getMessage(), $response->getCode());
            return $this->makeResponse('Success', 200, __("AuthLang.TheSpecifiedMailHasBeenContactedPleaseCheckYourInbox"));
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse('Failed', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Change Password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(AuthRequest $request)
    {
        try 
        {
            if ($request->guard == 'admin-api') 
            {
                $user = Admin::find($request->user->id);
                $data['Type'] = 'Admin';
            }
            else
            {
                $user = User::find($request->user->id);
                $data = [
                    'Type' => 'Client',
                    'EmailValidation' => (bool)$request->user->emailValidation
                ];
            }
            $user->password = bcrypt($request->password);
            $user->save();
            auth()->logout();
            $data['Token'] = 'Bearer ' . Auth::guard($request->guard)->login($user);
            return $this->makeResponse('Success', 200, __("AuthLang.PasswordChangedSuccessfully"), $data);
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
    public function refresh(AuthRequest $request)
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
                'EmailValidation' => (bool)$request->user->emailValidation
            ];
        return $this->makeResponse('Success', 200, __("AuthLang.TokenHasRefreshedSuccessfully"), $data);
    }
}

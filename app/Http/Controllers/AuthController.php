<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\GeneralFunctions;
use App\Http\Requests\AuthRequest;
use App\Http\Controllers\Controller;
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
                return $this->makeResponse('Failed', 403, "Access Denied");
            return $this->makeResponse('Success', 200, "Access Granted", array('Token' => 'Bearer ' . $token, 'Type' => $guard == 'admin-api' ? 'Admin' : 'Client'));
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
        return $this->makeResponse('Success', 200, "Token Has Refreshed Successfully", array('Token' => 'Bearer ' . Auth::refresh(), 'Type' => $request->guard == 'admin-api' ? 'Admin' : 'Client'));
    }
}

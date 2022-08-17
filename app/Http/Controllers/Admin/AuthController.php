<?php

namespace App\Http\Controllers\Admin;

use Exception;
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
        $this->middleware('tokenAuth:admin-api', ['except' => ['login']]);
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
            $credentials = $request->only(['userName', 'password']);
            $token = Auth::guard('admin-api')->attempt($credentials);
            if (!$token)
                return $this->makeResponse('Failed', 403, "Access Denied");
            return $this->makeResponse('Success', 200, "Access Granted", array('Token' => 'Bearer ' . $token, 'type' => 'Admin'));
        } 
        catch (Exception $e) 
        {
            return $this->makeResponse('Success', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try 
        {
            $admin = Auth::guard('admin-api')->user();
            return $this->makeResponse('Success', 200, "This Is Your Profile Data", array('name' => $admin['name'], 'userName' => $admin['userName']));
        }
        catch (Exception $e) 
        {
            return $this->makeResponse('Success', $e->getCode(), $e->getMessage());
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
            return $this->makeResponse('Success', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // return auth()->refresh();
    }
}

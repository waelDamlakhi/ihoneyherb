<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\GeneralFunctions;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class TokenAuth  extends BaseMiddleware
{
    use GeneralFunctions;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $guard = null)
    {
        auth()->shouldUse($guard == null ? 'admin-api' : $guard);
        try 
        {
            if (!$user = Auth::guard($guard == null ? 'admin-api' : $guard)->user()) 
            {
                if($guard == null) 
                {
                    auth()->shouldUse('user-api');
                    if (!$user = Auth::guard('user-api')->user())
                    {
                        throw new JWTException('UnAuthenticated', 403);
                    }
                    else
                    {
                        $guard = 'user-api';
                    }
                }
                else
                {
                    throw new JWTException('UnAuthenticated', 403);
                }
            }
            else
            {
                if ($guard == null) 
                {
                    $guard = 'admin-api';
                }
            }
        } 
        catch (TokenExpiredException $e) 
        {
            return $this->makeResponse("Failed",  $e->getCode(), $e->getMessage());
        }
        catch (JWTException $e) 
        {
            return $this->makeResponse("Failed",  $e->getCode(), $e->getMessage());
        }
        $request->request->add([
            $guard == 'admin-api' ? 'admin_id' : 'user_id' => $user['id'],
            'guard' => $guard
        ]);
        return $next($request);
    }
}

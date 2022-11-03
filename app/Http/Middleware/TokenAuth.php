<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\GeneralFunctions;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Route;

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
        try 
        {
            $user = null;
            $guards = is_null($guard) ? array_keys(array_slice(config('auth.guards'), 0, count(config('auth.guards')) - 1)) : array($guard);
            foreach ($guards as $guardType)
            {
                auth()->shouldUse($guardType);
                if ($user = Auth::guard($guardType)->user())
                {
                    $guard = $guardType;
                    break;
                }
            }
            if (!$user)
                throw new JWTException(__('AuthLang.UnAuthenticated'), 403);
            if (Route::getCurrentRoute()->getActionMethod() != 'update' || $guard != 'admin-api') 
            {
                $request->request->add([
                    $guard == 'admin-api' ? 'admin_id' : 'user_id' => $user['id'],
                    'guard' => $guard,
                    'user' => $user
                ]);
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
        return $next($request);
    }
}

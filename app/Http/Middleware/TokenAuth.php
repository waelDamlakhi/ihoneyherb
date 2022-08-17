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
        if($guard != null) {
            auth()->shouldUse($guard);
            try {
                if (!$admin = Auth::guard($guard)->user()) {
                    throw new JWTException('UnAuthenticated', 403);
                }
            } 
            catch (TokenExpiredException $e) {
                return $this->makeResponse("Failed",  $e->getCode(), $e->getMessage());
            }
            catch (JWTException $e) {
                return $this->makeResponse("Failed",  $e->getCode(), $e->getMessage());
            }
        }
        $request->request->add(['admin_id' => $admin['id']]);
        return $next($request);
    }
}

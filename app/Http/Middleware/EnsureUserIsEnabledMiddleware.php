<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureUserIsEnabledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->enabled) {
            if ($request->is('api/*')) {
                $request->user()->currentAccessToken()->delete();
                throw new AccessDeniedHttpException(trans('myMessages.AuthenticatedUserLocked'));
            } else {
                auth()->logout();
                $request->session()->flush();
                $request->session()->regenerate();
                return redirect()->route('login', Response::HTTP_FORBIDDEN)->with('userError', trans('myMessages.AuthenticatedUserLocked'));
            }
        }
        return $next($request);
    }
}

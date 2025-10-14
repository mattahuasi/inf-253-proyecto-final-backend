<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonApi\AuthenticationException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey || $apiKey !== env('API_KEY')) {
            return throw new AuthenticationException();
            // return response()->json([
                // 'error' => 'Unauthorized',
                // 'message' => 'Invalid or missing API key.',
            // ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}

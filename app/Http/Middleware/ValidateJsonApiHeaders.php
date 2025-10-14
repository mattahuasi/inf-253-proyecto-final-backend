<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class ValidateJsonApiHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->headers->get('Accept') !== 'application/vnd.api+json')
            return throw new NotAcceptableHttpException("The requested resource is capable of generating only content not acceptable according to the Accept headers sent in the request.");

        $contentType = $request->headers->get('Content-Type');
        $msg = "The request entity has a media type which the server or resource does not support.";
        if ($request->isMethod('DELETE') && Str::of($request->url())->contains('relationships')) {
            if ($contentType !== 'application/vnd.api+json')
                return throw new UnsupportedMediaTypeHttpException($msg);
        } elseif ($request->isMethod('POST') || $request->isMethod('PATCH')) {
            if ($request->routeIs('api.menus.update.photo')) {
                if (!Str::contains($contentType, 'multipart/form-data'))
                    return throw new UnsupportedMediaTypeHttpException($msg);
                return $next($request);
            } else {
                if ($contentType !== 'application/vnd.api+json' && !$request->routeIs('api.auth.logout'))
                    return throw new UnsupportedMediaTypeHttpException($msg);
            }
        }
        return $next($request)->withHeaders([
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonApi\ValidationException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonApiDocument
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('api.menus.update.photo'))
            return $next($request);

        if (($request->isMethod('POST') || $request->isMethod('PATCH')) && Str::of(request()->url())->contains('relationships')) {
            $request->validate(['data' => 'required|array']);
            if ($request->filled('data.id') && $request->filled('data.type')) {
                $request->validate([
                    'data' => 'required|array',
                    'data.id' => 'required|string',
                    'data.type' => 'required|string',
                ]);
            } elseif ($request->filled('data.*.type') && $request->filled('data.*.type')) {
                $request->validate([
                    'data' => 'required|array',
                    'data.*.id' => 'required|string',
                    'data.*.type' => 'required|string',
                ]);
            } else {
                throw ValidationException::withMessages([
                    'data' => ['El campo "data" tiene un formato inválido.']
                ]);
            }
        } elseif ($request->isMethod('POST')) {
            $request->validate([
                'data' => 'required|array',
                'data.type' => 'required|string',
                'data.attributes' => 'required|array'
            ]);
        } elseif ($request->isMethod('PATCH')) {
            $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|string',
                'data.type' => 'required|string',
                'data.attributes' => 'required|array'
            ]);
        }
        return $next($request);
    }
}

            // $request->validate([
            //     'data' => 'required|array',
            //     'data.type' => 'required|string',
            //     'data.attributes' => [
            //         Rule::requiredIf(
            //             !Str::of(request()->url())->contains('relationships')
            //         ),
            //         'array'
            //     ]
            // ]);

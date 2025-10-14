<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;


class AddressController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['show']),
        ];
    }

    public function show(string $id)
    {
        $address = Address::where('id', $id)
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $address);

        return AddressResource::make($address);
    }
}

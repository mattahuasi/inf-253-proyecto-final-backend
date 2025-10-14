<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CustomerUserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(Customer $customer)
    {
        Gate::authorize('view', $customer);
        return UserResource::make($customer->user);
    }

    public function showRelationship(Customer $customer)
    {
        Gate::authorize('view', $customer);
        return UserResource::identifier($customer->user);
    }
}

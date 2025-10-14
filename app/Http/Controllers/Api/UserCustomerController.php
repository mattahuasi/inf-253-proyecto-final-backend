<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class UserCustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship']),
        ];
    }

    public function index(User $user)
    {
        Gate::authorize('view', $user);
        return CustomerResource::make($user->person->customer);
    }

    public function showRelationship(User $user)
    {
        Gate::authorize('view', $user);
        return CustomerResource::identifier($user->person->customer);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class EmployeeUserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship']),
        ];
    }

    public function index(Employee $employee)
    {
        Gate::authorize('view', Employee::make());
        return UserResource::make($employee->user);
    }

    public function showRelationship(Employee $employee)
    {
        Gate::authorize('view', Employee::make());
        return UserResource::identifier($employee->user);
    }
}

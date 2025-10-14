<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Permission::make());
        $permissions = Permission::sparseFieldset()
            ->allowedIncludes(['roles'])
            ->allowedFilters(['id', 'name', 'description', 'type', 'roles:whereHasRoles'])
            ->allowedSorts(['id', 'name', 'description', 'type'])
            ->jsonApiPaginate();

        return PermissionResource::collection($permissions);
    }

    public function show(string $id)
    {
        $permission = Permission::where('id', $id)
            ->allowedIncludes(['roles'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $permission);

        return PermissionResource::make($permission);
    }
}

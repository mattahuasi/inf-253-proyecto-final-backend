<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class PermissionRolesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship']),
        ];
    }

    public function index(Permission $permission)
    {
        Gate::authorize('view', $permission);

        $roles = $permission->roles()
            ->allowedFilters(['id', 'name'])
            ->allowedSorts(['id', 'name'])
            ->sparseFieldset()
            ->jsonApiPaginate();

        return RoleResource::collection($roles);
    }

    public function showRelationship(Permission $permission)
    {
        Gate::authorize('view', $permission);

        return RoleResource::identifiers($permission->roles);
    }
}

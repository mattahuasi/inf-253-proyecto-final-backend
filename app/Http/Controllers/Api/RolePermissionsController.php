<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class RolePermissionsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'showRelationship', 'attachRelationship', 'updateRelationship', 'detachRelationship']),
        ];
    }

    public function index(Role $role)
    {
        Gate::authorize('view', $role);

        $permissions = $role->permissions()
            ->allowedFilters(['id', 'name', 'description', 'type'])
            ->allowedSorts(['id', 'name', 'description', 'type'])
            ->sparseFieldset()
            ->jsonApiPaginate();

        return PermissionResource::collection($permissions);
    }

    public function showRelationship(Role $role)
    {
        Gate::authorize('view', $role);
        return PermissionResource::identifiers($role->permissions);
    }

    public function updateRelationship(Request $request, Role $role)
    {
        Gate::authorize('update', $role);

        $validated = $request->validate([
            'data'        => 'required|array',
            'data.*.id'   => 'required|string|exists:permissions,id',
            'data.*.type' => 'required|string|in:permissions',
        ]);

        $permissionIds = collect($validated['data'])->pluck('id');
        $role->permissions()->sync($permissionIds);

        return PermissionResource::identifiers($role->permissions);
    }

    public function attachRelationship(Request $request, Role $role)
    {
        Gate::authorize('create', $role);

        $validated = $request->validate([
            'data'        => 'required|array',
            'data.*.id'   => 'required|string|exists:permissions,id',
            'data.*.type' => 'required|string|in:permissions',
        ]);

        $existingPermissionIds = $role->permissions->pluck('id');
        $permissionIds = collect($validated['data'])->pluck('id')->diff($existingPermissionIds);
        $role->permissions()->attach($permissionIds);
        return response()->noContent();
    }

    public function detachRelationship(Request $request, Role $role)
    {
        Gate::authorize('delete', $role);

        $validated = $request->validate([
            'data'        => 'required|array',
            'data.*.id'   => 'required|string|exists:permissions,id',
            'data.*.type' => 'required|string|in:permissions',
        ]);

        $permissionIds = collect($validated['data'])->pluck('id');
        $role->permissions()->detach($permissionIds);

        return response()->noContent();
    }
}

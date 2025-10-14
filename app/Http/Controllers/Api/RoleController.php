<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Role::make());

        $roles = Role::sparseFieldset()
            ->allowedIncludes(['permissions'])
            ->allowedFilters(['id', 'name', 'permissions:whereHasPermissions'])
            ->allowedSorts(['id', 'name'])
            ->jsonApiPaginate();

        return RoleResource::collection($roles);
    }

    public function store(SaveRoleRequest $request)
    {
        Gate::authorize('create', Role::make());

        $attributes = $request->validatedAttributes();
        $role = Role::make();
        $role->name = $attributes['name'];
        $role->save();

        return RoleResource::make($role);
    }

    public function show(string $id)
    {
        $role = Role::where('id', $id)
            ->allowedIncludes(['permissions'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $role);

        return RoleResource::make($role);
    }


    public function update(SaveRoleRequest $request, Role $role)
    {
        Gate::authorize('update', $role);

        $attributes = $request->validatedAttributes();
        $role->name = $attributes['name'];
        $role->save();

        return RoleResource::make($role);
    }

    public function destroy(Role $role)
    {
        Gate::authorize('delete', $role);
        $role->delete();
        return response()->noContent();
    }
}

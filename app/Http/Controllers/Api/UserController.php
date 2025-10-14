<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'update', 'destroy', 'resetPassword']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', User::make());
        $users = User::sparseFieldset()
            ->allowedIncludes(['role', 'people'])
            ->allowedFilters(['name', 'email'])
            ->allowedSorts(['name', 'email'])
            ->jsonApiPaginate();

        return UserResource::collection($users);
    }

    public function show(string $id)
    {
        $user = User::where('id', $id)
            ->allowedIncludes(['role', 'people'])
            ->sparseFieldset()
            ->firstOrFail();

        Gate::authorize('view', $user);

        return UserResource::make($user);
    }

    public function store(SaveUserRequest $request)
    {
        Gate::authorize('create', User::make());

        $attributes = $request->validatedAttributes();
        $attributes['password'] = Hash::make('password');
        $user = User::create($attributes);
        return UserResource::make($user);
    }

    public function update(SaveUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $attributes = $request->validatedAttributes();
        $user->update($attributes);
        return UserResource::make($user);
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return response()->noContent();
    }

    public function resetPassword(User $user)
    {
        Gate::authorize('update', $user);
        $user->update([
            'password' => Hash::make('password')
        ]);
        return response()->noContent();
    }
}

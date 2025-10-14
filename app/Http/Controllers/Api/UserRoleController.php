<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index(User $user)
    {
        return RoleResource::make($user->role);
    }

    public function showRelationship(User $user)
    {
        return RoleResource::identifier($user->role);
    }

    public function updateRelationship(User $user, Request $request)
    {
        // dd($request->input('data'));
        $request->validate([
            'data'        => 'required|array',
            'data.id'     => 'required|string|exists:roles,id',
            'data.type'   => 'required|string|in:roles',
        ]);

        $user->role_id = $request->input('data.id');
        $user->save();

        return RoleResource::identifier($user->role);
    }
}

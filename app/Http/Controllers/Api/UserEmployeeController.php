<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\User;

class UserEmployeeController extends Controller
{
    public function index(User $user)
    {
        return EmployeeResource::make($user->person->employee);
    }

    public function showRelationship(User $user)
    {
        return EmployeeResource::identifier($user->person->employee);
    }
}

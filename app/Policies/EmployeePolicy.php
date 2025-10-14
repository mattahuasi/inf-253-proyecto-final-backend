<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tokenCan('employee:index');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->tokenCan('employee:show');
    }

    public function create(User $user): bool
    {
        return $user->tokenCan('employee:create');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->tokenCan('employee:update');
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->tokenCan('employee:delete');
    }
}

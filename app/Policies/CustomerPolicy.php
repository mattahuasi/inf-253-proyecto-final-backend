<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tokenCan('customer:index');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->tokenCan('customer:show');
    }

    public function create(User $user): bool
    {
        return $user->tokenCan('customer:create');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->tokenCan('customer:update');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->tokenCan('customer:delete');
    }
}

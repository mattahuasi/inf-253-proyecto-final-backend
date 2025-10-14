<?php

namespace App\Policies;

use App\Models\State;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tokenCan('state:index');
    }

    public function view(User $user, State $state): bool
    {
        return $user->tokenCan('state:show');
    }

    public function create(User $user): bool
    {
        return $user->tokenCan('state:create');
    }

    public function update(User $user, State $state): bool
    {
        return $user->tokenCan('state:update');

    }

    public function delete(User $user, State $state): bool
    {
        return $user->tokenCan('state:delete');
    }
}

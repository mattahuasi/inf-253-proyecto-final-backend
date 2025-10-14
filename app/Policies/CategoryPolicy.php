<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tokenCan('category:index');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->tokenCan('category:show');
    }

    public function create(User $user): bool
    {
        return $user->tokenCan('category:create');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->tokenCan('category:update');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->tokenCan('category:delete');
    }
}

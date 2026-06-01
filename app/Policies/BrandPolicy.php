<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Brand $brand): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Brand $brand): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Brand $brand): bool
    {
        return $user->isOwner();
    }
}

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

    // Drag-reordering rewrites the public homepage's brand order — same tier
    // as update. Without this method Filament's non-strict authorization
    // silently ALLOWED any panel user (including staff) to reorder.
    public function reorder(User $user): bool
    {
        return $user->isAdmin();
    }
}

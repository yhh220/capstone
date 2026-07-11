<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Category $category): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->isOwner();
    }

    // Drag-reordering rewrites the storefront's category order — same tier as
    // update. Without this method Filament's non-strict authorization
    // silently ALLOWED any panel user (including staff) to reorder.
    public function reorder(User $user): bool
    {
        return $user->isAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // Staff manage the catalogue day-to-day (create/edit); destructive
    // operations below stay admin-only.
    public function create(User $user): bool
    {
        return $user->isStaffMember();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isStaffMember();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->isOwner();
    }
}

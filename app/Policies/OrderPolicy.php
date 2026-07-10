<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // Staff work orders day-to-day: opening the order page (whose fields are
    // read-only displays) and running the gated row actions both authorize
    // through update. Destructive operations below stay admin-only — the same
    // tiering ProductPolicy already uses.
    public function update(User $user, Order $order): bool
    {
        return $user->isStaffMember();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->isOwner();
    }
}

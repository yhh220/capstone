<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Service $service): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    // The service menu is a FIXED set (owner decision, Jul 2026): the shop
    // will not add or remove services, and the public Services page's curated
    // keyword-matched icons assume the existing names. Admins edit the rows
    // (copy, translations, photos, duration, visibility) — nothing more.
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Service $service): bool
    {
        return $user->isStaffMember();
    }

    public function delete(User $user, Service $service): bool
    {
        return false;
    }

    public function restore(User $user, Service $service): bool
    {
        return false;
    }

    public function forceDelete(User $user, Service $service): bool
    {
        return false;
    }

    // Drag-reordering rewrites the public services page (and its road
    // animation) — same tier as update. Without this method Filament's
    // non-strict authorization silently ALLOWED any panel user to reorder.
    public function reorder(User $user): bool
    {
        return $user->isStaffMember();
    }
}

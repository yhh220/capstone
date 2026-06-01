<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    // System settings (shopping toggle, business hours) are admin/owner only.
    // Staff must never be able to disable the store or change config.
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isOwner();
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->isOwner();
    }

    public function restore(User $user, Setting $setting): bool
    {
        return $user->isOwner();
    }

    public function forceDelete(User $user, Setting $setting): bool
    {
        return $user->isOwner();
    }
}

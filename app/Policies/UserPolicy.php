<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if ($model->isOwner() && ! $user->isOwner()) {
            return false;
        }
        // No peer administration: admins manage subordinates (staff), never
        // other admins — only the owner does. Editing your own account is the
        // one exception (profile upkeep, self-demotion).
        if (! $user->isOwner() && ! $model->isStaff() && $user->id !== $model->id) {
            return false;
        }

        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->isOwner()) {
            return false; // Protect owner
        }
        if ($user->id === $model->id) {
            return false; // Never let an admin delete their own account mid-session
        }
        // Only the owner may delete an admin; admins delete staff only —
        // a compromised admin account must not be able to take out its peers.
        if (! $user->isOwner() && ! $model->isStaff()) {
            return false;
        }

        return $user->isAdmin();
    }

    public function restore(User $user, User $model): bool
    {
        // Same hierarchy as delete: admins restore staff, the owner restores anyone.
        if (! $user->isOwner() && ! $model->isStaff()) {
            return false;
        }

        return $user->isAdmin();
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}

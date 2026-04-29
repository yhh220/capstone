<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;

class FeedbackPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Feedback $feedback): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Feedback $feedback): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Feedback $feedback): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Feedback $feedback): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Feedback $feedback): bool
    {
        return $user->isOwner();
    }
}

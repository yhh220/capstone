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

    // Staff curate testimonials day-to-day (add/edit); deletion stays admin-only.
    public function create(User $user): bool
    {
        return $user->isStaffMember();
    }

    public function update(User $user, Feedback $feedback): bool
    {
        return $user->isStaffMember();
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

<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Contact $contact): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Contact $contact): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Contact $contact): bool
    {
        return $user->isOwner();
    }
}

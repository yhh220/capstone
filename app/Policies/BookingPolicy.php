<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // Staff handle bookings day-to-day (reschedule, fix details) — the same
    // operational tier that already lets them confirm/complete from the table.
    // Delete/restore below stay admin-only.
    public function update(User $user, Booking $booking): bool
    {
        return $user->isStaffMember();
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return $user->isOwner();
    }
}

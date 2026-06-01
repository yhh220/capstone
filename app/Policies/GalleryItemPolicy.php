<?php

namespace App\Policies;

use App\Models\GalleryItem;
use App\Models\User;

class GalleryItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isOwner();
    }
}

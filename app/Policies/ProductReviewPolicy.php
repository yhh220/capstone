<?php

namespace App\Policies;

use App\Models\ProductReview;
use App\Models\User;

class ProductReviewPolicy
{
    public function viewAny(User $user): bool { return $user->isStaffMember(); }
    public function view(User $user, ProductReview $review): bool { return $user->isStaffMember(); }
    public function update(User $user, ProductReview $review): bool { return $user->isStaffMember(); }
    public function delete(User $user, ProductReview $review): bool { return $user->isAdmin(); }
}

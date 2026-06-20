<?php

namespace App\Policies;

use App\Models\ChatbotFaq;
use App\Models\User;

class ChatbotFaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ChatbotFaq $chatbotFaq): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ChatbotFaq $chatbotFaq): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ChatbotFaq $chatbotFaq): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, ChatbotFaq $chatbotFaq): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, ChatbotFaq $chatbotFaq): bool
    {
        return $user->isOwner();
    }
}

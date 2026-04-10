<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $hasProducts = Schema::hasTable('products');
        $hasCategories = Schema::hasTable('categories');
        $hasContacts = Schema::hasTable('contacts');
        $hasFeedback = Schema::hasTable('feedback');

        return view('livewire.admin.dashboard', [
            'stats' => [
                'products' => $hasProducts ? Product::count() : 0,
                'categories' => $hasCategories ? Category::count() : 0,
                'messages' => $hasContacts ? Contact::count() : 0,
                'unread_messages' => $hasContacts ? Contact::where('is_read', false)->count() : 0,
                'feedback' => $hasFeedback ? Feedback::count() : 0,
            ],
            'recentProducts' => $hasProducts ? Product::latest()->take(5)->get() : collect(),
            'recentMessages' => $hasContacts ? Contact::latest()->take(5)->get() : collect(),
            'recentFeedback' => $hasFeedback ? Feedback::latest()->take(5)->get() : collect(),
        ])->layout('layouts.admin');
    }
}

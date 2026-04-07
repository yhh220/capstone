<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\Contact;
use App\Models\Category;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => [
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'unread_messages' => Contact::where('is_read', false)->count(),
                'total_categories' => Category::count(),
                'total_revenue' => Order::whereIn('status', ['delivered', 'shipped'])->sum('total'),
            ],
            'recentOrders' => Order::latest()->take(5)->get(),
            'recentMessages' => Contact::where('is_read', false)->latest()->take(5)->get(),
        ])->layout('layouts.admin');
    }
}

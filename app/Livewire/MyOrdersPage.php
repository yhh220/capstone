<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrdersPage extends Component
{
    use SetsSeo, WithPagination;

    public function mount(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        $this->setSeo(
            title: 'My Orders',
            description: 'View your order history.',
        );
    }

    public function render()
    {
        return view('livewire.my-orders-page', [
            'orders' => Order::where('user_id', Auth::id())
                ->with('items')
                ->latest()
                ->paginate(10),
        ])->layout('layouts.app');
    }
}

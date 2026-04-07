<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class OrderManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $viewingId = null;

    public function updateStatus(int $id, string $status): void
    {
        Order::findOrFail($id)->update(['status' => $status]);
        session()->flash('success', 'Order status updated.');
    }

    public function viewOrder(int $id): void
    {
        $this->viewingId = $id;
    }

    public function closeView(): void
    {
        $this->viewingId = null;
    }

    public function render()
    {
        $query = Order::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.admin.order-manager', [
            'orders' => $query->latest()->paginate(15),
            'viewingOrder' => $this->viewingId ? Order::find($this->viewingId) : null,
        ])->layout('layouts.admin');
    }
}

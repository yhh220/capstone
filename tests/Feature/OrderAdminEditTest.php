<?php

namespace Tests\Feature;

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderAdminEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function order(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'WW-2026-00001',
            'customer_name' => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total_amount' => 100,
            'status' => 'pending',
            'payment_status' => 'paid',
        ], $overrides));
    }

    public function test_delete_action_hidden_for_pending_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');
        $order = $this->order(['status' => 'pending']);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('delete');
    }

    public function test_delete_action_hidden_for_processing_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');
        $order = $this->order(['status' => 'processing']);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('delete');
    }

    public function test_delete_action_visible_for_cancelled_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');
        $order = $this->order(['status' => 'cancelled']);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionVisible('delete');
    }

    public function test_owner_alert_view_order_link_opens_the_edit_page(): void
    {
        // Every owner-alert email deep-links to url('/admin/orders/{id}/edit').
        // This pins that URL shape to a real 200 for an existing order — and
        // documents that a DELETED order's link 404s (the email outlives the
        // record), which is expected, not a routing bug.
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        $order = $this->order(['status' => 'delivered']);
        $link = url('/admin/orders/'.$order->getKey().'/edit');

        $this->get($link)->assertOk();

        $order->delete(); // soft delete — what admin cleanup does
        // Status stays a true 404, but a signed-in admin sees the friendly
        // "record not found" page instead of the framework's bare error.
        $this->get($link)
            ->assertNotFound()
            ->assertSee('Record Not Found')
            ->assertSee('outlived the record');
    }
}

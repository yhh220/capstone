<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function paidOrder(User $user): Order
    {
        $order = Order::create([
            'user_id'        => $user->id,
            'order_number'   => Order::generateOrderNumber(),
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1 Jln', 'city' => 'KL', 'postcode' => '50000', 'state' => 'KL'],
            'subtotal'       => 600,
            'shipping_fee'   => 10,
            'total_amount'   => 610,
            'status'         => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'FPX - Maybank2u',
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => null, 'product_name' => 'Amp',
            'quantity' => 2, 'unit_price' => 300, 'subtotal' => 600,
        ]);

        return $order;
    }

    public function test_owner_can_view_invoice_and_download_pdf(): void
    {
        $user = User::create(['name' => 'Owner', 'email' => 'o@example.test', 'password' => 'password', 'role' => 'client']);
        $order = $this->paidOrder($user);

        $this->actingAs($user)
            ->get(route('invoice.show', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('610.00');

        $pdf = $this->actingAs($user)->get(route('invoice.pdf', $order->order_number));
        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));
    }

    public function test_a_stranger_cannot_view_someone_elses_invoice(): void
    {
        $owner = User::create(['name' => 'Owner', 'email' => 'o2@example.test', 'password' => 'password', 'role' => 'client']);
        $order = $this->paidOrder($owner);
        $stranger = User::create(['name' => 'Nosy', 'email' => 'nosy@example.test', 'password' => 'password', 'role' => 'client']);

        $this->actingAs($stranger)
            ->get(route('invoice.show', $order->order_number))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_invoice(): void
    {
        $owner = User::create(['name' => 'Owner', 'email' => 'o3@example.test', 'password' => 'password', 'role' => 'client']);
        $order = $this->paidOrder($owner);
        $admin = User::forceCreate(['name' => 'Admin', 'email' => 'a@example.test', 'password' => 'password', 'role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($admin)
            ->get(route('invoice.show', $order->order_number))
            ->assertOk();
    }
}

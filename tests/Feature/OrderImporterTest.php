<?php

namespace Tests\Feature;

use App\Filament\Imports\OrderImporter;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\User;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Regression test: every other way to change an order's status in this admin
 * panel (markPaid/markShipped/markDelivered) enforces a strict forward-only
 * chain gated on the order's current status — the order edit form's status
 * field is even ->disabled() so those actions are the only legitimate path.
 * OrderImporter::beforeValidate() had no equivalent guard, so an import row
 * could jump a never-paid order straight to "delivered" (wrongly emailing
 * the customer) or regress a delivered order back to "processing" (leaving
 * shipped_at/delivered_at stamped on a now-earlier status).
 */
class OrderImporterTest extends TestCase
{
    use RefreshDatabase;

    private const COLUMNS = ['order_number', 'status', 'tracking_number', 'customer_name', 'customer_phone', 'street', 'city', 'postcode', 'state'];

    private function makeImporter(bool $notifyCustomers = false): OrderImporter
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $import = Import::create([
            'file_name' => 'orders.csv',
            'file_path' => 'orders.csv',
            'importer' => OrderImporter::class,
            'total_rows' => 1,
            'user_id' => $admin->id,
        ]);

        $columnMap = array_combine(self::COLUMNS, self::COLUMNS);

        return new OrderImporter($import, $columnMap, ['notifyCustomers' => $notifyCustomers]);
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
            'payment_status' => 'pending',
        ], $overrides));
    }

    private function row(Order $order, array $overrides = []): array
    {
        return array_merge(array_fill_keys(self::COLUMNS, null), ['order_number' => $order->order_number], $overrides);
    }

    public function test_unpaid_order_cannot_be_jumped_straight_to_delivered(): void
    {
        Mail::fake();
        $order = $this->order(['status' => 'pending', 'payment_status' => 'pending']);
        $importer = $this->makeImporter();

        $this->expectException(RowImportFailedException::class);

        try {
            $importer($this->row($order, ['status' => 'delivered']));
        } finally {
            $this->assertSame('pending', $order->refresh()->status);
            Mail::assertNothingSent();
        }
    }

    public function test_pending_order_cannot_become_processing_without_being_paid(): void
    {
        $order = $this->order(['status' => 'pending', 'payment_status' => 'pending']);
        $importer = $this->makeImporter();

        $this->expectException(RowImportFailedException::class);

        try {
            $importer($this->row($order, ['status' => 'processing']));
        } finally {
            $this->assertSame('pending', $order->refresh()->status);
        }
    }

    public function test_paid_pending_order_can_become_processing(): void
    {
        $order = $this->order(['status' => 'pending', 'payment_status' => 'paid']);
        $importer = $this->makeImporter();

        $importer($this->row($order, ['status' => 'processing']));

        $this->assertSame('processing', $order->refresh()->status);
    }

    public function test_processing_order_can_become_shipped_with_tracking_number(): void
    {
        Mail::fake();
        $order = $this->order(['status' => 'processing', 'payment_status' => 'paid']);
        $importer = $this->makeImporter(notifyCustomers: true);

        $importer($this->row($order, ['status' => 'shipped', 'tracking_number' => 'TRK-1']));

        $order->refresh();
        $this->assertSame('shipped', $order->status);
        $this->assertNotNull($order->shipped_at);
        Mail::assertSent(OrderShippedMail::class);
    }

    public function test_pending_order_cannot_skip_directly_to_shipped(): void
    {
        $order = $this->order(['status' => 'pending', 'payment_status' => 'paid']);
        $importer = $this->makeImporter();

        $this->expectException(RowImportFailedException::class);

        try {
            $importer($this->row($order, ['status' => 'shipped', 'tracking_number' => 'TRK-1']));
        } finally {
            $this->assertSame('pending', $order->refresh()->status);
        }
    }

    public function test_delivered_order_cannot_be_regressed_to_processing(): void
    {
        $order = $this->order(['status' => 'delivered', 'payment_status' => 'paid', 'delivered_at' => now()]);
        $importer = $this->makeImporter();

        $this->expectException(RowImportFailedException::class);

        try {
            $importer($this->row($order, ['status' => 'processing']));
        } finally {
            $this->assertSame('delivered', $order->refresh()->status);
        }
    }

    public function test_reimporting_the_same_status_is_a_safe_noop(): void
    {
        $order = $this->order(['status' => 'shipped', 'payment_status' => 'paid', 'tracking_number' => 'TRK-1']);
        $importer = $this->makeImporter();

        $importer($this->row($order, ['status' => 'shipped', 'tracking_number' => 'TRK-1', 'customer_name' => 'Updated Name']));

        $order->refresh();
        $this->assertSame('shipped', $order->status);
        $this->assertSame('Updated Name', $order->customer_name);
    }

    public function test_cancelled_order_still_cannot_be_reopened(): void
    {
        $order = $this->order(['status' => 'cancelled', 'payment_status' => 'paid']);
        $importer = $this->makeImporter();

        $this->expectException(RowImportFailedException::class);

        $importer($this->row($order, ['status' => 'processing']));
    }
}

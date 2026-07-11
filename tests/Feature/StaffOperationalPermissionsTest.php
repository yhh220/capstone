<?php

namespace Tests\Feature;

use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Staff are the shop's operational workers: they confirm bookings, settle and
 * ship orders, import data, and maintain the catalogue — but they can never
 * EXPORT data (bulk exfiltration stays admin-only) and never delete anything.
 */
class StaffOperationalPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    private function makeOrder(): Order
    {
        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => 'Buyer',
            'customer_email' => 'buyer@example.test',
            'customer_phone' => '0123456789',
            'shipping_address' => ['street' => '1 Jln', 'city' => 'KL', 'postcode' => '50000', 'state' => 'KL'],
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total_amount' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'FPX - Maybank2u',
        ]);
    }

    public function test_staff_can_mark_an_order_paid(): void
    {
        Mail::fake();
        $order = $this->makeOrder();
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListOrders::class)
            ->callTableAction('markPaid', $order);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
    }

    public function test_staff_can_mark_a_paid_order_shipped(): void
    {
        Mail::fake();
        $order = $this->makeOrder();
        $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListOrders::class)
            ->callTableAction('markShipped', $order, ['tracking_number' => 'TRK123456']);

        $this->assertSame('shipped', $order->refresh()->status);
    }

    public function test_ready_for_pickup_emails_the_customer_with_collection_wording(): void
    {
        Mail::fake();
        $order = $this->makeOrder();
        $order->update(['payment_status' => 'paid', 'status' => 'processing', 'delivery_method' => 'pickup']);
        $this->actingAs($this->staff(), 'admin');

        // Same action as shipping, but a pickup order needs no tracking number.
        Livewire::test(ListOrders::class)
            ->callTableAction('markShipped', $order);

        $this->assertSame('shipped', $order->refresh()->status);

        // The customer must actually be told to come and collect — with the
        // pickup subject line, not the courier "has shipped" one.
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\OrderShippedMail::class, function ($mail) use ($order) {
            return $mail->hasTo($order->customer_email)
                && str_contains($mail->envelope()->subject, 'ready for pickup');
        });
    }

    public function test_staff_can_confirm_a_booking(): void
    {
        Mail::fake();
        $booking = Booking::create([
            'reference' => Booking::generateReference(),
            'customer_name' => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'preferred_date' => now()->addDay()->toDateString(),
            'start_at' => now()->addDay()->setTime(10, 0),
            'end_at' => now()->addDay()->setTime(11, 0),
            'status' => 'pending',
        ]);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListBookings::class)
            ->callTableAction('confirmBooking', $booking);

        $this->assertSame('confirmed', $booking->refresh()->status);
    }

    public function test_staff_can_import_but_never_export_orders(): void
    {
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListOrders::class)
            ->assertActionVisible('import')
            ->assertActionHidden('export');
    }

    public function test_staff_can_import_but_never_export_products(): void
    {
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListProducts::class)
            ->assertActionVisible('import')
            ->assertActionHidden('export');
    }

    public function test_admin_keeps_the_export_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        Livewire::test(ListOrders::class)->assertActionVisible('export');
        Livewire::test(ListProducts::class)->assertActionVisible('export');
    }

    public function test_staff_manage_products_but_cannot_delete_them(): void
    {
        $staff = $this->staff();
        $product = Product::create(['name' => 'Amp', 'slug' => 'amp', 'price' => 300, 'stock' => 1, 'is_active' => true]);

        $this->assertTrue($staff->can('create', Product::class));
        $this->assertTrue($staff->can('update', $product));
        $this->assertFalse($staff->can('delete', $product));
    }

    public function test_staff_can_cancel_and_refund_orders(): void
    {
        $pending = $this->makeOrder();
        $refundable = $this->makeOrder();
        $refundable->update([
            'payment_status' => 'paid',
            'status' => 'cancelled',
            'refund_amount' => 100,
        ]);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListOrders::class)
            ->assertTableActionVisible('cancelOrder', $pending)
            ->assertTableActionVisible('markRefunded', $refundable);
    }

    public function test_staff_can_read_the_contact_inbox_and_mark_messages_read(): void
    {
        $staff = $this->staff();
        $message = Contact::create([
            'name' => 'Asker',
            'email' => 'asker@example.test',
            'subject' => 'Question',
            'message' => 'When do you open?',
        ]);

        $this->assertTrue($staff->can('viewAny', Contact::class));
        $this->assertTrue($staff->can('update', $message));   // mark as read
        $this->assertFalse($staff->can('delete', $message));
    }

    public function test_staff_can_curate_testimonials_but_not_delete_them(): void
    {
        $staff = $this->staff();
        $testimonial = Feedback::create([
            'name' => 'Happy Customer',
            'message' => 'Great installation service!',
            'rating' => 5,
        ]);

        $this->assertTrue($staff->can('create', Feedback::class));
        $this->assertTrue($staff->can('update', $testimonial));
        $this->assertFalse($staff->can('delete', $testimonial));
    }

    public function test_staff_can_work_orders_and_bookings_but_not_admin_curated_content(): void
    {
        $staff = $this->staff();
        $order = $this->makeOrder();

        // Operational tier: order details and booking edits are staff work.
        // (Unsaved instances — these policies only inspect the user's role.)
        $this->assertTrue($staff->can('update', $order));
        $this->assertTrue($staff->can('update', new Booking));

        // Admin-curated content stays read-only for staff.
        $this->assertFalse($staff->can('update', new \App\Models\Service));
        $this->assertFalse($staff->can('update', new \App\Models\Brand));
        $this->assertFalse($staff->can('update', new \App\Models\Category));
    }

    public function test_staff_cannot_reorder_admin_curated_tables(): void
    {
        // Filament allows reordering whenever the policy has NO reorder()
        // method — these assertions pin the explicit methods so the storefront
        // ordering of brands, categories, and services stays admin-only, while
        // testimonial curation (a staff duty) keeps its reorder rights.
        $staff = $this->staff();

        $this->assertFalse($staff->can('reorder', \App\Models\Brand::class));
        $this->assertFalse($staff->can('reorder', \App\Models\Category::class));
        $this->assertFalse($staff->can('reorder', \App\Models\Service::class));
        $this->assertTrue($staff->can('reorder', Feedback::class));
    }
}

<?php

namespace Tests\Feature;

use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\Feedback\Pages\ListFeedback;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regression test: every policy below restricts delete() to isAdmin(), but
 * none of the tables' DeleteBulkAction had a ->visible()/->authorize() guard.
 * Filament's default per-action authorization for DeleteBulkAction resolves
 * the "deleteAny" ability, which none of these policies define — and with
 * policy-existence checking on (the default) and strict mode off (also the
 * default), an undefined ability silently resolves to allow for everyone.
 * Since staff already has viewAny() access to all of these lists, the bulk
 * delete button was visible and fully functional for staff despite delete()
 * being admin-only.
 */
class StaffBulkDeleteAuthorizationTest extends TestCase
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

    public function test_staff_cannot_bulk_delete_bookings(): void
    {
        $booking = Booking::create([
            'reference'      => Booking::generateReference(),
            'customer_name'  => 'Test',
            'customer_email' => 'test@example.test',
            'customer_phone' => '0123456789',
            'preferred_date' => now()->addDay()->toDateString(),
            'start_at'       => now()->addDay()->setTime(10, 0),
            'end_at'         => now()->addDay()->setTime(11, 0),
            'status'         => 'pending',
        ]);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListBookings::class)
            ->assertTableBulkActionHidden('delete');

        $this->assertNotNull(Booking::find($booking->id));
    }

    public function test_staff_cannot_bulk_delete_brands(): void
    {
        $brand = Brand::create(['name' => 'Pioneer']);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListBrands::class)
            ->assertTableBulkActionHidden('delete');

        $this->assertNotNull(Brand::find($brand->id));
    }

    public function test_staff_cannot_bulk_delete_categories(): void
    {
        $category = Category::create(['name' => 'Speakers', 'slug' => 'speakers']);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListCategories::class)
            ->assertTableBulkActionHidden('delete');

        $this->assertNotNull(Category::find($category->id));
    }

    public function test_staff_cannot_bulk_delete_contacts(): void
    {
        $contact = Contact::create([
            'name'    => 'Test',
            'email'   => 'test@example.test',
            'subject' => 'Question',
            'message' => 'Hello',
        ]);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListContacts::class)
            ->assertTableBulkActionHidden('delete');

        $this->assertNotNull(Contact::find($contact->id));
    }

    public function test_staff_cannot_bulk_delete_feedback(): void
    {
        $feedback = Feedback::create([
            'name'    => 'Test',
            'message' => 'Great service',
            'rating'  => 5,
        ]);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListFeedback::class)
            ->assertTableBulkActionHidden('delete');

        $this->assertNotNull(Feedback::find($feedback->id));
    }

    public function test_staff_cannot_bulk_delete_products(): void
    {
        $product = Product::create([
            'name'      => 'Speaker Kit',
            'slug'      => 'speaker-kit',
            'price'     => 250,
            'stock'     => 5,
            'is_active' => true,
        ]);
        $this->actingAs($this->staff(), 'admin');

        Livewire::test(ListProducts::class)
            ->assertTableBulkActionHidden('delete');

        $this->assertNotNull(Product::find($product->id));
    }

    public function test_admin_can_still_bulk_delete_products(): void
    {
        $product = Product::create([
            'name'      => 'Speaker Kit',
            'slug'      => 'speaker-kit',
            'price'     => 250,
            'stock'     => 5,
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        Livewire::test(ListProducts::class)
            ->callTableBulkAction('delete', [$product]);

        $this->assertNull(Product::find($product->id));
    }
}

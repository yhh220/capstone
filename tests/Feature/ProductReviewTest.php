<?php

namespace Tests\Feature;

use App\Livewire\ProductDetail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_a_completed_purchaser_can_submit_a_product_review(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $product = Product::create(['name' => 'Dashcam', 'slug' => 'review-dashcam', 'price' => 100, 'stock' => 1, 'is_active' => true]);

        Livewire::actingAs($user)->test(ProductDetail::class, ['slug' => $product->slug])
            ->set('reviewComment', 'This should not be accepted without a completed order.')
            ->call('submitReview')
            ->assertHasErrors('reviewComment');

        $order = Order::create([
            'user_id' => $user->id, 'order_number' => Order::generateOrderNumber(), 'customer_name' => $user->name,
            'customer_email' => $user->email, 'customer_phone' => '0123456789', 'subtotal' => 100,
            'shipping_fee' => 0, 'total_amount' => 100, 'status' => 'delivered', 'payment_status' => 'paid',
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'unit_price' => 100, 'subtotal' => 100]);

        Livewire::actingAs($user)->test(ProductDetail::class, ['slug' => $product->slug])
            ->set('reviewRating', 5)
            ->set('reviewComment', 'Clear picture quality and easy to use every day.')
            ->call('submitReview')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('product_reviews', ['product_id' => $product->id, 'user_id' => $user->id, 'rating' => 5, 'is_approved' => false]);
    }

    public function test_only_approved_reviews_are_shown_to_visitors(): void
    {
        $product = Product::create(['name' => 'Speaker', 'slug' => 'review-speaker', 'price' => 100, 'stock' => 1, 'is_active' => true]);
        $approved = User::factory()->create();
        $pending = User::factory()->create();
        ProductReview::create(['product_id' => $product->id, 'user_id' => $approved->id, 'rating' => 5, 'comment' => 'Approved review that customers can see.', 'is_approved' => true]);
        ProductReview::create(['product_id' => $product->id, 'user_id' => $pending->id, 'rating' => 1, 'comment' => 'Private pending review.', 'is_approved' => false]);

        Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->assertSee('Approved review that customers can see.')
            ->assertDontSee('Private pending review.');
    }
}

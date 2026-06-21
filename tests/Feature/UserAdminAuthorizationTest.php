<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_owner_admin_cannot_open_the_owner_edit_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        $this->get(UserResource::getUrl('edit', ['record' => $owner]))
            ->assertForbidden();
    }

    public function test_owner_can_open_their_own_edit_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $this->actingAs($owner, 'admin');

        $this->get(UserResource::getUrl('edit', ['record' => $owner]))
            ->assertOk();
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_admin_can_delete_a_staff_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $this->assertTrue($admin->can('delete', $staff));
    }

    public function test_nobody_can_delete_the_owner(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($admin->can('delete', $owner));
        $this->assertFalse($owner->can('delete', $owner));
    }

    public function test_admin_can_restore_a_soft_deleted_staff_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $staff->delete();

        $this->assertTrue($admin->can('restore', $staff));
    }
}

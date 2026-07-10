<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    /**
     * Regression test: UserPolicy::delete() always protected the owner, but
     * the table's bulk delete action never actually consulted it — Filament's
     * DeleteBulkAction only checks per-record policies when explicitly told
     * to via authorizeIndividualRecords(). Without that wired up, an admin
     * selecting the owner's row and confirming bulk delete would delete them
     * with no authorization check at all, despite every can('delete', ...)
     * unit test above passing.
     */
    public function test_bulk_delete_action_cannot_delete_the_owner(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        Livewire::test(ListUsers::class)
            ->callTableBulkAction('delete', [$owner]);

        $this->assertNotSoftDeleted($owner);
    }

    public function test_bulk_delete_action_can_delete_a_staff_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($admin, 'admin');

        Livewire::test(ListUsers::class)
            ->callTableBulkAction('delete', [$staff]);

        $this->assertSoftDeleted($staff);
    }

    /**
     * Unlike the bulk action, Filament auto-resolves single-record DeleteAction
     * authorization against the resource's policy with no opt-in needed — this
     * confirms the header "Delete" button on an admin's own edit page already
     * respects UserPolicy::delete()'s no-self-deletion rule.
     */
    public function test_single_delete_action_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        Livewire::test(EditUser::class, ['record' => $admin->getRouteKey()])
            ->assertActionHidden('delete');

        $this->assertNotSoftDeleted($admin);
    }

    public function test_admin_cannot_change_any_role_not_even_their_own(): void
    {
        // Role changes are owner-exclusive: an admin saving their own record
        // with a crafted role keeps their current role.
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        Livewire::test(EditUser::class, ['record' => $admin->getRouteKey()])
            ->fillForm(['role' => 'staff'])
            ->call('save');

        $this->assertSame('admin', $admin->refresh()->role);
    }

    public function test_admin_cannot_change_a_staff_members_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($admin, 'admin');

        Livewire::test(EditUser::class, ['record' => $staff->getRouteKey()])
            ->fillForm(['role' => 'admin'])
            ->call('save');

        $this->assertSame('staff', $staff->refresh()->role);
    }

    public function test_owner_can_promote_a_staff_member_to_admin(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($owner, 'admin');

        Livewire::test(EditUser::class, ['record' => $staff->getRouteKey()])
            ->fillForm(['role' => 'admin'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('admin', $staff->refresh()->role);
    }

    /**
     * The role Select only offers 'owner' as an option when the acting user
     * is already the owner, but that's UI-only. This proves the server-side
     * backstop in EditUser::handleRecordUpdate() actually holds if a request
     * is crafted to bypass the Select (e.g. devtools-edited form payload).
     */
    public function test_admin_cannot_promote_self_to_owner_via_a_crafted_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        Livewire::test(EditUser::class, ['record' => $admin->getRouteKey()])
            ->fillForm(['role' => 'owner'])
            ->call('save');

        $this->assertSame('admin', $admin->refresh()->role);
    }

    /*
     * Role hierarchy: admins manage subordinates (staff), never their peers.
     * Only the owner creates, edits, deletes, or restores admins.
     */

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peer = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($admin->can('delete', $peer));
    }

    public function test_owner_can_delete_an_admin(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($owner->can('delete', $admin));
    }

    public function test_admin_cannot_restore_a_soft_deleted_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peer = User::factory()->create(['role' => 'admin']);
        $peer->delete();

        $this->assertFalse($admin->can('restore', $peer));
    }

    public function test_admin_cannot_open_another_admins_edit_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peer = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        $this->get(UserResource::getUrl('edit', ['record' => $peer]))
            ->assertForbidden();
    }

    public function test_owner_can_open_an_admins_edit_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($owner, 'admin');

        $this->get(UserResource::getUrl('edit', ['record' => $admin]))
            ->assertOk();
    }

    /**
     * The role Select only offers Staff to a non-owner, but that's UI-only.
     * A crafted payload must never let an admin mint a peer.
     */
    public function test_admin_cannot_create_an_admin_via_a_crafted_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin');

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Crafted Admin',
                'email' => 'crafted-admin@example.test',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'admin',
            ])
            ->call('create');

        $created = User::where('email', 'crafted-admin@example.test')->first();
        $this->assertTrue($created === null || $created->role === 'staff', 'A crafted payload must never mint an admin.');
    }

    public function test_owner_can_create_an_admin(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $this->actingAs($owner, 'admin');

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New Admin',
                'email' => 'new-admin@example.test',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'admin',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('admin', User::where('email', 'new-admin@example.test')->first()->role);
    }

    /**
     * A customer (client role) must never reach the Filament panel: the web
     * guard doesn't carry over, and even a client session forced onto the
     * admin guard is logged out and bounced by AdminMiddleware +
     * canAccessPanel().
     */
    public function test_a_customer_cannot_access_the_admin_panel(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        // Logged in as a normal shopper (web guard): /admin is not authenticated.
        $this->actingAs($client)->get('/admin')->assertRedirect();

        // Even forced onto the admin guard, Filament's canAccessPanel()
        // hard-rejects the client role with a 403.
        $this->actingAs($client, 'admin');
        $this->get('/admin')->assertForbidden();
    }
}

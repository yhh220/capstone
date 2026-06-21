<?php

namespace Tests\Feature;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_view_the_activity_log(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff, 'admin')
            ->get(ActivityResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_the_activity_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'admin')
            ->get(ActivityResource::getUrl('index'))
            ->assertOk();
    }
}

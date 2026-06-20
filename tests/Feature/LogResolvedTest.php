<?php

namespace Tests\Feature;

use App\Filament\Resources\Logs\LogResource;
use App\Models\AppLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogResolvedTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolved_errors_drop_out_of_the_navigation_badge(): void
    {
        $fixed = AppLog::create(['level' => 3, 'level_name' => 'error', 'message' => 'Old, already fixed', 'channel' => 'test', 'logged_at' => now()]);
        AppLog::create(['level' => 3, 'level_name' => 'error', 'message' => 'Still happening', 'channel' => 'test', 'logged_at' => now()]);

        $this->assertSame('2', LogResource::getNavigationBadge());

        $fixed->update(['resolved_at' => now()]);

        $this->assertSame('1', LogResource::getNavigationBadge());
    }

    public function test_hide_fixed_filter_excludes_resolved_rows_by_default(): void
    {
        $admin = User::forceCreate([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('secret'), 'role' => 'admin',
        ]);

        AppLog::create(['level' => 3, 'level_name' => 'error', 'message' => 'Already fixed bug', 'channel' => 'test', 'logged_at' => now(), 'resolved_at' => now()]);
        AppLog::create(['level' => 3, 'level_name' => 'error', 'message' => 'Still open bug', 'channel' => 'test', 'logged_at' => now()]);

        $this->actingAs($admin, 'admin')
            ->get(LogResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Still open bug')
            ->assertDontSee('Already fixed bug');
    }
}

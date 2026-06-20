<?php

namespace Tests\Feature;

use App\Filament\Resources\Logs\LogResource;
use App\Models\AppLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_trace_link_filters_to_only_that_request(): void
    {
        $admin = User::forceCreate([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('secret'), 'role' => 'admin',
        ]);

        AppLog::create(['level' => 3, 'level_name' => 'error', 'message' => 'Error on trace AAA', 'channel' => 'test', 'trace_id' => 'trace-AAA', 'logged_at' => now()]);
        AppLog::create(['level' => 3, 'level_name' => 'error', 'message' => 'Error on trace BBB', 'channel' => 'test', 'trace_id' => 'trace-BBB', 'logged_at' => now()]);

        $url = LogResource::getUrl('index', ['trace_id' => 'trace-AAA']);

        $this->actingAs($admin, 'admin')
            ->get($url)
            ->assertOk()
            ->assertSee('Error on trace AAA')
            ->assertDontSee('Error on trace BBB');
    }
}

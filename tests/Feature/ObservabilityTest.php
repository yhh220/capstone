<?php

namespace Tests\Feature;

use App\Filament\Pages\SystemStatus;
use App\Models\AppLog;
use App\Support\Breadcrumbs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ObservabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_error_logs_are_persisted_with_trace_and_breadcrumbs(): void
    {
        Context::add('trace_id', 'trace-abc-123');
        Breadcrumbs::push('test', 'did a thing', ['x' => 1]);

        Log::channel('database')->error('something broke', ['k' => 'v']);

        $row = AppLog::where('message', 'something broke')->first();
        $this->assertNotNull($row);
        $this->assertSame('error', $row->level_name);
        $this->assertSame('trace-abc-123', $row->trace_id);
        $this->assertArrayHasKey('breadcrumbs', $row->context);
        $this->assertSame('did a thing', $row->context['breadcrumbs'][0]['message']);
    }

    public function test_debug_logs_below_the_threshold_are_not_persisted(): void
    {
        Log::channel('database')->debug('just noise');

        $this->assertDatabaseMissing('app_logs', ['message' => 'just noise']);
    }

    public function test_logging_never_throws_when_the_table_is_missing(): void
    {
        Schema::drop('app_logs');

        Log::channel('database')->error('orphan'); // must be a silent no-op, not a crash

        $this->assertTrue(true);
    }

    public function test_old_logs_are_prunable(): void
    {
        AppLog::create(['level' => 400, 'level_name' => 'error', 'message' => 'old', 'logged_at' => now()->subDays(30)]);
        AppLog::create(['level' => 400, 'level_name' => 'error', 'message' => 'fresh', 'logged_at' => now()]);

        $prunable = (new AppLog)->prunable()->pluck('message')->all();

        $this->assertContains('old', $prunable);
        $this->assertNotContains('fresh', $prunable);
    }

    public function test_a_trace_id_header_is_returned(): void
    {
        $this->get('/login')->assertHeader('X-Request-Id');
    }

    public function test_status_checks_report_database_and_cache_up(): void
    {
        $checks = collect(app(SystemStatus::class)->getChecks())->keyBy('name');

        $this->assertSame('ok', $checks['Your data']['status']);
        $this->assertSame('ok', $checks['Website speed']['status']);
    }
}

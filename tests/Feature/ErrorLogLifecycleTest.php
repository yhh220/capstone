<?php

namespace Tests\Feature;

use App\Models\AppLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * The admin error-log lifecycle: fingerprint grouping on write, automatic
 * regression-reopen, the "check for recurrence" state machine, and the
 * silence-window auto-resolve command.
 *
 * Regression guards: grouping used to compare PHP's byte-based substr()
 * against SQL's character-based SUBSTR() (breaking on any multibyte message),
 * and the recurrence check anchored on the group's NEWEST row — so "still
 * recurring" was unreachable and the action always resolved everything.
 */
class ErrorLogLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function makeLog(string $message, Carbon $at, ?Carbon $resolvedAt = null): AppLog
    {
        return AppLog::create([
            'level'       => 400,
            'level_name'  => 'error',
            'message'     => $message,
            'fingerprint' => AppLog::fingerprintFor($message),
            'logged_at'   => $at,
            'created_at'  => $at,
            'resolved_at' => $resolvedAt,
        ]);
    }

    public function test_fingerprint_is_multibyte_safe(): void
    {
        // 175 Chinese characters — a byte-based cut would split a character.
        $message = str_repeat('数据库连接失败，请检查配置。', 25);

        logger()->error($message);

        $row = AppLog::where('level_name', 'error')->latest('id')->first();

        $this->assertSame(mb_substr($message, 0, 100), $row->fingerprint);
        $this->assertSame(100, mb_strlen($row->fingerprint));
    }

    public function test_new_occurrence_reopens_a_resolved_error(): void
    {
        $message = '支付网关超时：' . str_repeat('订单处理失败', 20);

        $old = $this->makeLog($message, now()->subDays(3), resolvedAt: now()->subDay());

        logger()->error($message); // same error happens again

        $this->assertNull($old->fresh()->resolved_at, 'A recurrence must reopen the previously-resolved entry.');
    }

    public function test_recurrence_state_is_active_when_seen_within_the_hour(): void
    {
        $entry = $this->makeLog('Connection refused', now()->subMinutes(30));

        $this->assertSame('active', $entry->recurrenceState()['state']);
    }

    public function test_recurrence_state_detects_a_later_recurrence(): void
    {
        // The bug this guards against: anchoring on the group's newest row made
        // this exact case report "no recurrence" and resolve everything.
        $entry = $this->makeLog('Connection refused', now()->subDays(3));
        $this->makeLog('Connection refused', now()->subDays(2)); // recurred later

        $this->assertSame('recurred', $entry->recurrenceState()['state']);
    }

    public function test_recurrence_state_is_quiet_for_a_single_old_burst_and_resolves_the_group(): void
    {
        $entry = $this->makeLog('Connection refused', now()->subDays(3));
        $this->makeLog('Connection refused', now()->subDays(3)->addSeconds(30)); // same burst

        $this->assertSame('quiet', $entry->recurrenceState()['state']);

        $this->assertSame(2, $entry->resolveSiblings());
        $this->assertSame(0, AppLog::whereNull('resolved_at')->count());
    }

    public function test_auto_resolve_keeps_recently_seen_errors_open(): void
    {
        // Group A: old entry, but the same error recurred an hour ago → stays open.
        $stillActive = $this->makeLog('Gateway timeout', now()->subDays(3));
        $recent      = $this->makeLog('Gateway timeout', now()->subHour());

        // Group B: silent for 3 days → auto-resolved.
        $wentQuiet = $this->makeLog('Disk full', now()->subDays(3));

        $this->artisan('logs:auto-resolve', ['--hours' => 48])->assertSuccessful();

        $this->assertNull($stillActive->fresh()->resolved_at, 'An error still recurring inside the window must stay open.');
        $this->assertNull($recent->fresh()->resolved_at);
        $this->assertNotNull($wentQuiet->fresh()->resolved_at, 'An error silent past the window must be auto-resolved.');
    }
}

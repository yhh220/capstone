<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Filament's own import/export actions only persist a completion notification
 * to the database when the job runs on a real queue connection — ours are
 * forced to 'sync' (no queue worker required), so Filament shows a toast only.
 * This persists the same result to the acting user's notification bell too,
 * scoped to that user via the notifiable relation (each staff/admin/owner only
 * ever sees notifications sent to their own account).
 */
trait NotifiesImportExportCompletion
{
    private static function notifyCompletionToDatabase(
        ?Authenticatable $user,
        string $title,
        string $body,
        int $failedRowsCount,
        int $totalRows,
    ): void {
        if (! $user) {
            return;
        }

        Notification::make()
            ->title($title)
            ->body($body)
            ->when(! $failedRowsCount, fn (Notification $n) => $n->success())
            ->when($failedRowsCount && $failedRowsCount < $totalRows, fn (Notification $n) => $n->warning())
            ->when($failedRowsCount === $totalRows, fn (Notification $n) => $n->danger())
            ->sendToDatabase($user);
    }
}

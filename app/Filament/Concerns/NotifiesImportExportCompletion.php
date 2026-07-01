<?php

namespace App\Filament\Concerns;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Filament's own import/export actions only persist a completion notification
 * to the database when the job runs on a real queue connection — ours are
 * forced to 'sync' (no queue worker required), so Filament shows a toast only.
 * This persists the same result to the acting user's notification bell too,
 * scoped to that user via the notifiable relation (each staff/admin/owner only
 * ever sees notifications sent to their own account).
 *
 * For EXPORTS, the bell notification also carries the "Download CSV/XLSX"
 * buttons (the same signed download links Filament puts on its toast), so the
 * admin can grab the file straight from the bell instead of the export modal.
 */
trait NotifiesImportExportCompletion
{
    private static function notifyCompletionToDatabase(
        ?Authenticatable $user,
        string $title,
        string $body,
        int $failedRowsCount,
        int $totalRows,
        ?Export $export = null,
    ): void {
        if (! $user) {
            return;
        }

        // Admin panel guard — used to sign the download URL for the right guard.
        $authGuard = Filament::getCurrentPanel()?->getAuthGuard() ?? 'admin';

        Notification::make()
            ->title($title)
            ->body($body)
            ->when(! $failedRowsCount, fn (Notification $n) => $n->success())
            ->when($failedRowsCount && $failedRowsCount < $totalRows, fn (Notification $n) => $n->warning())
            ->when($failedRowsCount === $totalRows, fn (Notification $n) => $n->danger())
            // Exports only: attach a signed download button per available format
            // (CSV + XLSX by default), but only when at least one row succeeded —
            // there's nothing to download from a fully-failed export. Reuses
            // Filament's own ExportFormat::getDownloadNotificationAction() so the
            // signed URL / guard / format handling stays identical to the toast.
            ->when(
                $export !== null && $failedRowsCount < $totalRows,
                fn (Notification $n) => $n->actions(array_map(
                    fn (ExportFormat $format) => $format->getDownloadNotificationAction($export, $authGuard),
                    $export->getExporter([], [])->getFormats(),
                )),
            )
            ->sendToDatabase($user);
    }
}

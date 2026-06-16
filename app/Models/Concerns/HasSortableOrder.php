<?php

namespace App\Models\Concerns;

/**
 * Keeps the `sort_order` column tidy for models that are arranged by
 * drag-and-drop in the admin panel (Filament's ->reorderable('sort_order')).
 *
 * The admin no longer types a position by hand — which is what allowed
 * duplicate ranks and negative values. Instead, a brand-new record is simply
 * appended to the end (max + 1), and reordering is done by dragging rows.
 */
trait HasSortableOrder
{
    public static function bootHasSortableOrder(): void
    {
        static::creating(function ($model): void {
            if (is_null($model->sort_order)) {
                $model->sort_order = (int) static::max('sort_order') + 1;
            }
        });
    }
}

<?php

namespace App\Filament\Imports;

use App\Mail\OrderDeliveredMail;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderImporter extends Importer
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('order_number')
                ->label('Order Number')
                ->requiredMapping()
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('status')
                ->castStateUsing(fn (?string $state): ?string => filled($state) ? Str::lower($state) : null)
                ->rules(['nullable', Rule::in(array_diff(Order::statuses(), ['cancelled']))])
                ->ignoreBlankState(),
            ImportColumn::make('tracking_number')
                ->label('Tracking Number')
                ->rules(['nullable', 'string', 'max:100'])
                ->ignoreBlankState(),
            ImportColumn::make('customer_name')
                ->label('Customer Name')
                ->rules(['nullable', 'string', 'max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('customer_phone')
                ->label('Customer Phone')
                ->rules(['nullable', 'string', 'max:50'])
                ->ignoreBlankState(),
            ImportColumn::make('street')
                ->rules(['nullable', 'string', 'max:255'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('city')
                ->rules(['nullable', 'string', 'max:255'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('postcode')
                ->rules(['nullable', 'string', 'max:20'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('state')
                ->rules(['nullable', 'string', 'max:255'])
                ->fillRecordUsing(fn () => null),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('notifyCustomers')
                ->label('Email customers about status changes')
                ->default(true),
        ];
    }

    /**
     * Orders are never created via import — only matched by order_number, which is
     * looked up case-insensitively (trimmed by Filament's own cast pipeline already)
     * so a stray space or case slip doesn't fail a row that should have matched.
     */
    public function resolveRecord(): Order
    {
        $orderNumber = $this->data['order_number'] ?? null;

        $order = filled($orderNumber)
            ? Order::whereRaw('LOWER(order_number) = ?', [Str::lower($orderNumber)])->first()
            : null;

        if (! $order) {
            throw new RowImportFailedException("No order found with order number \"{$orderNumber}\".");
        }

        return $order;
    }

    protected function beforeValidate(): void
    {
        $status = $this->data['status'] ?? null;

        if ($status === 'cancelled') {
            if ($this->record->status === 'cancelled') {
                // Already cancelled and the row still says so — an untouched re-import
                // of an exported file, not an attempted transition. Treat as no-op.
                $this->data['status'] = null;
            } else {
                throw new RowImportFailedException('Cancelling an order via import is not supported — use the "Cancel & restock" action on the order instead.');
            }
        }

        if ($status === 'shipped') {
            $trackingNumber = $this->data['tracking_number'] ?? null;

            if (blank($trackingNumber) && blank($this->record->tracking_number)) {
                throw new RowImportFailedException('A tracking number is required to mark an order as shipped.');
            }
        }
    }

    protected function afterFill(): void
    {
        $address = $this->record->shipping_address ?? [];

        foreach (['street', 'city', 'postcode', 'state'] as $key) {
            if (array_key_exists($key, $this->data) && filled($this->data[$key])) {
                $address[$key] = $this->data[$key];
            }
        }

        $this->record->shipping_address = $address;
    }

    protected function afterSave(): void
    {
        if (! $this->record->wasChanged('status')) {
            return;
        }

        if (! in_array($this->record->status, ['shipped', 'delivered'], true)) {
            return;
        }

        if (! ($this->options['notifyCustomers'] ?? true)) {
            return;
        }

        try {
            $mail = $this->record->status === 'shipped'
                ? new OrderShippedMail($this->record->fresh())
                : new OrderDeliveredMail($this->record->fresh());

            Mail::to($this->record->customer_email)->send($mail);
        } catch (\Throwable $e) {
            logger()->error('Order import status-change email failed: ' . $e->getMessage());
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your order import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}

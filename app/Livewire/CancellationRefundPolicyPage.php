<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Services\RefundCalculator;
use Livewire\Component;

class CancellationRefundPolicyPage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'Cancellation & Refund Policy',
            description: 'How and when you can cancel a paid order at Win Win Car Audio, and how refunds are calculated.',
        );
    }

    public function render()
    {
        $calculator = new RefundCalculator;

        return view('livewire.cancellation-refund-policy-page', [
            'fullRefundHours' => $calculator->fullRefundHours(),
            'feePercent' => $calculator->feePercent(),
        ])->layout('layouts.app');
    }
}

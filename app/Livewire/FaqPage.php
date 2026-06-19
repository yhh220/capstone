<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Faq;
use Livewire\Component;

class FaqPage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'FAQ',
            description: 'Find quick answers about bookings, showroom visits, compatibility, and online shopping at Win Win Car Audio.',
        );
    }

    public function render()
    {
        // Active FAQs grouped by category in display order (uncategorised grouped under '').
        $faqGroups = Faq::published()->groupBy(fn (Faq $faq) => $faq->category ?: '');

        return view('livewire.faq-page', ['faqGroups' => $faqGroups])->layout('layouts.app');
    }
}

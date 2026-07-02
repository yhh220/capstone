<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesOwner;
use App\Livewire\Concerns\SetsSeo;
use Livewire\Component;
use App\Models\Contact;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class ContactPage extends Component
{
    use NotifiesOwner, SetsSeo, UsesSpamProtection;

    public HoneypotData $honeypotData;

    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $subject  = '';
    public string $message  = '';

    public function mount(): void
    {
        $this->honeypotData = new HoneypotData();

        $this->setSeo(
            title: 'Contact Us',
            description: 'Get in touch with Win Win Car Studio. Send us a message, chat on WhatsApp, or visit our showroom in Shah Alam.',
        );
    }

    protected array $rules = [
        'name'     => 'required|min:2|max:100',
        'email'    => 'required|email|max:100',
        'phone'    => 'nullable|max:20',
        'subject'  => 'required|min:3|max:150',
        'message'  => 'required|min:10|max:2000',
    ];

    public function submit(): void
    {
        // Livewire keeps the error bag across requests and addError() appends,
        // while @error shows first() — reset so retries report fresh messages.
        $this->resetErrorBag();

        // Honeypot check — powered by spatie/laravel-honeypot (field check + time gate)
        $this->protectAgainstSpam();

        $ip = request()->ip();

        // Layer 1 — burst limit: max 3 submissions per IP per 5 minutes.
        $key = 'contact.' . $ip;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('name', __('Too many submissions. Please wait :seconds seconds before trying again.', ['seconds' => $seconds]));
            return;
        }

        // Layer 2 — daily cap: stops slow drip-spam that resets the burst window.
        $dailyKey = 'contact-daily.' . $ip;
        if (RateLimiter::tooManyAttempts($dailyKey, 8)) {
            $this->addError('name', __('You have reached today’s message limit. Please WhatsApp us directly instead.'));
            return;
        }

        $this->validate();

        $clean = [
            'name'    => strip_tags($this->name),
            'email'   => $this->email,
            'phone'   => strip_tags($this->phone),
            'subject' => strip_tags($this->subject),
            'message' => strip_tags($this->message),
        ];

        // Layer 3 — duplicate guard: an identical message from the same email in
        // the last 10 minutes is treated as a silent success (no DB row, no hint
        // to a spammer, no owner email), so repeat-submit spam goes nowhere.
        $isDuplicate = Contact::where('email', $clean['email'])
            ->where('message', $clean['message'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        RateLimiter::hit($key, 300);        // 5-minute burst window
        RateLimiter::hit($dailyKey, 86400); // 24-hour daily window

        if (! $isDuplicate) {
            $contact = Contact::create($clean);

            $this->notifyOwner(
                'New enquiry',
                [
                    'Name'    => $clean['name'],
                    'Email'   => $clean['email'],
                    'Phone'   => $clean['phone'],
                    'Subject' => $clean['subject'],
                    'Message' => $clean['message'],
                ],
                url('/admin/contacts/' . $contact->getKey() . '/edit'),
                'View enquiry',
            );
        }

        $this->reset(['name', 'email', 'phone', 'subject', 'message']);
        session()->flash('success', __('Thank you! Your message has been sent. We will get back to you shortly.'));
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.contact-page')->layout('layouts.app');
    }
}

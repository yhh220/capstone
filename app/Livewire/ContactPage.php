<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use Livewire\Component;
use App\Models\Contact;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class ContactPage extends Component
{
    use SetsSeo, UsesSpamProtection;

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
        // Honeypot check — powered by spatie/laravel-honeypot (field check + time gate)
        $this->protectAgainstSpam();

        // Rate limiting: max 3 submissions per IP per 5 minutes
        $key = 'contact.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('name', __('Too many submissions. Please wait :seconds seconds before trying again.', ['seconds' => $seconds]));
            return;
        }

        $this->validate();

        RateLimiter::hit($key, 300); // 5-minute decay window

        Contact::create([
            'name'    => strip_tags($this->name),
            'email'   => $this->email,
            'phone'   => strip_tags($this->phone),
            'subject' => strip_tags($this->subject),
            'message' => strip_tags($this->message),
        ]);

        $this->reset(['name', 'email', 'phone', 'subject', 'message']);
        session()->flash('success', __('Thank you! Your message has been sent. We will get back to you shortly.'));
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.contact-page')->layout('layouts.app');
    }
}

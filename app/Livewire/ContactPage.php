<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use Livewire\Component;
use App\Models\Contact;
use Illuminate\Support\Facades\RateLimiter;

class ContactPage extends Component
{
    use SetsSeo;

    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $subject  = '';
    public string $message  = '';

    /** Honeypot — must stay empty; bots fill this field */
    public string $honeypot = '';

    public function mount(): void
    {
        $this->setSeo(
            title: 'Contact Us',
            description: 'Get in touch with Win Win Car Studio. Send us a message, chat on WhatsApp, or visit our showroom in Kuala Lumpur.',
        );
    }

    protected array $rules = [
        'name'     => 'required|min:2|max:100',
        'email'    => 'required|email|max:100',
        'phone'    => 'nullable|max:20',
        'subject'  => 'required|min:3|max:150',
        'message'  => 'required|min:10|max:2000',
        'honeypot' => 'max:0',   // must be empty
    ];

    public function submit(): void
    {
        // Honeypot check — silently discard bot submissions without revealing detection
        if ($this->honeypot !== '') {
            $this->reset(['name', 'email', 'phone', 'subject', 'message', 'honeypot']);
            session()->flash('success', __('Thank you! Your message has been sent. We will get back to you shortly.'));
            return;
        }

        // Rate limiting: max 3 submissions per IP per 5 minutes
        $key = 'contact.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('name', "Too many submissions. Please wait {$seconds} seconds before trying again.");
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

        $this->reset(['name', 'email', 'phone', 'subject', 'message', 'honeypot']);
        session()->flash('success', __('Thank you! Your message has been sent. We will get back to you shortly.'));
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.contact-page')->layout('layouts.app');
    }
}

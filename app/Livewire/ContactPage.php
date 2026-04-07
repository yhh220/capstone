<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact;

class ContactPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $subject = '';
    public string $message = '';

    protected array $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:100',
        'phone' => 'nullable|max:20',
        'subject' => 'required|min:3|max:150',
        'message' => 'required|min:10|max:2000',
    ];

    public function submit(): void
    {
        $this->validate();

        Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'phone', 'subject', 'message']);
        session()->flash('success', 'Thank you! Your message has been sent. We will get back to you shortly.');
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.contact-page')->layout('layouts.app');
    }
}

<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfilePage extends Component
{
    use SetsSeo;

    public string $name         = '';
    public string $email        = '';
    public string $phone        = '';
    public string $gender       = '';
    public string $addressLine  = '';
    public string $city         = '';
    public string $postcode     = '';
    public string $state        = '';
    public string $courier      = '';

    protected $rules = [
        'name'        => 'required|string|max:255',
        'phone'       => 'nullable|string|max:20',
        'gender'      => 'nullable|in:male,female,other',
        'addressLine' => 'nullable|string|max:500',
        'city'        => 'nullable|string|max:255',
        'postcode'    => 'nullable|string|max:10',
        'state'       => 'nullable|string|max:100',
        'courier'     => 'nullable|in:poslaju,dhl,ninjavan,gdex',
    ];

    public function mount(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        $user = Auth::user();
        $this->name        = $user->name ?? '';
        $this->email       = $user->email ?? '';
        $this->phone       = $user->phone ?? '';
        $this->gender      = $user->gender ?? '';
        $this->addressLine = $user->address_line ?? '';
        $this->city        = $user->city ?? '';
        $this->postcode    = $user->postcode ?? '';
        $this->state       = $user->state ?? '';
        $this->courier     = $user->preferred_courier ?? '';

        $this->setSeo(
            title: 'My Profile',
            description: 'Update your profile details.',
        );
    }

    public function updateProfile(): void
    {
        $this->validate();

        $user = Auth::user();
        $user->update([
            'name'              => $this->name,
            'phone'             => $this->phone,
            'gender'            => $this->gender ?: null,
            'address_line'      => $this->addressLine,
            'city'              => $this->city,
            'postcode'          => $this->postcode,
            'state'             => $this->state ?: null,
            'preferred_courier' => $this->courier ?: null,
        ]);

        session()->flash('success', __('Profile updated successfully!'));
    }

    public function render()
    {
        return view('livewire.profile-page')->layout('layouts.app');
    }
}

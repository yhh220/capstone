<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\CartItem;
use App\Services\EmailOtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

    // Change-password fields (accounts that already have a password)
    public string $current_password          = '';
    public string $new_password              = '';
    public string $new_password_confirmation = '';

    // Set-password fields (social-login accounts with no password yet) — OTP-gated
    public bool   $settingPassword               = false;
    public string $set_otp                        = '';
    public string $set_new_password               = '';
    public string $set_new_password_confirmation  = '';

    // Delete-account confirmation
    public string $delete_password = '';

    protected $rules = [
        'name'        => 'required|string|max:255',
        'phone'       => 'nullable|string|max:20',
        'gender'      => 'nullable|in:male,female,other',
        'addressLine' => 'nullable|string|max:500',
        'city'        => 'nullable|string|max:255',
        'postcode'    => 'nullable|string|max:10',
        'state'       => 'nullable|string|max:100',
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
            'name'         => $this->name,
            'phone'        => $this->phone,
            'gender'       => $this->gender ?: null,
            'address_line' => $this->addressLine,
            'city'         => $this->city,
            'postcode'     => $this->postcode,
            'state'        => $this->state ?: null,
        ]);

        session()->flash('success', __('Profile updated successfully!'));
    }

    /**
     * Change the signed-in user's password after confirming the current one.
     */
    public function updatePassword(): void
    {
        $this->validate([
            'current_password'          => ['required', 'current_password'],
            'new_password'              => ['required', 'confirmed', Password::defaults()],
            'new_password_confirmation' => ['required'],
        ], [
            'current_password.current_password' => __('Your current password is incorrect.'),
            'new_password.min'                  => __('Password must be at least 8 characters.'),
        ]);

        // The 'hashed' cast hashes the new password on save.
        Auth::user()->forceFill(['password' => $this->new_password])->save();

        $this->reset('current_password', 'new_password', 'new_password_confirmation');

        session()->flash('password_success', __('Password changed successfully!'));
    }

    /**
     * Step 1 of setting a password (social-login accounts with none): email a code.
     */
    public function sendSetPasswordCode(): void
    {
        $user = Auth::user();
        if ($user->hasPassword()) {
            return; // already has one — the Change Password form applies instead
        }

        $otp  = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_SET_PASSWORD, $user->email);
        if ($wait > 0) {
            $this->addError('set_otp', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));
            return;
        }

        $otp->send(EmailOtpService::PURPOSE_SET_PASSWORD, $user->email);
        $this->settingPassword = true;
        $this->set_otp = '';
        session()->flash('password_success', __('We sent a 6-digit code to :email.', ['email' => $user->email]));
    }

    /**
     * Step 2: confirm the code and set the password. The account can then sign in
     * with email + password as well as socially.
     */
    public function confirmSetPassword(): void
    {
        $user = Auth::user();
        if ($user->hasPassword()) {
            return;
        }

        $this->validate([
            'set_otp'                      => ['required', 'digits:6'],
            'set_new_password'             => ['required', 'confirmed', Password::defaults()],
            'set_new_password_confirmation' => ['required'],
        ], [
            'set_new_password.min' => __('Password must be at least 8 characters.'),
        ]);

        $otp = app(EmailOtpService::class);
        if (! $otp->verify(EmailOtpService::PURPOSE_SET_PASSWORD, $user->email, $this->set_otp)) {
            $this->addError('set_otp', __('Invalid or expired code. Please try again.'));
            return;
        }

        $user->forceFill(['password' => $this->set_new_password])->save();
        $otp->clear(EmailOtpService::PURPOSE_SET_PASSWORD, $user->email);

        $this->reset('settingPassword', 'set_otp', 'set_new_password', 'set_new_password_confirmation');
        session()->flash('password_success', __('Password set! You can now also log in with your email and password.'));
    }

    /**
     * Soft-delete the account after the user re-enters their password. The row
     * stays in the database (deleted_at) so order/booking history keeps its
     * foreign keys; the SoftDeletes scope stops the account from signing in again.
     */
    public function deleteAccount(): void
    {
        $this->validate([
            'delete_password' => ['required', 'current_password'],
        ], [
            'delete_password.current_password' => __('Your password is incorrect.'),
        ]);

        $user = Auth::user();

        // Release the guest/session cart so a deleted account leaves nothing behind.
        CartItem::where('user_id', $user->id)->delete();

        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('home'), navigate: false);
    }

    public function render()
    {
        return view('livewire.profile-page')->layout('layouts.app');
    }
}

<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\CartItem;
use App\Services\EmailOtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    // Set-password flow state (social-login accounts with no password yet)
    public bool $settingPassword = false;

    // Login verification (email 2FA) state
    public bool $twoFactorEnabled   = false;
    public bool $enablingTwoFactor  = false;
    public bool $disablingTwoFactor = false; // OTP path for social-only users

    // Account deletion OTP state (for social-only users without a password)
    public bool $deletingAccountByOtp = false;

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
        $this->twoFactorEnabled = $user->two_factor_enabled ?? false;

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
     * Change the signed-in user's password. Receives values as action params so
     * they never enter the Livewire snapshot (which is plaintext JSON in the DOM).
     */
    public function updatePassword(
        #[\SensitiveParameter] string $currentPassword,
        #[\SensitiveParameter] string $newPassword,
        #[\SensitiveParameter] string $confirmation,
    ): void {
        $v = Validator::make(
            ['current_password' => $currentPassword, 'new_password' => $newPassword, 'new_password_confirmation' => $confirmation],
            [
                'current_password'          => ['required', 'current_password'],
                'new_password'              => ['required', 'confirmed', Password::defaults()],
                'new_password_confirmation' => ['required'],
            ],
            [
                'current_password.current_password' => __('Your current password is incorrect.'),
                'new_password.min'                  => __('Password must be at least 8 characters.'),
            ]
        );

        if ($v->fails()) {
            foreach ($v->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }

        Auth::user()->forceFill(['password' => $newPassword])->save();
        $this->resetErrorBag(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('password-changed');
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
     * Step 2: confirm the OTP code and set the password. Params keep sensitive
     * values out of the Livewire snapshot.
     */
    public function confirmSetPassword(
        #[\SensitiveParameter] string $otp,
        #[\SensitiveParameter] string $newPassword,
        #[\SensitiveParameter] string $confirmation,
    ): void {
        $user = Auth::user();
        if ($user->hasPassword()) {
            return;
        }

        $v = Validator::make(
            ['set_otp' => $otp, 'set_new_password' => $newPassword, 'set_new_password_confirmation' => $confirmation],
            [
                'set_otp'                       => ['required', 'digits:6'],
                'set_new_password'              => ['required', 'confirmed', Password::defaults()],
                'set_new_password_confirmation' => ['required'],
            ],
            ['set_new_password.min' => __('Password must be at least 8 characters.')]
        );

        if ($v->fails()) {
            foreach ($v->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }

        $otpService = app(EmailOtpService::class);
        if (! $otpService->verify(EmailOtpService::PURPOSE_SET_PASSWORD, $user->email, $otp)) {
            $this->addError('set_otp', __('Invalid or expired code. Please try again.'));
            return;
        }

        $user->forceFill(['password' => $newPassword])->save();
        $otpService->clear(EmailOtpService::PURPOSE_SET_PASSWORD, $user->email);

        $this->settingPassword = false;
        $this->resetErrorBag(['set_otp', 'set_new_password', 'set_new_password_confirmation']);
        session()->flash('password_success', __('Password set! You can now also log in with your email and password.'));
    }

    /**
     * Step 1 of enabling login verification: email a code to prove the user
     * controls the inbox that will be guarding their future logins.
     */
    public function sendEnableTwoFactorCode(): void
    {
        $user = Auth::user();
        if ($user->two_factor_enabled) {
            return;
        }

        $otp  = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_ENABLE_2FA, $user->email);
        if ($wait > 0) {
            $this->addError('two_factor_otp', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));
            return;
        }

        $otp->send(EmailOtpService::PURPOSE_ENABLE_2FA, $user->email);
        $this->enablingTwoFactor = true;
        session()->flash('two_factor_success', __('We sent a 6-digit code to :email.', ['email' => $user->email]));
    }

    /**
     * Step 2: confirm the code, then turn login verification on.
     */
    public function confirmEnableTwoFactor(#[\SensitiveParameter] string $otp): void
    {
        $user = Auth::user();
        if ($user->two_factor_enabled) {
            return;
        }

        $v = Validator::make(['two_factor_otp' => $otp], ['two_factor_otp' => ['required', 'digits:6']]);
        if ($v->fails()) {
            $this->addError('two_factor_otp', $v->errors()->first('two_factor_otp'));
            return;
        }

        $otpService = app(EmailOtpService::class);
        if (! $otpService->verify(EmailOtpService::PURPOSE_ENABLE_2FA, $user->email, $otp)) {
            $this->addError('two_factor_otp', __('Invalid or expired code. Please try again.'));
            return;
        }

        $user->forceFill(['two_factor_enabled' => true])->save();

        $this->twoFactorEnabled  = true;
        $this->enablingTwoFactor = false;
        $this->resetErrorBag('two_factor_otp');
        session()->flash('two_factor_success', __('Login verification is now on. We will email you a code each time you sign in.'));
    }

    public function cancelEnableTwoFactor(): void
    {
        app(EmailOtpService::class)->clear(EmailOtpService::PURPOSE_ENABLE_2FA, Auth::user()->email);
        $this->enablingTwoFactor = false;
        $this->resetErrorBag('two_factor_otp');
    }

    /**
     * Turn login verification back off. Requires the current password so a
     * hijacked, already-open session can't silently downgrade the account's
     * security on its way out. Social-only accounts (no password) must use
     * the OTP path: sendDisableTwoFactorCode() + confirmDisableTwoFactorViaOtp().
     */
    public function disableTwoFactor(#[\SensitiveParameter] string $password): void
    {
        if (! Auth::user()->hasPassword()) {
            $this->addError('two_factor_password', __('Your account uses social sign-in. Please use the email code path to turn off verification.'));
            return;
        }

        $v = Validator::make(
            ['two_factor_password' => $password],
            ['two_factor_password' => ['required', 'current_password']],
            ['two_factor_password.current_password' => __('Your password is incorrect.')]
        );

        if ($v->fails()) {
            $this->addError('two_factor_password', $v->errors()->first('two_factor_password'));
            return;
        }

        Auth::user()->forceFill(['two_factor_enabled' => false])->save();
        $this->twoFactorEnabled = false;
        $this->resetErrorBag('two_factor_password');
        session()->flash('two_factor_success', __('Login verification is now off.'));
    }

    /**
     * Step 1 (social-only): send an OTP to prove inbox control before disabling 2FA.
     */
    public function sendDisableTwoFactorCode(): void
    {
        $user = Auth::user();
        if ($user->hasPassword() || ! $user->two_factor_enabled) {
            return;
        }

        $otp  = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_DISABLE_2FA, $user->email);
        if ($wait > 0) {
            $this->addError('disable_two_factor_otp', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));
            return;
        }

        $otp->send(EmailOtpService::PURPOSE_DISABLE_2FA, $user->email);
        $this->disablingTwoFactor = true;
        session()->flash('two_factor_success', __('We sent a 6-digit code to :email.', ['email' => $user->email]));
    }

    /**
     * Step 2 (social-only): confirm the code, then turn login verification off.
     */
    public function confirmDisableTwoFactorViaOtp(#[\SensitiveParameter] string $otp): void
    {
        $user = Auth::user();
        if ($user->hasPassword() || ! $user->two_factor_enabled) {
            return;
        }

        $v = Validator::make(['disable_two_factor_otp' => $otp], ['disable_two_factor_otp' => ['required', 'digits:6']]);
        if ($v->fails()) {
            $this->addError('disable_two_factor_otp', $v->errors()->first('disable_two_factor_otp'));
            return;
        }

        if (! app(EmailOtpService::class)->verify(EmailOtpService::PURPOSE_DISABLE_2FA, $user->email, $otp)) {
            $this->addError('disable_two_factor_otp', __('Invalid or expired code. Please try again.'));
            return;
        }

        $user->forceFill(['two_factor_enabled' => false])->save();
        $this->twoFactorEnabled   = false;
        $this->disablingTwoFactor = false;
        $this->resetErrorBag('disable_two_factor_otp');
        session()->flash('two_factor_success', __('Login verification is now off.'));
    }

    public function cancelDisableTwoFactor(): void
    {
        app(EmailOtpService::class)->clear(EmailOtpService::PURPOSE_DISABLE_2FA, Auth::user()->email);
        $this->disablingTwoFactor = false;
        $this->resetErrorBag('disable_two_factor_otp');
    }

    /**
     * Soft-delete the account after the user re-enters their password. Social-only
     * accounts (no password) must use the OTP path instead.
     */
    public function deleteAccount(#[\SensitiveParameter] string $password = ''): void
    {
        if (! Auth::user()->hasPassword()) {
            $this->addError('delete_password', __('Your account uses social sign-in. Please use the email code path to delete your account.'));
            return;
        }

        $v = Validator::make(
            ['delete_password' => $password],
            ['delete_password' => ['required', 'current_password']],
            ['delete_password.current_password' => __('Your password is incorrect.')]
        );

        if ($v->fails()) {
            foreach ($v->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }

        $this->performAccountDeletion();
    }

    /**
     * Step 1 (social-only): send an OTP before deleting the account.
     */
    public function sendDeleteAccountCode(): void
    {
        $user = Auth::user();
        if ($user->hasPassword()) {
            return;
        }

        $otp  = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_DELETE_ACCOUNT, $user->email);
        if ($wait > 0) {
            $this->addError('delete_otp', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));
            return;
        }

        $otp->send(EmailOtpService::PURPOSE_DELETE_ACCOUNT, $user->email);
        $this->deletingAccountByOtp = true;
    }

    /**
     * Step 2 (social-only): confirm OTP then delete the account.
     */
    public function confirmDeleteAccountViaOtp(#[\SensitiveParameter] string $otp): void
    {
        $user = Auth::user();
        if ($user->hasPassword()) {
            return;
        }

        $v = Validator::make(['delete_otp' => $otp], ['delete_otp' => ['required', 'digits:6']]);
        if ($v->fails()) {
            $this->addError('delete_otp', $v->errors()->first('delete_otp'));
            return;
        }

        if (! app(EmailOtpService::class)->verify(EmailOtpService::PURPOSE_DELETE_ACCOUNT, $user->email, $otp)) {
            $this->addError('delete_otp', __('Invalid or expired code. Please try again.'));
            return;
        }

        $this->performAccountDeletion();
    }

    private function performAccountDeletion(): void
    {
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

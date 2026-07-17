<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Illuminate\Support\Js;

class EditProfile extends BaseEditProfile
{
    /**
     * Changing the password updates the AuthenticateSession-tracked hash for
     * THIS request, but the session itself still ends up invalidated shortly
     * after (e.g. other guards/devices, or anything that touches the session
     * store), which silently bounced the admin back to the login page with no
     * explanation. Block on an explicit "OK" before sending them there instead.
     */
    public function save(): void
    {
        $passwordChanged = filled($this->data['password'] ?? null);

        parent::save();

        if ($passwordChanged) {
            $this->js(
                'alert('.Js::from(__('Your password has been changed successfully. Click OK to log in again.')).');'
                .'window.location.href = '.Js::from(Filament::getLoginUrl()).';'
            );
        }
    }
}

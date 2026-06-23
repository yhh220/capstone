<?php

namespace App\Exceptions;

/**
 * Thrown by EmailOtpService::send() when the underlying mail send fails.
 * Unlike booking/order notifications (best-effort, non-blocking), the OTP
 * email is the entire point of the action — silently continuing would leave
 * the user waiting on a code that never arrives, with no indication anything
 * went wrong. Carries a message safe to show directly to the user.
 */
class OtpSendFailedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct(__('We could not send the verification email. Please try again in a moment.'));
    }
}

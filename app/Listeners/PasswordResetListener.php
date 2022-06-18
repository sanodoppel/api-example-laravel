<?php

namespace App\Listeners;

use App\Services\AuthService;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(private AuthService $authService)
    {
    }


    /**
     * @param PasswordReset $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        $this->authService->logoutForUser($event->user);
    }
}

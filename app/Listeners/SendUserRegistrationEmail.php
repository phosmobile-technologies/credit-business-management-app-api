<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use App\Mail\NewUserRegisteredMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserRegistrationEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewUserRegistered $event
     * @return void
     */
    public function handle(NewUserRegistered $event)
    {
        $user = $event->user;
        $defaultPassword = $event->defaultPassword;

        /**
         * This check was added because when importing existing users, they might not have an email address.
         */
        if ($user->getEmailForVerification()) {
            Mail::to($user)->send(new NewUserRegisteredMail($user, $defaultPassword));
        }
    }

    /**
     * Handle a job failure.
     *
     * @param NewUserRegistered $event
     * @param  \Exception $exception
     * @return void
     */
    public function failed(NewUserRegistered $event, $exception)
    {
        Log::info($exception);
    }
}

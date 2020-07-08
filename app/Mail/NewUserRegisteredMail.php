<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $defaultPassword;
    /**
     * @var
     */
    public $registration_source;

    /**
     * @var string
     */
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $defaultPassword
     * @param $registration_source
     */
    public function __construct(User $user, string $defaultPassword, $registration_source)
    {
        $this->user = $user;
        $this->defaultPassword = $defaultPassword;
        $this->registration_source = $registration_source;
        $frontEndUrl = env('FRONT_END_URL');
        $this->loginUrl = "{$frontEndUrl}/login";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('UMC - New User Sign Up')
            ->view('email.signup.user');
    }
}

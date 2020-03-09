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
    public $source;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $defaultPassword
     * @param $source
     */
    public function __construct(User $user, string $defaultPassword, $source)
    {
        $this->user = $user;
        $this->defaultPassword = $defaultPassword;
        $this->source = $source;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Springverse - New User Registration')
            ->markdown('email.signup.user');
    }
}

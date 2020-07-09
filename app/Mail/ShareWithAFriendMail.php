<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareWithAFriendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    public $data;
    /**
     * @var User
     */
    public $user;

    /**
     * @var
     */
    public  $redirectUrl;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @param Authenticatable $user
     */
    public function __construct(array $data, Authenticatable $user)
    {
        //
        $this->data = $data;
        $this->user = $user;
        $frontEndUrl = env('FRONT_END_URL');
        $this->redirectUrl = $frontEndUrl."/signup";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Invitation to join UMC Upspring Multipurpose Cooperative')
            ->markdown('email.shareWithAFriend');
    }
}

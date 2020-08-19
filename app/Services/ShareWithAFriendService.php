<?php


namespace App\Services;


use App\Mail\ShareWithAFriendMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ShareWithAFriendService
{
    public function inviteFriend(array $data)
    {
        Mail::to($data['email'])->send(new ShareWithAFriendMail($data));
        return 'Invite email was sent to'. $data['email'];
    }
}

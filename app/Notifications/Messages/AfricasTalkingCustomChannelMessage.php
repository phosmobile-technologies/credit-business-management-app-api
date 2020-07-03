<?php

namespace App\Notifications\Messages;


class AfricasTalkingCustomChannelMessage
{
    public $message = "";

    public function message($message)
    {
        $this->message = $message;
        return $this->message;
    }
}
